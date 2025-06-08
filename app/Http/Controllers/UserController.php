<?php

namespace App\Http\Controllers;

use App\Models\User; // Import model User
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash; // Untuk hashing password
use Illuminate\Validation\Rule; // Untuk validasi unique kecuali diri sendiri
use Illuminate\Support\Facades\Log; // Untuk logging
use Illuminate\Support\Facades\Auth; // Tambahkan import untuk Auth facade jika menggunakan Auth::user()

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     * Tampilkan daftar semua pengguna.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        /** @var \App\Models\User $authUser */ // DocBlock untuk membantu IDE
        $authUser = Auth::user(); // Menggunakan Auth::user() secara eksplisit

        // Hanya admin yang bisa mengakses manajemen user
        if (!$authUser->isAdmin()) {
            abort(403, 'Akses Ditolak. Anda tidak memiliki izin untuk melihat manajemen pengguna.');
        }

        $users = User::paginate(10); // Paginate untuk performa
        Log::info('Accessing User Management page.');
        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     * Tampilkan form untuk membuat pengguna baru.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        /** @var \App\Models\User $authUser */ // DocBlock untuk membantu IDE
        $authUser = Auth::user(); // Menggunakan Auth::user() secara eksplisit

        if (!$authUser->isAdmin()) {
            abort(403, 'Akses Ditolak. Anda tidak memiliki izin untuk menambahkan pengguna.');
        }
        $roles = ['admin', 'manager', 'kasir']; // Daftar peran yang tersedia
        return view('users.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     * Simpan pengguna baru ke database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        /** @var \App\Models\User $authUser */ // DocBlock untuk membantu IDE
        $authUser = Auth::user(); // Menggunakan Auth::user() secara eksplisit

        if (!$authUser->isAdmin()) {
            abort(403, 'Akses Ditolak. Anda tidak memiliki izin untuk menambahkan pengguna.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role' => ['required', 'string', Rule::in(['admin', 'manager', 'kasir'])],
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        Log::info('New user created: ' . $request->email . ' with role ' . $request->role);
        return redirect()->route('users.index')
            ->with('success', 'Pengguna berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     * Tampilkan detail pengguna tertentu (opsional, jarang digunakan untuk manajemen user).
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function show(User $user)
    {
        /** @var \App\Models\User $authUser */ // DocBlock untuk membantu IDE
        $authUser = Auth::user(); // Menggunakan Auth::user() secara eksplisit

        if (!$authUser->isAdmin()) {
            abort(403, 'Akses Ditolak. Anda tidak memiliki izin untuk melihat detail pengguna.');
        }
        return view('users.show', compact('user')); // Anda bisa membuat view show.blade.php jika perlu
    }

    /**
     * Show the form for editing the specified resource.
     * Tampilkan form untuk mengedit pengguna yang sudah ada.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\View\View
     */
    public function edit(User $user)
    {
        /** @var \App\Models\User $authUser */ // DocBlock untuk membantu IDE
        $authUser = Auth::user(); // Menggunakan Auth::user() secara eksplisit

        if (!$authUser->isAdmin()) {
            abort(403, 'Akses Ditolak. Anda tidak memiliki izin untuk mengedit pengguna.');
        }
        $roles = ['admin', 'manager', 'kasir'];
        return view('users.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     * Perbarui pengguna yang sudah ada di database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, User $user)
    {
        /** @var \App\Models\User $authUser */ // DocBlock untuk membantu IDE
        $authUser = Auth::user(); // Menggunakan Auth::user() secara eksplisit

        if (!$authUser->isAdmin()) {
            abort(403, 'Akses Ditolak. Anda tidak memiliki izin untuk memperbarui pengguna.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|string|min:8|confirmed', // Password opsional saat update
            'role' => ['required', 'string', Rule::in(['admin', 'manager', 'kasir'])],
        ]);

        $userData = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
        ];

        if ($request->filled('password')) {
            $userData['password'] = Hash::make($request->password);
        }

        $user->update($userData);

        Log::info('User updated: ' . $user->email . ' with new role ' . $user->role);
        return redirect()->route('users.index')
            ->with('success', 'Pengguna berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     * Hapus pengguna dari database.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(User $user)
    {
        /** @var \App\Models\User $authUser */ // DocBlock untuk membantu IDE
        $authUser = Auth::user(); // Menggunakan Auth::user() secara eksplisit

        if (!$authUser->isAdmin()) {
            abort(403, 'Akses Ditolak. Anda tidak memiliki izin untuk menghapus pengguna.');
        }

        // Jangan izinkan admin menghapus dirinya sendiri
        if ($authUser->id === $user->id) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $user->delete();
        Log::info('User deleted: ' . $user->email);
        return redirect()->route('users.index')
            ->with('success', 'Pengguna berhasil dihapus!');
    }
}
