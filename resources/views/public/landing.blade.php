@php
    $s       = fn ($k, $d = '') => $settings[$k] ?? $d;
    $name    = $s('name', 'Kos Adin');
    $wa      = preg_replace('/^0/', '62', preg_replace('/\D/', '', $s('whatsapp')));
    $waUrl   = 'https://wa.me/' . $wa . '?text=' . rawurlencode('Halo, saya tertarik dengan ' . $name);
    $count   = $availableRooms->count();
    $rp      = fn ($v) => 'Rp ' . number_format($v, 0, ',', '.');
    $oldRoom = $availableRooms->firstWhere('id', (int) old('room_id'));
    $minPrice = $count ? $availableRooms->min('price') : 0;

    $icons = [
        'wifi'    => 'M5 12.55a11 11 0 0 1 14.08 0M1.42 9a16 16 0 0 1 21.16 0M8.53 16.11a6 6 0 0 1 6.95 0M12 20h.01',
        'ac'      => 'M2 12h20M12 2v20M20 16l-4-4 4-4M4 8l4 4-4 4M16 4l-4 4-4-4M8 20l4-4 4 4',
        'bath'    => 'M9 6 6.5 3.5a1.5 1.5 0 0 0-1-.5C4.68 3 4 3.68 4 4.5V17a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-5M10 5 8 7M2 12h20M7 19l-1 2M18 19l1 2',
        'car'     => 'M5 3h14a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2zM9 17V7h4a3 3 0 0 1 0 6H9',
        'pin'     => 'M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0zM12 13a3 3 0 1 0 0-6 3 3 0 0 0 0 6z',
        'phone'   => 'M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.13.96.36 1.9.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.91.34 1.85.57 2.81.7A2 2 0 0 1 22 16.92z',
        'mail'    => 'M4 4h16a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2zM22 6l-10 7L2 6',
        'wa'      => 'M21 11.5a8.4 8.4 0 0 1-12.3 7.4L3 21l2.1-5.6A8.4 8.4 0 1 1 21 11.5zM9 9.5c0 3 2.5 5.5 5.5 5.5l1.2-1.4-1.9-1-.9.7a3.6 3.6 0 0 1-1.7-1.7l.7-.9-1-1.9L9 9.5z',
        'arrow'   => 'M5 12h14M12 5l7 7-7 7',
        'check'   => 'M20 6 9 17l-5-5',
        'bed'     => 'M2 4v16M2 8h18a2 2 0 0 1 2 2v10M2 17h20M6 8v9',
        'home'    => 'M3 10.5 12 3l9 7.5V20a1 1 0 0 1-1 1h-5v-6H9v6H4a1 1 0 0 1-1-1z',
        'menu'    => 'M4 6h16M4 12h16M4 18h16',
        'x'       => 'M6 6l12 12M18 6 6 18',
        'star'    => 'M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z',
        'shield'  => 'M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z',
        'sparkle' => 'M12 3l1.9 5.1L19 10l-5.1 1.9L12 17l-1.9-5.1L5 10l5.1-1.9L12 3z',
        'plus'    => 'M12 5v14M5 12h14',
        'minus'   => 'M5 12h14',
        'coffee'  => 'M18 8h1a4 4 0 0 1 0 8h-1M2 8h16v9a4 4 0 0 1-4 4H6a4 4 0 0 1-4-4V8zM6 1v3M10 1v3M14 1v3',
        'sun'     => 'M12 17a5 5 0 1 0 0-10 5 5 0 0 0 0 10zM12 1v2M12 21v2M4.2 4.2l1.4 1.4M18.4 18.4l1.4 1.4M1 12h2M21 12h2M4.2 19.8l1.4-1.4M18.4 5.6l1.4-1.4',
        'heart'   => 'M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.7l-1-1.1a5.5 5.5 0 0 0-7.8 7.8l1.1 1L12 21l7.7-7.6 1.1-1a5.5 5.5 0 0 0 0-7.8z',
    ];
    $svg = fn ($n, $c = 'h-5 w-5') => '<svg class="' . $c . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="' . ($icons[$n] ?? '') . '"/></svg>';

    $field = 'w-full rounded-2xl border border-amber-100 bg-white px-4 py-3.5 text-sm text-stone-900 placeholder:text-stone-400 transition focus:border-amber-400 focus:outline-none focus:ring-4 focus:ring-amber-100';

    $testimonials = \App\Models\Review::with('user')->approved()->orderBy('approved_at', 'desc')->limit(6)->get();
