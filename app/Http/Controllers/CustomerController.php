<?php

namespace App\Http\Controllers;

use App\Models\Customer; // Import model Customer
use Illuminate\Http\Request;
use Illuminate\Validation\Rule; // Untuk validasi unique kecuali diri sendiri
use Illuminate\Support\Facades\Log; // Untuk logging

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     * Tampilkan daftar semua pelanggan.
     */
    public function index()
    {
        // Akses bisa dibatasi untuk Admin/Manager/Kasir sesuai kebutuhan.
        // Untuk saat ini, kita asumsikan bisa diakses oleh peran yang relevan.
        $customers = Customer::orderBy('name')->paginate(10); // Paginate untuk performa
        Log::info('Accessing Customer Management page.');
        return view('customers.index', compact('customers'));
    }

    /**
     * Show the form for creating a new resource.
     * Tampilkan form untuk membuat pelanggan baru.
     */
    public function create()
    {
        return view('customers.create');
    }

    /**
     * Store a newly created resource in storage.
     * Simpan pelanggan baru ke database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|string|email|max:255|unique:customers,email',
            'phone' => 'nullable|string|max:20|unique:customers,phone',
            'address' => 'nullable|string|max:500',
        ]);

        Customer::create($request->all());

        Log::info('New customer created: ' . $request->name);
        return redirect()->route('customers.index')
            ->with('success', 'Pelanggan berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     * Tampilkan detail pelanggan tertentu (opsional).
     */
    public function show(Customer $customer)
    {
        return view('customers.show', compact('customer'));
    }

    /**
     * Show the form for editing the specified resource.
     * Tampilkan form untuk mengedit pelanggan yang sudah ada.
     */
    public function edit(Customer $customer)
    {
        return view('customers.edit', compact('customer'));
    }

    /**
     * Update the specified resource in storage.
     * Perbarui pelanggan yang sudah ada di database.
     */
    public function update(Request $request, Customer $customer)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['nullable', 'string', 'email', 'max:255', Rule::unique('customers')->ignore($customer->id)],
            'phone' => ['nullable', 'string', 'max:20', Rule::unique('customers')->ignore($customer->id)],
            'address' => 'nullable|string|max:500',
        ]);

        $customer->update($request->all());

        Log::info('Customer updated: ' . $customer->name . ' (ID: ' . $customer->id . ')');
        return redirect()->route('customers.index')
            ->with('success', 'Pelanggan berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     * Hapus pelanggan dari database.
     */
    public function destroy(Customer $customer)
    {
        // Pertimbangkan penanganan transaksi terkait pelanggan ini jika diperlukan.
        // Jika Anda ingin menghapus transaksi terkait, tambahkan onDelete('cascade') pada foreign key customer_id di tabel transactions.
        $customer->delete();
        Log::info('Customer deleted: ' . $customer->name . ' (ID: ' . $customer->id . ')');
        return redirect()->route('customers.index')
            ->with('success', 'Pelanggan berhasil dihapus!');
    }
}
