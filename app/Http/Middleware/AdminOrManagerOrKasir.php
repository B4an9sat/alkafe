<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class AdminOrManagerOrKasir
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Pastikan user login dan memiliki salah satu dari peran yang diizinkan
        if (Auth::check() && $user && ($user->isAdmin() || $user->isManager() || $user->isCashier())) { // <-- DI SINI PERBAIKAN
            return $next($request);
        }

        return abort(403, 'Akses Ditolak. Anda tidak memiliki izin untuk mengakses halaman ini.');
    }
}