@endphp
<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $name }} — {{ $s('tagline', 'Hunian nyaman dan strategis') }}</title>
    <meta name="description" content="{{ $s('tagline') }}. {{ $s('address') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        [x-cloak]{display:none!important}
        .grain { background-image: radial-gradient(circle at 1px 1px, rgba(120,53,15,.06) 1px, transparent 0); background-size: 24px 24px; }
        .blob-amber { background: radial-gradient(circle, rgba(251,191,36,.25) 0%, transparent 70%); }
        .blob-orange { background: radial-gradient(circle, rgba(251,146,60,.2) 0%, transparent 70%); }
        @keyframes float-slow { 0%, 100% { transform: translateY(0) rotate(0deg); } 50% { transform: translateY(-12px) rotate(2deg); } }
        .float-slow { animation: float-slow 6s ease-in-out infinite; }
        @media (prefers-reduced-motion:reduce){*{transition:none!important;animation:none!important}}
    </style>
</head>
<body class="bg-[#fdfaf6] text-stone-700 antialiased" style="font-family:Inter,ui-sans-serif,system-ui,-apple-system,'Segoe UI',Roboto,sans-serif"
      x-data="{
        menu: false,
        faq: 0,
        room: @js($oldRoom ? ['id' => $oldRoom->id, 'number' => $oldRoom->room_number, 'type' => $oldRoom->type, 'price' => $rp($oldRoom->price)] : null),
        pick(r) { this.room = r; $nextTick(() => document.getElementById('minat').scrollIntoView({ behavior: 'smooth' })) }
      }">

{{-- NAVBAR --}}
<header class="sticky top-0 z-40 border-b border-amber-100/60 bg-[#fdfaf6]/80 backdrop-blur-xl">
    <div class="mx-auto flex max-w-7xl items-center justify-between px-5 py-4 sm:px-8">
        <a href="#top" class="flex items-center gap-2.5">
            @if ($s('logo'))
                <img src="{{ asset('storage/' . $s('logo')) }}" alt="{{ $name }}" class="h-9 w-9 rounded-2xl object-cover">
            @else
                <span class="flex h-9 w-9 items-center justify-center rounded-2xl bg-gradient-to-br from-amber-400 to-orange-500 text-white shadow-lg shadow-amber-200">{!! $svg('home', 'h-4 w-4') !!}</span>
            @endif
            <span class="text-base font-bold tracking-tight text-stone-900">{{ $name }}</span>
        </a>

        <nav class="hidden items-center gap-1 md:flex">
            <a href="#keunggulan" class="rounded-full px-4 py-2 text-sm font-medium text-stone-600 hover:bg-amber-50 hover:text-stone-900 transition">Keunggulan</a>
            <a href="#kamar" class="rounded-full px-4 py-2 text-sm font-medium text-stone-600 hover:bg-amber-50 hover:text-stone-900 transition">Kamar</a>
            <a href="#testimoni" class="rounded-full px-4 py-2 text-sm font-medium text-stone-600 hover:bg-amber-50 hover:text-stone-900 transition">Testimoni</a>
            <a href="#faq" class="rounded-full px-4 py-2 text-sm font-medium text-stone-600 hover:bg-amber-50 hover:text-stone-900 transition">FAQ</a>
        </nav>

        <div class="flex items-center gap-2">
            <a href="{{ $waUrl }}" target="_blank" rel="noopener"
               class="hidden items-center gap-2 rounded-full border border-amber-200 bg-white px-4 py-2 text-sm font-medium text-stone-700 hover:border-amber-300 hover:bg-amber-50 transition sm:inline-flex">
                {!! $svg('wa', 'h-3.5 w-3.5 text-emerald-600') !!}
                WhatsApp
            </a>
            <a href="#minat" class="inline-flex items-center gap-1.5 rounded-full bg-stone-900 px-4 py-2 text-sm font-semibold text-white shadow-lg shadow-stone-900/10 hover:bg-stone-800 transition">
                Isi Form
                {!! $svg('arrow', 'h-3.5 w-3.5') !!}
            </a>
            <button @click="menu = !menu" class="rounded-full p-2 text-stone-700 hover:bg-amber-50 md:hidden" aria-label="Menu">
                <span x-show="!menu">{!! $svg('menu', 'h-5 w-5') !!}</span>
                <span x-show="menu" x-cloak>{!! $svg('x', 'h-5 w-5') !!}</span>
            </button>
        </div>
    </div>

    <div x-show="menu" x-cloak x-transition.opacity @click="menu = false"
         class="border-t border-amber-100 bg-[#fdfaf6] px-5 py-3 md:hidden">
        <a href="#keunggulan" class="block rounded-2xl px-4 py-3 text-sm font-medium text-stone-700 hover:bg-amber-50">Keunggulan</a>
        <a href="#kamar" class="block rounded-2xl px-4 py-3 text-sm font-medium text-stone-700 hover:bg-amber-50">Kamar</a>
        <a href="#testimoni" class="block rounded-2xl px-4 py-3 text-sm font-medium text-stone-700 hover:bg-amber-50">Testimoni</a>
        <a href="#faq" class="block rounded-2xl px-4 py-3 text-sm font-medium text-stone-700 hover:bg-amber-50">FAQ</a>
    </div>
