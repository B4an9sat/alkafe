<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Transaksi #{{ $transaction->invoice_number }}</title>
    {{-- Tailwind CSS untuk styling dasar --}}
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        /* Gaya umum untuk tampilan di browser sebelum dicetak (jika dibuka langsung) */
        body {
            margin: 0;
            padding: 0;
            font-family: 'monospace'; /* Font yang lebih cocok untuk struk */
            color: #000; /* Pastikan teks hitam */
            background-color: #fff; /* Pastikan latar belakang putih */
            -webkit-print-color-adjust: exact; /* Penting untuk latar belakang dan warna */
            print-color-adjust: exact;
        }

        .receipt-container {
            width: 80mm; /* Lebar standar untuk printer termal POS */
            margin: 0 auto; /* Pusatkan di halaman cetak */
            padding: 10px; /* Padding minimal */
            box-shadow: none;
            border-radius: 0;
            background-color: #fff;
            color: #000;
            font-size: 10pt;
            overflow: hidden; /* Pastikan tidak ada overflow atau potongan konten */
            height: auto; /* Tinggi menyesuaikan konten */
        }

        /* Kelas-kelas dasar Tailwind CSS (diulang di sini agar mandiri) */
        .text-center { text-align: center; }
        .text-left { text-align: left; }
        .text-right { text-align: right; }
        .flex { display: flex; }
        .justify-between { justify-content: space-between; }
        .font-bold { font-weight: bold; }
        .font-semibold { font-weight: 600; }
        .text-sm { font-size: 0.875rem; /* 14px */ }
        .text-xl { font-size: 1.25rem; /* 20px */ }
        .text-lg { font-size: 1.125rem; /* 18px */ }
        .mb-4 { margin-bottom: 1rem; }
        .mb-1 { margin-bottom: 0.25rem; }
        .mt-4 { margin-top: 1rem; }
        .my-3 { margin-top: 0.75rem; margin-bottom: 0.75rem; }

        hr {
            border: none;
            border-top: 1px dashed #000;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }
        table th, table td {
            padding: 2px 0;
            vertical-align: top;
            border-bottom: none; /* Hapus border di tabel */
        }
        table thead {
            border-bottom: 1px dashed #000; /* Garis bawah untuk header tabel */
        }
        table tbody {
            border-bottom: 1px dashed #000; /* Garis bawah untuk isi tabel */
        }

        /* Gaya khusus untuk cetak */
        @page {
            size: auto;  /* auto is the initial value */
            margin: 0mm; /* Atur margin ke 0 untuk memaksimalkan area cetak */
        }
    </style>
</head>
<body>
    <div class="receipt-container">
        <div class="text-center mb-4">
            <h3 class="text-xl font-bold text-gray-800 mb-1">ALKAFE</h3>
            <p class="text-sm text-gray-600">Jalan Delima No. 003, Kota Kediri</p>
            <p class="text-sm text-gray-600">Telp: 0856-4586-9475</p>
            <hr class="my-3">
            <p class="text-md font-semibold text-gray-800">STRUK PENJUALAN</p>
        </div>

        <div class="mb-4 text-sm text-gray-700">
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

        <hr class="my-3">

        <div class="mb-4">
            <table class="min-w-full text-sm text-gray-700">
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

        <hr class="my-3">

        <div class="text-sm text-gray-800">
            <div class="flex justify-between mb-1">
                <span class="font-semibold">Total Belanja:</span>
                <span class="font-bold text-lg">Rp{{ number_format($transaction->total_amount, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between mb-1">
                <span class="font-semibold">Jumlah Bayar:</span>
                <span>Rp{{ number_format($transaction->payment_amount, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between font-bold text-lg text-green-600">
                <span class="font-semibold">Kembalian:</span>
                <span>Rp{{ number_format($transaction->change_amount, 0, ',', '.') }}</span>
            </div>
        </div>

        <hr class="my-3">

        <div class="text-center mt-4 text-sm text-gray-600">
            <p>Terima kasih atas kunjungan Anda!</p>
            <p>Barang yang sudah dibeli tidak dapat dikembalikan.</p>
        </div>
    </div>

    <script>
        // Secara otomatis memicu dialog cetak setelah halaman dimuat
        window.onload = function() {
            window.print();
            // Opsional: Tutup tab setelah mencetak (beberapa browser mungkin memblokir ini)
            // window.onafterprint = function() {
            //     window.close();
            // };
        };
    </script>
</body>
</html>