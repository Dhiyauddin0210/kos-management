<?php

namespace Database\Seeders;

use App\Models\Invoice;
use App\Models\Payment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class PaymentSeeder extends Seeder
{
    public function run(): void
    {
        if (Payment::exists()) {
            return;
        }

        $admin = User::where('role', 'admin')->firstOrFail();

        // Invoice lunas -> payment verified. Invoice pending -> payment pending.
        $invoices = Invoice::whereIn('status', ['paid', 'pending'])->orderBy('id')->get();

        foreach ($invoices as $invoice) {
            if ($invoice->status === 'paid') {
                $paidAt = Carbon::parse($invoice->paid_at);

                Payment::create([
                    'invoice_id'   => $invoice->id,
                    'amount'       => $invoice->amount,
                    'payment_date' => $paidAt->toDateString(),
                    'proof'        => 'proofs/' . $invoice->invoice_number . '.jpg',
                    'status'       => 'verified',
                    'notes'        => 'Transfer via BCA',
                    'verified_at'  => $paidAt->copy()->addDay(), // diverifikasi H+1
                    'verified_by'  => $admin->id,
                ]);
            } else {
                // Sudah upload bukti, tapi admin belum verifikasi
                Payment::create([
                    'invoice_id'   => $invoice->id,
                    'amount'       => $invoice->amount,
                    'payment_date' => Carbon::today()->subDays(mt_rand(0, 2))->toDateString(),
                    'proof'        => 'proofs/' . $invoice->invoice_number . '.jpg',
                    'status'       => 'pending',
                    'notes'        => 'Transfer via Mandiri, mohon dicek',
                    'verified_at'  => null,
                    'verified_by'  => null,
                ]);
            }
        }
    }
}
