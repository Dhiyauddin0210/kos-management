<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class SettingController extends Controller
{
    public const SETTING_KEYS = [
        'kos_name', 'kos_tagline', 'kos_address', 'kos_phone', 'kos_whatsapp',
        'kos_email', 'kos_logo', 'qr_base_url',
        'default_due_date', 'reminder_days_before',
    ];

    public function index()
    {
        $settings = Setting::many(self::SETTING_KEYS, [
            'kos_name'              => 'Kos Adin',
            'kos_tagline'           => '',
            'kos_address'           => '',
            'kos_phone'             => '',
            'kos_whatsapp'          => '',
            'kos_email'             => '',
            'kos_logo'              => null,
            'qr_base_url'           => url('/kos'),
            'default_due_date'      => 10,
            'reminder_days_before'  => 3,
        ]);

        $bankAccounts = BankAccount::orderByDesc('is_primary')->orderBy('bank_name')->get();

        return view('admin.settings.index', compact('settings', 'bankAccounts'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'kos_name'              => ['required', 'string', 'max:100'],
            'kos_tagline'           => ['nullable', 'string', 'max:200'],
            'kos_address'           => ['nullable', 'string', 'max:300'],
            'kos_phone'             => ['nullable', 'string', 'max:30'],
            'kos_whatsapp'          => ['nullable', 'string', 'max:30'],
            'kos_email'             => ['nullable', 'email', 'max:100'],
            'kos_logo'              => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,svg', 'max:1024'],
            'qr_base_url'           => ['nullable', 'string', 'max:255'],
            'default_due_date'      => ['nullable', 'integer', 'min:1', 'max:28'],
            'reminder_days_before'  => ['nullable', 'integer', 'min:1', 'max:30'],
        ]);

        if ($request->hasFile('kos_logo')) {
            $oldLogo = Setting::get('kos_logo');
            if ($oldLogo) {
                Storage::disk('public')->delete($oldLogo);
            }
            $validated['kos_logo'] = $request->file('kos_logo')->store('settings', 'public');
        } else {
            unset($validated['kos_logo']);
        }

        foreach ($validated as $key => $value) {
            if ($value !== null) {
                Setting::set($key, $value);
            }
        }

        return redirect()->route('admin.settings.index', ['tab' => $request->input('_tab', 'info')])
            ->with('success', 'Pengaturan berhasil disimpan.');
    }

    /**
     * Download QR sebagai SVG (ga butuh imagick extension).
     */
    public function qrCode(Request $request)
    {
        $url = $request->query('url') ?: Setting::get('qr_base_url', url('/kos'));
        $size = (int) ($request->query('size') ?: 600);

        // Pake format SVG biar ga butuh imagick
        $qr = QrCode::format('svg')
            ->size($size)
            ->margin(2)
            ->errorCorrection('H')
            ->generate($url);

        $filename = 'qr-kos-' . now()->format('Ymd-His') . '.svg';

        return response($qr)
            ->header('Content-Type', 'image/svg+xml')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    /**
     * Preview QR sebagai SVG inline (langsung tampil di img src).
     */
    public function qrPreview(Request $request)
    {
        $url = $request->query('url') ?: Setting::get('qr_base_url', url('/kos'));
        $size = (int) ($request->query('size') ?: 300);

        $qr = QrCode::format('svg')
            ->size($size)
            ->margin(1)
            ->errorCorrection('H')
            ->generate($url);

        return response($qr)->header('Content-Type', 'image/svg+xml');
    }
}