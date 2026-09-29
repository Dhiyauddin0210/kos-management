@extends('layouts.admin')

@section('title', 'Buat Tagihan')

@section('content')
    <x-ui.page-header title="Buat Tagihan" subtitle="Buat tagihan manual untuk penghuni" />

    <x-ui.card>
        <form method="POST" action="{{ route('admin.invoices.store') }}"
              x-data="{
                tenants: @js($tenants->map(fn($t) => ['id' => $t->id, 'name' => $t->full_name . ' - Kamar ' . $t->room->room_number, 'price' => (float) $t->room->price])->values()),
                tenantId: @js(old('tenant_id')),
                get selected() { return this.tenants.find(t => t.id == this.tenantId) },
                get defaultAmount() { return this.selected ? this.selected.price : 0 }
              }">
            @csrf

            <div class="grid gap-5 md:grid-cols-2">
                {{-- Pilih Penghuni --}}
                <div class="md:col-span-2">
                    <label for="tenant_id" class="mb-1 block text-sm font-medium text-slate-700">
                        Penghuni <span class="text-red-500">*</span>
                    </label>
                    <select id="tenant_id" name="tenant_id" x-model="tenantId" required
                            class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">-- Pilih penghuni --</option>
                        @foreach ($tenants as $t)
                            <option value="{{ $t->id }}">{{ $t->full_name }} - Kamar {{ $t->room->room_number }} (Rp {{ number_format($t->room->price, 0, ',', '.') }}/bln)</option>
                        @endforeach
                    </select>
                    @error('tenant_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                {{-- Bulan --}}
                <div>
                    <label for="month" class="mb-1 block text-sm font-medium text-slate-700">
                        Bulan <span class="text-red-500">*</span>
                    </label>
                    <select id="month" name="month" required
                            class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @foreach ($months as $num => $name)
                            <option value="{{ $num }}" @selected(old('month', now()->month) == $num)>{{ $name }}</option>
                        @endforeach
                    </select>
                    @error('month') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                {{-- Tahun --}}
                <div>
                    <label for="year" class="mb-1 block text-sm font-medium text-slate-700">
                        Tahun <span class="text-red-500">*</span>
                    </label>
                    <select id="year" name="year" required
                            class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @foreach ($years as $y)
                            <option value="{{ $y }}" @selected(old('year', now()->year) == $y)>{{ $y }}</option>
                        @endforeach
                    </select>
                    @error('year') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                {{-- Jumlah --}}
                <div>
                    <label for="amount" class="mb-1 block text-sm font-medium text-slate-700">
                        Jumlah (Rp)
                    </label>
                    <input type="number" id="amount" name="amount" min="0" step="1000"
                           :value="defaultAmount"
                           class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <p class="mt-1 text-xs text-slate-500">Kosongkan untuk pakai harga default kamar</p>
                    @error('amount') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                {{-- Jatuh Tempo --}}
                <div>
                    <label for="due_date" class="mb-1 block text-sm font-medium text-slate-700">
                        Jatuh Tempo <span class="text-red-500">*</span>
                    </label>
                    <input type="date" id="due_date" name="due_date" required
                           value="{{ old('due_date', now()->addDays(10)->format('Y-m-d')) }}"
                           class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @error('due_date') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Preview --}}
            <template x-if="selected">
                <div class="mt-6 rounded-lg border border-indigo-100 bg-indigo-50 p-4">
                    <p class="text-xs uppercase tracking-wide text-indigo-600 font-semibold mb-2">Preview</p>
                    <div class="grid gap-2 text-sm sm:grid-cols-2">
                        <div>
                            <span class="text-slate-500">Penghuni:</span>
                            <span class="font-medium text-slate-800" x-text="selected.name"></span>
                        </div>
                        <div>
                            <span class="text-slate-500">Harga default:</span>
                            <span class="font-medium text-slate-800">Rp <span x-text="new Intl.NumberFormat('id-ID').format(defaultAmount)"></span></span>
                        </div>
                    </div>
                </div>
            </template>

            <div class="mt-6 flex items-center gap-3 border-t border-slate-100 pt-5">
                <x-ui.button type="submit">
                    <x-icon name="check" :size="16" />
                    Simpan Tagihan
                </x-ui.button>
                <x-ui.button :href="route('admin.invoices.index')" variant="secondary">Batal</x-ui.button>
            </div>
        </form>
    </x-ui.card>
@endsection