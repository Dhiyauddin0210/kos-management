<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class PaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'invoice_id'   => ['required', 'exists:invoices,id'],
            'amount'       => ['required', 'numeric', 'min:0'],
            'payment_date' => ['required', 'date'],
            'proof'        => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'notes'        => ['nullable', 'string', 'max:500'],
        ];
    }

    public function attributes(): array
    {
        return [
            'invoice_id'   => 'tagihan',
            'amount'       => 'jumlah',
            'payment_date' => 'tanggal pembayaran',
            'proof'        => 'bukti transfer',
            'notes'        => 'catatan',
        ];
    }
}