</header>

{{-- HERO --}}
<section id="top" class="relative isolate overflow-hidden">
    <div class="grain absolute inset-0 -z-10"></div>
    <div class="blob-amber absolute -left-40 -top-40 -z-10 h-[500px] w-[500px] rounded-full"></div>
    <div class="blob-orange absolute -right-40 top-40 -z-10 h-[500px] w-[500px] rounded-full"></div>

    <div class="mx-auto max-w-7xl px-5 pb-24 pt-20 sm:px-8 sm:pb-32 sm:pt-28">
        <div class="grid items-center gap-16 lg:grid-cols-2 lg:gap-20">
            <div>
                <div class="inline-flex items-center gap-2 rounded-full border border-amber-200 bg-white px-3.5 py-1.5 text-xs font-medium text-stone-700 shadow-sm">
                    <span class="relative flex h-2 w-2">
                        <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-amber-400 opacity-75"></span>
                        <span class="relative inline-flex h-2 w-2 rounded-full bg-amber-500"></span>
                    </span>
                    {{ $count }} kamar tersedia
                </div>

                <h1 class="mt-7 text-balance text-5xl font-bold leading-[1.05] tracking-tight text-stone-900 sm:text-6xl lg:text-7xl">
                    {{ $name }}
                </h1>

                <p class="mt-6 max-w-lg text-lg leading-relaxed text-stone-600 sm:text-xl">
                    {{ $s('tagline', 'Hunian hangat yang bikin kamu betah pulang setiap hari.') }}
                </p>

                @if ($s('address', $property?->address ?? ''))
                    <p class="mt-6 inline-flex items-center gap-2 text-sm text-stone-500">
                        <span class="text-amber-600">{!! $svg('pin', 'h-4 w-4') !!}</span>
                        {{ $s('address', $property?->address ?? '') }}
                    </p>
                @endif

                <div class="mt-10 flex flex-col gap-3 sm:flex-row">
                    <a href="#kamar" class="group inline-flex items-center justify-center gap-2 rounded-2xl bg-stone-900 px-7 py-4 text-sm font-semibold text-white shadow-xl shadow-stone-900/10 transition hover:bg-stone-800">
                        Lihat Kamar Tersedia
                        <span class="transition group-hover:translate-x-0.5">{!! $svg('arrow', 'h-4 w-4') !!}</span>
                    </a>
                    <a href="#minat" class="inline-flex items-center justify-center gap-2 rounded-2xl border-2 border-stone-200 bg-white px-7 py-4 text-sm font-semibold text-stone-700 transition hover:border-amber-300 hover:bg-amber-50">
                        Isi Form Minat
                    </a>
                </div>

                <div class="mt-10 flex items-center gap-4">
                    <div class="flex -space-x-2">
                        <span class="flex h-8 w-8 items-center justify-center rounded-full border-2 border-white bg-amber-200 text-xs font-bold text-amber-900">A</span>
                        <span class="flex h-8 w-8 items-center justify-center rounded-full border-2 border-white bg-emerald-200 text-xs font-bold text-emerald-900">S</span>
                        <span class="flex h-8 w-8 items-center justify-center rounded-full border-2 border-white bg-rose-200 text-xs font-bold text-rose-900">B</span>
                        <span class="flex h-8 w-8 items-center justify-center rounded-full border-2 border-white bg-sky-200 text-xs font-bold text-sky-900">D</span>
                    </div>
                    <div>
                        <div class="flex gap-0.5 text-amber-500">
                            @for ($i = 0; $i < 5; $i++)
                                <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                            @endfor
                        </div>
                        <p class="mt-0.5 text-xs text-stone-500">Dipercaya {{ $count > 0 ? '50+' : '0' }} penghuni</p>
                    </div>
                </div>
            </div>

            <div class="relative">
                <div class="relative overflow-hidden rounded-[2.5rem] border-8 border-white shadow-2xl shadow-amber-200/50">
                    @if ($property?->photo)
                        <img src="{{ asset('storage/' . $property->photo) }}" alt="{{ $name }}" class="aspect-[4/5] w-full object-cover">
                    @else
                        <div class="flex aspect-[4/5] w-full items-center justify-center bg-gradient-to-br from-amber-100 via-orange-100 to-rose-100">
                            <div class="text-center">
                                <span class="mx-auto flex h-20 w-20 items-center justify-center rounded-3xl bg-white/60 text-amber-600 backdrop-blur">{!! $svg('home', 'h-10 w-10') !!}</span>
                                <p class="mt-4 text-sm font-medium text-amber-800">{{ $name }}</p>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="float-slow absolute -left-4 top-12 hidden rounded-2xl border border-amber-100 bg-white/95 p-4 shadow-xl shadow-amber-200/30 backdrop-blur sm:block">
                    <div class="flex items-center gap-3">
                        <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-100 text-amber-600">{!! $svg('sun', 'h-5 w-5') !!}</span>
                        <div>
                            <p class="text-xs text-stone-500">Suasana</p>
                            <p class="text-sm font-bold text-stone-900">Hangat & Homey</p>
                        </div>
                    </div>
                </div>

                <div class="float-slow absolute -right-4 bottom-12 hidden rounded-2xl border border-amber-100 bg-white/95 p-4 shadow-xl shadow-amber-200/30 backdrop-blur sm:block" style="animation-delay: 1s">
                    <div class="flex items-center gap-3">
                        <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-100 text-emerald-600">{!! $svg('heart', 'h-5 w-5') !!}</span>
                        <div>
                            <p class="text-xs text-stone-500">Kepuasan</p>
                            <p class="text-sm font-bold text-stone-900">98% Betah</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- STATS --}}
