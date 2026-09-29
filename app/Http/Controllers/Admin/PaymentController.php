<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\PaymentRejected;
use App\Mail\PaymentVerified;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class PaymentController extends Controller
{
    public const STATUS_LABELS = [
        'pending'  => 'Menunggu Verifikasi',
        'verified' => 'Terverifikasi',
        'rejected' => 'Ditolak',
    ];

    public function index(Request $request)
    {
        $query = Payment::with(['invoice.tenant.room', 'verifier'])
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->when($request->filled('search'), function ($q) use ($request) {
                $s = $request->search;
                $q->where(function ($q) use ($s) {
                    $q->whereHas('invoice', fn ($i) => $i->where('invoice_number', 'like', "%{$s}%"))
                      ->orWhereHas('invoice.tenant', fn ($t) => $t->where('full_name', 'like', "%{$s}%"));
                });
            })
            ->orderByRaw("FIELD(status, 'pending', 'verified', 'rejected')")
            ->orderByDesc('payment_date')
            ->orderByDesc('id');

        $payments = $query->paginate(15)->withQueryString();

        $stats = [
            'pending_count'  => Payment::where('status', 'pending')->count(),
            'pending_amount' => Payment::where('status', 'pending')->sum('amount'),
            'verified_count' => Payment::where('status', 'verified')->count(),
            'verified_amount'=> Payment::where('status', 'verified')->sum('amount'),
            'rejected_count' => Payment::where('status', 'rejected')->count(),
        ];

        $statusLabels = self::STATUS_LABELS;

        return view('admin.payments.index', compact('payments', 'stats', 'statusLabels'));
    }

    public function show(Payment $payment)
    {
        $payment->load(['invoice.tenant.room.property', 'invoice.tenant.user', 'verifier']);

        return view('admin.payments.show', compact('payment'));
    }

    public function verify(Request $request, Payment $payment)
    {
        if ($payment->status !== 'pending') {
            return back()->with('error', 'Pembayaran ini sudah diproses sebelumnya.');
        }

        $validated = $request->validate([
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        DB::transaction(function () use ($payment, $validated) {
            $payment->update([
                'status'      => 'verified',
                'verified_at' => now(),
                'verified_by' => auth()->id(),
                'notes'       => $validated['notes'] ?? $payment->notes,
            ]);

            $payment->invoice->update([
                'status'  => 'paid',
                'paid_at' => $payment->payment_date->setTime(now()->hour, now()->minute),
            ]);
        });

        // Kirim email notifikasi (setelah transaction berhasil)
        try {
            $email = $payment->invoice->tenant->user?->email;
            if ($email) {
                Mail::to($email)->send(new PaymentVerified($payment));
            }
        } catch (\Exception $e) {
            logger()->error('Gagal kirim email PaymentVerified: ' . $e->getMessage());
        }

        return redirect()->route('admin.payments.show', $payment)
            ->with('success', "Pembayaran {$payment->invoice->invoice_number} berhasil diverifikasi.");
    }

    public function reject(Request $request, Payment $payment)
    {
        if ($payment->status !== 'pending') {
            return back()->with('error', 'Pembayaran ini sudah diproses sebelumnya.');
        }

        $validated = $request->validate([
            'notes' => ['required', 'string', 'max:500'],
        ], [
            'notes.required' => 'Alasan penolakan wajib diisi.',
        ]);

        DB::transaction(function () use ($payment, $validated) {
            $payment->update([
                'status'      => 'rejected',
                'verified_at' => now(),
                'verified_by' => auth()->id(),
                'notes'       => $validated['notes'],
            ]);

            if ($payment->invoice->status === 'pending') {
                $payment->invoice->update(['status' => 'unpaid']);
            }
        });

        // Kirim email notifikasi
        try {
            $email = $payment->invoice->tenant->user?->email;
            if ($email) {
                Mail::to($email)->send(new PaymentRejected($payment));
            }
        } catch (\Exception $e) {
            logger()->error('Gagal kirim email PaymentRejected: ' . $e->getMessage());
        }

        return redirect()->route('admin.payments.show', $payment)
            ->with('success', "Pembayaran {$payment->invoice->invoice_number} ditolak.");
    }

    public function destroy(Payment $payment)
    {
        DB::transaction(function () use ($payment) {
            if ($payment->status === 'verified' && $payment->invoice->status === 'paid') {
                $payment->invoice->update([
                    'status'  => 'unpaid',
                    'paid_at' => null,
                ]);
            }

            $payment->delete();
        });

        return redirect()->route('admin.payments.index')
            ->with('success', 'Pembayaran berhasil dihapus.');
    }
}