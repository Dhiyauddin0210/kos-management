@php
    $review = $review ?? null;
    $categories = [
        'rating_cleanliness'  => 'Kebersihan',
        'rating_security'     => 'Keamanan',
        'rating_facilities'   => 'Fasilitas',
        'rating_price'        => 'Harga',
        'rating_friendliness' => 'Keramahan',
    ];
@endphp

{{-- Rating Utama --}}
<div x-data="{ rating: {{ old('rating', $review?->rating ?? 0) }}, hover: 0 }">
    <label class="mb-2 block text-sm font-medium text-slate-700">
        Rating Keseluruhan <span class="text-red-500">*</span>
    </label>
    <input type="hidden" name="rating" :value="rating" required>
    <div class="flex items-center gap-2">
        <div class="flex gap-1">
            <template x-for="i in 5" :key="i">
                <button type="button"
                        @click="rating = i"
                        @mouseenter="hover = i"
                        @mouseleave="hover = 0"
                        class="transition">
                    <svg class="h-9 w-9 transition"
                         :class="(hover || rating) >= i ? 'text-amber-500 scale-110' : 'text-slate-300'"
                         viewBox="0 0 24 24"
                         :fill="(hover || rating) >= i ? 'currentColor' : 'none'"
                         stroke="currentColor" stroke-width="1.5">
                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                    </svg>
                </button>
            </template>
        </div>
        <span class="text-sm font-medium text-slate-600" x-text="rating ? rating + ' / 5' : 'Pilih rating'"></span>
    </div>
    @error('rating') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
</div>

{{-- Kategori Rating --}}
<div class="rounded-lg border border-slate-200 bg-slate-50 p-4">
    <p class="text-xs font-semibold uppercase tracking-wide text-slate-500 mb-3">Rating Kategori (opsional)</p>
    <div class="grid gap-3 sm:grid-cols-2">
        @foreach ($categories as $field => $label)
            <div x-data="{ val: {{ old($field, $review?->$field ?? 0) }} }">
                <div class="flex items-center justify-between mb-1">
                    <span class="text-sm text-slate-700">{{ $label }}</span>
                    <span class="text-xs font-semibold text-slate-500" x-text="val ? val + '/5' : '—'"></span>
                </div>
                <input type="hidden" name="{{ $field }}" :value="val">
                <div class="flex gap-0.5">
                    <template x-for="i in 5" :key="i">
                        <button type="button" @click="val = (val === i ? 0 : i)" class="transition">
                            <svg class="h-5 w-5" :class="val >= i ? 'text-amber-500' : 'text-slate-300'"
                                 viewBox="0 0 24 24" :fill="val >= i ? 'currentColor' : 'none'"
                                 stroke="currentColor" stroke-width="1.5">
                                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                            </svg>
                        </button>
                    </template>
                </div>
            </div>
        @endforeach
    </div>
</div>

{{-- Komentar --}}
<div>
    <label for="comment" class="mb-1 block text-sm font-medium text-slate-700">
        Komentar <span class="text-red-500">*</span>
    </label>
    <textarea id="comment" name="comment" rows="5" required minlength="10" maxlength="500"
              placeholder="Ceritakan pengalaman Anda tinggal di kos ini. Contoh: lokasi strategis, kamar bersih, WiFi kencang..."
              class="w-full rounded-lg border-slate-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">{{ old('comment', $review?->comment) }}</textarea>
    <p class="mt-1 text-xs text-slate-500">Minimal 10 karakter, maksimal 500 karakter.</p>
    @error('comment') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
</div>

{{-- Anonymous --}}
<label class="inline-flex cursor-pointer items-center gap-2 text-sm text-slate-700">
    <input type="hidden" name="is_anonymous" value="0">
    <input type="checkbox" name="is_anonymous" value="1"
           @checked(old('is_anonymous', $review?->is_anonymous))
           class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
    Kirim sebagai anonim (nama Anda akan disembunyikan)
</label>