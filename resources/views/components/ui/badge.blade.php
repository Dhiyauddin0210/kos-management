{{-- Pemakaian: <x-ui.badge status="paid" />  atau  <x-ui.badge color="indigo">Teks</x-ui.badge> --}}
@props(['status' => null, 'color' => 'slate'])

@php
    // status => [warna, label Indonesia]
    $map = [
        'available'   => ['green', 'Tersedia'],
        'occupied'    => ['indigo', 'Terisi'],
        'maintenance' => ['amber', 'Perbaikan'],
        'active'      => ['green', 'Aktif'],
        'inactive'    => ['slate', 'Nonaktif'],
        'paid'        => ['green', 'Lunas'],
        'unpaid'      => ['slate', 'Belum Bayar'],
        'pending'     => ['amber', 'Menunggu'],
        'overdue'     => ['red', 'Terlambat'],
        'verified'    => ['green', 'Terverifikasi'],
        'rejected'    => ['red', 'Ditolak'],
        'approved'    => ['green', 'Disetujui'],
        'new'         => ['blue', 'Baru'],
        'contacted'   => ['indigo', 'Dihubungi'],
        'closed'      => ['slate', 'Ditutup'],
        'reported'    => ['amber', 'Dilaporkan'],
        'in_progress' => ['blue', 'Diproses'],
        'resolved'    => ['green', 'Selesai'],
        'low'         => ['slate', 'Rendah'],
        'medium'      => ['amber', 'Sedang'],
        'high'        => ['red', 'Tinggi'],
    ];

    // Class Tailwind ditulis utuh supaya terdeteksi compiler (jangan dirakit dinamis)
    $colors = [
        'green'  => 'bg-green-100 text-green-700',
        'indigo' => 'bg-indigo-100 text-indigo-700',
        'amber'  => 'bg-amber-100 text-amber-700',
        'red'    => 'bg-red-100 text-red-700',
        'blue'   => 'bg-blue-100 text-blue-700',
        'slate'  => 'bg-slate-100 text-slate-600',
    ];

    $label = null;
    if ($status) {
        [$color, $label] = $map[$status] ?? [$color, ucfirst(str_replace('_', ' ', $status))];
    }
@endphp

<span class="inline-flex items-center whitespace-nowrap rounded-full px-2.5 py-0.5 text-xs font-medium {{ $colors[$color] ?? $colors['slate'] }}">
    {{ $label ?? $slot }}
</span>
