<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB; // Import DB facade

class HomeController extends Controller
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
        $totalUsers = User::count();

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

        // --- Data untuk Grafik Penjualan Bulanan ---
        $monthlySales = Transaction::select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('SUM(total_amount) as total_sales')
            )
            ->whereYear('created_at', Carbon::now()->year) // Ambil data tahun ini
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->keyBy('month')
            ->toArray();

        $salesLabels = [];
        $salesData = [];
        for ($i = 1; $i <= 12; $i++) {
            $monthName = Carbon::create(null, $i, 1)->translatedFormat('M'); // Nama bulan singkat
            $salesLabels[] = $monthName;
            $salesData[] = $monthlySales[$i]['total_sales'] ?? 0; // Ambil total penjualan atau 0 jika tidak ada
        }

        // --- Data untuk Statistik Pengguna (berdasarkan peran) ---
        $userRoles = User::select(
                'role',
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('role')
            ->get();
        
        $userRoleLabels = $userRoles->pluck('role')->toArray();
        $userRoleData = $userRoles->pluck('count')->toArray();

        return view('dashboard', compact(
            'totalProducts',
            'totalCustomers',
            'totalUsers',
            'salesToday',
            'transactionsToday',
            'salesThisMonth',
            'transactionsThisMonth',
            'lowStockProducts',
            'salesLabels', // Data untuk grafik
            'salesData',   // Data untuk grafik
            'userRoleLabels', // Data untuk grafik pengguna
            'userRoleData'    // Data untuk grafik pengguna
        ));
    }
}
