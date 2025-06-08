<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Belanja #{{ $transaction->invoice_number }}</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f3f4f6;
            /* bg-gray-100 */
            color: #1f2937;
            /* text-gray-900 */
        }

        .receipt-container {
            max-width: 400px;
            /* Lebar maksimal struk */
            margin: 20px auto;
            background-color: #ffffff;
            /* bg-white */
            border-radius: 8px;
            /* rounded-lg */
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            /* shadow-lg */
            padding: 20px;
        }

        @media print {
            body {
                background-color: #fff;
            }

            .receipt-container {
                box-shadow: none;
                margin: 0;
                max-width: none;
                width: 100%;
                border-radius: 0;
                padding: 0;
            }

            .no-print {
                display: none !important;
            }
        }

        /* Dark mode compatibility for the receipt if it's viewed without x-app-layout */
        @media (prefers-color-scheme: dark) {
            body {
                background-color: #111827;
                /* dark:bg-gray-900 */
                color: #e5e7eb;
                /* dark:text-gray-100 */
            }

            .receipt-container {
                background-color: #1f2937;
                /* dark:bg-gray-800 */
            }

            .receipt-header,
            .receipt-footer {
                color: #e5e7eb;
                /* dark:text-gray-100 */
            }

            .receipt-item-name,
            .receipt-total-label,
            .receipt-amount {
                color: #e5e7eb;
                /* dark:text-gray-100 */
            }

            .receipt-item-details,
            .receipt-info-label,
            .receipt-info-value {
                color: #9ca3af;
                /* dark:text-gray-400 */
            }

            .border-gray-200 {
                border-color: #374151;
                /* dark:border-gray-700 */
            }
        }
    </style>
</head>

<body>
    <div class="receipt-container">
        <div class="text-center mb-6">
            <h1 class="text-2xl font-bold mb-1 receipt-header">{{ config('app.name', 'POS Cafe') }}</h1>
            <p class="text-sm text-gray-600 receipt-info-value">Alamat Cafe Anda</p>
            <p class="text-sm text-gray-600 receipt-info-value">Telp: (021) 123-4567</p>
        </div>

        <div class="border-b border-gray-200 pb-4 mb-4 dark:border-gray-700">
            <p class="text-sm receipt-info-label"><strong>No. Invoice:</strong> <span class="receipt-info-value">{{ $transaction->invoice_number }}</span></p>
            <p class="text-sm receipt-info-label"><strong>Tanggal:</strong> <span class="receipt-info-value">{{ $transaction->created_at->format('d M Y H:i:s') }}</span></p>
            <p class="text-sm receipt-info-label"><strong>Kasir:</strong> <span class="receipt-info-value">{{ $transaction->user->name ?? 'N/A' }}</span></p>
            <p class="text-sm receipt-info-label"><strong>Pelanggan:</strong> <span class="receipt-info-value">{{ $transaction->customer->name ?? 'Umum' }}</span></p>
        </div>

        <div class="mb-4">
            @foreach ($transaction->transactionItems as $item)
            <div class="flex justify-between text-sm mb-1">
                <span class="receipt-item-name">{{ $item->product_name ?? ($item->product->name ?? 'Produk Tidak Ditemukan') }} ({{ $item->quantity }}x)</span>
                <span class="receipt-amount">Rp{{ number_format($item->subtotal, 0, ',', '.') }}</span>
            </div>
            <p class="text-xs text-gray-500 ml-4 receipt-item-details">@ Rp{{ number_format($item->price, 0, ',', '.') }}</p>
            @endforeach
        </div>

        <div class="border-t border-gray-200 pt-4 mt-4 dark:border-gray-700">
            <div class="flex justify-between text-md font-semibold mb-2">
                <span class="receipt-total-label">TOTAL</span>
                <span class="receipt-amount">Rp{{ number_format($transaction->total_amount, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between text-sm text-gray-700 dark:text-gray-300">
                <span class="receipt-total-label">Bayar</span>
                <span class="receipt-amount">Rp{{ number_format($transaction->payment_amount, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between text-sm font-semibold text-green-600 dark:text-green-400">
                <span class="receipt-total-label">Kembalian</span>
                <span class="receipt-amount">Rp{{ number_format($transaction->change_amount, 0, ',', '.') }}</span>
            </div>
        </div>

        <div class="text-center mt-6 text-sm text-gray-700 receipt-footer dark:text-gray-300">
            <p>Terima kasih atas kunjungan Anda!</p>
            <p>Silakan datang kembali.</p>
        </div>

        <div class="mt-6 text-center no-print">
            <button onclick="window.print()" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-full focus:outline-none focus:shadow-outline transition duration-150 ease-in-out">
                Cetak Struk
            </button>
            <a href="{{ route('pos.index') }}" class="ml-4 bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded-full focus:outline-none focus:shadow-outline transition duration-150 ease-in-out dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600">
                Kembali ke POS
            </a>
        </div>
    </div>
</body>

</html>