<section class="border-y border-amber-100 bg-white/60">
    <div class="mx-auto grid max-w-7xl grid-cols-2 gap-y-8 px-5 py-10 sm:px-8 lg:grid-cols-4">
        @foreach ([
            [$count . '+', 'Kamar Tersedia', 'sparkle'],
            [$minPrice ? $rp($minPrice) : '—', 'Harga Mulai/Bulan', 'coffee'],
            ['24/7', 'Keamanan & CCTV', 'shield'],
            [$property?->city ?? '—', 'Lokasi Strategis', 'pin'],
        ] as [$val, $label, $ic])
            <div class="flex items-start gap-4">
                <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-amber-100 text-amber-700">{!! $svg($ic, 'h-5 w-5') !!}</span>
                <div>
                    <p class="text-xl font-bold tracking-tight text-stone-900 sm:text-2xl">{{ $val }}</p>
                    <p class="mt-0.5 text-sm text-stone-500">{{ $label }}</p>
                </div>
            </div>
        @endforeach
    </div>
</section>

{{-- KEUNGGULAN --}}
<section id="keunggulan" class="relative py-24 sm:py-32">
    <div class="mx-auto max-w-7xl px-5 sm:px-8">
        <div class="mx-auto max-w-2xl text-center">
            <p class="text-xs font-bold uppercase tracking-[0.2em] text-amber-600">Kenapa {{ $name }}?</p>
            <h2 class="mt-3 text-balance text-3xl font-bold tracking-tight text-stone-900 sm:text-5xl">
                Semua yang kamu butuhkan untuk betah
            </h2>
            <p class="mt-4 text-lg leading-relaxed text-stone-600">
                Kami rancang setiap sudut supaya kamu merasa seperti di rumah sendiri.
            </p>
        </div>

        <div class="mt-16 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ([
                ['wifi', 'WiFi Ngebut', 'Internet stabil 24 jam. Streaming, meeting, atau gaming tanpa buffering.', 'amber'],
                ['ac', 'AC Dingin', 'Kamar sejuk sepanjang hari. Tidur nyenyak, bangun segar.', 'sky'],
                ['bath', 'KM Dalam', 'Kamar mandi pribadi, privasi penuh. Ga perlu antre pagi-pagi.', 'emerald'],
                ['coffee', 'Dapur Bersama', 'Dapur bersama buat masak sendiri. Hemat, sehat, enak.', 'rose'],
            ] as [$ic, $title, $desc, $color])
                @php
                    $colors = [
                        'amber'   => 'bg-amber-100 text-amber-700',
                        'sky'     => 'bg-sky-100 text-sky-700',
                        'emerald' => 'bg-emerald-100 text-emerald-700',
                        'rose'    => 'bg-rose-100 text-rose-700',
                    ];
                @endphp
                <div class="group rounded-3xl border border-amber-100 bg-white p-7 shadow-sm transition duration-300 hover:-translate-y-1 hover:border-amber-200 hover:shadow-xl hover:shadow-amber-100/60">
                    <span class="flex h-14 w-14 items-center justify-center rounded-2xl {{ $colors[$color] }} transition group-hover:scale-110">{!! $svg($ic, 'h-6 w-6') !!}</span>
                    <h3 class="mt-6 text-lg font-bold text-stone-900">{{ $title }}</h3>
                    <p class="mt-2 text-sm leading-relaxed text-stone-600">{{ $desc }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- KAMAR --}}
