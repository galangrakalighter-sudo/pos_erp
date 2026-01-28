@extends('layouts.client')

@section('content')
<style>
    body, html {
        overflow: hidden !important;
        height: 100vh !important;
    }
    
    .pos-container {
        height: 100vh !important;
        max-height: 100vh !important;
        overflow: hidden !important;
    }
    
    .catalog-section,
    .cart-section,
    .payment-section {
        height: calc(100vh - 2rem) !important;
        max-height: calc(100vh - 2rem) !important;
        min-height: 0 !important;
        overflow: hidden !important;
    }
    /* Scroll khusus panel pembayaran agar tetap bisa diakses di layar kecil */
    .payment-section {
        display: flex;
        flex-direction: column;
    }
    .payment-section > .payment-scrollable {
        flex: 1;
        min-height: 0;
        overflow-y: auto !important;
        height: 100% !important;
        max-height: 100% !important;
        -webkit-overflow-scrolling: touch;
    }

    /* Kompak dan responsif untuk layar laptop dengan tinggi terbatas */
    @media (max-height: 820px) {
        .catalog-section,
        .cart-section,
        .payment-section {
            height: calc(100vh - 1rem) !important;
            max-height: calc(100vh - 1rem) !important;
        }
        .payment-section .p-4 { padding: 0.75rem !important; }
        .payment-section .gap-4 { gap: 0.75rem !important; }
        .payment-section .space-y-2 > :not([hidden]) ~ :not([hidden]) { margin-top: 0.35rem !important; }
    }


    /* Tooltip instan (tanpa delay) untuk menampilkan nama item penuh */
    .tooltip[data-tip] { position: relative; }
    .tooltip[data-tip]:hover::after {
        content: attr(data-tip);
        position: absolute;
        left: 0;
        top: 100%;
        background: rgba(0,0,0,0.85);
        color: #fff;
        padding: 6px 8px;
        border-radius: 6px;
        font-size: 12px;
        white-space: nowrap;
        transform: translateY(6px);
        z-index: 50;
        pointer-events: none;
    }
    .tooltip[data-tip]:hover::before {
        content: '';
        position: absolute;
        left: 10px;
        top: calc(100% + 2px);
        width: 0; height: 0;
        border-left: 6px solid transparent;
        border-right: 6px solid transparent;
        border-bottom: 6px solid rgba(0,0,0,0.85);
        transform: translateY(-8px);
        z-index: 50;
        pointer-events: none;
    }
