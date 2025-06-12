<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Transaksi #{{ $transaction->invoice_number }}</title>
    {{-- Tailwind CSS untuk styling dasar --}}
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'monospace';
            /* Font yang lebih cocok untuk struk */
            color: #000;
            /* Pastikan teks hitam untuk cetak */
            background-color: #fff;
            /* Pastikan latar belakang putih */
            -webkit-print-color-adjust: exact;
            /* Penting untuk latar belakang dan warna */
            print-color-adjust: exact;
        }

        .receipt-container {
            width: 80mm;
            /* Lebar standar untuk printer termal POS */
            margin: 0 auto;
            /* Pusatkan di halaman cetak */
            padding: 10px;
            /* Padding minimal */
            box-shadow: none;
            border-radius: 0;
            background-color: #fff;
            color: #000;
            font-size: 10pt;
            overflow: hidden;
            /* Pastikan tidak ada overflow atau potongan konten */
            height: auto;
            /* Tinggi menyesuaikan konten */
        }

        /* Kelas-kelas dasar Tailwind CSS (diulang di sini agar mandiri) */
        .text-center {
            text-align: center;
        }

        .text-left {
            text-align: left;
        }

        .text-right {
            text-align: right;
        }

        .flex {
            display: flex;
        }

        .justify-between {
            justify-content: space-between;
        }

        .font-bold {
            font-weight: bold;
        }

        .font-semibold {
            font-weight: 600;
        }

        .text-sm {
            font-size: 0.875rem;
            /* 14px */
        }

        .text-xl {
            font-size: 1.25rem;
            /* 20px */
        }

        .text-lg {
            font-size: 1.125rem;
            /* 18px */
        }

        .mb-4 {
            margin-bottom: 1rem;
        }

        .mb-2 {
            margin-bottom: 0.5rem;
        }

        .mb-1 {
            margin-bottom: 0.25rem;
        }

        .mt-4 {
            margin-top: 1rem;
        }

        .my-3 {
            margin-top: 0.75rem;
            margin-bottom: 0.75rem;
        }

        hr {
            border: none;
            border-top: 1px dashed #000;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table th,
        table td {
            padding: 2px 0;
            /* Padding lebih kecil untuk tabel */
            vertical-align: top;
            border-bottom: none;
            /* Hapus border di tabel */
        }

        table thead {
            border-bottom: 1px dashed #000;
            /* Garis bawah untuk header tabel */
        }

        table tbody {
            border-bottom: 1px dashed #000;
            /* Garis bawah untuk isi tabel */
        }

        /* Gaya khusus untuk cetak */
        @page {
            size: auto;
            /* auto is the initial value */
            margin: 0mm;
            /* Atur margin ke 0 untuk memaksimalkan area cetak */
        }
    </style>
</head>

<body>
    <div class="receipt-container">
        {{-- Header Toko --}}
        <div class="text-center mb-4">
            <h3 class="text-xl font-bold mb-1">ALKAFE</h3> {{-- Tetap ALKAFE --}}
            <p class="text-sm">Jalan Delima No. 003, Kota Kediri</p> {{-- Tetap alamat asli --}}
            <p class="text-sm">Telp: 0856-4586-9475</p> {{-- Tetap telepon asli --}}
        </div>

        <hr class="my-3">

        {{-- Informasi Transaksi --}}
        <div class="mb-4 text-sm">
            <div class="flex justify-between mb-1">
                <span class="font-semibold">Tanggal:</span>
                <span>{{ $transaction->created_at->format('d/m/Y H:i') }}</span>
            </div>
            <div class="flex justify-between mb-1">
                <span class="font-semibold">Kasir:</span>
                <span>{{ $transaction->user->name ?? 'N/A' }}</span>
            </div>
        </div>

        <hr class="my-3">

        {{-- Tabel Item Transaksi --}}
        <div class="mb-4">
            <table class="w-full text-sm">
                <thead>
                    <tr>
                        <th class="py-1 text-left" style="width: 50%;">Produk</th>
                        <th class="py-1 text-center" style="width: 10%;">Qty</th>
                        <th class="py-1 text-right" style="width: 20%;">Harga</th>
                        <th class="py-1 text-right" style="width: 20%;">Subtotal</th>
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

        {{-- Ringkasan Pembayaran --}}
        <div class="text-sm">
            <div class="flex justify-between mb-1">
                <span class="font-semibold">Total Belanja:</span>
                <span>Rp{{ number_format($transaction->total_amount, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between mb-1">
                <span class="font-semibold">Diskon:</span>
                <span>Rp0</span> {{-- Asumsi diskon 0, sesuaikan jika ada logika diskon --}}
            </div>
            <div class="flex justify-between mb-2">
                <span class="font-bold">Total Akhir:</span>
                <span class="font-bold">Rp{{ number_format($transaction->total_amount, 0, ',', '.') }}</span> {{-- Sesuaikan jika ada diskon --}}
            </div>
            <div class="flex justify-between mb-1">
                <span class="font-semibold">Tunai:</span>
                <span>Rp{{ number_format($transaction->payment_amount, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between font-bold text-lg text-green-600">
                <span class="font-semibold">Kembalian:</span>
                <span>Rp{{ number_format($transaction->change_amount, 0, ',', '.') }}</span>
            </div>
        </div>

        <hr class="my-3">

        {{-- Informasi Tambahan --}}
        <div class="text-center mt-4 text-sm">
            <p class="mb-1">Metode Pembayaran: Cash</p> {{-- Asumsi metode pembayaran cash --}}
            <p class="mb-1">Terima kasih atas kunjungan Anda!</p>
            <p>Produk yang sudah dibeli tidak dapat dikembalikan.</p>
        </div>

        <hr class="my-3">

        {{-- No. Faktur di bagian bawah --}}
        <div class="text-sm text-right">
            <p class="font-semibold">No. Faktur: {{ $transaction->invoice_number }}</p>
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