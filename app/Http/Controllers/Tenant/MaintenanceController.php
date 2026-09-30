<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Maintenance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
        $tenant = auth()->user()->tenant;

        $query = Maintenance::where('tenant_id', $tenant->id)
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->orderByDesc('created_at');

        $maintenances = $query->paginate(10)->withQueryString();

        $stats = [
            'total'       => Maintenance::where('tenant_id', $tenant->id)->count(),
            'reported'    => Maintenance::where('tenant_id', $tenant->id)->where('status', 'reported')->count(),
            'in_progress' => Maintenance::where('tenant_id', $tenant->id)->where('status', 'in_progress')->count(),
            'resolved'    => Maintenance::where('tenant_id', $tenant->id)->where('status', 'resolved')->count(),
        ];

        $statusLabels = self::STATUS_LABELS;
        $priorityLabels = self::PRIORITY_LABELS;

        return view('tenant.maintenances.index', compact('maintenances', 'stats', 'statusLabels', 'priorityLabels'));
    }

    public function create()
    {
        $priorityLabels = self::PRIORITY_LABELS;

        return view('tenant.maintenances.create', compact('priorityLabels'));
    }

    public function store(Request $request)
    {
        $tenant = auth()->user()->tenant;

        $validated = $request->validate([
            'title'       => ['required', 'string', 'max:100'],
            'description' => ['required', 'string', 'max:1000'],
            'priority'    => ['required', 'in:low,medium,high'],
            'photo'       => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ], [
            'title.required'       => 'Judul laporan wajib diisi.',
            'description.required' => 'Deskripsi kerusakan wajib diisi.',
            'photo.max'            => 'Ukuran foto maksimal 2 MB.',
        ]);

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('maintenances', 'public');
        }

        Maintenance::create([
            'tenant_id'   => $tenant->id,
            'room_id'     => $tenant->room_id,
            'title'       => $validated['title'],
            'description' => $validated['description'],
            'photo'       => $photoPath,
            'status'      => 'reported',
            'priority'    => $validated['priority'],
        ]);

        return redirect()->route('tenant.maintenances.index')
            ->with('success', 'Laporan kerusakan berhasil dikirim. Admin akan segera menindaklanjuti.');
    }

    public function show(Maintenance $maintenance)
    {
        // Cek kepemilikan
        $tenant = auth()->user()->tenant;
        if ($maintenance->tenant_id !== $tenant->id) {
            abort(403, 'Anda tidak berhak melihat laporan ini.');
        }

        $maintenance->load(['room.property']);

        $statusLabels = self::STATUS_LABELS;
        $priorityLabels = self::PRIORITY_LABELS;

        return view('tenant.maintenances.show', compact('maintenance', 'statusLabels', 'priorityLabels'));
    }
}