<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class PosController extends Controller
{
    /**
     * Display the POS interface.
     */
    public function index()
    {
        /** @var \App\Models\User $authUser */
        $authUser = Auth::user();

        // Hanya admin, manager, atau kasir yang bisa mengakses POS
        if (!$authUser->isAdmin() && !$authUser->isManager() && !$authUser->isCashier()) {
            abort(403, 'Akses Ditolak. Anda tidak memiliki izin untuk mengakses Point of Sale.');
        }

        $categories = Category::all();
        $customers = Customer::orderBy('name')->get();

        return view('kasir.pos', compact('categories', 'customers'));
    }

    /**
     * Proses transaksi POS.
     */
    public function processTransaction(Request $request)
    {
        /** @var \App\Models\User $authUser */
        $authUser = Auth::user();

        if (!$authUser->isAdmin() && !$authUser->isManager() && !$authUser->isCashier()) {
            return response()->json(['message' => 'Akses Ditolak. Anda tidak memiliki izin untuk memproses transaksi.'], 403);
        }

        Log::info('POS Transaction Request Received (Raw):', $request->all());

        $cartString = $request->input('cart');
        $cartData = $cartString ? json_decode($cartString, true) : [];

        if (json_last_error() !== JSON_ERROR_NONE && $cartString !== null && $cartString !== '') {
            Log::error('JSON decoding error for cart:', ['error' => json_last_error_msg(), 'cart_string' => $request->input('cart')]);
            return response()->json(['message' => 'Format data keranjang tidak valid.'], 400);
        }

        $paymentAmount = (float) $request->input('payment_amount');

        $validatedData = [
            'cart' => $cartData,
            'payment_amount' => $paymentAmount,
            'customer_id' => $request->input('customer_id'),
        ];

        $validator = Validator::make($validatedData, [
            'cart' => 'required|array|min:1',
            'cart.*.product_id' => 'required|exists:products,id',
            'cart.*.quantity' => 'required|integer|min:1',
            'payment_amount' => 'required|numeric|min:0',
            'customer_id' => 'nullable|exists:customers,id',
        ]);

        if ($validator->fails()) {
            Log::warning('POS Transaction Validation Failed:', $validator->errors()->toArray());
            return response()->json(['message' => 'Data transaksi tidak valid.', 'errors' => $validator->errors()], 422);
        }

        $cartItems = $validatedData['cart'];
        $paymentAmount = $validatedData['payment_amount'];
        $customerId = $validatedData['customer_id'];

        Log::info('POS Transaction Validation Passed. Cart (Processed):', $cartItems);
        Log::info('Payment Amount (Processed):', ['amount' => $paymentAmount]);
        Log::info('Customer ID (Processed):', ['customer_id' => $customerId]);

        $totalAmount = 0;
        foreach ($cartItems as $item) {
            $product = Product::find($item['product_id']);
            if (!$product) {
                Log::warning('Product not found during stock validation: ' . $item['product_id']);
                return response()->json(['message' => 'Produk dengan ID ' . $item['product_id'] . ' tidak ditemukan.'], 404);
            }

            if ($product->stock < $item['quantity']) {
                Log::warning('Insufficient stock for product ' . $product->name . ': ' . $item['quantity'] . ' requested, ' . $product->stock . ' available.');
                return response()->json(['message' => 'Stok produk ' . $product->name . ' tidak cukup. Stok tersedia: ' . $product->stock], 400);
            }
            $subtotal = $product->price * $item['quantity'];
            $totalAmount += $subtotal;
        }

        if ($paymentAmount < $totalAmount) {
            Log::warning('Insufficient payment amount: ' . $paymentAmount . ' paid, ' . $totalAmount . ' required.');
            return response()->json(['message' => 'Jumlah pembayaran tidak cukup. Total belanja: Rp' . number_format($totalAmount, 0, ',', '.')], 400);
        }
        $changeAmount = $paymentAmount - $totalAmount;

        DB::beginTransaction();
        try {
            $latestTransaction = Transaction::latest()->first();
            $lastInvoiceNumber = $latestTransaction ? $latestTransaction->invoice_number : 'INV-000000';
            $invoiceNumber = $this->generateNextInvoiceNumber($lastInvoiceNumber);

            $transaction = Transaction::create([
                'invoice_number' => $invoiceNumber,
                'user_id' => Auth::id(),
                'customer_id' => $customerId,
                'total_amount' => $totalAmount,
                'payment_amount' => $paymentAmount,
                'change_amount' => $changeAmount,
            ]);

            Log::info('Transaction created:', ['invoice_number' => $invoiceNumber, 'total_amount' => $totalAmount, 'customer_id' => $customerId]);

            foreach ($cartItems as $item) {
                $product = Product::find($item['product_id']);
                $transaction->transactionItems()->create([
                    'product_id' => $item['product_id'],
                    'product_name' => $product->name,
                    'price' => $product->price,
                    'quantity' => $item['quantity'],
                    'subtotal' => $product->price * $item['quantity'],
                ]);

                Product::where('id', $item['product_id'])->decrement('stock', $item['quantity']);
                Log::info('Stock decremented for product ' . $product->name . ' (ID: ' . $item['product_id'] . ') by ' . $item['quantity']);
            }

            DB::commit();
            Log::info('Transaction ' . $invoiceNumber . ' committed successfully.');
            // Mengembalikan respons JSON dengan transaction_id
            return response()->json([
                'message' => 'Transaksi berhasil diproses!',
                'change_amount' => $changeAmount,
                'invoice_number' => $invoiceNumber,
                'transaction_id' => $transaction->id // <-- BARIS INI DITAMBAHKAN
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Transaction failed: ' . $e->getMessage(), ['exception' => $e]);
            // Mengembalikan respons JSON dengan error
            return response()->json(['message' => 'Gagal memproses transaksi: ' . $e->getMessage()], 500);
        }
    }

    protected function generateNextInvoiceNumber($lastInvoiceNumber)
    {
        $prefix = 'INV-';
        $parts = explode('-', $lastInvoiceNumber);
        $lastNumber = (int) end($parts);
        $nextNumber = str_pad($lastNumber + 1, 6, '0', STR_PAD_LEFT);
        return $prefix . $nextNumber;
    }

    /**
     * Menampilkan daftar riwayat transaksi.
     */
    public function transactionHistory(Request $request)
    {
        /** @var \App\Models\User $authUser */
        $authUser = Auth::user();

        if (!$authUser->isAdmin() && !$authUser->isManager() && !$authUser->isCashier()) {
            abort(403, 'Akses Ditolak. Anda tidak memiliki izin untuk melihat riwayat transaksi.');
        }

        $transactions = Transaction::with(['user', 'customer']) // Cukup load user dan customer
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        Log::info('Accessing Transaction History page.');

        return view('kasir.transactions.history', compact('transactions'));
    }

    /**
     * Menampilkan laporan penjualan sederhana dengan filter rentang tanggal.
     */
    public function salesReport(Request $request)
    {
        /** @var \App\Models\User $authUser */
        $authUser = Auth::user();

        if (!$authUser->isAdmin() && !$authUser->isManager() && !$authUser->isCashier()) {
            abort(403, 'Akses Ditolak. Anda tidak memiliki izin untuk melihat laporan penjualan.');
        }

        $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date'))->startOfDay() : Carbon::today()->startOfDay();
        $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date'))->endOfDay() : Carbon::today()->endOfDay();

        if ($startDate->greaterThan($endDate)) {
            return back()->with('error', 'Tanggal mulai tidak boleh lebih besar dari tanggal akhir.');
        }

        $transactionsQuery = Transaction::whereBetween('created_at', [$startDate, $endDate]);

        $totalSalesFiltered = (clone $transactionsQuery)->sum('total_amount');

        $topSellingProducts = TransactionItem::whereHas('transaction', function ($query) use ($startDate, $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        })
            ->select(
                'product_name',
                DB::raw('SUM(quantity) as total_quantity_sold'),
                DB::raw('SUM(subtotal) as total_revenue')
            )
            ->groupBy('product_name')
            ->orderByDesc('total_quantity_sold')
            ->limit(10)
            ->get();

        Log::info('Accessing Sales Report page with date filter.', [
            'start_date' => $startDate->toDateString(),
            'end_date' => $endDate->toDateString(),
            'total_sales' => $totalSalesFiltered
        ]);

        return view('kasir.reports.sales', compact('totalSalesFiltered', 'topSellingProducts', 'startDate', 'endDate'));
    }

    /**
     * Menampilkan detail dari satu transaksi tertentu.
     */
    public function showTransactionDetail(Transaction $transaction)
    {
        /** @var \App\Models\User $authUser */
        $authUser = Auth::user();

        if (!$authUser->isAdmin() && !$authUser->isManager() && !$authUser->isCashier()) {
            abort(403, 'Akses Ditolak. Anda tidak memiliki izin untuk melihat detail transaksi.');
        }

        $transaction->load(['user', 'customer', 'transactionItems.product']);

        Log::info('Accessing Transaction Detail page for Invoice:', ['invoice_number' => $transaction->invoice_number]);

        return view('kasir.transactions.detail', compact('transaction'));
    }

    /**
     * Menampilkan halaman struk transaksi.
     *
     * @param  \App\Models\Transaction  $transaction
     * @return \Illuminate\View\View|\Illuminate\Http\Response
     */
     public function showReceipt(Transaction $transaction)
    {
        /** @var \App\Models\User $authUser */
        $authUser = Auth::user();

        if (!$authUser->isAdmin() && !$authUser->isManager() && !$authUser->isCashier()) {
            abort(403, 'Akses Ditolak. Anda tidak memiliki izin untuk melihat struk transaksi.');
        }

        $transaction->load(['user', 'customer', 'transactionItems.product']);

        Log::info('Accessing Transaction Receipt page for Invoice:', ['invoice_number' => $transaction->invoice_number]);

        return view('kasir.receipt.show', compact('transaction'));
    }

    /**
     * Menampilkan halaman khusus untuk mencetak struk transaksi.
     * (METODE BARU UNTUK PRINT)
     *
     * @param  \App\Models\Transaction  $transaction
     * @return \Illuminate\View\View
     */
    public function printReceipt(Transaction $transaction)
    {
        /** @var \App\Models\User $authUser */
        $authUser = Auth::user();

        // Verifikasi izin akses jika diperlukan
        if (!$authUser->isAdmin() && !$authUser->isManager() && !$authUser->isCashier()) {
            abort(403, 'Akses Ditolak. Anda tidak memiliki izin untuk mencetak struk transaksi.');
        }

        $transaction->load(['user', 'customer', 'transactionItems.product']);

        Log::info('Generating print receipt for Invoice:', ['invoice_number' => $transaction->invoice_number]);

        // Mengembalikan view print.blade.php yang baru dibuat
        return view('kasir.receipt.print', compact('transaction'));
    }
}
