<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Laporan Penjualan') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-bold mb-4">Ringkasan Penjualan</h3>

                    {{-- Bagian Notifikasi (Success/Error) --}}
                    @if (session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                        {{ session('success') }}
                    </div>
                    @endif
                    @if (session('error'))
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                        {{ session('error') }}
                    </div>
                    @endif

                    {{-- Form Filter Rentang Tanggal --}}
                    {{-- PERBAIKAN DI SINI: route('kasir.laporan') diganti menjadi route('sales.report') --}}
                    <form action="{{ route('sales.report') }}" method="GET" class="mb-6 p-4 border rounded-lg bg-gray-50 dark:bg-gray-700 flex flex-col md:flex-row items-end md:items-center space-y-3 md:space-y-0 md:space-x-4">
                        <div>
                            <x-input-label for="start_date" :value="__('Tanggal Mulai')" class="dark:text-gray-200" />
                            <x-text-input id="start_date" type="date" name="start_date" :value="$startDate->toDateString()" class="block mt-1 w-full dark:bg-gray-800 dark:border-gray-600 dark:text-gray-200" />
                        </div>
                        <div>
                            <x-input-label for="end_date" :value="__('Tanggal Akhir')" class="dark:text-gray-200" />
                            <x-text-input id="end_date" type="date" name="end_date" :value="$endDate->toDateString()" class="block mt-1 w-full dark:bg-gray-800 dark:border-gray-600 dark:text-gray-200" />
                        </div>
                        <div class="w-full md:w-auto">
                            <x-primary-button type="submit" class="w-full justify-center">
                                {{ __('Filter Laporan') }}
                            </x-primary-button>
                        </div>
                    </form>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded dark:bg-blue-900 dark:border-blue-700 dark:text-blue-200">
                            <p class="font-semibold text-sm">Total Penjualan dalam Rentang Ini:</p>
                            <p class="text-xl font-bold">Rp{{ number_format($totalSalesFiltered, 0, ',', '.') }}</p>
                        </div>
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded dark:bg-green-900 dark:border-green-700 dark:text-green-200">
                            <p class="font-semibold text-sm">Rentang Tanggal Dipilih:</p>
                            <p class="text-xl font-bold">{{ $startDate->format('d/m/Y') }} - {{ $endDate->format('d/m/Y') }}</p>
                        </div>
                    </div>

                    <h3 class="text-lg font-bold mb-4 text-gray-800 dark:text-gray-200">Produk Terlaris (Kuantitas Terjual) dalam Rentang Ini</h3>

                    @if ($topSellingProducts->isEmpty())
                    <p class="text-gray-500 text-center py-4 dark:text-gray-400">Belum ada data produk terlaris untuk rentang tanggal ini.</p>
                    @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-200 uppercase tracking-wider">
                                        Nama Produk
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-200 uppercase tracking-wider">
                                        Jumlah Terjual
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-200 uppercase tracking-wider">
                                        Total Pendapatan
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach ($topSellingProducts as $product)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                        {{ $product->product_name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                        {{ $product->total_quantity_sold }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                        Rp{{ number_format($product->total_revenue, 0, ',', '.') }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>