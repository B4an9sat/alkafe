<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Detail Produk: ') . $product->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            @if ($product->image)
                                <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="w-full h-auto object-cover rounded-lg shadow-md">
                            @else
                                <div class="w-full h-64 bg-gray-200 rounded-lg flex items-center justify-center text-gray-500 text-lg">Tidak Ada Gambar</div>
                            @endif
                        </div>
                        <div>
                            <h3 class="text-lg font-bold mb-2">{{ $product->name }}</h3>
                            <p class="text-gray-600 mb-2"><strong>SKU:</strong> {{ $product->sku ?? '-' }}</p>
                            <p class="text-gray-600 mb-2"><strong>Kategori:</strong> {{ $product->category->name ?? 'Uncategorized' }}</p>
                            <p class="text-gray-600 mb-2"><strong>Harga:</strong> Rp{{ number_format($product->price, 0, ',', '.') }}</p>
                            <p class="text-gray-600 mb-2"><strong>Stok:</strong> {{ $product->stock }}</p>
                            <p class="text-gray-600 mb-4"><strong>Deskripsi:</strong> {{ $product->description ?? '-' }}</p>

                            <div class="mt-4">
                                <a href="{{ route('products.edit', $product->id) }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-500 focus:bg-green-500 active:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150 mr-2">
                                    Edit Produk
                                </a>
                                <form action="{{ route('products.destroy', $product->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus produk ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 focus:bg-red-500 active:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                        Hapus Produk
                                    </button>
                                </form>
                            </div>
                            <div class="mt-4">
                                <a href="{{ route('products.index') }}" class="inline-flex items-center text-indigo-600 hover:text-indigo-900">
                                    &larr; Kembali ke Daftar Produk
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>