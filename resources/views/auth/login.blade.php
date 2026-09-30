<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Masuk — Kos Adin</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: Inter, ui-sans-serif, system-ui, -apple-system, 'Segoe UI', Roboto, sans-serif; }
        .grain { background-image: radial-gradient(circle at 1px 1px, rgba(120,53,15,.06) 1px, transparent 0); background-size: 24px 24px; }
        .blob-amber { background: radial-gradient(circle, rgba(251,191,36,.35) 0%, transparent 70%); }
        .blob-orange { background: radial-gradient(circle, rgba(251,146,60,.25) 0%, transparent 70%); }
        @keyframes float-slow { 0%, 100% { transform: translateY(0) rotate(0deg); } 50% { transform: translateY(-10px) rotate(2deg); } }
        .float-slow { animation: float-slow 6s ease-in-out infinite; }
        @media (prefers-reduced-motion:reduce){*{transition:none!important;animation:none!important}}
    </style>
</head>
<body class="min-h-screen bg-[#fdfaf6] text-stone-800 antialiased">

<div class="min-h-screen grid lg:grid-cols-2">

    {{-- ============================================ --}}
    {{-- KIRI: BRANDING (hidden di mobile) --}}
    {{-- ============================================ --}}
    <div class="relative hidden overflow-hidden lg:block">
        {{-- Background gradient --}}
        <div class="absolute inset-0 bg-gradient-to-br from-amber-400 via-orange-400 to-rose-400"></div>
        <div class="grain absolute inset-0 opacity-40"></div>
        <div class="absolute -top-40 -left-40 h-96 w-96 rounded-full bg-white/20 blur-3xl"></div>
        <div class="absolute -bottom-40 -right-40 h-96 w-96 rounded-full bg-white/20 blur-3xl"></div>

        {{-- Content --}}
        <div class="relative flex h-full flex-col justify-between p-12 text-white">
            {{-- Logo --}}
            <a href="/kos" class="flex items-center gap-3">
                <span class="flex h-10 w-10 items-center justify-center rounded-2xl bg-white/20 backdrop-blur">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                        <polyline points="9 22 9 12 15 12 15 22"/>
                    </svg>
                </span>
                <span class="text-lg font-bold tracking-tight">Kos Adin</span>
            </a>

            {{-- Hero text --}}
            <div class="max-w-md">
                <div class="inline-flex items-center gap-2 rounded-full bg-white/20 px-3.5 py-1.5 text-xs font-medium backdrop-blur">
                    <span class="relative flex h-2 w-2">
                        <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-white opacity-75"></span>
                        <span class="relative inline-flex h-2 w-2 rounded-full bg-white"></span>
                    </span>
                    Portal Kos Adin
                </div>

                <h1 class="mt-6 text-4xl font-bold leading-[1.1] tracking-tight sm:text-5xl">
                    Selamat datang kembali.
                </h1>
                <p class="mt-5 text-lg leading-relaxed text-white/90">
                    Kelola kos, tagihan, dan penghuni dengan mudah. Semua dalam satu portal.
                </p>

                {{-- Small testimonial --}}
                <div class="mt-10 rounded-2xl border border-white/20 bg-white/10 p-5 backdrop-blur">
                    <div class="flex gap-0.5 text-white">
                        @for ($i = 0; $i < 5; $i++)
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                        @endfor
                    </div>
                    <p class="mt-3 text-sm leading-relaxed text-white/90">
                        "Sejak pakai portal ini, kerjaan ngelola kos jadi jauh lebih gampang. Laporan otomatis, penghuni juga gampang bayar."
                    </p>
                    <p class="mt-3 text-xs font-medium text-white/70">— Admin Kos Adin</p>
                </div>
            </div>

            {{-- Footer --}}
            <p class="text-xs text-white/70">
                &copy; {{ date('Y') }} Kos Adin. Semua hak dilindungi.
            </p>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- KANAN: FORM LOGIN --}}
    {{-- ============================================ --}}
    <div class="relative flex items-center justify-center px-5 py-12 sm:px-8">
        {{-- Decorative blobs (mobile) --}}
        <div class="blob-amber absolute -top-40 -right-40 h-96 w-96 rounded-full lg:hidden"></div>
        <div class="blob-orange absolute -bottom-40 -left-40 h-96 w-96 rounded-full lg:hidden"></div>

        <div class="relative w-full max-w-md">
            {{-- Mobile logo --}}
            <div class="mb-10 text-center lg:hidden">
                <a href="/kos" class="inline-flex items-center gap-3">
                    <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-br from-amber-400 to-orange-500 shadow-lg shadow-amber-200">
                        <svg class="h-6 w-6 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                            <polyline points="9 22 9 12 15 12 15 22"/>
                        </svg>
                    </span>
                    <span class="text-xl font-bold tracking-tight text-stone-900">Kos Adin</span>
                </a>
            </div>

            {{-- Header --}}
            <div class="text-center lg:text-left">
                <h2 class="text-3xl font-bold tracking-tight text-stone-900">Masuk ke akun</h2>
                <p class="mt-2 text-sm text-stone-500">
                    Belum punya akun?
                    <a href="{{ route('register') }}" class="font-semibold text-amber-600 hover:text-amber-700 hover:underline">Daftar sekarang</a>
                </p>
            </div>

            {{-- Session status --}}
            @if (session('status'))
                <div class="mt-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                    {{ session('status') }}
                </div>
            @endif

            {{-- Error umum --}}
            @if ($errors->any() && ! $errors->has('email') && ! $errors->has('password'))
                <div class="mt-6 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">
                    {{ $errors->first() }}
                </div>
            @endif

            {{-- Form --}}
            <form method="POST" action="{{ route('login') }}" class="mt-8 space-y-5">
                @csrf

                {{-- Email --}}
                <div>
                    <label for="email" class="mb-2 block text-sm font-semibold text-stone-700">Email</label>
                    <div class="relative">
                        <span class="pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-stone-400">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                                <rect width="20" height="16" x="2" y="4" rx="2"/>
                                <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/>
                            </svg>
                        </span>
                        <input id="email" name="email" type="email" required autofocus autocomplete="username"
                               value="{{ old('email') }}"
                               placeholder="email@example.com"
                               class="w-full rounded-2xl border @error('email') border-rose-300 bg-rose-50/30 @else border-amber-100 bg-white @enderror px-4 py-3.5 pl-12 text-sm text-stone-900 placeholder:text-stone-400 transition focus:border-amber-400 focus:outline-none focus:ring-4 focus:ring-amber-100">
                    </div>
                    @error('email')
                        <p class="mt-2 flex items-center gap-1.5 text-sm text-rose-600">
                            <svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" x2="12" y1="8" y2="12"/><line x1="12" x2="12.01" y1="16" y2="16"/></svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Password --}}
                <div x-data="{ show: false }">
                    <label for="password" class="mb-2 block text-sm font-semibold text-stone-700">Password</label>
                    <div class="relative">
                        <span class="pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-stone-400">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                                <rect width="18" height="11" x="3" y="11" rx="2" ry="2"/>
                                <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                            </svg>
                        </span>
                        <input id="password" name="password" :type="show ? 'text' : 'password'" required autocomplete="current-password"
                               placeholder="••••••••"
                               class="w-full rounded-2xl border @error('password') border-rose-300 bg-rose-50/30 @else border-amber-100 bg-white @enderror px-4 py-3.5 pl-12 pr-12 text-sm text-stone-900 placeholder:text-stone-400 transition focus:border-amber-400 focus:outline-none focus:ring-4 focus:ring-amber-100">
                        <button type="button" @click="show = !show"
                                class="absolute right-4 top-1/2 -translate-y-1/2 text-stone-400 hover:text-stone-600 transition"
                                :aria-label="show ? 'Sembunyikan password' : 'Tampilkan password'">
                            <template x-if="!show">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/>
                                    <circle cx="12" cy="12" r="3"/>
                                </svg>
                            </template>
                            <template x-if="show">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"/>
                                    <path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"/>
                                    <path d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"/>
                                    <line x1="2" x2="22" y1="2" y2="22"/>
                                </svg>
                            </template>
                        </button>
                    </div>
                    @error('password')
                        <p class="mt-2 flex items-center gap-1.5 text-sm text-rose-600">
                            <svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" x2="12" y1="8" y2="12"/><line x1="12" x2="12.01" y1="16" y2="16"/></svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Remember + Forgot --}}
                <div class="flex items-center justify-between gap-3">
                    <label for="remember_me" class="inline-flex cursor-pointer items-center gap-2 text-sm text-stone-600">
                        <input id="remember_me" type="checkbox" name="remember"
                               class="h-4 w-4 rounded border-stone-300 text-amber-600 focus:ring-amber-500">
                        Ingat saya
                    </label>

                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-sm font-semibold text-amber-600 hover:text-amber-700 hover:underline">
                            Lupa password?
                        </a>
                    @endif
                </div>

                {{-- Submit --}}
                <button type="submit"
                        class="group flex w-full items-center justify-center gap-2 rounded-2xl bg-stone-900 px-6 py-4 text-sm font-bold text-white shadow-xl shadow-stone-900/10 transition hover:bg-amber-500 hover:text-stone-900">
                    Masuk
                    <svg class="h-4 w-4 transition group-hover:translate-x-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M5 12h14"/>
                        <path d="m12 5 7 7-7 7"/>
                    </svg>
                </button>
            </form>

            {{-- Divider --}}
            <div class="mt-8 flex items-center gap-4">
                <div class="h-px flex-1 bg-amber-100"></div>
                <span class="text-xs font-medium uppercase tracking-wider text-stone-400">atau</span>
                <div class="h-px flex-1 bg-amber-100"></div>
            </div>

            {{-- Link ke landing --}}
            <a href="/kos" class="mt-6 flex w-full items-center justify-center gap-2 rounded-2xl border border-amber-200 bg-white px-6 py-3.5 text-sm font-semibold text-stone-700 transition hover:border-amber-300 hover:bg-amber-50">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                    <polyline points="9 22 9 12 15 12 15 22"/>
                </svg>
                Lihat halaman kos
            </a>

            {{-- Akun demo --}}
            <div class="mt-8 rounded-2xl border border-amber-100 bg-amber-50/50 p-4">
                <p class="text-xs font-bold uppercase tracking-wider text-amber-700">Akun Demo</p>
                <div class="mt-3 space-y-2 text-xs">
                    <div class="flex items-center justify-between gap-3">
                        <span class="text-stone-600">Admin</span>
                        <code class="rounded bg-white px-2 py-1 font-mono text-stone-800">admin@kos.test / 12345678</code>
                    </div>
                    <div class="flex items-center justify-between gap-3">
                        <span class="text-stone-600">Penghuni</span>
                        <code class="rounded bg-white px-2 py-1 font-mono text-stone-800">penghuni7@kos.test / 12345678</code>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

</body>
</html>