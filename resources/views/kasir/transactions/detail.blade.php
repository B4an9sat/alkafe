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
                    <div id="receipt-content">
                        <div class="text-center mb-4">
                            <h3 class="text-xl font-bold text-gray-800 dark:text-gray-100 mb-1">Nama Toko Anda</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Jalan Raya No. 123, Kota Anda</p>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Telp: 0812-3456-7890</p>
                            <hr class="my-3 border-gray-300 dark:border-gray-600">
                            <p class="text-md font-semibold text-gray-800 dark:text-gray-100">STRUK PENJUALAN</p>
                        </div>

                        <div class="mb-4 text-sm text-gray-700 dark:text-gray-300">
                            <div class="flex justify-between">
                                <span class="font-semibold">Invoice:</span>
                                <span>{{ $transaction->invoice_number }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="font-semibold">Tanggal:</span>
                                <span>{{ $transaction->created_at->format('d/m/Y H:i') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="font-semibold">Kasir:</span>
                                <span>{{ $transaction->user->name ?? 'N/A' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="font-semibold">Pelanggan:</span>
                                <span>{{ $transaction->customer->name ?? 'Umum' }}</span>
                            </div>
                        </div>

                        <hr class="my-3 border-gray-300 dark:border-gray-600">

                        <div class="mb-4">
                            <table class="min-w-full text-sm text-gray-700 dark:text-gray-300">
                                <thead>
                                    <tr>
                                        <th class="py-1 text-left">Produk</th>
                                        <th class="py-1 text-center">Qty</th>
                                        <th class="py-1 text-right">Harga</th>
                                        <th class="py-1 text-right">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($transaction->transactionItems as $item)
                                        <tr>
                                            <td class="py-1 text-left">{{ $item->product_name ?? ($item->product->name ?? 'Produk Tidak Ditemukan') }}</td>
                                            <td class="py-1 text-center">{{ $item->quantity }}</td>
                                            <td class="py-1 text-right">{{ number_format($item->price, 0, ',', '.') }}</td>
                                            <td class="py-1 text-right">{{ number_format($item->subtotal, 0, ',', '.') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <hr class="my-3 border-gray-300 dark:border-gray-600">

                        <div class="text-sm text-gray-800 dark:text-gray-100">
                            <div class="flex justify-between mb-1">
                                <span class="font-semibold">Total Belanja:</span>
                                <span class="font-bold text-lg">Rp{{ number_format($transaction->total_amount, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between mb-1">
                                <span class="font-semibold">Jumlah Bayar:</span>
                                <span>Rp{{ number_format($transaction->payment_amount, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between font-bold text-lg text-green-600 dark:text-green-400">
                                <span class="font-semibold">Kembalian:</span>
                                <span>Rp{{ number_format($transaction->change_amount, 0, ',', '.') }}</span>
                            </div>
                        </div>

                        <hr class="my-3 border-gray-300 dark:border-gray-600">

                        <div class="text-center mt-4 text-sm text-gray-600 dark:text-gray-400">
                            <p>Terima kasih atas kunjungan Anda!</p>
                            <p>Barang yang sudah dibeli tidak dapat dikembalikan.</p>
                        </div>
                    </div>

                    {{-- Tombol Cetak dan Kembali (Tidak akan tercetak) --}}
                    <div class="mt-6 text-right no-print">
                        <button onclick="window.print()" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150 mr-2">
                            {{ __('Cetak Struk') }}
                        </button>
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
            body {
                margin: 0;
                padding: 0;
                font-family: 'monospace'; /* Font yang lebih cocok untuk struk */
                color: #000; /* Pastikan teks hitam untuk cetak */
                background-color: #fff; /* Pastikan latar belakang putih */
            }

            /* Menyembunyikan seluruh body secara default, lalu menampilkan hanya konten struk */
            body {
                visibility: hidden; /* Sembunyikan seluruh body */
            }
            #receipt-content {
                visibility: visible; /* Jadikan konten struk terlihat */
                display: block !important;
                position: absolute; /* Posisikan absolut untuk menumpuk di atas konten yang tersembunyi */
                top: 0;
                left: 50%;
                transform: translateX(-50%);
                width: 80mm; /* Lebar standar untuk printer termal POS */
                margin: 0;
                padding: 10px;
                box-shadow: none;
                border-radius: 0;
                background-color: #fff;
                z-index: 9999;
            }

            /* Sembunyikan elemen utama layout Breeze/Jetstream */
            /* Ini akan menyembunyikan sidebar, header, dan konten utama non-struk */
            .min-h-screen, /* Pembungkus utama layout */
            .antialiased, /* Kelas body utama */
            .flex.min-h-screen /* Kelas untuk layout sidebar + konten */
            {
                display: none !important;
                width: 0 !important;
                height: 0 !important;
                overflow: hidden !important;
            }

            /* Selektor spesifik untuk layout default Breeze/Jetstream */
            .bg-gray-100, /* Background utama */
            .dark\:bg-gray-900, /* Background dark mode utama */
            .flex-col.md\:flex-row.md\:min-h-screen, /* Struktur layout utama */
            .md\:w-64.bg-gray-900, /* Sidebar */
            .md\:block, /* Untuk elemen yang hanya tampil di desktop, termasuk user info di sidebar */
            .md\:flex-grow.p-4, /* Konten utama */
            .max-w-7xl.mx-auto.sm\:px-6.lg\:px-8, /* Container padding */
            .bg-white.dark\:bg-gray-800.overflow-hidden.shadow-sm.sm\:rounded-lg, /* Card utama */
            .p-6 /* Padding di dalam card */
            {
                display: none !important;
            }

            /* Sembunyikan elemen navigasi dan header yang spesifik */
            nav, header, aside, footer {
                display: none !important;
            }
            .no-print {
                display: none !important;
            }

            /* Atur ulang ukuran font agar sesuai untuk struk */
            h3 {
                font-size: 1.2em !important; /* Ukuran yang lebih besar untuk judul */
            }
            p, span, table {
                font-size: 0.8em !important; /* Ukuran font standar untuk isi struk */
            }
            table {
                width: 100%;
                border-collapse: collapse;
            }
            table th, table td {
                padding: 2px 0; /* Padding lebih kecil untuk tabel */
                border-bottom: 0px solid #eee; /* Hapus border di tabel */
            }
            table tbody tr:last-child td {
                border-bottom: none; /* Hapus border untuk baris terakhir */
            }

            .flex.justify-between {
                display: flex;
                justify-content: space-between;
                width: 100%;
            }
            .text-right { text-align: right !important; }
            .text-center { text-align: center !important; }
            .text-left { text-align: left !important; }

            hr {
                border-top: 1px dashed #000; /* Garis putus-putus untuk pemisah */
                margin: 5px 0; /* Margin lebih kecil */
            }
        }
    </style>
    @endpush
</x-app-layout>