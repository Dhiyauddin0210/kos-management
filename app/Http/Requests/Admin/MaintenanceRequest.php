<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MaintenanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status'      => ['required', Rule::in(['reported', 'in_progress', 'resolved', 'rejected'])],
            'priority'    => ['required', Rule::in(['low', 'medium', 'high'])],
            'admin_notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function attributes(): array
    {
        return [
            'status'      => 'status',
            'priority'    => 'prioritas',
            'admin_notes' => 'catatan admin',
        ];
    }
}