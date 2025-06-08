<?php

namespace App\Http\Controllers;

use App\Models\Category; // Import model Category
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log; // Untuk logging
use Illuminate\Support\Facades\Auth; // Untuk Auth::user()

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     * Menampilkan daftar semua kategori.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        /** @var \App\Models\User $authUser */ // DocBlock untuk membantu IDE
        $authUser = Auth::user();

        // Hanya admin atau manager yang bisa mengakses manajemen kategori
        if (!$authUser->isAdmin() && !$authUser->isManager()) {
            abort(403, 'Akses Ditolak. Anda tidak memiliki izin untuk melihat manajemen kategori.');
        }

        $categories = Category::latest()->paginate(10); // Ambil kategori terbaru, paginasi 10 per halaman
        Log::info('Accessing Category Management page.');
        return view('categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     * Menampilkan form untuk membuat kategori baru.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        /** @var \App\Models\User $authUser */ // DocBlock untuk membantu IDE
        $authUser = Auth::user();

        if (!$authUser->isAdmin() && !$authUser->isManager()) {
            abort(403, 'Akses Ditolak. Anda tidak memiliki izin untuk menambahkan kategori.');
        }
        return view('categories.create');
    }

    /**
     * Store a newly created resource in storage.
     * Menyimpan kategori baru ke database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        /** @var \App\Models\User $authUser */ // DocBlock untuk membantu IDE
        $authUser = Auth::user();

        if (!$authUser->isAdmin() && !$authUser->isManager()) {
            abort(403, 'Akses Ditolak. Anda tidak memiliki izin untuk menambahkan kategori.');
        }

        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
        ]);

        Category::create($request->all());

        Log::info('New category created: ' . $request->name);
        return redirect()->route('categories.index')
            ->with('success', 'Kategori berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     * Menampilkan detail kategori tertentu (opsional).
     * Umumnya tidak digunakan untuk manajemen kategori sederhana.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\View\View
     */
    public function show(Category $category)
    {
        /** @var \App\Models\User $authUser */ // DocBlock untuk membantu IDE
        $authUser = Auth::user();

        if (!$authUser->isAdmin() && !$authUser->isManager()) {
            abort(403, 'Akses Ditolak. Anda tidak memiliki izin untuk melihat detail kategori.');
        }
        return view('categories.show', compact('category'));
    }

    /**
     * Show the form for editing the specified resource.
     * Menampilkan form untuk mengedit kategori yang sudah ada.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\View\View
     */
    public function edit(Category $category)
    {
        /** @var \App\Models\User $authUser */ // DocBlock untuk membantu IDE
        $authUser = Auth::user();

        if (!$authUser->isAdmin() && !$authUser->isManager()) {
            abort(403, 'Akses Ditolak. Anda tidak memiliki izin untuk mengedit kategori.');
        }
        return view('categories.edit', compact('category'));
    }

    /**
     * Update the specified resource in storage.
     * Memperbarui kategori yang sudah ada di database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Category $category)
    {
        /** @var \App\Models\User $authUser */ // DocBlock untuk membantu IDE
        $authUser = Auth::user();

        if (!$authUser->isAdmin() && !$authUser->isManager()) {
            abort(403, 'Akses Ditolak. Anda tidak memiliki izin untuk memperbarui kategori.');
        }

        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
        ]);

        $category->update($request->all());

        Log::info('Category updated: ' . $category->name);
        return redirect()->route('categories.index')
            ->with('success', 'Kategori berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     * Menghapus kategori dari database.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Category $category)
    {
        /** @var \App\Models\User $authUser */ // DocBlock untuk membantu IDE
        $authUser = Auth::user();

        if (!$authUser->isAdmin() && !$authUser->isManager()) {
            abort(403, 'Akses Ditolak. Anda tidak memiliki izin untuk menghapus kategori.');
        }

        // TODO: Pertimbangkan untuk menambahkan logika untuk mencegah penghapusan kategori jika ada produk yang terkait dengannya.
        // Untuk saat ini, kita anggap kategori bisa langsung dihapus.

        $category->delete();
        Log::info('Category deleted: ' . $category->name);
        return redirect()->route('categories.index')
            ->with('success', 'Kategori berhasil dihapus!');
    }
}