<section id="kamar" class="relative bg-amber-50/40 py-24 sm:py-32">
    <div class="grain absolute inset-0 opacity-50"></div>
    <div class="relative mx-auto max-w-7xl px-5 sm:px-8">
        <div class="flex flex-wrap items-end justify-between gap-6">
            <div class="max-w-2xl">
                <p class="text-xs font-bold uppercase tracking-[0.2em] text-amber-600">Kamar Tersedia</p>
                <h2 class="mt-3 text-balance text-3xl font-bold tracking-tight text-stone-900 sm:text-5xl">
                    Temukan kamar yang pas buatmu
                </h2>
                <p class="mt-4 text-lg text-stone-600">
                    Semua kamar udah include fasilitas dasar. Tinggal bawa koper.
                </p>
            </div>
            <div class="flex items-center gap-3 rounded-full border border-amber-200 bg-white px-5 py-2.5 shadow-sm">
                <span class="h-2 w-2 rounded-full bg-amber-500"></span>
                <span class="text-sm font-semibold text-stone-700"><span class="text-amber-600">{{ $count }}</span> kamar available</span>
            </div>
        </div>

        <div class="mt-14">
            @if ($count === 0)
                <div class="rounded-[2rem] border-2 border-dashed border-amber-200 bg-white/60 py-20 text-center">
                    <span class="mx-auto flex h-16 w-16 items-center justify-center rounded-3xl bg-amber-100 text-amber-600">{!! $svg('bed', 'h-7 w-7') !!}</span>
                    <h3 class="mt-6 text-xl font-bold text-stone-900">Semua kamar sedang terisi</h3>
                    <p class="mx-auto mt-2 max-w-md text-stone-600">Tinggalin kontak kamu, nanti kami kabarin begitu ada kamar kosong.</p>
                    <a href="#minat" class="mt-7 inline-flex items-center gap-2 rounded-2xl bg-stone-900 px-6 py-3 text-sm font-semibold text-white hover:bg-stone-800 transition">Daftar Tunggu {!! $svg('arrow', 'h-4 w-4') !!}</a>
                </div>
            @else
                <div class="grid gap-7 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($availableRooms as $room)
                        @php $fac = collect($room->facilities ?? []); @endphp
                        <article class="group flex flex-col overflow-hidden rounded-3xl border border-amber-100 bg-white shadow-sm transition duration-300 hover:-translate-y-2 hover:shadow-2xl hover:shadow-amber-200/40">
                            <div class="relative aspect-[4/3] overflow-hidden bg-amber-50">
                                @if ($room->photo)
                                    <img src="{{ asset('storage/' . $room->photo) }}" alt="Kamar {{ $room->room_number }}" loading="lazy" class="h-full w-full object-cover transition duration-500 group-hover:scale-110">
                                @else
                                    <div class="flex h-full w-full items-center justify-center bg-gradient-to-br from-amber-100 via-orange-100 to-rose-100 text-amber-400 transition duration-500 group-hover:scale-105">{!! $svg('bed', 'h-16 w-16') !!}</div>
                                @endif
                                <span class="absolute left-4 top-4 inline-flex items-center gap-1.5 rounded-full bg-white/95 px-3 py-1.5 text-xs font-bold text-stone-800 backdrop-blur shadow-sm">
                                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span> Tersedia
                                </span>
                                <span class="absolute right-4 top-4 rounded-full bg-white/95 px-3 py-1.5 text-xs font-bold text-amber-700 backdrop-blur shadow-sm">
                                    {{ $room->type }}
                                </span>
                            </div>

                            <div class="flex flex-1 flex-col p-6">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <h3 class="text-xl font-bold text-stone-900">Kamar {{ $room->room_number }}</h3>
                                        <p class="mt-1 text-sm text-stone-500">@if ($room->size) {{ $room->size }} m² @else Kamar nyaman @endif</p>
                                    </div>
                                </div>

                                @if ($fac->isNotEmpty())
                                    <ul class="mt-5 flex flex-wrap gap-1.5">
                                        @foreach ($fac->take(3) as $f)
                                            <li class="rounded-full bg-amber-50 px-3 py-1 text-xs font-medium text-amber-800">{{ $f }}</li>
                                        @endforeach
                                        @if ($fac->count() > 3)
                                            <li class="rounded-full bg-amber-50 px-3 py-1 text-xs font-medium text-amber-800">+{{ $fac->count() - 3 }}</li>
                                        @endif
                                    </ul>
                                @endif

                                <div class="mt-auto flex items-end justify-between gap-4 border-t border-amber-50 pt-5">
                                    <div>
                                        <p class="text-2xl font-bold tracking-tight text-stone-900">{{ $rp($room->price) }}</p>
                                        <p class="text-xs text-stone-500">per bulan</p>
                                    </div>
                                    <button type="button"
                                            @click="pick(@js(['id' => $room->id, 'number' => $room->room_number, 'type' => $room->type, 'price' => $rp($room->price)]))"
                                            class="rounded-2xl bg-stone-900 px-5 py-2.5 text-sm font-semibold text-white shadow-lg shadow-stone-900/10 transition hover:bg-amber-500 hover:text-stone-900">
                                        Minat
                                    </button>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</section>

