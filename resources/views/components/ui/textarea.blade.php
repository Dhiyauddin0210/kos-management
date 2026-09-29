@props(['label' => null, 'name', 'value' => null, 'hint' => null, 'rows' => 3])

<div {{ $attributes->only('class') }}>
    @if ($label)
        <label for="{{ $name }}" class="mb-1 block text-sm font-medium text-slate-700">
            {{ $label }}@if ($attributes->has('required')) <span class="text-red-500">*</span>@endif
        </label>
    @endif

    <textarea id="{{ $name }}" name="{{ $name }}" rows="{{ $rows }}"
              {{ $attributes->except('class')->merge(['class' => 'w-full rounded-lg text-sm focus:border-indigo-500 focus:ring-indigo-500 ' . ($errors->has($name) ? 'border-red-400' : 'border-slate-300')]) }}>{{ old($name, $value) }}</textarea>

    @if ($hint)
        <p class="mt-1 text-xs text-slate-500">{{ $hint }}</p>
    @endif
    @error($name)
        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
    @enderror
</div>