</style>
<div class="grid grid-cols-12 gap-6 h-screen overflow-hidden pos-container" x-data="posApp()" x-init="init()" x-cloak>
    <!-- Catalog (Left) -->
    <div class="col-span-12 lg:col-span-3 h-full catalog-section">
        <div class="bg-white rounded-2xl shadow p-4 flex flex-col h-full">
            <div class="mb-3 flex-shrink-0">
                <div class="flex items-center bg-white rounded-full px-4 py-2 border">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-[#28C328] mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 105 11a6 6 0 0012 0z" /></svg>
                    <input type="text" x-model="searchQuery" class="flex-1 bg-transparent border-none outline-none text-sm" placeholder="Scan/Cari produk/Kode" />
                </div>
            </div>
            <div class="mb-3 flex-shrink-0">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-semibold text-gray-700">Stok</span>
                    <div class="flex items-center bg-gray-50 rounded-lg px-3 py-1 border w-32">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-gray-400 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 105 11a6 6 0 0012 0z" />
                        </svg>
                        <input type="text" x-model="stockSearchQuery" class="flex-1 bg-transparent border-none outline-none text-xs" placeholder="Cari item..." />
                    </div>
                </div>
            </div>
            <div class="flex items-center gap-2 overflow-x-auto pb-1 flex-shrink-0">
                <button @click="selectedCategory = ''" :class="selectedCategory === '' ? 'bg-[#28C328] text-white' : 'bg-gray-100 text-gray-600'" class="px-3 py-1 rounded-full text-xs font-semibold whitespace-nowrap">Semua</button>
                <template x-for="cat in categories" :key="cat">
                    <button @click="selectedCategory = cat" :class="selectedCategory === cat ? 'bg-[#28C328] text-white' : 'bg-gray-100 text-gray-600'" class="px-3 py-1 rounded-full text-xs font-semibold whitespace-nowrap" x-text="cat"></button>
                </template>
            </div>
            <div class="mt-3 border-t pt-3 flex-shrink-0"></div>
            <div class="flex-1 overflow-y-auto space-y-2 pr-1 min-h-0">
                <template x-for="item in filteredItems" :key="item.id">
                    <div class="flex items-center bg-[#F7FFF7] rounded-lg p-3 gap-3 border hover:shadow-sm transition-all duration-200">
                        <!-- Gambar/icon dihilangkan agar teks lebih lebar -->
                        <div class="flex-1 min-w-0 tooltip" :data-tip="item.name">
                            <div class="font-semibold text-sm text-gray-800 truncate" x-text="truncateWords(item.name, 2)"></div>
                            <div class="text-[11px] text-gray-500 font-mono truncate" x-text="item.sku"></div>
                            <div class="text-[11px] text-[#28C328] font-semibold">Stock: <span x-text="Number(item.stock).toLocaleString('id-ID')"></span></div>
                        </div>
                        <div class="text-right flex-shrink-0">
                            <div class="text-xs text-gray-500">Harga</div>
                            <div class="text-sm font-bold text-gray-800">Rp<span x-text="formatNumber(item.price)"></span></div>
                        </div>
                        <button @click="addToCart(item)" class="p-2 hover:bg-[#28C328] hover:text-white rounded-lg transition-all duration-200 flex-shrink-0" aria-label="Tambah ke keranjang">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-[#28C328] hover:text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        </button>
                    </div>
                </template>
                <template x-if="filteredItems.length === 0">
                    <div class="text-center text-gray-400 text-sm py-6">Produk tidak ditemukan</div>
                </template>
            </div>
        </div>
    </div>

    <!-- Cart (Middle) -->
    <div class="col-span-12 lg:col-span-6 flex flex-col h-full cart-section">
        <div class="bg-white rounded-2xl shadow flex-1 flex flex-col h-full">
            <div class="flex items-center justify-between p-4 border-b flex-shrink-0">
                <div class="font-bold text-[#28C328] text-lg">Keranjang</div>
                <div class="flex items-center gap-2">
                    <button @click="clearCart()" class="px-3 py-1 rounded-lg bg-red-50 text-red-600 text-xs font-semibold hover:bg-red-100">Kosongkan</button>
                </div>
            </div>
            <div class="flex-1 overflow-y-auto min-h-0">
                        <table class="w-full text-sm">
                    <thead class="bg-[#28C328] text-white sticky top-0">
                        <tr>
                            <th class="py-2 px-3 rounded-tl-xl text-left">Item</th>
                            <th class="py-2 px-3 text-right">Harga</th>
                            <th class="py-2 px-3 text-center">Qty</th>
                            <th class="py-2 px-3 text-center">Diskon (Rp)</th>
                            <th class="py-2 px-3 text-right">Subtotal</th>
                            <th class="py-2 px-3 rounded-tr-xl text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                        <template x-for="(line, idx) in cart" :key="line.id">
                            <tr class="odd:bg-[#F7FFF7]">
                                <td class="py-2 px-3 align-middle w-48">
                                    <div class="leading-tight min-w-0 tooltip" :data-tip="line.name">
                                        <div class="font-semibold text-gray-800 truncate text-sm" x-text="truncateWords(line.name, 2)"></div>
                                        <div class="text-[11px] text-gray-500 font-mono truncate" x-text="line.sku"></div>
                                    </div>
                                </td>
                                <td class="py-2 px-3 align-middle text-right w-24">Rp<span x-text="formatNumber(line.price)"></span></td>
                                <td class="py-2 px-3 align-middle w-32">
                                    <div class="flex items-center justify-center gap-2">
                                        <button @click="decreaseQty(idx)" class="p-1 hover:bg-gray-100 rounded transition-colors" aria-label="Kurangi">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-[#28C328]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/></svg>
                                        </button>
                                        <input type="number" min="1" class="w-12 text-center border rounded px-2 py-1 text-sm" :value="line.quantity" @change="onQtyInput(idx, $event.target.value)" />
                                        <button @click="increaseQty(idx)" class="p-1 hover:bg-gray-100 rounded transition-colors" aria-label="Tambah">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-[#28C328]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                        </button>
                                    </div>
                                </td>
                                <td class="py-2 px-3 align-middle w-20">
                                    <input type="number" min="0" class="w-20 text-center border rounded px-2 py-1 text-sm" :value="line.discountAmount" @change="onDiscountInput(idx, $event.target.value)" />
                                </td>
                                <td class="py-2 px-3 align-middle text-right w-28">Rp<span x-text="formatNumber(lineSubtotal(line))"></span></td>
                                <td class="py-2 px-3 align-middle text-center w-16">
                                    <button @click="removeFromCart(idx)" class="w-6 h-6 bg-red-500 text-white rounded-full flex items-center justify-center text-xs leading-none hover:bg-red-600 transition-colors">×</button>
                                    </td>
                                </tr>
                        </template>
                        <template x-if="cart.length === 0">
                            <tr><td colspan="6" class="p-6 text-center text-gray-400">Keranjang kosong</td></tr>
                        </template>
                            </tbody>
                        </table>
                    </div>
            <div class="p-4 border-t text-xs text-gray-500 flex-shrink-0">
                - Diskon per item dalam rupiah. Subtotal = (harga x qty) - diskon.
            </div>
        </div>
                        </div>
                        
    <!-- Payment (Right) -->
    <div class="col-span-12 lg:col-span-3 h-full payment-section">
        <div class="bg-white rounded-2xl p-4 shadow flex flex-col gap-4 h-full payment-scrollable">
            <div class="text-center">
                <div class="text-sm text-gray-500">No Pesanan#</div>
                <div class="text-lg font-bold text-[#28C328]" x-text="orderNumber"></div>
            </div>
            <div class="space-y-2 text-sm">
                            <div class="flex justify-between items-center">
                    <span class="text-gray-600">Total item</span>
                    <span class="font-semibold" x-text="cart.length"></span>
                            </div>
                            <div class="flex justify-between items-center">
                    <span class="text-gray-600">Total Qty</span>
                    <span class="font-semibold" x-text="totalQuantity()"></span>
                            </div>
                            <div class="flex justify-between items-center">
                    <span class="text-gray-600">Subtotal</span>
                    <span class="font-semibold">Rp<span x-text="formatNumber(cartSubtotal())"></span></span>
                            </div>
                            <div class="flex justify-between items-center">
                    <span class="text-gray-600">Diskon</span>
                    <span class="font-semibold text-[#28C328]">Rp<span x-text="formatNumber(cartDiscountTotal())"></span></span>
                </div>
                <div class="border-t my-2"></div>
                <div class="flex justify-between items-center">
                    <span class="text-lg font-bold text-gray-800">Total</span>
                    <span class="text-xl font-bold text-[#28C328]">Rp<span x-text="formatNumber(cartTotal())"></span></span>
                            </div>
                        </div>
                        
            <!-- Catatan Section -->
            <div class="space-y-2">
                <label class="text-xs text-gray-500 font-semibold">Catatan</label>
                <textarea x-model="notes" class="w-full rounded-lg border px-3 py-2 text-sm resize-none" rows="2" placeholder="Tambahkan catatan untuk transaksi ini (opsional)"></textarea>
            </div>
                        
            <div class="space-y-2">
                <label class="text-xs text-gray-500">Metode Pembayaran</label>
                <select x-model="paymentMethod" class="w-full rounded-lg border px-3 py-2 text-sm">
                    <option value="Cash">Tunai</option>
                    <option value="Transfer">Transfer</option>
                    <option value="QRIS">QRIS</option>
                </select>
            </div>
            <div class="space-y-2" x-show="paymentMethod === 'Transfer'">
                <label class="text-xs text-gray-500">Referensi/ID Transfer</label>
                <input type="text" x-model="paymentReference" class="w-full rounded-lg border px-3 py-2 text-sm" placeholder="Opsional">
            </div>
            <div class="space-y-2">
                <label class="text-xs text-gray-500">Bayar</label>
                <div class="relative">
                    <span class="absolute left-3 top-2 text-gray-400 text-xs">Rp</span>
                    <input type="text" inputmode="numeric" x-model="amountPaidInput" @input="onAmountPaidInput" class="w-full rounded-lg border pl-7 pr-3 py-2 text-sm" placeholder="0">
                </div>
            </div>
            <div class="flex justify-between items-center text-sm">
                <span class="text-gray-600">Kembalian</span>
                <span class="font-semibold" :class="change() < 0 ? 'text-red-600' : 'text-gray-800'">Rp<span x-text="formatNumber(Math.max(0, change()))"></span></span>
            </div>

            <div class="grid grid-cols-1 gap-2 pt-2">
                <button @click="checkout()" :disabled="cart.length === 0 || change() < 0" class="w-full py-3 rounded-xl bg-[#28C328] text-white font-semibold text-sm hover:bg-[#22a322] disabled:opacity-50 disabled:cursor-not-allowed">Bayar</button>
            </div>
        </div>
    </div>
</div>
@endsection

<script>
function posApp() {
    return {
        // Catalog state
        items: [],
        searchQuery: '',
        stockSearchQuery: '',
        selectedCategory: '',
        // Cart state
        cart: [],
        paymentMethod: 'Cash',
        paymentReference: '',
        amountPaid: 0,
        amountPaidInput: '0',
        orderNumber: '',
        notes: '',

        init() {
            this.loadCatalog();
            this.loadDraft();
            this.orderNumber = this.generateOrderNumber();
        },

        // Catalog loading: client-only storage
        loadCatalog() {
            this.items = [];
            fetch('/client/stock-items', {
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                }
            })
            .then(r => r.json())
            .then(res => {
                const clientItems = res.data || [];
                this.items = clientItems.map((it, idx) => ({
                    id: it.id || idx + 1,
                    name: it.nama,
                    sku: it.sku,
                    price: Number(it.harga),
                    stock: Number(it.tersedia),
                    image: '', // tambahkan jika ada
                    category: it.kategori || 'Umum',
                    source: 'client'
                }));
            })
            .catch(err => {
                this.items = [];
                alert('Gagal memuat data katalog: ' + err.message);
            });
        },

        get categories() {
            return [...new Set(this.items.map(i => i.category))];
        },

        get filteredItems() {
            const q = this.searchQuery.toLowerCase();
            const stockQ = this.stockSearchQuery.toLowerCase();
            return this.items.filter(i => {
                const matchCat = !this.selectedCategory || i.category === this.selectedCategory;
                const matchQ = !q || [i.name, i.sku].some(v => String(v).toLowerCase().includes(q));
                const matchStockQ = !stockQ || [i.name, i.sku].some(v => String(v).toLowerCase().includes(stockQ));
                return matchCat && matchQ && matchStockQ;
            });
        },

        // Cart operations
        addToCart(item) {
            // Check if stock is 0
            if (Number(item.stock) <= 0) {
                this.showNotification('error', `Stok ${item.name} habis (0). Tidak dapat ditambahkan ke keranjang.`);
                return;
            }

            const idx = this.cart.findIndex(c => c.id === item.id);
            if (idx >= 0) {
                const newQty = Number(this.cart[idx].quantity) + 1;
                // Check if new quantity exceeds available stock
                if (newQty > Number(item.stock)) {
                    this.showNotification('error', `Stok ${item.name} tidak mencukupi. Tersedia: ${item.stock}`);
                    return;
                }
                this.cart[idx].quantity = newQty;
            } else {
                this.cart.push({
                    id: item.id,
                    name: item.name,
                    sku: item.sku,
                    price: Number(item.price) || 0,
                    quantity: 1,
                    discountAmount: 0,
                    image: item.image
                });
            }
            this.persistDraft();
        },
        removeFromCart(index) {
            this.cart.splice(index, 1);
            this.persistDraft();
        },
        clearCart() {
            this.cart = [];
            this.amountPaid = 0;
            this.amountPaidInput = '0';
            this.notes = '';
            this.persistDraft();
        },
        increaseQty(index) {
            const cartItem = this.cart[index];
            const newQty = Number(cartItem.quantity) + 1;
            
            // Find the original item to check stock
            const originalItem = this.items.find(item => item.id === cartItem.id);
            if (originalItem && newQty > Number(originalItem.stock)) {
                this.showNotification('error', `Stok ${cartItem.name} tidak mencukupi. Tersedia: ${originalItem.stock}`);
                return;
            }
            
            this.cart[index].quantity = Math.max(1, newQty);
            this.persistDraft();
        },
        decreaseQty(index) {
            this.cart[index].quantity = Math.max(1, Number(this.cart[index].quantity) - 1);
            this.persistDraft();
        },
        onQtyInput(index, value) {
            const newQty = Math.max(1, Number(value) || 1);
            const cartItem = this.cart[index];
            
            // Find the original item to check stock
            const originalItem = this.items.find(item => item.id === cartItem.id);
            if (originalItem && newQty > Number(originalItem.stock)) {
                this.showNotification('error', `Stok ${cartItem.name} tidak mencukupi. Tersedia: ${originalItem.stock}`);
                // Reset to maximum available stock
                this.cart[index].quantity = Math.min(newQty, Number(originalItem.stock));
                this.persistDraft();
                return;
            }
            
            this.cart[index].quantity = newQty;
            this.persistDraft();
        },
        onDiscountInput(index, value) {
            let v = Number(value) || 0;
            if (v < 0) v = 0;
            // Maksimal diskon tidak boleh melebihi subtotal item
            const maxDiscount = this.cart[index].price * this.cart[index].quantity;
            if (v > maxDiscount) v = maxDiscount;
            this.cart[index].discountAmount = v;
            this.persistDraft();
        },
        lineSubtotal(line) {
            const price = Number(line.price) || 0;
            const qty = Number(line.quantity) || 0;
            const disc = Number(line.discountAmount) || 0;
            const subtotal = (price * qty) - disc;
            return Math.max(0, Math.round(subtotal));
        },
        
        // Helper function to calculate subtotal for backend
        calculateItemSubtotal(item) {
            const price = Number(item.price) || 0;
            const qty = Number(item.quantity) || 0;
            const disc = Number(item.discountAmount) || 0;
            const subtotal = (price * qty) - disc;
            return Math.max(0, Math.round(subtotal));
        },

        // Totals
        totalQuantity() {
            return this.cart.reduce((n, l) => n + (Number(l.quantity) || 0), 0);
        },
        cartSubtotal() {
            return this.cart.reduce((sum, l) => sum + (Number(l.price) || 0) * (Number(l.quantity) || 0), 0);
        },
        cartDiscountTotal() {
            return this.cart.reduce((sum, l) => sum + (Number(l.discountAmount) || 0), 0);
        },
        cartTotal() {
            return Math.max(0, Math.round(this.cartSubtotal() - this.cartDiscountTotal()));
        },

        // Payments
        onAmountPaidInput(e) {
            const raw = e?.target?.value ?? this.amountPaidInput;
            const cleaned = String(raw).replace(/[^0-9]/g, '');
            const num = cleaned ? Number(cleaned) : 0;
            this.amountPaid = num;
            this.amountPaidInput = num.toLocaleString('id-ID');
        },
        change() {
            return this.amountPaid - this.cartTotal();
        },

        // Checkout / Draft
        saveDraft() {
            const draft = {
                cart: this.cart,
                paymentMethod: this.paymentMethod,
                paymentReference: this.paymentReference,
                amountPaid: this.amountPaid,
                savedAt: new Date().toISOString(),
            };
            localStorage.setItem('client_pos_draft', JSON.stringify(draft));
            alert('Draft disimpan.');
        },
        persistDraft() {
            const draft = JSON.parse(localStorage.getItem('client_pos_draft') || '{}');
            draft.cart = this.cart;
            draft.paymentMethod = this.paymentMethod;
            draft.paymentReference = this.paymentReference;
            draft.amountPaid = this.amountPaid;
            draft.notes = this.notes;
            localStorage.setItem('client_pos_draft', JSON.stringify(draft));
        },
        loadDraft() {
            const draft = JSON.parse(localStorage.getItem('client_pos_draft') || '{}');
            if (draft && Array.isArray(draft.cart)) this.cart = draft.cart;
            if (draft && draft.paymentMethod) this.paymentMethod = draft.paymentMethod;
            if (draft && draft.paymentReference) this.paymentReference = draft.paymentReference;
            if (draft && typeof draft.amountPaid === 'number') {
                this.amountPaid = draft.amountPaid;
                this.amountPaidInput = draft.amountPaid.toLocaleString('id-ID');
            }
            if (draft && draft.notes) this.notes = draft.notes;
        },
        async checkout() {
            if (this.cart.length === 0) return;
            if (this.change() < 0) {
                this.showNotification('error', 'Nominal bayar kurang dari total transaksi.');
                return;
            }

            try {
                // Prepare data for backend - using the same format as addSale
                const checkoutData = {
                    order_number: this.orderNumber,
                    sale_date: new Date().toISOString().split('T')[0],
                    total_items: this.cart.length,
                    total_quantity: this.totalQuantity(),
                    total_amount: this.cartTotal(),
                    payment_method: this.paymentMethod,
                    amount_paid: this.amountPaid,
                    change_amount: this.change(),
                    status: 'completed',
                    notes: this.notes || '',
                    items: this.cart.map(line => ({
                        item_name: line.name,
                        item_sku: line.sku,
                        quantity: line.quantity,
                        unit_price: line.price,
                        discount_amount: line.discountAmount || 0,
                        subtotal: this.calculateItemSubtotal(line)
                    }))
                };

                console.log('Sending checkout data:', checkoutData);

                // Send to backend using addSale endpoint instead of checkout
                const response = await fetch('/client/sales', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    },
                    body: JSON.stringify(checkoutData)
                });

                const result = await response.json();
                console.log('Checkout response:', result);

                if (!response.ok) {
                    throw new Error(result.error || result.messages || 'Checkout failed');
                }

                // Success - show notification with order details
                this.showNotification('success', `Transaksi berhasil ! Order: ${result.data.order_number}`);
                
                // Reset state
                this.clearCart();
                this.paymentMethod = 'Cash';
                this.paymentReference = '';
                this.orderNumber = this.generateOrderNumber();
                
                // Reload catalog to reflect updated stock
                this.loadCatalog();

            } catch (error) {
                console.error('Checkout error:', error);
                this.showNotification('error', 'Checkout gagal: ' + error.message);
            }
        },

        // Utils
        formatNumber(n) {
            return Number(n || 0).toLocaleString('id-ID');
        },
        // Batasi menjadi N kata lalu tambahkan elipsis
        truncateWords(text, limit = 2) {
            const str = String(text || '').trim();
            if (!str) return '';
            const words = str.split(/\s+/);
            if (words.length <= limit) return str;
            return words.slice(0, limit).join(' ') + ' ...';
        },
        generateOrderNumber() {
            const d = new Date();
            const ymd = d.getFullYear().toString().slice(-2) + String(d.getMonth()+1).padStart(2,'0') + String(d.getDate()).padStart(2,'0');
            return ymd + '-' + Math.random().toString(36).substring(2, 6).toUpperCase();
        },
        
        // Notification system
        showNotification(type, message) {
            // Create notification element
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg max-w-sm ${
                type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'
            }`;
            notification.innerHTML = `
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="${
                            type === 'success' ? 'M5 13l4 4L19 7' : 'M6 18L18 6M6 6l12 12'
                        }"></path>
                    </svg>
                    <span class="font-semibold">${message}</span>
                </div>
            `;
            
            // Add to page
            document.body.appendChild(notification);
            
            // Remove after 3 seconds
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, 3000);
        },
    }
}
</script>
