{{-- Dipakai bersama oleh create & edit. Variabel: $roomOptions, opsional: $tenant --}}
@php
    $tenant   = $tenant ?? null;
    $isEdit   = (bool) $tenant;
    $inactive = $isEdit && $tenant->status === 'inactive';
@endphp

<div class="grid gap-5 md:grid-cols-2">
    <div class="md:col-span-2">
        <h3 class="text-sm font-semibold uppercase tracking-wide text-indigo-600">Akun Penghuni</h3>
    </div>

    <x-ui.input name="full_name" label="Nama Lengkap (sesuai KTP)" :value="$tenant?->full_name" required />
    <x-ui.input name="email" type="email" label="Email (untuk login)" :value="$tenant?->user?->email" required />

    <x-ui.input name="password" type="password" label="Password" autocomplete="new-password"
                :hint="$isEdit
                    ? 'Kosongkan jika tidak ingin mengganti password.'
                    : 'Kosongkan untuk dibuatkan password otomatis (ditampilkan sekali setelah disimpan). Manual: minimal 8 karakter.'" />
    <x-ui.input name="phone" label="No. HP / WhatsApp" :value="$tenant?->phone" placeholder="0812xxxxxxx" required />

    <div class="md:col-span-2 mt-2">
        <h3 class="text-sm font-semibold uppercase tracking-wide text-indigo-600">Data Penghuni</h3>
    </div>

    <x-ui.input name="ktp_number" label="No. KTP (16 digit)" :value="$tenant?->ktp_number"
                maxlength="16" inputmode="numeric" pattern="[0-9]{16}" required />

    <div>
        @if ($inactive)
            {{-- Penghuni nonaktif: kamar dikunci. Select disabled tidak terkirim, jadi kirim lewat hidden input. --}}
            <input type="hidden" name="room_id" value="{{ $tenant->room_id }}">
            <x-ui.select name="room_id" label="Kamar" :options="$roomOptions" :value="$tenant->room_id" disabled
                         hint="Penghuni sudah checkout, kamar tidak dapat diubah." />
        @else
            <x-ui.select name="room_id" label="Kamar" :options="$roomOptions" :value="$tenant?->room_id"
                         placeholder="-- Pilih kamar --" required
                         :hint="$isEdit ? 'Memindahkan penghuni akan mengosongkan kamar lama otomatis.' : 'Hanya kamar berstatus tersedia yang tampil.'" />
        @endif
    </div>

    <x-ui.textarea name="address" label="Alamat Asal" :value="$tenant?->address" rows="2" class="md:col-span-2" />

    {{-- Tanggal selesai dihitung otomatis (Alpine) untuk tampilan; server tetap menghitung ulang. --}}
    <div class="grid gap-5 md:col-span-2 md:grid-cols-3"
         x-data="{
            start: @js(old('start_date', $tenant?->start_date?->format('Y-m-d'))),
            months: @js((int) old('duration_months', $tenant?->duration_months ?? 12)),
            fixedEnd: @js($inactive ? $tenant->end_date->format('Y-m-d') : null),
            get end() {
                if (this.fixedEnd) return this.fixedEnd;
                if (!this.start || !this.months) return '';
                const d = new Date(this.start + 'T00:00:00');
                d.setMonth(d.getMonth() + parseInt(this.months));
                const p = (n) => String(n).padStart(2, '0');
                return d.getFullYear() + '-' + p(d.getMonth() + 1) + '-' + p(d.getDate());
            }
         }">
        <x-ui.input name="start_date" type="date" label="Tanggal Mulai Sewa" x-model="start" required />
        <x-ui.input name="duration_months" type="number" label="Durasi Sewa (bulan)" min="1" max="60" x-model="months" required />

        <div>
            <label class="mb-1 block text-sm font-medium text-slate-700">Tanggal Selesai (otomatis)</label>
            <input type="date" :value="end" readonly tabindex="-1"
                   class="w-full cursor-not-allowed rounded-lg border-slate-200 bg-slate-50 text-sm text-slate-600">
            <p class="mt-1 text-xs text-slate-500">{{ $inactive ? 'Tanggal checkout sebenarnya.' : 'Mulai + durasi.' }}</p>
        </div>
    </div>
</div>
