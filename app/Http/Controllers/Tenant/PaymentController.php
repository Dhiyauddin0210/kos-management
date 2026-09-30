<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public const STATUS_LABELS = [
        'pending'  => 'Menunggu Verifikasi',
        'verified' => 'Terverifikasi',
        'rejected' => 'Ditolak',
    ];

    public function index(Request $request)
    {
        $tenant = auth()->user()->tenant;

        $query = Payment::whereHas('invoice', fn ($q) => $q->where('tenant_id', $tenant->id))
            ->with('invoice')
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->orderByDesc('payment_date')
            ->orderByDesc('id');

        $payments = $query->paginate(15)->withQueryString();

        $stats = [
            'total'    => Payment::whereHas('invoice', fn ($q) => $q->where('tenant_id', $tenant->id))->count(),
            'pending'  => Payment::whereHas('invoice', fn ($q) => $q->where('tenant_id', $tenant->id))->where('status', 'pending')->count(),
            'verified' => Payment::whereHas('invoice', fn ($q) => $q->where('tenant_id', $tenant->id))->where('status', 'verified')->count(),
            'rejected' => Payment::whereHas('invoice', fn ($q) => $q->where('tenant_id', $tenant->id))->where('status', 'rejected')->count(),
        ];

        $statusLabels = self::STATUS_LABELS;

        return view('tenant.payments.index', compact('payments', 'stats', 'statusLabels'));
    }

    public function store(Request $request, Invoice $invoice)
    {
        // Pastikan invoice milik tenant yang login
        $tenant = auth()->user()->tenant;
        if ($invoice->tenant_id !== $tenant->id) {
            abort(403, 'Anda tidak berhak mengakses tagihan ini.');
        }

        if ($invoice->status === 'paid') {
            return back()->with('error', 'Tagihan ini sudah lunas.');
        }

        if ($invoice->status === 'pending') {
            return back()->with('error', 'Tagihan ini sudah ada pembayaran yang menunggu verifikasi.');
        }

        $validated = $request->validate([
            'amount'       => ['required', 'numeric', 'min:1'],
            'payment_date' => ['required', 'date', 'before_or_equal:today'],
            'proof'        => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'notes'        => ['nullable', 'string', 'max:500'],
        ], [
            'proof.required' => 'Bukti transfer wajib diupload.',
            'proof.image'    => 'File harus berupa gambar.',
            'proof.max'      => 'Ukuran file maksimal 2 MB.',
        ]);

        DB::transaction(function () use ($validated, $invoice, $request) {
            $proofPath = $request->file('proof')->store('proofs', 'public');

            Payment::create([
                'invoice_id'   => $invoice->id,
                'amount'       => $validated['amount'],
                'payment_date' => $validated['payment_date'],
                'proof'        => $proofPath,
                'status'       => 'pending',
                'notes'        => $validated['notes'] ?? null,
            ]);

            $invoice->update(['status' => 'pending']);
        });

        return redirect()->route('tenant.invoices.show', $invoice)
            ->with('success', 'Bukti transfer berhasil diupload. Menunggu verifikasi admin.');
    }
}