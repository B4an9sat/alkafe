<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class IsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Pastikan user login dan memiliki role 'admin'
        if (Auth::check() && Auth::user()->isAdmin()) {
            return $next($request);
        }

        // Jika tidak, arahkan kembali atau tampilkan pesan error
        return abort(403, 'Unauthorized access.'); // Memberikan HTTP 403 Forbidden
        // Atau Anda bisa redirect:
        // return redirect('/dashboard')->with('error', 'Anda tidak memiliki akses sebagai Admin.');
    }
}