{{-- TESTIMONI (dari database) --}}
@if ($testimonials->isNotEmpty())
<section id="testimoni" class="py-24 sm:py-32">
    <div class="mx-auto max-w-7xl px-5 sm:px-8">
        <div class="mx-auto max-w-2xl text-center">
            <p class="text-xs font-bold uppercase tracking-[0.2em] text-amber-600">Testimoni</p>
            <h2 class="mt-3 text-balance text-3xl font-bold tracking-tight text-stone-900 sm:text-5xl">
                Cerita dari penghuni kami
            </h2>
            <p class="mt-4 text-lg text-stone-600">
                Ulasan asli dari {{ $testimonials->count() }} penghuni yang sudah merasakan tinggal di sini.
            </p>
        </div>

        <div class="mt-16 grid gap-6 lg:grid-cols-3">
            @foreach ($testimonials as $review)
                @php
                    $colors = ['from-amber-300 to-orange-400', 'from-emerald-300 to-teal-400', 'from-sky-300 to-blue-400', 'from-rose-300 to-pink-400', 'from-violet-300 to-purple-400', 'from-cyan-300 to-teal-400'];
                    $color = $colors[$loop->index % count($colors)];
                @endphp
                <figure class="relative rounded-3xl border border-amber-100 bg-white p-8 shadow-sm transition hover:shadow-lg hover:shadow-amber-100/60">
                    <span class="absolute -top-3 left-8 text-5xl font-serif text-amber-300">"</span>
                    <div class="flex gap-0.5 text-amber-500">
                        @for ($i = 1; $i <= 5; $i++)
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="{{ $i <= $review->rating ? 'currentColor' : 'none' }}" stroke="currentColor" stroke-width="1.5">
                                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                            </svg>
                        @endfor
                    </div>
                    <blockquote class="mt-5 text-base leading-relaxed text-stone-700">{{ $review->comment }}</blockquote>
                    <figcaption class="mt-7 flex items-center gap-3 border-t border-amber-50 pt-6">
                        <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-br {{ $color }} text-base font-bold text-white shadow-lg">
                            {{ $review->initial }}
                        </span>
                        <div>
                            <p class="text-sm font-bold text-stone-900">{{ $review->display_name }}</p>
                            <p class="text-xs text-stone-500">Penghuni{{ $review->is_anonymous ? ' (Anonim)' : '' }}</p>
                        </div>
                    </figcaption>
                </figure>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- FAQ --}}
<section id="faq" class="bg-amber-50/40 py-24 sm:py-32">
    <div class="mx-auto max-w-3xl px-5 sm:px-8">
        <div class="text-center">
            <p class="text-xs font-bold uppercase tracking-[0.2em] text-amber-600">FAQ</p>
            <h2 class="mt-3 text-balance text-3xl font-bold tracking-tight text-stone-900 sm:text-5xl">
                Pertanyaan yang sering ditanya
            </h2>
        </div>

        <div class="mt-14 space-y-3">
            @foreach ([
                ['Bisa survey kamar dulu sebelum booking?', 'Bisa banget! Isi form minat sewa atau chat WhatsApp kami buat atur jadwal survey. Kami seneng nunjukin kamar langsung ke kamu.'],
                ['Berapa lama minimal sewa?', 'Minimal sewa 1 bulan. Untuk 6 atau 12 bulan, tersedia diskon khusus. Chat admin buat detail.'],
                ['Fasilitas apa saja yang termasuk?', 'Kasur, lemari, WiFi, dan kamar mandi dalam udah include. AC tersedia di tipe kamar tertentu. Dapur bersama bebas dipakai.'],
                ['Bagaimana cara pembayarannya?', 'Bisa transfer ke rekening yang kami sediain. Upload bukti transfer di portal penghuni, admin verifikasi dalam 1x24 jam.'],
            ] as $i => [$q, $a])
                <div class="overflow-hidden rounded-2xl border border-amber-100 bg-white transition hover:border-amber-200">
                    <button type="button" @click="faq = faq === {{ $i }} ? null : {{ $i }}"
                            class="flex w-full items-center justify-between gap-4 px-6 py-5 text-left">
                        <span class="text-sm font-bold text-stone-900">{{ $q }}</span>
                        <span class="shrink-0 rounded-full bg-amber-100 p-1.5 text-amber-700 transition" :class="faq === {{ $i }} && 'rotate-180 bg-amber-500 text-white'">
                            {!! $svg('plus', 'h-4 w-4') !!}
                        </span>
                    </button>
                    <div x-show="faq === {{ $i }}" x-cloak x-collapse
                         class="border-t border-amber-50 px-6 py-5 text-sm leading-relaxed text-stone-600">{{ $a }}</div>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- FORM MINAT --}}
