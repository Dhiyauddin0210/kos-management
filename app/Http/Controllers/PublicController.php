<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\Property;
use App\Models\Room;
use App\Models\Setting;
use Illuminate\Http\Request;

class PublicController extends Controller
{
    /**
     * Landing page kos (dari QR scan).
     */
    public function landing()
    {
        // Ambil properti aktif (kalo multi-kos, ambil yang pertama)
        $property = Property::where('is_active', true)->first();

        // Ambil kamar kosong
        $availableRooms = Room::with('property')
            ->where('status', 'available')
            ->orderBy('room_number', 'asc')
            ->get();

        // Setting kos
        $settings = [
            'name'     => Setting::get('kos_name', 'Kos Adin'),
            'tagline'  => Setting::get('kos_tagline', 'Hunian nyaman dan strategis'),
            'address'  => Setting::get('kos_address', ''),
            'phone'    => Setting::get('kos_phone', ''),
            'whatsapp' => Setting::get('kos_whatsapp', ''),
            'email'    => Setting::get('kos_email', ''),
            'logo'     => Setting::get('kos_logo'),
        ];

        return view('public.landing', compact('property', 'availableRooms', 'settings'));
    }

    /**
     * Submit form minat sewa → masuk ke tabel leads.
     */
    public function submitLead(Request $request)
    {
        $validated = $request->validate([
            'name'    => ['required', 'string', 'max:100'],
            'phone'   => ['required', 'string', 'max:20'],
            'email'   => ['nullable', 'email', 'max:100'],
            'room_id' => ['nullable', 'exists:rooms,id'],
            'message' => ['nullable', 'string', 'max:500'],
        ], [
            'name.required'  => 'Nama wajib diisi.',
            'phone.required' => 'No. HP wajib diisi.',
            'phone.max'      => 'No. HP maksimal 20 karakter.',
        ]);

        $property = Property::where('is_active', true)->first();

        Lead::create([
            'property_id' => $property?->id,
            'room_id'     => $validated['room_id'] ?? null,
            'name'        => $validated['name'],
            'phone'       => $validated['phone'],
            'email'       => $validated['email'] ?? null,
            'message'     => $validated['message'] ?? null,
            'source'      => 'qr',
            'status'      => 'new',
        ]);

        return redirect()->route('public.thanks');
    }

    /**
     * Halaman terima kasih setelah submit form.
     */
    public function thanks()
    {
        $settings = [
            'name'     => Setting::get('kos_name', 'Kos Adin'),
            'whatsapp' => Setting::get('kos_whatsapp', ''),
            'phone'    => Setting::get('kos_phone', ''),
        ];

        return view('public.thanks', compact('settings'));
    }
}