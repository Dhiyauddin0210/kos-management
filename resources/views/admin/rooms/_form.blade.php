{{-- Dipakai bersama oleh create & edit.
     Variabel: $properties, $facilityOptions, $statusOptions, opsional: $room, $selectedProperty --}}
@php
    $room = $room ?? null;
    $selectedFacilities = old('facilities', $room?->facilities ?? []);
@endphp

<div class="grid gap-5 md:grid-cols-2">
    <x-ui.select name="property_id" label="Properti" :options="$properties"
                 :value="$room?->property_id ?? ($selectedProperty ?? null)" placeholder="-- Pilih properti --" required />

    <x-ui.input name="room_number" label="Nomor Kamar" :value="$room?->room_number" placeholder="mis. A11" required />

    <x-ui.input name="type" label="Tipe Kamar" :value="$room?->type" placeholder="Standard / Deluxe" list="tipe-kamar" required />
    <datalist id="tipe-kamar"><option value="Standard"><option value="Deluxe"></datalist>

    <x-ui.input name="price" type="number" label="Harga per Bulan (Rp)" :value="$room?->price !== null ? (int) $room?->price : null"
                min="0" step="1000" required />

    <x-ui.input name="size" label="Ukuran" :value="$room?->size" placeholder="mis. 3x4" hint="Dalam meter." />

    <x-ui.select name="status" label="Status" :options="$statusOptions" :value="$room?->status ?? 'available'"
                 :hint="$room ? null : 'Status Terisi otomatis aktif saat penghuni ditambahkan.'" />

    {{-- Fasilitas: checkbox + input tambahan --}}
    <div class="md:col-span-2">
        <span class="mb-2 block text-sm font-medium text-slate-700">Fasilitas</span>
        <div class="grid grid-cols-2 gap-2 sm:grid-cols-4">
            @foreach ($facilityOptions as $facility)
                <label class="inline-flex cursor-pointer items-center gap-2 rounded-lg border border-slate-200 px-3 py-2 text-sm text-slate-700 hover:bg-slate-50">
                    <input type="checkbox" name="facilities[]" value="{{ $facility }}"
                           @checked(in_array($facility, (array) $selectedFacilities))
                           class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                    {{ $facility }}
                </label>
            @endforeach
        </div>
        @error('facilities') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
    </div>

    <x-ui.input name="facilities_other" label="Fasilitas Lainnya (opsional)" :value="null"
                placeholder="Pisahkan dengan koma, mis. Kulkas, Dapur Pribadi" class="md:col-span-2" />

    <x-ui.textarea name="description" label="Deskripsi" :value="$room?->description" rows="3" class="md:col-span-2" />

    <x-ui.image-upload name="photo" label="Foto Kamar" :current="$room?->photo" class="md:col-span-2" />
</div>
