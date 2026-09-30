@extends('layouts.tenant')

@section('title', 'Lapor Kerusakan')

@section('content')
    <x-ui.page-header title="Lapor Kerusakan" subtitle="Laporkan kerusakan di kamar Anda" />

    <x-ui.card>
        <form method="POST" action="{{ route('tenant.maintenances.store') }}" enctype="multipart/form-data" class="space-y-5">
            @csrf

            <div class="rounded-lg border border-indigo-200 bg-indigo-50 p-4">
                <div class="flex items-start gap-3">
                    <x-icon name="alert-circle" :size="20" class="mt-0.5 shrink-0 text-indigo-600" />
                    <div class="text-sm">
                        <p class="font-medium text-indigo-800">Tips Melaporkan</p>
                        <ul class="mt-1 list-disc list-inside space-y-0.5 text-indigo-700">
                            <li>Jelaskan kerusakan dengan detail</li>
                            <li>Upload foto kerusakan biar admin cepat paham</li>
                            <li>Pilih prioritas sesuai tingkat urgensi</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div>
                <label for="title" class="mb-1 block text-sm font-medium text-slate-700">
                    Judul Kerusakan <span class="text-red-500">*</span>
                </label>
                <input type="text" id="title" name="title" required maxlength="100"
                       value="{{ old('title') }}"
                       placeholder="Contoh: AC tidak dingin"
                       class="w-full rounded-lg border-slate-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                @error('title') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="description" class="mb-1 block text-sm font-medium text-slate-700">
                    Deskripsi Kerusakan <span class="text-red-500">*</span>
                </label>
                <textarea id="description" name="description" rows="5" required maxlength="1000"
                          placeholder="Jelaskan detail kerusakan. Contoh: AC di kamar sudah 3 hari hanya mengeluarkan angin, tidak dingin sama sekali."
                          class="w-full rounded-lg border-slate-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">{{ old('description') }}</textarea>
                @error('description') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="priority" class="mb-1 block text-sm font-medium text-slate-700">
                    Prioritas <span class="text-red-500">*</span>
                </label>
                <select id="priority" name="priority" required
                        class="w-full rounded-lg border-slate-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                    <option value="low" @selected(old('priority') === 'low')>Rendah — Bisa ditunggu</option>
                    <option value="medium" @selected(old('priority', 'medium') === 'medium')>Sedang — Perlu segera</option>
                    <option value="high" @selected(old('priority') === 'high')>Tinggi — Darurat</option>
                </select>
                @error('priority') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="photo" class="mb-1 block text-sm font-medium text-slate-700">
                    Foto Kerusakan (opsional)
                </label>
                <input type="file" id="photo" name="photo" accept="image/*"
                       class="block w-full text-sm text-slate-600 file:mr-3 file:rounded-lg file:border-0 file:bg-emerald-50 file:px-4 file:py-2 file:text-sm file:font-medium file:text-emerald-700 hover:file:bg-emerald-100">
                <p class="mt-1 text-xs text-slate-500">Format JPG/PNG/WEBP, maks 2 MB. Upload biar admin cepat paham.</p>
                @error('photo') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="flex items-center gap-3 border-t border-slate-100 pt-5">
                <x-ui.button type="submit">
                    <x-icon name="check" :size="16" />
                    Kirim Laporan
                </x-ui.button>
                <x-ui.button :href="route('tenant.maintenances.index')" variant="secondary">Batal</x-ui.button>
            </div>
        </form>
    </x-ui.card>
@endsection