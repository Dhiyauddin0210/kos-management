<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RoomRequest;
use App\Models\Property;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RoomController extends Controller
{
    public const FACILITIES = ['AC', 'WiFi', 'KM Dalam', 'Water Heater', 'TV', 'Kasur', 'Lemari', 'Meja'];

    public const STATUS_LABELS = [
        'available'   => 'Tersedia',
        'occupied'    => 'Terisi',
        'maintenance' => 'Perbaikan',
    ];

    public function index(Request $request)
    {
        $rooms = Room::with('property')
            ->when($request->filled('property_id'), fn ($q) => $q->where('property_id', $request->property_id))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->when($request->filled('search'), fn ($q) => $q->where('room_number', 'like', '%' . $request->search . '%'))
            ->orderBy('property_id', 'asc')
            ->orderBy('room_number', 'asc')
            ->paginate(10)
            ->withQueryString();

        $properties   = Property::orderBy('name', 'asc')->pluck('name', 'id');
        $statusLabels = self::STATUS_LABELS;

        return view('admin.rooms.index', compact('rooms', 'properties', 'statusLabels'));
    }

    public function create(Request $request)
    {
        return view('admin.rooms.create', [
            'properties'      => Property::where('is_active', true)->orderBy('name', 'asc')->pluck('name', 'id'),
            'facilityOptions' => self::FACILITIES,
            'statusOptions'   => collect(self::STATUS_LABELS)->only(['available', 'maintenance'])->all(),
            'selectedProperty' => $request->query('property_id'),
        ]);
    }

    public function store(RoomRequest $request)
    {
        $data = $request->validated();

        $data['facilities'] = $this->buildFacilities($request);
        $data['status']     = $data['status'] ?? 'available';
        unset($data['facilities_other']);

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('rooms', 'public');
        }

        Room::create($data);

        return redirect()->route('admin.rooms.index')
            ->with('success', 'Kamar berhasil ditambahkan.');
    }

    public function show(Room $room)
    {
        $room->load(['property', 'activeTenant.user']);

        $invoices = $room->invoices()
            ->with('tenant')
            ->orderByDesc('invoices.year')
            ->orderByDesc('invoices.month')
            ->limit(12)
            ->get();

        return view('admin.rooms.show', compact('room', 'invoices'));
    }

    public function edit(Room $room)
    {
        return view('admin.rooms.edit', [
            'room'       => $room,
            'properties' => Property::orderBy('name', 'asc')->pluck('name', 'id'),
            'facilityOptions' => array_values(array_unique(array_merge(self::FACILITIES, $room->facilities ?? []))),
            'statusOptions'   => self::STATUS_LABELS,
        ]);
    }

    public function update(RoomRequest $request, Room $room)
    {
        $data = $request->validated();

        $hasActiveTenant = $room->activeTenant()->exists();
        $newStatus       = $data['status'] ?? $room->status;

        if ($hasActiveTenant && $newStatus !== 'occupied') {
            return back()->withInput()->withErrors([
                'status' => 'Kamar masih dihuni penghuni aktif. Lakukan checkout penghuni terlebih dahulu.',
            ]);
        }
        if (! $hasActiveTenant && $newStatus === 'occupied') {
            return back()->withInput()->withErrors([
                'status' => 'Status "Terisi" diatur otomatis saat penghuni ditambahkan ke kamar ini.',
            ]);
        }

        $data['facilities'] = $this->buildFacilities($request);
        $data['status']     = $newStatus;
        unset($data['facilities_other']);

        if ($request->hasFile('photo')) {
            if ($room->photo) {
                Storage::disk('public')->delete($room->photo);
            }
            $data['photo'] = $request->file('photo')->store('rooms', 'public');
        } else {
            unset($data['photo']);
        }

        $room->update($data);

        return redirect()->route('admin.rooms.index')
            ->with('success', 'Kamar berhasil diperbarui.');
    }

    public function destroy(Room $room)
    {
        if ($room->activeTenant()->exists()) {
            return back()->with('error', 'Kamar tidak bisa dihapus karena masih dihuni penghuni aktif.');
        }

        if ($room->photo) {
            Storage::disk('public')->delete($room->photo);
        }

        $room->delete();

        return redirect()->route('admin.rooms.index')
            ->with('success', 'Kamar berhasil dihapus.');
    }

    private function buildFacilities(Request $request): array
    {
        $checked = (array) $request->input('facilities', []);

        $others = collect(explode(',', (string) $request->input('facilities_other')))
            ->map(fn ($item) => trim($item))
            ->filter();

        return collect($checked)->merge($others)->unique()->values()->all();
    }
}