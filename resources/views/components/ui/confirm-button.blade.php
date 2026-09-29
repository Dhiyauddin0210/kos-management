{{-- Tombol dengan konfirmasi JavaScript confirm(). Dipakai untuk hapus & checkout. --}}
@props([
    'action',
    'message' => 'Yakin ingin melanjutkan?',
    'label' => 'Hapus',
    'method' => 'DELETE',
    'variant' => 'ghost-danger',
    'size' => 'link',
])

<form method="POST" action="{{ $action }}" class="inline"
      data-confirm="{{ $message }}" onsubmit="return confirm(this.dataset.confirm)">
    @csrf
    @if (strtoupper($method) !== 'POST')
        @method($method)
    @endif
    <x-ui.button type="submit" :variant="$variant" :size="$size">{{ $label }}</x-ui.button>
</form>
