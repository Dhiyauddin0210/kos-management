<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TenantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // Pada edit, {tenant} terisi (route model binding); pada create bernilai null.
        $tenant = $this->route('tenant');

        return [
            // Nama lengkap dipakai sebagai nama akun (users.name) sekaligus tenants.full_name
            'full_name' => ['required', 'string', 'max:255'],
            'email'     => [
                'required', 'email', 'max:255',
                Rule::unique('users', 'email')->ignore($tenant?->user_id),
            ],
            // Kosong = auto-generate (create) / tidak diganti (edit)
            'password'        => ['nullable', 'string', 'min:8', 'max:100'],
            'phone'           => ['required', 'string', 'max:20'],
            'ktp_number'      => ['required', 'digits:16'], // tepat 16 digit angka
            'address'         => ['nullable', 'string'],
            'room_id'         => ['required', 'exists:rooms,id'],
            'start_date'      => ['required', 'date'],
            'duration_months' => ['required', 'integer', 'min:1', 'max:60'],
        ];
    }
}