<section id="minat" class="py-24 sm:py-32">
    <div class="mx-auto max-w-7xl px-5 sm:px-8">
        <div class="grid gap-16 lg:grid-cols-2 lg:gap-20">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.2em] text-amber-600">Hubungi Kami</p>
                <h2 class="mt-3 text-balance text-3xl font-bold tracking-tight text-stone-900 sm:text-5xl">
                    Yuk, ngobrol dulu aja
                </h2>
                <p class="mt-5 text-lg leading-relaxed text-stone-600">
                    Isi form di samping atau chat kami langsung. Kami balas secepatnya di jam kerja — biasanya dalam 1x24 jam.
                </p>

                <ul class="mt-10 space-y-5">
                    @foreach ([
                        ['pin', 'Alamat', $s('address', $property?->address ?? '')],
                        ['phone', 'Telepon', $s('phone')],
                        ['mail', 'Email', $s('email')],
                    ] as [$ic, $label, $val])
                        @if ($val)
                            <li class="flex items-start gap-4">
                                <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-amber-100 text-amber-700">{!! $svg($ic, 'h-5 w-5') !!}</span>
                                <div>
                                    <p class="text-xs font-bold uppercase tracking-wider text-stone-400">{{ $label }}</p>
                                    <p class="mt-0.5 text-sm font-semibold text-stone-900">{{ $val }}</p>
                                </div>
                            </li>
                        @endif
                    @endforeach
                </ul>

                <a href="{{ $waUrl }}" target="_blank" rel="noopener"
                   class="mt-10 inline-flex items-center gap-2.5 rounded-2xl bg-emerald-500 px-7 py-4 text-sm font-bold text-white shadow-xl shadow-emerald-500/20 transition hover:bg-emerald-600">
                    {!! $svg('wa', 'h-5 w-5') !!}
                    Chat via WhatsApp
                </a>
            </div>

            <div class="rounded-[2rem] border border-amber-100 bg-white p-7 shadow-xl shadow-amber-100/50 sm:p-9">
                <h3 class="text-xl font-bold text-stone-900">Form minat sewa</h3>
                <p class="mt-1 text-sm text-stone-500">Data kamu cuma kami pakai buat hubungi kamu. Aman.</p>

                <form method="POST" action="{{ route('public.lead') }}" class="mt-7 space-y-5">
                    @csrf
                    <input type="hidden" name="property_id" value="{{ $property?->id }}">
                    <input type="hidden" name="room_id" :value="room ? room.id : ''">

                    <div x-show="room" x-cloak x-transition class="flex items-center justify-between gap-4 rounded-2xl border border-emerald-200 bg-emerald-50 p-4">
                        <div class="flex items-center gap-3">
                            <span class="flex h-10 w-10 items-center justify-center rounded-2xl bg-emerald-500 text-white">{!! $svg('check', 'h-5 w-5') !!}</span>
                            <div>
                                <p class="text-sm font-bold text-stone-900">
                                    Kamar <span x-text="room && room.number"></span>
                                    <span class="font-normal text-stone-500">· <span x-text="room && room.type"></span></span>
                                </p>
                                <p class="text-sm font-semibold text-emerald-700" x-text="room && room.price + ' / bulan'"></p>
                            </div>
                        </div>
                        <button type="button" @click="room = null" class="rounded-xl px-3 py-1.5 text-xs font-bold text-emerald-700 hover:bg-emerald-100">Ganti</button>
                    </div>

                    @foreach ([
                        ['name', 'Nama lengkap', 'text', true, 'name'],
                        ['phone', 'No. HP / WhatsApp', 'tel', true, 'tel'],
                        ['email', 'Email (opsional)', 'email', false, 'email'],
                    ] as [$f, $label, $type, $req, $ac])
                        <div>
                            <label for="{{ $f }}" class="mb-2 block text-sm font-bold text-stone-700">{{ $label }}</label>
                            <input id="{{ $f }}" name="{{ $f }}" type="{{ $type }}" value="{{ old($f) }}" autocomplete="{{ $ac }}" @required($req) class="{{ $field }}">
                            @error($f)<p class="mt-1.5 text-sm text-rose-600">{{ $message }}</p>@enderror
                        </div>
                    @endforeach

                    <div>
                        <label for="message" class="mb-2 block text-sm font-bold text-stone-700">Pesan (opsional)</label>
                        <textarea id="message" name="message" rows="3" placeholder="Rencana mulai sewa, pertanyaan, dll." class="{{ $field }}">{{ old('message') }}</textarea>
                        @error('message')<p class="mt-1.5 text-sm text-rose-600">{{ $message }}</p>@enderror
                    </div>
                    @error('room_id')<p class="text-sm text-rose-600">{{ $message }}</p>@enderror

                    <button type="submit" class="group flex w-full items-center justify-center gap-2 rounded-2xl bg-stone-900 px-6 py-4 text-base font-bold text-white shadow-xl shadow-stone-900/10 transition hover:bg-amber-500 hover:text-stone-900">
                        Kirim Minat Sewa
                        <span class="transition group-hover:translate-x-0.5">{!! $svg('arrow', 'h-4 w-4') !!}</span>
                    </button>

                    <p class="text-center text-xs text-stone-400">Dengan mengirim form ini, kamu setuju dihubungi admin.</p>
                </form>
            </div>
        </div>
    </div>
