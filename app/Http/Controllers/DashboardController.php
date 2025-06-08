<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        // Statistik untuk Dashboard
        $totalProducts = Product::count();
        $totalCustomers = Customer::count();
        $totalUsers = User::count(); // Total pengguna terdaftar

        // Penjualan Hari Ini
        $today = Carbon::today();
        $salesToday = Transaction::whereDate('created_at', $today)->sum('total_amount');
        $transactionsToday = Transaction::whereDate('created_at', $today)->count();

        // Penjualan Bulan Ini
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();
        $salesThisMonth = Transaction::whereBetween('created_at', [$startOfMonth, $endOfMonth])->sum('total_amount');
        $transactionsThisMonth = Transaction::whereBetween('created_at', [$startOfMonth, $endOfMonth])->count();

        // Produk dengan Stok Rendah (misal, stok < 10)
        $lowStockProducts = Product::where('stock', '<', 10)->orderBy('stock', 'asc')->get();

        // Data yang akan dikirim ke view
        return view('dashboard', compact(
            'totalProducts',
            'totalCustomers',
            'totalUsers',
            'salesToday',
            'transactionsToday',
            'salesThisMonth',
            'transactionsThisMonth',
            'lowStockProducts'
            // salesLabels, salesData, userRoleLabels, userRoleData DIHAPUS
        ));
    }
}
