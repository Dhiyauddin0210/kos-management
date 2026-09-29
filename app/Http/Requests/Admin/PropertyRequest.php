<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class PropertyRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Akses sudah dijaga oleh middleware 'admin'
        return true;
    }

    protected function prepareForValidation(): void
    {
        // Checkbox tidak terkirim kalau tidak dicentang -> paksa jadi boolean
        $this->merge(['is_active' => $this->boolean('is_active')]);
    }

    public function rules(): array
    {
        return [
            'name'        => ['required', 'string', 'max:255'],
            'address'     => ['required', 'string', 'max:255'],
            'city'        => ['required', 'string', 'max:100'],
            'phone'       => ['nullable', 'string', 'max:20'],
            'description' => ['nullable', 'string'],
            'photo'       => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'], // 2 MB
            'is_active'   => ['boolean'],
        ];
    }
}
