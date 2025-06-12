{{-- Logo dan Nama Aplikasi --}}
<div class="flex items-center justify-center p-4 border-b border-gray-700">
    <a href="{{ route('dashboard') }}" class="flex items-center space-x-2">
        {{-- Hapus atau komentari baris ini jika Anda tidak ingin menggunakan logo Laravel default --}}
        {{-- <x-application-logo class="block h-9 w-auto fill-current text-gray-200" /> --}}

        {{-- Ganti teks "Laravel" dengan "ALKAFE" --}}
        <span class="text-white text-xl font-semibold">{{ __('ALKAFE') }}</span>
        {{-- Atau jika Anda ingin menggunakan config: --}}
        {{-- <span class="text-white text-xl font-semibold">{{ config('app.name', 'ALKAFE') }}</span> --}}
    </a>
</div>

{{-- User Info (Desktop Only) --}}
<div class="p-4 border-b border-gray-700 hidden md:block">
    <div class="font-medium text-base text-gray-200">{{ Auth::user()->name }}</div>
    <div class="font-medium text-sm text-gray-400">{{ Auth::user()->email }}</div>
</div>

{{-- Navigation Links --}}
<div class="py-4 space-y-1">
    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
        {{ __('Dashboard') }}
    </x-nav-link>

    {{-- Link untuk POS --}}
    <x-nav-link :href="route('pos.index')" :active="request()->routeIs('pos.index')">
        {{ __('Point of Sale (POS)') }}
    </x-nav-link>

    {{-- Link untuk Riwayat Transaksi --}}
    <x-nav-link :href="route('transactions.index')" :active="request()->routeIs('transactions.index') || request()->routeIs('kasir.transaksi.detail')">
        {{ __('Riwayat Transaksi') }}
    </x-nav-link>

    {{-- Link untuk Laporan Penjualan --}}
    <x-nav-link :href="route('sales.report')" :active="request()->routeIs('sales.report')">
        {{ __('Laporan Penjualan') }}
    </x-nav-link>

    {{-- Navigation untuk Admin dan Manager (kondisional) --}}
    @if (Auth::user()->isAdmin() || Auth::user()->isManager())
    <x-nav-link :href="route('products.index')" :active="request()->routeIs('products.*')">
        {{ __('Manajemen Produk') }}
    </x-nav-link>
    <x-nav-link :href="route('categories.index')" :active="request()->routeIs('categories.*')">
        {{ __('Manajemen Kategori') }}
    </x-nav-link>
    @endif

    {{-- Navigation untuk Admin saja (kondisional) --}}
    @if (Auth::user()->isAdmin())
    <x-nav-link :href="route('users.index')" :active="request()->routeIs('users.*')">
        {{ __('Manajemen Pengguna') }}
    </x-nav-link>
    @endif
</div>

{{-- Profile and Logout Links (Desktop Only) --}}
<div class="p-4 border-t border-gray-700 mt-auto hidden md:block">
    <x-nav-link :href="route('profile.edit')" class="text-gray-300 hover:text-white">
        {{ __('Profil') }}
    </x-nav-link>
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <x-nav-link :href="route('logout')"
            onclick="event.preventDefault(); this.closest('form').submit();"
            class="text-red-300 hover:text-red-100">
            {{ __('Log Out') }}
        </x-nav-link>
    </form>
</div>