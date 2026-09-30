<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Extension;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ExtensionController extends Controller
{
    public const STATUS_LABELS = [
        'pending'  => 'Menunggu',
        'approved' => 'Disetujui',
        'rejected' => 'Ditolak',
    ];

    public function index(Request $request)
    {
        $tenant = auth()->user()->tenant;

        $query = Extension::where('tenant_id', $tenant->id)
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->orderByDesc('created_at');

        $extensions = $query->paginate(10)->withQueryString();

        $stats = [
            'total'    => Extension::where('tenant_id', $tenant->id)->count(),
            'pending'  => Extension::where('tenant_id', $tenant->id)->where('status', 'pending')->count(),
            'approved' => Extension::where('tenant_id', $tenant->id)->where('status', 'approved')->count(),
            'rejected' => Extension::where('tenant_id', $tenant->id)->where('status', 'rejected')->count(),
        ];

        $statusLabels = self::STATUS_LABELS;

        return view('tenant.extensions.index', compact('extensions', 'stats', 'statusLabels'));
    }

    public function create()
    {
        $tenant = auth()->user()->tenant;
        $tenant->load('room');

        // Cek apakah masih ada pengajuan pending
        $hasPending = Extension::where('tenant_id', $tenant->id)
            ->where('status', 'pending')
            ->exists();

        return view('tenant.extensions.create', compact('tenant', 'hasPending'));
    }

    public function store(Request $request)
    {
        $tenant = auth()->user()->tenant;
        $tenant->load('room');

        // Cek duplikat pending
        $hasPending = Extension::where('tenant_id', $tenant->id)
            ->where('status', 'pending')
            ->exists();

        if ($hasPending) {
            return back()->with('error', 'Anda masih punya pengajuan perpanjangan yang menunggu. Tunggu admin memproses dulu.');
        }

        $validated = $request->validate([
            'duration_months' => ['required', 'integer', 'min:1', 'max:24'],
            'notes'           => ['nullable', 'string', 'max:500'],
        ], [
            'duration_months.required' => 'Durasi perpanjangan wajib diisi.',
            'duration_months.min'      => 'Minimal 1 bulan.',
            'duration_months.max'      => 'Maksimal 24 bulan.',
        ]);

        $currentEnd = $tenant->end_date;
        $requestedEnd = $currentEnd->copy()->addMonths((int) $validated['duration_months']);
        $additionalCost = $tenant->room->price * (int) $validated['duration_months'];

        Extension::create([
            'tenant_id'          => $tenant->id,
            'current_end_date'   => $currentEnd,
            'requested_end_date' => $requestedEnd,
            'duration_months'    => (int) $validated['duration_months'],
            'additional_cost'    => $additionalCost,
            'status'             => 'pending',
            'notes'              => $validated['notes'] ?? null,
        ]);

        return redirect()->route('tenant.extensions.index')
            ->with('success', 'Pengajuan perpanjangan berhasil dikirim. Menunggu persetujuan admin.');
    }

    public function show(Extension $extension)
    {
        // Cek kepemilikan
        $tenant = auth()->user()->tenant;
        if ($extension->tenant_id !== $tenant->id) {
            abort(403, 'Anda tidak berhak melihat pengajuan ini.');
        }

        $extension->load(['tenant.room.property']);

        $statusLabels = self::STATUS_LABELS;

        return view('tenant.extensions.show', compact('extension', 'statusLabels'));
    }
}