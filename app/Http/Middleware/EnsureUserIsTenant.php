<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsTenant
{
    /**
     * Hanya user dengan role 'penghuni' yang boleh lewat.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || $user->role !== 'penghuni') {
            abort(403, 'Halaman ini khusus untuk penghuni.');
        }

        // Pastikan user punya data tenant
        if (! $user->tenant) {
            abort(403, 'Akun Anda belum terhubung ke data penghuni. Hubungi admin.');
        }

        return $next($request);
    }
}