</section>

{{-- CTA BANNER --}}
<section class="px-5 pb-20 sm:px-8">
    <div class="relative mx-auto max-w-7xl overflow-hidden rounded-[2.5rem] bg-gradient-to-br from-amber-400 via-orange-400 to-rose-400 px-8 py-16 text-center shadow-2xl shadow-amber-200/50 sm:px-16 sm:py-20">
        <div class="grain absolute inset-0 opacity-30"></div>
        <div class="relative">
            <h2 class="text-balance text-3xl font-bold tracking-tight text-white sm:text-5xl">
                Siap pindah ke hunian yang bikin betah?
            </h2>
            <p class="mx-auto mt-5 max-w-2xl text-lg text-white/90">
                Kamar terbatas. Amankan tempat kamu sekarang sebelum kehabisan.
            </p>
            <div class="mt-10 flex flex-col items-center justify-center gap-3 sm:flex-row">
                <a href="#minat" class="inline-flex w-full items-center justify-center gap-2 rounded-2xl bg-white px-7 py-4 text-sm font-bold text-stone-900 shadow-xl transition hover:bg-stone-50 sm:w-auto">
                    Isi Form Minat
                    {!! $svg('arrow', 'h-4 w-4') !!}
                </a>
                <a href="{{ $waUrl }}" target="_blank" rel="noopener" class="inline-flex w-full items-center justify-center gap-2 rounded-2xl border-2 border-white/40 bg-white/10 px-7 py-4 text-sm font-bold text-white backdrop-blur transition hover:bg-white/20 sm:w-auto">
                    {!! $svg('wa', 'h-4 w-4') !!}
                    Chat WhatsApp
                </a>
            </div>
        </div>
    </div>
</section>

{{-- FOOTER --}}
<footer class="border-t border-amber-100 bg-white">
    <div class="mx-auto max-w-7xl px-5 py-14 sm:px-8">
        <div class="grid gap-10 sm:grid-cols-2 lg:grid-cols-4">
            <div class="lg:col-span-2">
                <a href="#top" class="flex items-center gap-2.5">
                    @if ($s('logo'))
                        <img src="{{ asset('storage/' . $s('logo')) }}" alt="{{ $name }}" class="h-9 w-9 rounded-2xl object-cover">
                    @else
                        <span class="flex h-9 w-9 items-center justify-center rounded-2xl bg-gradient-to-br from-amber-400 to-orange-500 text-white">{!! $svg('home', 'h-4 w-4') !!}</span>
                    @endif
                    <span class="text-base font-bold text-stone-900">{{ $name }}</span>
                </a>
                <p class="mt-5 max-w-md text-sm leading-relaxed text-stone-500">
                    {{ $s('tagline', 'Hunian hangat yang bikin kamu betah pulang setiap hari.') }}
                </p>
            </div>

            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-stone-900">Navigasi</p>
                <ul class="mt-4 space-y-2.5 text-sm text-stone-500">
                    <li><a href="#keunggulan" class="hover:text-amber-600 transition">Keunggulan</a></li>
                    <li><a href="#kamar" class="hover:text-amber-600 transition">Kamar</a></li>
                    <li><a href="#testimoni" class="hover:text-amber-600 transition">Testimoni</a></li>
                    <li><a href="#faq" class="hover:text-amber-600 transition">FAQ</a></li>
                </ul>
            </div>

            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-stone-900">Kontak</p>
                <ul class="mt-4 space-y-2.5 text-sm text-stone-500">
                    @if ($s('phone'))<li>{{ $s('phone') }}</li>@endif
                    @if ($s('email'))<li>{{ $s('email') }}</li>@endif
                    @if ($s('address'))<li class="leading-relaxed">{{ $s('address') }}</li>@endif
                </ul>
            </div>
        </div>

        <div class="mt-12 flex flex-col items-center justify-between gap-4 border-t border-amber-50 pt-8 sm:flex-row">
            <p class="text-xs text-stone-400">&copy; {{ date('Y') }} {{ $name }}. Dibuat dengan ❤ untuk kenyamanan kamu.</p>
            <p class="text-xs text-stone-400">Hak cipta dilindungi.</p>
        </div>
    </div>
</footer>

</body>
</html>