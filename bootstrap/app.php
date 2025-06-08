<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\IsKasir;
use App\Http\Middleware\IsAdmin;
use App\Http\Middleware\IsManager;
use App\Http\Middleware\Authenticate;
use App\Http\Middleware\RedirectIfAuthenticated;
use App\Http\Middleware\VerifyCsrfToken;
use App\Http\Middleware\AdminOrManagerOrKasir;

// Tambahkan use statement untuk middleware session yang hilang
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse; // Ini juga penting untuk cookies

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'auth' => Authenticate::class,
            'guest' => RedirectIfAuthenticated::class,
            'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
            // 'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class, // Tetap dikomentari/dihapus sesuai keinginan Anda
            'admin' => IsAdmin::class,
            'kasir' => IsKasir::class,
            'manager' => IsManager::class,
            'admin_or_manager_or_kasir' => AdminOrManagerOrKasir::class,
        ]);

        // Tambahkan middleware global untuk grup 'web'
        $middleware->web(append: [
            AddQueuedCookiesToResponse::class, // Penting untuk cookies, termasuk session ID
            StartSession::class,             // <-- INI YANG HILANG (Memulai Session)
            ShareErrorsFromSession::class,   // <-- INI YANG HILANG (Membagikan Flash Data ke View)
            VerifyCsrfToken::class,          // Ini sudah ada
            \Illuminate\Routing\Middleware\SubstituteBindings::class, // Ini juga penting
            // Anda mungkin juga memiliki middleware lain di sini dari instalasi Breeze, seperti:
            // \App\Http\Middleware\dTrustProxies::class,
            // \Illuminate\Http\Middleware\HandleCors::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
