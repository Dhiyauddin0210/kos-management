<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class InvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tenant_id' => ['required', 'exists:tenants,id'],
            'month'     => ['required', 'integer', 'min:1', 'max:12'],
            'year'      => ['required', 'integer', 'min:2020', 'max:2100'],
            'amount'    => ['nullable', 'numeric', 'min:0'],
            'due_date'  => ['required', 'date'],
        ];
    }

    public function attributes(): array
    {
        return [
            'tenant_id' => 'penghuni',
            'month'     => 'bulan',
            'year'      => 'tahun',
            'amount'    => 'jumlah',
            'due_date'  => 'jatuh tempo',
        ];
    }
}