<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    {{ __("Selamat Datang, ") }} {{ Auth::user()->name }}!
                </div>
            </div>

            <div class="mt-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Card Total Produk -->
                <div class="bg-indigo-600 text-white p-6 rounded-lg shadow-md flex items-center justify-between">
                    <div>
                        <p class="text-sm opacity-80">Total Produk</p>
                        <p class="text-3xl font-bold">{{ $totalProducts }}</p>
                    </div>
                    <div>
                        <svg class="h-12 w-12 opacity-50" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M5 21h14c1.103 0 2-.897 2-2V5c0-1.103-.897-2-2-2H5c-1.103 0-2 .897-2 2v14c0 1.103.897 2 2 2zm14-16v14H5V5h14zm-8 4h-2v3H8v2h1v-3h2V9zm4 0h-2v3h-1v2h1v-3h2V9z" />
                        </svg>
                    </div>
                </div>

                <!-- Card Total Transaksi (atau bisa diganti dengan Sales Today) -->
                <div class="bg-green-600 text-white p-6 rounded-lg shadow-md flex items-center justify-between">
                    <div>
                        <p class="text-sm opacity-80">Penjualan Hari Ini</p>
                        <p class="text-3xl font-bold">Rp{{ number_format($salesToday, 0, ',', '.') }}</p>
                    </div>
                    <div>
                        <svg class="h-12 w-12 opacity-50" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 15H9v-2h2v2zm0-4H9V7h2v6z" />
                        </svg>
                    </div>
                </div>

                <!-- Card Total Penjualan Bulan Ini -->
                <div class="bg-yellow-600 text-white p-6 rounded-lg shadow-md flex items-center justify-between">
                    <div>
                        <p class="text-sm opacity-80">Penjualan Bulan Ini</p>
                        <p class="text-3xl font-bold">Rp{{ number_format($salesThisMonth, 0, ',', '.') }}</p>
                    </div>
                    <div>
                        <svg class="h-12 w-12 opacity-50" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 11c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3zm0 8c-3.31 0-6-2.69-6-6s2.69-6 6-6 6 2.69 6 6-2.69 6-6 6zM5 4v2h14V4H5zm-1 7H2c.55 0 1 .45 1 1v7c0 .55-.45 1-1 1h2c-.55 0-1-.45-1-1v-7c0-.55.45-1 1-1zM22 11h-2c-.55 0-1 .45-1 1v7c0 .55.45 1 1 1h2c.55 0 1-.45 1-1v-7c0-.55-.45-1-1-1z" />
                        </svg>
                    </div>
                </div>

                <!-- Card Total Pelanggan -->
                <div class="bg-teal-600 text-white p-6 rounded-lg shadow-md flex items-center justify-between">
                    <div>
                        <p class="text-sm opacity-80">Total Pelanggan</p>
                        <p class="text-3xl font-bold">{{ $totalCustomers }}</p>
                    </div>
                    <div>
                        <svg class="h-12 w-12 opacity-50" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Bagian untuk Produk Stok Rendah (Opsional, Anda bisa hilangkan jika tidak diinginkan) -->
            @if (!$lowStockProducts->isEmpty())
            <div class="mt-6 bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-bold mb-4">Produk dengan Stok Rendah (< 10)</h3>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                    <thead class="bg-gray-50 dark:bg-gray-700">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-200 uppercase tracking-wider">Nama Produk</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-200 uppercase tracking-wider">Stok</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-200 uppercase tracking-wider">Kategori</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                        @foreach ($lowStockProducts as $product)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">{{ $product->name }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-red-500 dark:text-red-400 font-bold">{{ $product->stock }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">{{ $product->category->name ?? 'N/A' }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</x-app-layout>