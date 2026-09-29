<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\LeadRequest;
use App\Models\Lead;
use App\Models\Room;
use Illuminate\Http\Request;

class LeadController extends Controller
{
    public const STATUS_LABELS = [
        'new'       => 'Baru',
        'contacted' => 'Dihubungi',
        'closed'    => 'Ditutup',
    ];

    public const SOURCE_LABELS = [
        'qr'     => 'QR Code',
        'web'    => 'Website',
        'manual' => 'Manual',
    ];

    public function index(Request $request)
    {
        $query = Lead::with(['property', 'room'])
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->when($request->filled('source'), fn ($q) => $q->where('source', $request->source))
            ->when($request->filled('search'), function ($q) use ($request) {
                $s = $request->search;
                $q->where(function ($q) use ($s) {
                    $q->where('name', 'like', "%{$s}%")
                      ->orWhere('phone', 'like', "%{$s}%")
                      ->orWhere('email', 'like', "%{$s}%")
                      ->orWhereHas('room', fn ($r) => $r->where('room_number', 'like', "%{$s}%"));
                });
            })
            ->orderByRaw("FIELD(status, 'new', 'contacted', 'closed')")
            ->orderByDesc('created_at');

        $leads = $query->paginate(15)->withQueryString();

        $stats = [
            'total'     => Lead::count(),
            'new'       => Lead::where('status', 'new')->count(),
            'contacted' => Lead::where('status', 'contacted')->count(),
            'closed'    => Lead::where('status', 'closed')->count(),
        ];

        $statusLabels = self::STATUS_LABELS;
        $sourceLabels = self::SOURCE_LABELS;

        return view('admin.leads.index', compact('leads', 'stats', 'statusLabels', 'sourceLabels'));
    }

    public function show(Lead $lead)
    {
        $lead->load(['property', 'room.property']);

        // Kamar tersedia untuk opsi convert (kalau nanti mau langsung pilih)
        $availableRooms = Room::with('property')
            ->where('status', 'available')
            ->orderBy('property_id')
            ->orderBy('room_number')
            ->get();

        $statusLabels = self::STATUS_LABELS;
        $sourceLabels = self::SOURCE_LABELS;

        return view('admin.leads.show', compact('lead', 'availableRooms', 'statusLabels', 'sourceLabels'));
    }

    public function update(LeadRequest $request, Lead $lead)
    {
        $data = $request->validated();

        $lead->update([
            'status'          => $data['status'],
            'follow_up_notes' => $data['follow_up_notes'] ?? $lead->follow_up_notes,
        ]);

        return redirect()->route('admin.leads.show', $lead)
            ->with('success', 'Status lead berhasil diperbarui.');
    }

    public function destroy(Lead $lead)
    {
        $lead->delete();

        return redirect()->route('admin.leads.index')
            ->with('success', 'Lead berhasil dihapus.');
    }

    /**
     * Convert lead ke form tambah penghuni dengan data pre-filled.
     * Redirect ke /admin/tenants/create dengan query string berisi data lead.
     */
    public function convert(Lead $lead)
    {
        // Tandai lead sebagai contacted kalau masih new
        if ($lead->status === 'new') {
            $lead->update(['status' => 'contacted']);
        }

        // Redirect ke form tenant create dengan data pre-filled
        return redirect()->route('admin.tenants.create', [
            'from_lead' => $lead->id,
            'full_name' => $lead->name,
            'phone'     => $lead->phone,
            'email'     => $lead->email,
            'room_id'   => $lead->room_id,
        ]);
    }
}