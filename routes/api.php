<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\CategoryController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Middleware 'auth:sanctum' biasanya digunakan untuk SPA atau mobile app
// Pastikan rute /products tidak di bawah middleware ini jika Anda tidak menggunakan otentikasi API
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// API untuk mendapatkan daftar produk (digunakan di POS frontend)
// Pastikan tidak ada middleware lain yang memblokir akses ke rute ini.
Route::get('/products', [ProductController::class, 'apiIndex']);

// API untuk mendapatkan daftar pelanggan (opsional, jika ingin pencarian dinamis di masa depan)
Route::get('/customers', [CustomerController::class, 'apiIndex']);
// API untuk mendapatkan daftar kategori (opsional, jika ingin filter dinamis)
Route::get('/categories', [CategoryController::class, 'apiIndex']);
