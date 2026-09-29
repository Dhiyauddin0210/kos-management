{{-- <x-ui.select name="status" label="Status" :options="['a' => 'A']" :value="$x" placeholder="Semua" /> --}}
@props(['label' => null, 'name', 'options' => [], 'value' => null, 'placeholder' => null, 'hint' => null])

<div {{ $attributes->only('class') }}>
    @if ($label)
        <label for="{{ $name }}" class="mb-1 block text-sm font-medium text-slate-700">
            {{ $label }}@if ($attributes->has('required')) <span class="text-red-500">*</span>@endif
        </label>
    @endif

    <select id="{{ $name }}" name="{{ $name }}"
            {{ $attributes->except('class')->merge(['class' => 'w-full rounded-lg text-sm focus:border-indigo-500 focus:ring-indigo-500 ' . ($errors->has($name) ? 'border-red-400' : 'border-slate-300')]) }}>
        @if ($placeholder !== null)
            <option value="">{{ $placeholder }}</option>
        @endif
        @foreach ($options as $key => $text)
            <option value="{{ $key }}" @selected((string) old($name, $value) === (string) $key)>{{ $text }}</option>
        @endforeach
    </select>

    @if ($hint)
        <p class="mt-1 text-xs text-slate-500">{{ $hint }}</p>
    @endif
    @error($name)
        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
    @enderror
</div>
