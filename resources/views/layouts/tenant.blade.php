<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - Kos Adin</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        [x-cloak] { display: none !important; }
        body { font-family: ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto, sans-serif; }
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in-up { animation: fadeInUp 0.4s ease-out; }
    </style>
    @stack('head')
</head>
<body class="bg-slate-50 font-sans antialiased text-slate-800">

@php
    $menu = [
        ['Dashboard',        'layout-dashboard', 'tenant.dashboard',          'tenant.dashboard'],
        ['Tagihan Saya',     'receipt',          'tenant.invoices.index',     'tenant.invoices.*'],
        ['Pembayaran Saya',  'credit-card',      'tenant.payments.index',     'tenant.payments.*'],
        ['Lapor Kerusakan',  'wrench',           'tenant.maintenances.index', 'tenant.maintenances.*'],
        ['Perpanjangan',     'refresh-cw',       'tenant.extensions.index',   'tenant.extensions.*'],
    ];

    $resourceLabels = [
        'dashboard'    => 'Dashboard',
        'invoices'     => 'Tagihan Saya',
        'payments'     => 'Pembayaran Saya',
        'maintenances' => 'Lapor Kerusakan',
        'extensions'   => 'Perpanjangan',
    ];
    $actionLabels = ['show' => 'Detail', 'create' => 'Buat'];

    $parts  = explode('.', request()->route()?->getName() ?? '');
    $crumbs = [['Penghuni', route('tenant.dashboard')]];
    $resource = $parts[1] ?? 'dashboard';

    if ($resource !== 'dashboard') {
        $crumbs[] = [
            $resourceLabels[$resource] ?? ucfirst($resource),
            Route::has("tenant.{$resource}.index") ? route("tenant.{$resource}.index") : null,
        ];
    } else {
        $crumbs[] = ['Dashboard', null];
    }
    if (isset($parts[2], $actionLabels[$parts[2]])) {
        $crumbs[] = [$actionLabels[$parts[2]], null];
    }
@endphp

<div class="min-h-screen" x-data="{ sidebarOpen: false }">
    <div x-show="sidebarOpen" x-cloak @click="sidebarOpen = false"
         class="fixed inset-0 z-30 bg-slate-900/60 backdrop-blur-sm lg:hidden"></div>

    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
           class="fixed inset-y-0 left-0 z-40 flex w-64 transform flex-col bg-gradient-to-b from-emerald-900 via-emerald-900 to-teal-950 text-emerald-100 transition-transform duration-200 ease-out lg:translate-x-0">

        <div class="flex h-16 shrink-0 items-center gap-3 px-5">
            <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 shadow-lg shadow-emerald-500/30">
                <x-icon name="home" :size="18" class="text-white" />
            </div>
            <div class="flex flex-col">
                <span class="text-base font-bold text-white leading-tight">Kos Adin</span>
                <span class="text-[10px] text-emerald-300 leading-tight">Portal Penghuni</span>
            </div>
            <button type="button" class="ml-auto text-emerald-300 hover:text-white lg:hidden" @click="sidebarOpen = false" aria-label="Tutup menu">
                <x-icon name="x" :size="20" />
            </button>
        </div>

        <nav class="flex-1 space-y-1 overflow-y-auto px-3 py-4">
            @foreach ($menu as [$label, $icon, $routeName, $pattern])
                @php
                    $exists = Route::has($routeName);
                    $active = $exists && request()->routeIs($pattern);
                @endphp
                <a href="{{ $exists ? route($routeName) : '#' }}"
                   @class([
                       'group relative flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-all duration-150',
                       'bg-gradient-to-r from-emerald-500 to-teal-500 text-white shadow-md shadow-emerald-500/20' => $active,
                       'hover:bg-emerald-800/60 hover:text-white' => $exists && ! $active,
                       'cursor-not-allowed opacity-40' => ! $exists,
                   ])>
                    @if ($active)
                        <span class="absolute left-0 top-1/2 h-6 w-1 -translate-y-1/2 rounded-r-full bg-white"></span>
                    @endif
                    <x-icon :name="$icon" :size="18" class="shrink-0" />
                    <span class="flex-1">{{ $label }}</span>
                </a>
            @endforeach
        </nav>

        <div class="shrink-0 border-t border-emerald-800 p-3">
            <div class="flex items-center gap-3 rounded-lg bg-emerald-800/50 px-3 py-2 mb-2">
                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-emerald-500 to-teal-600 text-sm font-bold text-white">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </div>
                <div class="min-w-0 flex-1">
                    <p class="truncate text-sm font-medium text-white">{{ Auth::user()->name }}</p>
                    <p class="truncate text-[11px] text-emerald-300">Penghuni</p>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="flex w-full items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium text-emerald-100 transition hover:bg-red-500/10 hover:text-red-300">
                    <x-icon name="log-out" :size="18" class="shrink-0" />
                    <span>Logout</span>
                </button>
            </form>
        </div>
    </aside>

    <div class="lg:pl-64">
        <header class="sticky top-0 z-20 flex h-16 items-center justify-between gap-4 border-b border-slate-200 bg-white/80 backdrop-blur-md px-4 sm:px-6">
            <div class="flex min-w-0 items-center gap-3">
                <button type="button" class="rounded-lg p-2 text-slate-600 hover:bg-slate-100 lg:hidden" @click="sidebarOpen = true" aria-label="Buka menu">
                    <x-icon name="menu" :size="22" />
                </button>

                <nav class="hidden min-w-0 text-sm text-slate-500 sm:block" aria-label="Breadcrumb">
                    <ol class="flex items-center gap-2">
                        @foreach ($crumbs as $i => [$text, $url])
                            @if ($i > 0)
                                <li class="text-slate-300">
                                    <x-icon name="chevron-right" :size="14" />
                                </li>
                            @endif
                            <li class="truncate">
                                @if ($url && ! $loop->last)
                                    <a href="{{ $url }}" class="hover:text-emerald-600 transition">{{ $text }}</a>
                                @else
                                    <span @class(['font-semibold text-slate-800' => $loop->last])>{{ $text }}</span>
                                @endif
                            </li>
                        @endforeach
                    </ol>
                </nav>
            </div>

            <div class="flex items-center gap-2">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button type="button" class="flex items-center gap-2 rounded-lg px-2 py-1.5 text-sm font-medium text-slate-700 hover:bg-slate-100 transition">
                            <span class="flex h-8 w-8 items-center justify-center rounded-full bg-gradient-to-br from-emerald-500 to-teal-600 text-sm font-semibold text-white">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </span>
                            <span class="hidden sm:block">{{ Auth::user()->name }}</span>
                            <x-icon name="chevron-down" :size="16" class="hidden sm:block" />
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')" class="flex items-center gap-2">
                            <x-icon name="user" :size="16" />
                            Profil
                        </x-dropdown-link>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();" class="flex items-center gap-2">
                                <x-icon name="log-out" :size="16" />
                                Logout
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>
        </header>

        <main class="p-4 sm:p-6 animate-fade-in-up">
            @if (session('success') || session('error') || $errors->any())
                <div class="mb-4 space-y-3">
                    @if (session('success'))
                        <x-ui.alert type="success">{{ session('success') }}</x-ui.alert>
                    @endif
                    @if (session('error'))
                        <x-ui.alert type="error">{{ session('error') }}</x-ui.alert>
                    @endif
                    @if ($errors->any())
                        <x-ui.alert type="error">Ada isian yang belum sesuai. Silakan periksa kembali form di bawah.</x-ui.alert>
                    @endif
                </div>
            @endif

            @yield('content')
        </main>
    </div>
</div>

@stack('scripts')
</body>
</html>