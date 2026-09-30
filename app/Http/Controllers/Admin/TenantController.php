<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\TenantRequest;
use App\Models\Room;
use App\Models\Tenant;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class TenantController extends Controller
{
    public function index(Request $request)
    {
        $tenants = Tenant::with(['room.property', 'user'])
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->when($request->filled('room_id'), fn ($q) => $q->where('room_id', $request->room_id))
            ->when($request->filled('search'), function ($q) use ($request) {
                $s = $request->search;
                $q->where(function ($q) use ($s) {
                    $q->where('full_name', 'like', "%{$s}%")
                        ->orWhere('ktp_number', 'like', "%{$s}%")
                        ->orWhereHas('room', fn ($r) => $r->where('room_number', 'like', "%{$s}%"));
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $roomFilterOptions = $this->roomOptions(Room::with('property')->orderBy('property_id', 'asc')->orderBy('room_number', 'asc')->get());

        return view('admin.tenants.index', compact('tenants', 'roomFilterOptions'));
    }

    public function create(Request $request)
    {
        $rooms = Room::with('property')->where('status', 'available')
            ->orderBy('property_id', 'asc')->orderBy('room_number', 'asc')->get();

        $prefill = [
            'from_lead' => $request->query('from_lead'),
            'full_name' => $request->query('full_name', ''),
            'phone'     => $request->query('phone', ''),
            'email'     => $request->query('email', ''),
            'room_id'   => $request->query('room_id', ''),
        ];

        return view('admin.tenants.create', [
            'roomOptions' => $this->roomOptions($rooms),
            'prefill'     => $prefill,
        ]);
    }

    public function store(TenantRequest $request)
    {
        $data = $request->validated();

        $generated     = empty($data['password']);
        $plainPassword = $generated ? Str::random(10) : $data['password'];

        DB::transaction(function () use ($data, $plainPassword) {
            $room = Room::whereKey($data['room_id'])->lockForUpdate()->firstOrFail();

            if ($room->status !== 'available') {
                throw ValidationException::withMessages(['room_id' => 'Kamar yang dipilih sudah tidak tersedia.']);
            }

            $user = User::create([
                'name'              => $data['full_name'],
                'email'             => $data['email'],
                'password'          => Hash::make($plainPassword),
                'role'              => 'penghuni',
                'phone'             => $data['phone'],
                'email_verified_at' => now(),
            ]);

            $start = Carbon::parse($data['start_date']);

            Tenant::create([
                'user_id'         => $user->id,
                'room_id'         => $room->id,
                'full_name'       => $data['full_name'],
                'ktp_number'      => $data['ktp_number'],
                'phone'           => $data['phone'],
                'address'         => $data['address'] ?? null,
                'start_date'      => $start,
                'end_date'        => $start->copy()->addMonths((int) $data['duration_months']),
                'duration_months' => (int) $data['duration_months'],
                'status'          => 'active',
            ]);

            $room->update(['status' => 'occupied']);
        });

        $message = 'Penghuni berhasil ditambahkan.';
        if ($generated) {
            $message .= " Password akun (catat sekarang, tidak ditampilkan lagi): {$plainPassword}";
        }

        return redirect()->route('admin.tenants.index')->with('success', $message);
    }

    public function show(Tenant $tenant)
    {
        $tenant->load([
            'user',
            'room.property',
            'invoices'     => fn ($q) => $q->orderByDesc('year')->orderByDesc('month'),
            'maintenances' => fn ($q) => $q->latest(),
            'extensions'   => fn ($q) => $q->latest(),
        ]);

        return view('admin.tenants.show', compact('tenant'));
    }

    public function edit(Tenant $tenant)
    {
        $tenant->load('user');

        $rooms = Room::with('property')
            ->where('status', 'available')
            ->orWhere('id', $tenant->room_id)
            ->orderBy('property_id', 'asc')->orderBy('room_number', 'asc')->get();

        return view('admin.tenants.edit', [
            'tenant'      => $tenant,
            'roomOptions' => $this->roomOptions($rooms),
        ]);
    }

    public function update(TenantRequest $request, Tenant $tenant)
    {
        $data     = $request->validated();
        $isActive = $tenant->status === 'active';

        DB::transaction(function () use ($data, $tenant, $isActive) {
            $userData = [
                'name'  => $data['full_name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
            ];
            if (! empty($data['password'])) {
                $userData['password'] = Hash::make($data['password']);
            }
            $tenant->user->update($userData);

            $roomId = $tenant->room_id;

            if ($isActive && (int) $data['room_id'] !== $tenant->room_id) {
                $newRoom = Room::whereKey($data['room_id'])->lockForUpdate()->firstOrFail();

                if ($newRoom->status !== 'available') {
                    throw ValidationException::withMessages(['room_id' => 'Kamar tujuan sudah tidak tersedia.']);
                }

                Room::whereKey($tenant->room_id)->update(['status' => 'available']);
                $newRoom->update(['status' => 'occupied']);
                $roomId = $newRoom->id;
            }

            $fields = [
                'room_id'         => $roomId,
                'full_name'       => $data['full_name'],
                'ktp_number'      => $data['ktp_number'],
                'phone'           => $data['phone'],
                'address'         => $data['address'] ?? null,
                'start_date'      => $data['start_date'],
                'duration_months' => (int) $data['duration_months'],
            ];

            if ($isActive) {
                $fields['end_date'] = Carbon::parse($data['start_date'])->addMonths((int) $data['duration_months']);
            }

            $tenant->update($fields);
        });

        return redirect()->route('admin.tenants.show', $tenant)
            ->with('success', 'Data penghuni berhasil diperbarui.');
    }

    public function checkout(Tenant $tenant)
    {
        if ($tenant->status !== 'active') {
            return back()->with('error', 'Penghuni ini sudah tidak aktif.');
        }

        DB::transaction(function () use ($tenant) {
            $tenant->update([
                'status'   => 'inactive',
                'end_date' => today(),
            ]);

            $tenant->room()->update(['status' => 'available']);
        });

        return redirect()->route('admin.tenants.show', $tenant)
            ->with('success', "Checkout berhasil. Kamar {$tenant->room->room_number} kini tersedia.");
    }

    public function destroy(Tenant $tenant)
    {
        DB::transaction(function () use ($tenant) {
            if ($tenant->status === 'active') {
                $tenant->room()->update(['status' => 'available']);
            }

            $user = $tenant->user;

            $tenant->delete();
            $user?->delete();
        });

        return redirect()->route('admin.tenants.index')
            ->with('success', 'Penghuni dan akunnya berhasil dihapus.');
    }

    private function roomOptions($rooms): array
    {
        return $rooms->mapWithKeys(fn (Room $r) => [
            $r->id => sprintf(
                '%s - %s (%s, Rp %s)',
                $r->room_number,
                $r->property?->name,
                $r->type,
                number_format($r->price, 0, ',', '.')
            ),
        ])->all();
    }
}