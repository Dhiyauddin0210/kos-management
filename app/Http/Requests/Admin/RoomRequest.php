<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RoomRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // Saat CREATE, status 'occupied' tidak boleh dipilih manual:
        // status itu diatur otomatis saat penghuni ditambahkan.
        $allowedStatus = $this->isMethod('post')
            ? ['available', 'maintenance']
            : ['available', 'occupied', 'maintenance'];

        return [
            'property_id' => ['required', 'exists:properties,id'],
            // Nomor kamar unik PER properti (kamar yang sedang diedit diabaikan)
            'room_number' => [
                'required', 'string', 'max:20',
                Rule::unique('rooms', 'room_number')
                    ->where(fn ($q) => $q->where('property_id', $this->input('property_id')))
                    ->ignore($this->route('room')),
            ],
            'type'             => ['required', 'string', 'max:50'],
            'price'            => ['required', 'numeric', 'min:0'],
            'size'             => ['nullable', 'string', 'max:20'],
            'facilities'       => ['nullable', 'array'],
            'facilities.*'     => ['string', 'max:50'],
            'facilities_other' => ['nullable', 'string', 'max:255'], // fasilitas tambahan, pisah koma
            'status'           => ['nullable', Rule::in($allowedStatus)],
            'description'      => ['nullable', 'string'],
            'photo'            => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }
}
