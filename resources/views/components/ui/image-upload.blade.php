{{-- Upload foto + preview (foto lama di edit, atau foto baru yang baru dipilih) --}}
@props(['name' => 'photo', 'label' => 'Foto', 'current' => null, 'hint' => 'Format JPG, PNG, atau WEBP. Maksimal 2 MB.'])

<div x-data="{ preview: @js($current ? asset('storage/' . $current) : null) }" {{ $attributes->only('class') }}>
    <label for="{{ $name }}" class="mb-1 block text-sm font-medium text-slate-700">{{ $label }}</label>

    <div class="flex items-center gap-4">
        <div class="flex h-24 w-24 shrink-0 items-center justify-center overflow-hidden rounded-lg border border-slate-200 bg-slate-100 text-center text-xs text-slate-400">
            <template x-if="preview">
                <img :src="preview" alt="Preview" class="h-full w-full object-cover">
            </template>
            <span x-show="!preview">Belum ada foto</span>
        </div>

        <input type="file" id="{{ $name }}" name="{{ $name }}" accept="image/*"
               @change="const f = $event.target.files[0]; if (f) { preview = URL.createObjectURL(f) }"
               class="block w-full text-sm text-slate-600 file:mr-3 file:rounded-lg file:border-0 file:bg-indigo-50 file:px-4 file:py-2 file:text-sm file:font-medium file:text-indigo-700 hover:file:bg-indigo-100">
    </div>

    <p class="mt-1 text-xs text-slate-500">{{ $hint }}</p>
    @error($name)
        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
    @enderror
</div>
