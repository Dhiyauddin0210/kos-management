{{-- <x-ui.input name="city" label="Kota" :value="$property?->city" required /> --}}
@props(['label' => null, 'name', 'type' => 'text', 'value' => null, 'hint' => null])

<div {{ $attributes->only('class') }}>
    @if ($label)
        <label for="{{ $name }}" class="mb-1 block text-sm font-medium text-slate-700">
            {{ $label }}@if ($attributes->has('required')) <span class="text-red-500">*</span>@endif
        </label>
    @endif

    <input id="{{ $name }}" name="{{ $name }}" type="{{ $type }}"
           @if (! in_array($type, ['password', 'file'])) value="{{ old($name, $value) }}" @endif
           {{ $attributes->except('class')->merge(['class' => 'w-full rounded-lg text-sm focus:border-indigo-500 focus:ring-indigo-500 ' . ($errors->has($name) ? 'border-red-400' : 'border-slate-300')]) }}>

    @if ($hint)
        <p class="mt-1 text-xs text-slate-500">{{ $hint }}</p>
    @endif
    @error($name)
        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
    @enderror
</div>
