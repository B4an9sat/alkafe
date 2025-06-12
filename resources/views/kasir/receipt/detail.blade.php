<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Detail Transaksi') }} #{{ $transaction->invoice_number }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    {{-- Konten yang akan dicetak --}}
                    {{-- ID #receipt-content TIDAK LAGI DIBUTUHKAN DI SINI,
                         karena konten cetak sudah diurus oleh receipt/print.blade.php --}}
                    <div>
                        <h3 class="text-lg font-semibold mb-4">Informasi Transaksi</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                            <div>
                                <p><strong>ID Transaksi:</strong> {{ $transaction->id }}</p>
                                <p><strong>Tanggal:</strong> {{ $transaction->created_at->format('d M Y H:i:s') }}</p>
                                <p><strong>Pelanggan:</strong> {{ $transaction->customer ? $transaction->customer->name : 'Umum' }}</p>
                            </div>
                            <div>
                                <p><strong>Total Pembayaran:</strong> Rp{{ number_format($transaction->total_amount, 0, ',', '.') }}</p>
                                <p><strong>Jumlah Dibayar:</strong> Rp{{ number_format($transaction->payment_amount, 0, ',', '.') }}</p>
                                <p><strong>Kembalian:</strong> Rp{{ number_format($transaction->change_amount, 0, ',', '.') }}</p>
                            </div>
                        </div>

                        <h3 class="text-lg font-semibold mb-4">Detail Item Transaksi</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-200 uppercase tracking-wider">Produk</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-200 uppercase tracking-wider">Harga</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-200 uppercase tracking-wider">Jumlah</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-200 uppercase tracking-wider">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach ($transaction->transactionItems as $item)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $item->product ? $item->product->name : 'Produk Tidak Ditemukan' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">Rp{{ number_format($item->price, 0, ',', '.') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $item->quantity }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">Rp{{ number_format($item->subtotal, 0, ',', '.') }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- Tombol Cetak dan Kembali --}}
                    <div class="mt-6 text-right">
                        {{-- Mengarahkan ke rute print.blade.php dan membuka di tab baru --}}
                        <a href="{{ route('receipt.print', $transaction->id) }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150 mr-2">
                            {{ __('Cetak Struk') }}
                        </a>
                        <a href="{{ route('transactions.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:bg-gray-700 dark:text-gray-200 dark:border-gray-600 dark:hover:bg-gray-600">
                            {{ __('Kembali ke Riwayat Transaksi') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
    <style>
        /* CSS khusus untuk tampilan cetak */
        @media print {

            /* Sembunyikan semua konten body secara default */
            body {
                visibility: hidden;
                margin: 0 !important;
                padding: 0 !important;
            }

            /* Tampilkan hanya konten struk (#receipt-content) */
            #receipt-content {
                visibility: visible;
                display: block !important;
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                max-width: 80mm;
                margin: 0 auto;
                padding: 10px;
                box-shadow: none;
                border-radius: 0;
                background-color: #fff;
                color: #000;
                font-family: 'monospace';
                font-size: 10pt;
                z-index: 9999;
            }

            /* Sembunyikan elemen utama layout aplikasi Laravel secara menyeluruh */
            /* Ini menargetkan div utama dari resources/views/layouts/app.blade.php */
            .min-h-screen.bg-gray-100.dark\:bg-gray-900.flex,
            /* Wrapper utama aplikasi */
            .w-64.bg-gray-800.text-white.flex-shrink-0.h-screen.overflow-y-auto,
            /* Sidebar */
            .flex-grow.flex.flex-col,
            /* Area konten utama di samping sidebar */
            header.bg-white.dark\:bg-gray-800.shadow,
            /* Header halaman */
            main.flex-grow.p-6,
            /* Tag main yang membungkus konten */
            .no-print,
            /* Tombol cetak dan kembali */
            /* Tambahan selektor untuk elemen yang mungkin masih terlihat */
            .max-w-7xl.mx-auto.py-6.px-4.sm\:px-6.lg\:px-8

            /* Inner div dari header jika masih ada */
                {
                display: none !important;
                width: 0 !important;
                height: 0 !important;
                overflow: hidden !important;
                padding: 0 !important;
                margin: 0 !important;
            }

            /* Atur ulang elemen-elemen layout utama agar tidak mengganggu tata letak cetak */
            .py-6,
            .max-w-7xl,
            .sm\:px-6,
            .lg\:px-8,
            .bg-white,
            .shadow-sm,
            .sm\:rounded-lg,
            .p-6 {
                padding: 0 !important;
                margin: 0 !important;
                max-width: none !important;
                box-shadow: none !important;
                border-radius: 0 !important;
                background-color: transparent !important;
                border: none !important;
            }

            /* Penyesuaian teks dan tabel untuk tampilan struk */
            h3 {
                font-size: 1.2em !important;
                margin-bottom: 0.5em !important;
            }

            p,
            span,
            table {
                font-size: 0.9em !important;
            }

            table {
                width: 100%;
                border-collapse: collapse;
            }

            table th,
            table td {
                padding: 2px 0;
                border-bottom: none;
            }

            table thead {
                border-bottom: 1px dashed #000;
            }

            table tbody {
                border-bottom: 1px dashed #000;
            }

            .flex.justify-between {
                display: flex;
                justify-content: space-between;
                width: 100%;
            }

            .text-right {
                text-align: right !important;
            }

            .text-center {
                text-align: center !important;
            }

            .text-left {
                text-align: left !important;
            }

            hr {
                border-top: 1px dashed #000;
                margin: 5px 0;
            }
        }
    </style>
    @endpush
</x-app-layout>