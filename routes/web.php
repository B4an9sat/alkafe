<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Auth;

// Rute default yang mengarahkan ke halaman dashboard jika sudah login.
// Jika belum login, akan di-redirect ke halaman login Breeze standar (/login).
Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
});

// Rute Dashboard utama yang dilindungi oleh middleware 'auth'.
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// --- Grup Rute yang Membutuhkan Autentikasi (Auth::check()) ---
Route::middleware('auth')->group(function () {

    // Rute Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // --- Rute untuk Fungsionalitas POS (Point of Sale) ---
    // Halaman utama Point of Sale (tempat kasir melakukan transaksi)
    Route::get('/kasir/pos', [PosController::class, 'index'])->name('pos.index'); // Nama rute: pos.index
    // Proses transaksi dari halaman POS
    Route::post('/kasir/pos/process', [PosController::class, 'processTransaction'])->name('kasir.processTransaction');
    // Halaman riwayat transaksi
    Route::get('/kasir/transaksi', [PosController::class, 'transactionHistory'])->name('transactions.index'); // Nama rute: transactions.index
    // Halaman detail transaksi individual
    Route::get('/kasir/transaksi/{transaction}', [PosController::class, 'showTransactionDetail'])->name('kasir.transaksi.detail');
    // Halaman laporan penjualan
    Route::get('/kasir/laporan', [PosController::class, 'salesReport'])->name('sales.report'); // Nama rute: sales.report
    // Rute untuk Struk Belanja (Receipt)
    Route::get('/kasir/receipt/{transaction}', [PosController::class, 'showReceipt'])->name('receipt.show');

    // Rute Manajemen Produk
    Route::resource('products', ProductController::class);

    // --- Rute untuk Manajemen Kategori dan Pengguna (Hanya Admin) ---
    Route::middleware('admin')->group(function () {
        Route::resource('categories', CategoryController::class);
        Route::resource('users', UserController::class);
    });

    // Rute Khusus Dashboard Berdasarkan Peran
    Route::get('/admin/dashboard', function () {
        return view('admin.dashboard');
    })->middleware('admin')->name('admin.dashboard');

    Route::get('/manager/dashboard', function () {
        return view('manager.dashboard');
    })->middleware('manager')->name('manager.dashboard');
});

// Rute otentikasi yang disediakan oleh Breeze
require __DIR__ . '/auth.php';
