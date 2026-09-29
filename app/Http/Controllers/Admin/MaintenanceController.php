<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\MaintenanceRequest;
use App\Models\Maintenance;
use Illuminate\Http\Request;

class MaintenanceController extends Controller
{
    public const STATUS_LABELS = [
        'reported'    => 'Dilaporkan',
        'in_progress' => 'Diproses',
        'resolved'    => 'Selesai',
        'rejected'    => 'Ditolak',
    ];

    public const PRIORITY_LABELS = [
        'low'    => 'Rendah',
        'medium' => 'Sedang',
        'high'   => 'Tinggi',
    ];

    public function index(Request $request)
    {
        $query = Maintenance::with(['tenant.room', 'room.property'])
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->when($request->filled('priority'), fn ($q) => $q->where('priority', $request->priority))
            ->when($request->filled('search'), function ($q) use ($request) {
                $s = $request->search;
                $q->where(function ($q) use ($s) {
                    $q->where('title', 'like', "%{$s}%")
                      ->orWhere('description', 'like', "%{$s}%")
                      ->orWhereHas('tenant', fn ($t) => $t->where('full_name', 'like', "%{$s}%"))
                      ->orWhereHas('room', fn ($r) => $r->where('room_number', 'like', "%{$s}%"));
                });
            })
            ->orderByRaw("FIELD(status, 'reported', 'in_progress', 'resolved', 'rejected')")
            ->orderByRaw("FIELD(priority, 'high', 'medium', 'low')")
            ->orderByDesc('created_at');

        $maintenances = $query->paginate(15)->withQueryString();

        $stats = [
            'total'       => Maintenance::count(),
            'reported'    => Maintenance::where('status', 'reported')->count(),
            'in_progress' => Maintenance::where('status', 'in_progress')->count(),
            'resolved'    => Maintenance::where('status', 'resolved')->count(),
            'high'        => Maintenance::where('priority', 'high')->whereIn('status', ['reported', 'in_progress'])->count(),
        ];

        $statusLabels = self::STATUS_LABELS;
        $priorityLabels = self::PRIORITY_LABELS;

        return view('admin.maintenances.index', compact('maintenances', 'stats', 'statusLabels', 'priorityLabels'));
    }

    public function show(Maintenance $maintenance)
    {
        $maintenance->load(['tenant.user', 'tenant.room.property', 'room.property']);

        $statusLabels = self::STATUS_LABELS;
        $priorityLabels = self::PRIORITY_LABELS;

        return view('admin.maintenances.show', compact('maintenance', 'statusLabels', 'priorityLabels'));
    }

    public function update(MaintenanceRequest $request, Maintenance $maintenance)
    {
        $data = $request->validated();

        $updateData = [
            'status'      => $data['status'],
            'priority'    => $data['priority'],
            'admin_notes' => $data['admin_notes'] ?? $maintenance->admin_notes,
        ];

        // Set resolved_at otomatis kalau status jadi resolved
        if ($data['status'] === 'resolved' && $maintenance->status !== 'resolved') {
            $updateData['resolved_at'] = now();
        }

        // Reset resolved_at kalau status balik dari resolved
        if ($data['status'] !== 'resolved' && $maintenance->status === 'resolved') {
            $updateData['resolved_at'] = null;
        }

        $maintenance->update($updateData);

        return redirect()->route('admin.maintenances.show', $maintenance)
            ->with('success', 'Laporan maintenance berhasil diperbarui.');
    }

    public function destroy(Maintenance $maintenance)
    {
        $maintenance->delete();

        return redirect()->route('admin.maintenances.index')
            ->with('success', 'Laporan maintenance berhasil dihapus.');
    }
}