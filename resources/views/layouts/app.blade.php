<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Tambahkan script yang di-push dari view anak di sini --}}
    @stack('scripts')
</head>

<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100 dark:bg-gray-900 flex"> {{-- Menggunakan flexbox untuk layout sidebar dan konten --}}

        {{-- Sidebar --}}
        <div class="w-64 bg-gray-800 text-white flex-shrink-0 h-screen overflow-y-auto"> {{-- Lebar tetap, tinggi penuh, bisa di-scroll --}}
            @include('layouts.navigation') {{-- Menginclude file navigasi yang akan menjadi sidebar Anda --}}
        </div>

        {{-- Area Konten Utama --}}
        <div class="flex-grow flex flex-col">
            {{-- Ini adalah tempat Anda bisa meletakkan topbar kecil jika diperlukan (misalnya untuk user profile/logout di pojok kanan atas) --}}
            {{-- Jika tidak ada topbar, bagian ini bisa dikosongkan atau dihilangkan jika semua di sidebar --}}

            @if (isset($header))
            <header class="bg-white dark:bg-gray-800 shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
            @endif

            <main class="flex-grow p-6"> {{-- Padding untuk konten utama --}}
                {{ $slot }}
            </main>
        </div>
    </div>
</body>

</html>
