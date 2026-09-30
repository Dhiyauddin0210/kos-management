@php
    $settings = $settings ?? [];
    $name  = $settings['name'] ?? 'Kos Adin';
    $wa    = preg_replace('/^0/', '62', preg_replace('/\D/', '', $settings['whatsapp'] ?? ''));
    $waUrl = $wa ? 'https://wa.me/' . $wa . '?text=' . rawurlencode('Halo, saya baru mengisi form minat sewa di ' . $name) : null;
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Terima kasih - {{ $name }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="relative isolate flex min-h-screen items-center justify-center overflow-hidden bg-gradient-to-b from-slate-950 via-[#0a1428] to-slate-900 px-5 py-12 antialiased" style="font-family:Inter,ui-sans-serif,system-ui,-apple-system,'Segoe UI',Roboto,sans-serif">
    <div class="pointer-events-none absolute left-1/2 top-1/4 -z-10 h-96 w-96 -translate-x-1/2 rounded-full bg-emerald-500/25 blur-3xl"></div>

    <main class="w-full max-w-lg rounded-3xl bg-white p-8 text-center shadow-2xl shadow-black/40 sm:p-12">
        <span class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-emerald-50 text-emerald-600 ring-8 ring-emerald-50/60">
            <svg class="h-8 w-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 6 9 17l-5-5"/></svg>
        </span>
        <h1 class="mt-7 text-3xl font-semibold tracking-tight text-slate-900">Terima kasih!</h1>
        <p class="mt-3 leading-relaxed text-slate-500">Minat sewa Anda sudah kami terima. Tim {{ $name }} akan menghubungi Anda melalui nomor yang Anda isi, biasanya dalam 1 x 24 jam.</p>

        <div class="mt-9 flex flex-col gap-3">
            @if ($waUrl)
                <a href="{{ $waUrl }}" target="_blank" rel="noopener" class="inline-flex items-center justify-center gap-2 rounded-2xl bg-emerald-600 px-6 py-3.5 text-sm font-semibold text-white shadow-lg shadow-emerald-600/25 transition hover:bg-emerald-500">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 11.5a8.4 8.4 0 0 1-12.3 7.4L3 21l2.1-5.6A8.4 8.4 0 1 1 21 11.5z"/></svg>
                    Ingin lebih cepat? Chat WhatsApp
                </a>
            @endif
            <a href="{{ url('/kos') }}" class="inline-flex items-center justify-center rounded-2xl px-6 py-3.5 text-sm font-semibold text-slate-700 ring-1 ring-slate-200 transition hover:bg-slate-50">Kembali ke halaman utama</a>
        </div>
    </main>
</body>
</html>