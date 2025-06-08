<x-app-layout>
    {{-- Header Halaman --}}
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Point of Sale (POS)') }}
        </h2>
    </x-slot>

    {{-- Konten Utama Halaman --}}
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Bagian Notifikasi (Success/Error) --}}
            <div id="appMessages" class="mb-4">
                @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-2" role="alert">
                    {{ session('success') }}
                </div>
                @endif
                @if (session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-2" role="alert">
                    {{ session('error') }}
                </div>
                @endif
            </div>

            {{-- Menampilkan pesan error validasi (default Laravel) --}}
            @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            {{-- Struktur Utama Halaman POS (Daftar Produk + Keranjang) --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg flex flex-col md:flex-row h-[calc(100vh-200px)]">

                {{-- Bagian Daftar Produk (Kiri/Atas) --}}
                <div class="md:w-3/5 p-4 overflow-y-auto border-r border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-bold mb-4 text-gray-800 dark:text-gray-200">Daftar Produk</h3>

                    {{-- Search dan Filter Kategori --}}
                    <div class="mb-4 flex space-x-2">
                        <input type="text" id="productSearch" placeholder="Cari produk..."
                            class="flex-grow rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-sm py-2 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200">
                        <select id="categoryFilter"
                            class="rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-sm py-2 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200">
                            <option value="">Semua Kategori</option>
                            @foreach ($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Grid Produk --}}
                    <div id="productList" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                        <p class="text-center text-gray-500 col-span-full py-4">Memuat produk...</p>
                    </div>
                </div>

                {{-- Bagian Keranjang Belanja (Kanan/Bawah) --}}
                <div class="md:w-2/5 p-4 flex flex-col">
                    <h3 class="text-lg font-bold mb-4 text-gray-800 dark:text-gray-200">Keranjang Belanja</h3>

                    {{-- Daftar Item di Keranjang --}}
                    <div id="cartItems" class="flex-grow overflow-y-auto border-b border-gray-200 pb-4 mb-4 dark:border-gray-700">
                        <p id="emptyCartMessage" class="text-gray-500 text-center py-4">Keranjang masih kosong. Klik produk untuk menambahkannya.</p>
                    </div>

                    {{-- Ringkasan Total dan Pembayaran --}}
                    <div class="mb-4">
                        <p class="text-xl font-bold mb-2 text-gray-800 dark:text-gray-200">Total: <span id="cartTotal">Rp0</span></p>

                        <div class="mt-4">
                            <x-input-label for="payment_amount" :value="__('Jumlah Pembayaran')" />
                            <x-text-input id="payment_amount" class="block mt-1 w-full text-lg py-2 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200" type="number" step="1000" name="payment_amount" value="0" min="0" required />
                            @error('payment_amount')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <p class="text-lg font-bold mt-2 text-gray-800 dark:text-gray-200">Kembalian: <span id="changeAmount">Rp0</span></p>
                    </div>

                    {{-- Form Proses Transaksi --}}
                    <form id="transactionForm" action="{{ route('kasir.processTransaction') }}" method="POST">
                        @csrf
                        <input type="hidden" name="cart" id="hiddenCartInput">
                        <input type="hidden" name="payment_amount" id="hiddenPaymentAmountInput">
                        <input type="hidden" name="customer_id" id="hiddenCustomerIdInput">

                        <div class="mb-4">
                            <x-input-label for="customer_select" :value="__('Pilih Pelanggan (Opsional)')" />
                            <select id="customer_select" name="customer_select" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200">
                                <option value="">-- Pelanggan Umum --</option>
                                @foreach ($customers as $customer)
                                <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                                @endforeach
                            </select>
                            @error('customer_id')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <x-primary-button type="submit" id="processTransactionButton" class="w-full py-3 text-lg" disabled>
                            {{ __('Proses Transaksi') }}
                        </x-primary-button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Blok JavaScript untuk Fungsionalitas POS --}}
    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // --- Elemen DOM yang Dibutuhkan ---
            const productList = document.getElementById('productList');
            const cartItemsContainer = document.getElementById('cartItems');
            const emptyCartMessage = document.getElementById('emptyCartMessage');
            const cartTotalSpan = document.getElementById('cartTotal');
            const paymentAmountInput = document.getElementById('payment_amount');
            const changeAmountSpan = document.getElementById('changeAmount');
            const processTransactionButton = document.getElementById('processTransactionButton');
            const hiddenCartInput = document.getElementById('hiddenCartInput');
            const hiddenPaymentAmountInput = document.getElementById('hiddenPaymentAmountInput');
            const hiddenCustomerIdInput = document.getElementById('hiddenCustomerIdInput');
            const productSearchInput = document.getElementById('productSearch');
            const categoryFilterSelect = document.getElementById('categoryFilter');
            const customerSelect = document.getElementById('customer_select');
            const transactionForm = document.getElementById('transactionForm');
            const appMessages = document.getElementById('appMessages');

            // --- State Aplikasi ---
            let cart = [];
            let allProducts = [];

            // --- Fungsi Utilitas untuk Menampilkan Pesan ---
            function showMessage(type, message) {
                const alertDiv = document.createElement('div');
                alertDiv.classList.add(
                    'px-4', 'py-3', 'rounded', 'relative', 'mb-2'
                );
                if (type === 'success') {
                    alertDiv.classList.add('bg-green-100', 'border', 'border-green-400', 'text-green-700');
                } else if (type === 'error') {
                    alertDiv.classList.add('bg-red-100', 'border', 'border-red-400', 'text-red-700');
                } else if (type === 'warning') {
                    alertDiv.classList.add('bg-yellow-100', 'border', 'border-yellow-400', 'text-yellow-700');
                }
                alertDiv.innerHTML = `<span>${message}</span>`;
                appMessages.appendChild(alertDiv);

                setTimeout(() => {
                    alertDiv.remove();
                }, 5000);
            }

            /**
             * Mengambil produk dari API dan merender di UI.
             */
            async function fetchAndRenderProducts() {
                try {
                    productList.innerHTML = '<p class="text-center text-gray-500 dark:text-gray-400 col-span-full py-4">Memuat produk...</p>';
                    const response = await fetch(window.location.origin + '/api/products');
                    if (!response.ok) {
                        const errorText = await response.text();
                        throw new Error('Failed to fetch products: ' + response.status + ' ' + response.statusText + ' - ' + errorText);
                    }
                    allProducts = await response.json();

                    renderProducts();
                } catch (error) {
                    console.error('Error fetching products:', error);
                    productList.innerHTML = `<p class="text-center text-red-500 col-span-full">Gagal memuat produk: ${error.message}. Silakan coba lagi nanti.</p>`;
                    showMessage('error', `Gagal memuat produk: ${error.message}`);
                }
            }

            /**
             * Merender produk ke UI berdasarkan filter dan pencarian.
             */
            function renderProducts() {
                productList.innerHTML = '';

                const searchTerm = productSearchInput.value.toLowerCase();
                const selectedCategory = categoryFilterSelect.value;

                const filteredProducts = allProducts.filter(product => {
                    const matchesSearch = product.name.toLowerCase().includes(searchTerm) ||
                        (product.sku && product.sku.toLowerCase().includes(searchTerm));
                    const matchesCategory = selectedCategory === '' || (product.category_id && product.category_id == selectedCategory);
                    return matchesSearch && matchesCategory;
                });

                if (filteredProducts.length === 0) {
                    productList.innerHTML = '<p class="text-center text-gray-500 dark:text-gray-400 col-span-full py-4">Tidak ada produk ditemukan.</p>';
                    return;
                }

                filteredProducts.forEach(product => {
                    const productDiv = document.createElement('div');
                    productDiv.classList.add(
                        'product-item', 'bg-gray-100', 'rounded-lg', 'p-3', 'text-center',
                        'shadow-sm', 'cursor-pointer', 'hover:bg-gray-200', 'transition', 'duration-150', 'ease-in-out',
                        'dark:bg-gray-700', 'dark:hover:bg-gray-600'
                    );
                    productDiv.dataset.id = product.id;
                    productDiv.dataset.name = product.name;
                    productDiv.dataset.price = product.price;
                    productDiv.dataset.stock = product.stock;
                    productDiv.dataset.categoryId = product.category_id;

                    productDiv.innerHTML = `
                        ${product.image ? `<img src="/storage/${product.image}" alt="${product.name}" class="w-full h-24 object-cover mb-2 rounded">` : `<div class="w-full h-24 bg-gray-300 dark:bg-gray-600 flex items-center justify-center text-gray-500 dark:text-gray-400 text-xs font-semibold mb-2 rounded">Tidak ada Gambar</div>`}
                        <p class="font-semibold text-sm truncate text-gray-800 dark:text-gray-200">${product.name}</p>
                        <p class="text-xs text-gray-600 dark:text-gray-400">Rp${parseFloat(product.price).toLocaleString('id-ID')}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Stok: ${product.stock}</p>
                    `;
                    productList.appendChild(productDiv);

                    productDiv.addEventListener('click', function() {
                        const id = parseInt(this.dataset.id);
                        const name = this.dataset.name;
                        const price = parseFloat(this.dataset.price);
                        const stock = parseInt(this.dataset.stock);
                        addProductToCart(id, name, price, stock);
                    });
                });
            }

            /**
             * Memperbarui tampilan keranjang belanja di UI.
             */
            function updateCartDisplay() {
                cartItemsContainer.innerHTML = '';
                if (cart.length === 0) {
                    emptyCartMessage.style.display = 'block';
                } else {
                    emptyCartMessage.style.display = 'none';
                    cart.forEach(item => {
                        const cartItemDiv = document.createElement('div');
                        cartItemDiv.classList.add('flex', 'justify-between', 'items-center', 'py-2', 'border-b', 'border-gray-100', 'dark:border-gray-700');
                        cartItemDiv.innerHTML = `
                        <div>
                            <p class="font-medium text-gray-800 dark:text-gray-200">${item.name}</p>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Rp${item.price.toLocaleString('id-ID')} x
                                <input type="number" min="1" max="${item.stock_available}" value="${item.quantity}"
                                    class="w-16 text-center border-gray-300 rounded-md text-sm quantity-input py-1 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200"
                                    data-product-id="${item.product_id}">
                            </p>
                        </div>
                        <div class="flex items-center">
                            <p class="font-semibold mr-2 text-gray-800 dark:text-gray-200">Rp${(item.price * item.quantity).toLocaleString('id-ID')}</p>
                            <button class="text-red-500 hover:text-red-700 remove-item" data-product-id="${item.product_id}" title="Hapus Item">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm6 0a1 1 0 012 0v6a1 1 0 11-2 0V8z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </div>
                        `;
                        cartItemsContainer.appendChild(cartItemDiv);
                    });

                    document.querySelectorAll('.quantity-input').forEach(input => {
                        input.addEventListener('change', updateQuantity);
                        input.addEventListener('input', updateQuantity);
                    });
                    document.querySelectorAll('.remove-item').forEach(button => {
                        button.addEventListener('click', removeItem);
                    });
                }
            }

            /**
             * Menambahkan produk ke keranjang atau meningkatkan kuantitas jika sudah ada.
             */
            function addProductToCart(productId, name, price, stock) {
                const existingItem = cart.find(item => item.product_id === productId);

                if (existingItem) {
                    if (existingItem.quantity < stock) {
                        existingItem.quantity++;
                        showMessage('success', `Kuantitas "${name}" di keranjang diperbarui.`);
                    } else {
                        showMessage('warning', `Stok "${name}" hanya ${stock}. Tidak bisa menambah lagi.`);
                        return;
                    }
                } else {
                    if (stock > 0) {
                        cart.push({
                            product_id: productId,
                            name: name,
                            price: price,
                            quantity: 1,
                            stock_available: stock
                        });
                        showMessage('success', `"${name}" ditambahkan ke keranjang.`);
                    } else {
                        showMessage('warning', `"${name}" sedang tidak tersedia (stok habis).`);
                        return;
                    }
                }
                updateCartDisplay();
                updateTotals();
            }

            /**
             * Memperbarui kuantitas item di keranjang berdasarkan input user.
             */
            function updateQuantity(event) {
                const productId = parseInt(event.target.dataset.productId);
                let newQuantity = parseInt(event.target.value);
                const maxStock = parseInt(event.target.max);

                if (isNaN(newQuantity) || newQuantity < 1) {
                    newQuantity = 1;
                    event.target.value = 1;
                }
                if (newQuantity > maxStock) {
                    newQuantity = maxStock;
                    event.target.value = maxStock;
                    showMessage('warning', `Stok maksimum untuk produk ini adalah ${maxStock}.`);
                }

                const itemIndex = cart.findIndex(item => item.product_id === productId);
                if (itemIndex !== -1) {
                    cart[itemIndex].quantity = newQuantity;
                    showMessage('success', `Kuantitas di keranjang diperbarui.`);
                }
                updateCartDisplay();
                updateTotals();
            }

            /**
             * Menghapus item dari keranjang.
             */
            function removeItem(event) {
                const productId = parseInt(event.currentTarget.dataset.productId);
                const removedItem = cart.find(item => item.product_id === productId);
                cart = cart.filter(item => item.product_id !== productId);
                updateCartDisplay();
                updateTotals();
                showMessage('success', `"${removedItem.name}" dihapus dari keranjang.`);
            }

            /**
             * Menghitung dan memperbarui total belanja serta kembalian.
             * Juga mengelola status tombol proses transaksi.
             */
            function updateTotals() {
                let total = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
                cartTotalSpan.textContent = `Rp${total.toLocaleString('id-ID')}`;

                const paymentAmount = parseFloat(paymentAmountInput.value) || 0;
                const change = paymentAmount - total;
                changeAmountSpan.textContent = `Rp${change.toLocaleString('id-ID')}`;

                if (cart.length > 0 && paymentAmount >= total) {
                    processTransactionButton.disabled = false;
                    processTransactionButton.classList.remove('opacity-50', 'cursor-not-allowed');
                } else {
                    processTransactionButton.disabled = true;
                    processTransactionButton.classList.add('opacity-50', 'cursor-not-allowed');
                }

                hiddenCartInput.value = JSON.stringify(cart.map(item => ({
                    product_id: item.product_id,
                    quantity: item.quantity,
                    price: item.price
                })));
                hiddenPaymentAmountInput.value = paymentAmount;
                hiddenCustomerIdInput.value = customerSelect.value;
            }

            // --- Handler Submit Form Transaksi (AJAX) ---
            transactionForm.addEventListener('submit', async function(event) {
                event.preventDefault(); // Mencegah submit form default (reload halaman)

                processTransactionButton.disabled = true; // Nonaktifkan tombol saat memproses
                processTransactionButton.textContent = 'Memproses...';
                processTransactionButton.classList.add('opacity-50', 'cursor-not-allowed');

                const formData = new FormData(this);
                const cartData = JSON.parse(formData.get('cart'));

                if (cartData.length === 0) {
                    showMessage('error', 'Keranjang belanja kosong. Harap tambahkan produk.');
                    processTransactionButton.disabled = false;
                    processTransactionButton.textContent = 'Proses Transaksi';
                    processTransactionButton.classList.remove('opacity-50', 'cursor-not-allowed');
                    return;
                }

                try {
                    const response = await fetch(this.action, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json',
                        },
                        body: formData
                    });

                    const result = await response.json();

                    if (response.ok) {
                        // Setelah sukses, arahkan ke halaman struk
                        // Pastikan 'transaction_id' dikembalikan dari server
                        if (result.transaction_id) {
                            window.location.href = `/kasir/receipt/${result.transaction_id}`;
                        } else {
                            showMessage('success', result.message + ` Kembalian: Rp${result.change_amount.toLocaleString('id-ID')} (Invoice: ${result.invoice_number})`);
                            // Fallback jika transaction_id tidak ada, tetap reset POS
                            cart = [];
                            updateCartDisplay();
                            paymentAmountInput.value = 0;
                            customerSelect.value = '';
                            updateTotals();
                            fetchAndRenderProducts();
                        }
                    } else {
                        let errorMessage = result.message || 'Terjadi kesalahan tidak dikenal saat memproses transaksi.';
                        if (result.errors) {
                            for (const key in result.errors) {
                                errorMessage += '\n- ' + result.errors[key][0];
                            }
                        }
                        showMessage('error', errorMessage);
                    }
                } catch (error) {
                    console.error('Error saat submit transaksi:', error);
                    showMessage('error', 'Terjadi kesalahan jaringan atau sistem. Silakan coba lagi.');
                } finally {
                    processTransactionButton.disabled = false;
                    processTransactionButton.textContent = 'Proses Transaksi';
                    processTransactionButton.classList.remove('opacity-50', 'cursor-not-allowed');
                }
            });

            // --- Event Listeners Utama ---
            productSearchInput.addEventListener('input', renderProducts);
            categoryFilterSelect.addEventListener('change', renderProducts);
            paymentAmountInput.addEventListener('input', updateTotals);
            customerSelect.addEventListener('change', updateTotals);

            // --- Inisialisasi Saat Halaman Dimuat ---
            fetchAndRenderProducts();
            updateCartDisplay();
            updateTotals();
        });
    </script>
    @endpush
</x-app-layout>