{{-- Dipakai bersama oleh create & edit. Variabel opsional: $property --}}
@php $property = $property ?? null; @endphp

<div class="grid gap-5 md:grid-cols-2">
    <x-ui.input name="name" label="Nama Properti" :value="$property?->name" placeholder="Contoh: Kos Adin Tebet" required class="md:col-span-2" />

    <x-ui.input name="address" label="Alamat" :value="$property?->address" required class="md:col-span-2" />

    <x-ui.input name="city" label="Kota" :value="$property?->city" required />
    <x-ui.input name="phone" label="No. Telepon" :value="$property?->phone" placeholder="021-1234567" />

    <x-ui.textarea name="description" label="Deskripsi" :value="$property?->description" rows="4" class="md:col-span-2" />

    <x-ui.image-upload name="photo" label="Foto Properti" :current="$property?->photo" class="md:col-span-2" />

    <div class="md:col-span-2">
        {{-- hidden 0 supaya unchecked tetap terkirim sebagai "0" --}}
        <input type="hidden" name="is_active" value="0">
        <label class="inline-flex cursor-pointer items-center gap-2 text-sm text-slate-700">
            <input type="checkbox" name="is_active" value="1"
                   @checked(old('is_active', $property?->is_active ?? true))
                   class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
            Properti aktif (tampil dan bisa dipilih saat menambah kamar)
        </label>
    </div>
</div>
