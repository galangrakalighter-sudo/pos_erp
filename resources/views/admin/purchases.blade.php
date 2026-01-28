@extends('layouts.admin')

@section('content')
<div class="bg-white rounded-xl shadow p-8" x-data="purchaseManagement()" x-init="init()">
    <h1 class="text-2xl font-bold text-[#28C328] mb-6">Pembelian Internal GAFI</h1>
    
    <!-- Action Buttons -->
    <div class="mb-6 flex justify-between items-center">
        <div class="flex gap-2">
            <button @click="showCreateModal = true" class="rounded-lg bg-[#28C328] px-4 py-2 text-white text-sm font-semibold flex items-center gap-2 hover:bg-[#22a322] transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Pembelian
            </button>
        </div>
        
        <!-- Filter & Search -->
        <div class="flex flex-1 gap-2 items-center">
            <!-- Search -->
            <div class="w-64">
                <div class="flex items-center border border-gray-300 rounded-lg px-4 py-1 bg-white">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-[#28C328] mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M11 19a8 8 0 100-16 8 8 0 000 16z" /></svg>
                    <input type="text" x-model="search" placeholder="Cari pembelian..." class="flex-1 bg-transparent border-none outline-none text-gray-400 text-sm font-medium placeholder-gray-400 h-6">
                </div>
            </div>
            
            <!-- Filter Tanggal -->
            <div class="flex gap-2">
                <select x-model="dateFilter" @change="applyDateFilter()" class="rounded-lg border border-gray-300 px-3 py-2 text-gray-700 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-[#28C328] focus:border-transparent">
                    <option value="">Semua Tanggal</option>
                    <option value="today">Hari Ini</option>
                    <option value="week">Minggu Ini</option>
                    <option value="month">Bulan Ini</option>
                    <option value="year">Tahun Ini</option>
                    <option value="custom">Custom Range</option>
                </select>
                
                <!-- Custom date range inputs -->
                <div x-show="dateFilter === 'custom'" class="flex gap-2 items-center">
                    <input type="date" x-model="customStartDate" @change="applyDateFilter()" class="rounded-lg border border-gray-300 px-3 py-2 text-gray-700 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-[#28C328] focus:border-transparent" placeholder="Tanggal Mulai">
                    <span class="text-gray-500">sampai</span>
                    <input type="date" x-model="customEndDate" @change="applyDateFilter()" class="rounded-lg border border-gray-300 px-3 py-2 text-gray-700 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-[#28C328] focus:border-transparent" placeholder="Tanggal Akhir">
                </div>
                
                <!-- Clear filter button -->
                <button x-show="dateFilter" @click="clearDateFilter()" class="px-3 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition text-sm flex items-center gap-1" title="Hapus Filter Tanggal">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    Clear
                </button>
            </div>
            
            <!-- Filter Status -->
            <div class="relative" x-data="{ open: false }" @click.away="open = false">
                <button @click="open = !open" class="px-4 py-2 border border-gray-300 rounded-lg text-sm flex items-center gap-2">
                    <span x-text="statusFilter === '' ? 'Semua Status' : statusFilter"></span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div x-show="open" x-transition class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 z-10">
                    <div @click="statusFilter = ''; open = false" class="px-4 py-2 hover:bg-gray-100 cursor-pointer rounded-t-lg">Semua Status</div>
                    <div @click="statusFilter = 'pending'; open = false" class="px-4 py-2 hover:bg-gray-100 cursor-pointer">Pending</div>
                    <div @click="statusFilter = 'approved'; open = false" class="px-4 py-2 hover:bg-gray-100 cursor-pointer">Approved</div>
                    <div @click="statusFilter = 'rejected'; open = false" class="px-4 py-2 hover:bg-gray-100 cursor-pointer">Rejected</div>
                    <div @click="statusFilter = 'completed'; open = false" class="px-4 py-2 hover:bg-gray-100 cursor-pointer">Completed</div>
                    <div @click="statusFilter = 'returned'; open = false" class="px-4 py-2 hover:bg-gray-100 cursor-pointer">Returned</div>
                </div>
            </div>
            
            <!-- Filter Payment -->
            <div class="relative" x-data="{ open: false }" @click.away="open = false">
                <button @click="open = !open" class="px-4 py-2 border border-gray-300 rounded-lg text-sm flex items-center gap-2">
                    <span x-text="paymentFilter === '' ? 'Semua Payment' : paymentFilter"></span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div x-show="open" x-transition class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 z-10">
                    <div @click="paymentFilter = ''; open = false" class="px-4 py-2 hover:bg-gray-100 cursor-pointer rounded-t-lg">Semua Payment</div>
                    <div @click="paymentFilter = 'unpaid'; open = false" class="px-4 py-2 hover:bg-gray-100 cursor-pointer">Unpaid</div>
                    <div @click="paymentFilter = 'partial'; open = false" class="px-4 py-2 hover:bg-gray-100 cursor-pointer">Partial</div>
                    <div @click="paymentFilter = 'paid'; open = false" class="px-4 py-2 hover:bg-gray-100 cursor-pointer">Paid</div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Filter status indicator -->
    <div x-show="dateFilter" class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded-lg">
        <div class="flex items-center gap-2 text-sm text-blue-700">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.207A1 1 0 013 6.5V4z" />
            </svg>
            <span x-text="getFilterStatusText()"></span>
        </div>
    </div>

    <!-- Purchases Table -->
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead>
                <tr class="bg-[#28C328] text-white">
                    <th class="p-3 text-left rounded-tl-xl">No. Pembelian</th>
                    <th class="p-3 text-left">Supplier</th>
                    <th class="p-3 text-left">Tanggal</th>
                    <th class="p-3 text-left">Items</th>
                    <th class="p-3 text-right">Total</th>
                    <th class="p-3 text-center">Status</th>
                    <th class="p-3 text-center">Payment</th>
                    <th class="p-3 text-center rounded-tr-xl">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-100">
                <template x-for="purchase in filteredPurchases" :key="purchase.id">
                    <tr class="hover:bg-gray-50">
                        <td class="p-3">
                            <div class="font-semibold text-gray-800" x-text="purchase.purchase_number"></div>
                            <div class="text-xs text-gray-500" x-text="purchase.invoice_number || 'No Invoice'"></div>
                        </td>
                        <td class="p-3">
                            <div class="text-sm text-gray-800" x-text="purchase.supplier_name"></div>
                            <div class="text-xs text-gray-500" x-text="purchase.supplier_contact || '-'"></div>
                        </td>
                        <td class="p-3 text-gray-600" x-text="formatDate(purchase.purchase_date)"></td>
                        <td class="p-3">
                            <div class="text-sm text-gray-800" x-text="purchase.items.length + ' items'"></div>
                            <div class="text-xs text-gray-500" x-text="purchase.items.map(i => i.item_name).join(', ')"></div>
                        </td>
                        <td class="p-3 text-right">
                            <div class="font-semibold text-gray-800">Rp<span x-text="formatNumber(purchase.total_amount)"></span></div>
                        </td>
                        <td class="p-3 text-center">
                            <span class="px-2 py-1 rounded-full text-xs font-semibold" 
                                  :class="getStatusClass(purchase.status)" 
                                  x-text="getStatusText(purchase.status)"></span>
                        </td>
                        <td class="p-3 text-center">
                            <span class="px-2 py-1 rounded-full text-xs font-semibold" 
                                  :class="getPaymentClass(purchase.payment_status)" 
                                  x-text="getPaymentText(purchase.payment_status)"></span>
                        </td>
                        <td class="p-3 text-center">
                            <div class="flex gap-2 justify-center">
                                <button @click="viewPurchaseDetail(purchase)" class="text-[#28C328] hover:text-[#22a322] text-sm font-medium">
                                    Detail
                                </button>
                                <template x-if="purchase.status === 'pending'">
                                    <div class="flex gap-2">
                                        <button @click="approvePurchase(purchase.id)" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                            Approve
                                        </button>
                                        <button @click="returnPurchase(purchase.id)" class="text-purple-600 hover:text-purple-800 text-sm font-medium">
                                            Return
                                        </button>
                                    </div>
                                </template>
                                <template x-if="purchase.status === 'approved'">
                                    <button @click="completePurchase(purchase.id)" class="text-green-600 hover:text-green-800 text-sm font-medium">
                                        Complete
                                    </button>
                                </template>
                            </div>
                        </td>
                    </tr>
                </template>
                <template x-if="filteredPurchases.length === 0">
                    <tr>
                        <td colspan="8" class="p-8 text-center text-gray-500">
                            Belum ada pembelian
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>
    </div>

    <!-- Create Purchase Modal -->
    <div x-show="showCreateModal" x-transition class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40 p-4" x-cloak>
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-6xl mx-4 relative max-h-[90vh] overflow-hidden">
            <!-- Header -->
            <div class="bg-[#28C328] text-white px-6 py-4 flex items-center justify-between">
                <div class="font-semibold text-lg">Tambah Pembelian Baru</div>
                <button @click="showCreateModal = false; resetForm()" class="w-8 h-8 rounded-full bg-white/20 hover:bg-white/30 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Content -->
            <div class="p-6 overflow-y-auto max-h-[calc(90vh-120px)]">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Left: Purchase Info -->
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Informasi Pembelian</h3>
                        
                        <div class="space-y-4">
                            <!-- Purchase Number -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">No. Pembelian</label>
                                <input type="text" x-model="purchaseData.purchase_number" class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50" readonly>
                            </div>

                            <!-- Supplier Name -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Supplier</label>
                                <input type="text" x-model="purchaseData.supplier_name" placeholder="Masukkan nama supplier" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#28C328] focus:border-[#28C328]">
                            </div>

                            <!-- Supplier Contact -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Kontak Supplier</label>
                                <input type="text" x-model="purchaseData.supplier_contact" placeholder="Masukkan kontak supplier" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#28C328] focus:border-[#28C328]">
                            </div>

                            <!-- Invoice Number -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">No. Invoice</label>
                                <input type="text" x-model="purchaseData.invoice_number" placeholder="Masukkan nomor invoice" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#28C328] focus:border-[#28C328]">
                            </div>

                            <!-- Purchase Date -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Pembelian</label>
                                <input type="date" x-model="purchaseData.purchase_date" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#28C328] focus:border-[#28C328]">
                            </div>

                            <!-- Due Date -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Jatuh Tempo</label>
                                <input type="date" x-model="purchaseData.due_date" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#28C328] focus:border-[#28C328]">
                            </div>

                            <!-- Notes -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Catatan</label>
                                <textarea x-model="purchaseData.notes" placeholder="Masukkan catatan (optional)" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#28C328] focus:border-[#28C328]"></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Right: Items -->
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Items Pembelian</h3>
                        
                        <!-- Add Item Button -->
                        <div class="mb-4">
                            <button @click="addNewItem()" class="w-full bg-blue-600 text-white py-2 rounded-lg font-semibold hover:bg-blue-700 transition flex items-center justify-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                Tambah Item
                            </button>
                        </div>

                        <!-- Items List -->
                        <div class="space-y-3 max-h-96 overflow-y-auto">
                            <template x-for="(item, index) in purchaseData.items" :key="index">
                                <div class="border border-gray-200 rounded-lg p-4 bg-gray-50">
                                    <div class="grid grid-cols-2 gap-3 mb-3">
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700 mb-1">Nama Item</label>
                                            <input type="text" x-model="item.item_name" placeholder="Nama item" class="w-full px-2 py-1 border border-gray-300 rounded text-sm">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700 mb-1">SKU</label>
                                            <input type="text" x-model="item.sku" placeholder="SKU" class="w-full px-2 py-1 border border-gray-300 rounded text-sm">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700 mb-1">Kategori</label>
                                            <input type="text" x-model="item.category" placeholder="Kategori" class="w-full px-2 py-1 border border-gray-300 rounded text-sm">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700 mb-1">Unit</label>
                                            <input type="text" x-model="item.unit" placeholder="pcs, kg, etc" class="w-full px-2 py-1 border border-gray-300 rounded text-sm">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700 mb-1">Quantity</label>
                                            <input type="number" x-model="item.quantity" min="1" @input="updateItemSubtotal(index)" class="w-full px-2 py-1 border border-gray-300 rounded text-sm">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700 mb-1">Harga Satuan</label>
                                            <input type="number" x-model="item.unit_price" min="0" step="0.01" @input="updateItemSubtotal(index)" class="w-full px-2 py-1 border border-gray-300 rounded text-sm">
                                        </div>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700 mb-1">Deskripsi</label>
                                            <input type="text" x-model="item.item_description" placeholder="Deskripsi item" class="w-full px-2 py-1 border border-gray-300 rounded text-sm">
                                        </div>
                                        <div class="text-right ml-3">
                                            <div class="text-sm font-semibold text-[#28C328]">Subtotal: Rp<span x-text="formatNumber(item.subtotal)"></span></div>
                                            <button @click="removeItem(index)" class="text-red-600 hover:text-red-800 text-sm">
                                                Hapus
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </template>
                            <template x-if="purchaseData.items.length === 0">
                                <div class="text-center text-gray-500 py-8 text-sm">Belum ada item</div>
                            </template>
                        </div>

                        <!-- Total & Submit -->
                        <div class="border-t pt-4 mt-4">
                            <div class="flex justify-between items-center mb-4">
                                <span class="text-lg font-semibold text-gray-800">Total:</span>
                                <span class="text-2xl font-bold text-[#28C328]">Rp<span x-text="formatNumber(totalAmount)"></span></span>
                            </div>
                            <button @click="submitPurchase()" :disabled="purchaseData.items.length === 0" class="w-full bg-[#28C328] text-white py-3 rounded-lg font-semibold hover:bg-[#22a322] transition disabled:opacity-50 disabled:cursor-not-allowed">
                                Simpan Pembelian
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Purchase Detail Modal -->
    <div x-show="showDetailModal" x-transition class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 p-4" x-cloak>
        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-6xl mx-4 relative max-h-[95vh] overflow-hidden border border-gray-100">
            <!-- Header -->
            <div class="bg-gradient-to-r from-[#28C328] to-[#22a322] text-white px-8 py-6 flex items-center justify-between rounded-t-3xl">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                    </div>
                    <div>
                        <div class="font-bold text-xl">Detail Pembelian Internal</div>
                        <div class="text-white/80 text-sm" x-text="selectedPurchase ? selectedPurchase.purchase_number : ''"></div>
                    </div>
                </div>
                <button @click="showDetailModal = false" class="w-10 h-10 rounded-full bg-white/20 hover:bg-white/30 flex items-center justify-center transition-all duration-200 hover:scale-105">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Content -->
            <div class="p-8 overflow-y-auto max-h-[calc(95vh-140px)]">
                <template x-if="selectedPurchase">
                    <!-- Purchase Header -->
                    <div class="mb-8">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                            <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-4 border border-blue-200">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="text-xs text-blue-600 font-medium uppercase tracking-wide">No. Pembelian</div>
                                        <div class="font-bold text-lg text-blue-800" x-text="selectedPurchase.purchase_number"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-gradient-to-br from-yellow-50 to-yellow-100 rounded-xl p-4 border border-yellow-200">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-yellow-500 rounded-lg flex items-center justify-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="text-xs text-yellow-600 font-medium uppercase tracking-wide">Status</div>
                                        <span class="px-3 py-1 rounded-full text-sm font-semibold" 
                                              :class="getStatusClass(selectedPurchase.status)" 
                                              x-text="getStatusText(selectedPurchase.status)"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl p-4 border border-green-200">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-green-500 rounded-lg flex items-center justify-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="text-xs text-green-600 font-medium uppercase tracking-wide">Tanggal Pembelian</div>
                                        <div class="font-bold text-lg text-green-800" x-text="formatDate(selectedPurchase.purchase_date)"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl p-4 border border-purple-200">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-purple-500 rounded-lg flex items-center justify-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="text-xs text-purple-600 font-medium uppercase tracking-wide">Payment Status</div>
                                        <span class="px-3 py-1 rounded-full text-sm font-semibold" 
                                              :class="getPaymentClass(selectedPurchase.payment_status)" 
                                              x-text="getPaymentText(selectedPurchase.payment_status)"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Supplier Info -->
                        <div class="bg-gradient-to-r from-gray-50 to-gray-100 rounded-2xl p-6 mb-6 border border-gray-200">
                            <div class="flex items-center gap-3 mb-4">
                                <div class="w-8 h-8 bg-[#28C328] rounded-lg flex items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                </div>
                                <h4 class="font-bold text-lg text-gray-800">Informasi Supplier</h4>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div class="bg-white rounded-xl p-4 border border-gray-200">
                                    <div class="text-xs text-gray-500 font-medium uppercase tracking-wide mb-1">Nama Supplier</div>
                                    <div class="font-bold text-gray-800" x-text="selectedPurchase.supplier_name"></div>
                                </div>
                                <div class="bg-white rounded-xl p-4 border border-gray-200">
                                    <div class="text-xs text-gray-500 font-medium uppercase tracking-wide mb-1">Kontak</div>
                                    <div class="text-gray-800 font-medium" x-text="selectedPurchase.supplier_contact || '-'"></div>
                                </div>
                                <div class="bg-white rounded-xl p-4 border border-gray-200">
                                    <div class="text-xs text-gray-500 font-medium uppercase tracking-wide mb-1">No. Invoice</div>
                                    <div class="text-gray-800 font-medium" x-text="selectedPurchase.invoice_number || '-'"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Items Table -->
                    <div class="mb-8">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-8 h-8 bg-[#28C328] rounded-lg flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                </svg>
                            </div>
                            <h3 class="font-bold text-xl text-gray-800">Items Pembelian</h3>
                        </div>
                        <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-sm">
                            <div class="overflow-x-auto">
                                <table class="min-w-full">
                                    <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                                        <tr>
                                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Item</th>
                                            <th class="px-6 py-4 text-center text-xs font-bold text-gray-600 uppercase tracking-wider">SKU</th>
                                            <th class="px-6 py-4 text-center text-xs font-bold text-gray-600 uppercase tracking-wider">Kategori</th>
                                            <th class="px-6 py-4 text-center text-xs font-bold text-gray-600 uppercase tracking-wider">Quantity</th>
                                            <th class="px-6 py-4 text-center text-xs font-bold text-gray-600 uppercase tracking-wider">Unit</th>
                                            <th class="px-6 py-4 text-right text-xs font-bold text-gray-600 uppercase tracking-wider">Harga Satuan</th>
                                            <th class="px-6 py-4 text-right text-xs font-bold text-gray-600 uppercase tracking-wider">Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100">
                                        <template x-for="item in selectedPurchase.items" :key="item.id">
                                            <tr class="hover:bg-gray-50 transition-colors">
                                                <td class="px-6 py-4">
                                                    <div class="flex items-center gap-3">
                                                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                                            </svg>
                                                        </div>
                                                        <div>
                                                            <div class="font-bold text-gray-800" x-text="item.item_name"></div>
                                                            <div class="text-xs text-gray-500" x-text="item.item_description || ''"></div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 text-center">
                                                    <span class="font-mono text-sm text-gray-600 bg-gray-100 px-2 py-1 rounded" x-text="item.sku || '-'"></span>
                                                </td>
                                                <td class="px-6 py-4 text-center">
                                                    <span class="text-sm text-gray-600" x-text="item.category || '-'"></span>
                                                </td>
                                                <td class="px-6 py-4 text-center">
                                                    <span class="font-bold text-lg text-gray-800" x-text="item.quantity"></span>
                                                </td>
                                                <td class="px-6 py-4 text-center">
                                                    <span class="text-sm text-gray-600" x-text="item.unit"></span>
                                                </td>
                                                <td class="px-6 py-4 text-right">
                                                    <span class="font-semibold text-gray-800">Rp<span x-text="formatNumber(item.unit_price)"></span></span>
                                                </td>
                                                <td class="px-6 py-4 text-right">
                                                    <span class="font-bold text-lg text-[#28C328]">Rp<span x-text="formatNumber(item.subtotal)"></span></span>
                                                </td>
                                            </tr>
                                        </template>
                                    </tbody>
                                    <tfoot class="bg-gradient-to-r from-[#28C328] to-[#22a322]">
                                        <tr>
                                            <td colspan="6" class="px-6 py-4 text-right font-bold text-white text-lg">Total:</td>
                                            <td class="px-6 py-4 text-right font-bold text-white text-2xl">Rp<span x-text="formatNumber(selectedPurchase.total_amount)"></span></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex justify-end gap-4 pt-6 border-t border-gray-200">
                        <template x-if="selectedPurchase.status === 'pending'">
                            <div class="flex gap-3">
                                <button @click="approvePurchase(selectedPurchase.id)" class="px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-xl hover:from-blue-700 hover:to-blue-800 transition-all duration-200 font-semibold flex items-center gap-2 shadow-lg hover:shadow-xl transform hover:scale-105">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Approve
                                </button>
                                <button @click="rejectPurchase(selectedPurchase.id)" class="px-6 py-3 bg-gradient-to-r from-red-600 to-red-700 text-white rounded-xl hover:from-red-700 hover:to-red-800 transition-all duration-200 font-semibold flex items-center gap-2 shadow-lg hover:shadow-xl transform hover:scale-105">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                    Reject
                                </button>
                            </div>
                        </template>
                        <template x-if="selectedPurchase.status === 'approved'">
                            <button @click="completePurchase(selectedPurchase.id)" class="px-6 py-3 bg-gradient-to-r from-green-600 to-green-700 text-white rounded-xl hover:from-green-700 hover:to-green-800 transition-all duration-200 font-semibold flex items-center gap-2 shadow-lg hover:shadow-xl transform hover:scale-105">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Mark as Completed
                            </button>
                        </template>
                        <button @click="showDetailModal = false" class="px-6 py-3 bg-gradient-to-r from-gray-200 to-gray-300 text-gray-700 rounded-xl hover:from-gray-300 hover:to-gray-400 transition-all duration-200 font-semibold flex items-center gap-2 shadow-lg hover:shadow-xl transform hover:scale-105">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            Tutup
                        </button>
                    </div>
                </template>
            </div>
        </div>
    </div>
</div>

<script>
function purchaseManagement() {
    return {
        // Data
        search: '',
        statusFilter: '',
        paymentFilter: '',
        dateFilter: '',
        customStartDate: '',
        customEndDate: '',
        showCreateModal: false,
        showDetailModal: false,
        selectedPurchase: null,
        purchases: [],
        
        // Form data
        purchaseData: {
            purchase_number: '',
            supplier_name: '',
            supplier_contact: '',
            invoice_number: '',
            purchase_date: '',
            due_date: '',
            notes: '',
            items: []
        },
        
        async init() {
            await this.loadPurchases();
            this.generatePurchaseNumber();
            this.setPurchaseDate();
        },
        
        async loadPurchases() {
            try {
                const response = await fetch('/admin/purchases/api', {
                    headers: { 'Accept': 'application/json' }
                });
                if (response.ok) {
                    this.purchases = await response.json();
                }
            } catch (error) {
                console.error('Error loading purchases:', error);
            }
        },
        
        generatePurchaseNumber() {
            const now = new Date();
            const year = now.getFullYear();
            const month = String(now.getMonth() + 1).padStart(2, '0');
            const day = String(now.getDate()).padStart(2, '0');
            const random = Math.floor(Math.random() * 1000).toString().padStart(3, '0');
            this.purchaseData.purchase_number = `PUR-${year}${month}${day}-${random}`;
        },
        
        setPurchaseDate() {
            this.purchaseData.purchase_date = new Date().toISOString().split('T')[0];
        },
        
        get filteredPurchases() {
            let purchases = this.purchases;
            
            if (this.search) {
                purchases = purchases.filter(purchase => 
                    purchase.purchase_number.toLowerCase().includes(this.search.toLowerCase()) ||
                    purchase.supplier_name.toLowerCase().includes(this.search.toLowerCase()) ||
                    purchase.invoice_number?.toLowerCase().includes(this.search.toLowerCase()) ||
                    purchase.items.some(item => item.item_name.toLowerCase().includes(this.search.toLowerCase()))
                );
            }
            
            if (this.statusFilter) {
                purchases = purchases.filter(purchase => purchase.status === this.statusFilter);
            }
            
            if (this.paymentFilter) {
                purchases = purchases.filter(purchase => purchase.payment_status === this.paymentFilter);
            }
            
            if (this.dateFilter) {
                purchases = this.applyDateFilterToPurchases(purchases);
            }
            
            return purchases;
        },
        
        get totalAmount() {
            return this.purchaseData.items.reduce((total, item) => {
                return total + (item.quantity * item.unit_price);
            }, 0);
        },
        
        addNewItem() {
            this.purchaseData.items.push({
                item_name: '',
                item_description: '',
                sku: '',
                category: '',
                quantity: 1,
                unit: 'pcs',
                unit_price: 0,
                subtotal: 0
            });
        },
        
        removeItem(index) {
            this.purchaseData.items.splice(index, 1);
        },
        
        updateItemSubtotal(index) {
            const item = this.purchaseData.items[index];
            item.subtotal = item.quantity * item.unit_price;
        },
        
        async submitPurchase() {
            if (this.purchaseData.items.length === 0) return;
            
            // Update subtotals
            this.purchaseData.items.forEach(item => {
                item.subtotal = item.quantity * item.unit_price;
            });
            
            try {
                const purchaseData = {
                    ...this.purchaseData,
                    total_amount: this.totalAmount
                };
                
                const response = await fetch('/admin/purchases', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content')
                    },
                    body: JSON.stringify(purchaseData)
                });
                
                if (response.ok) {
                    await this.loadPurchases();
                    this.showCreateModal = false;
                    this.resetForm();
                    this.showSuccessNotification('Pembelian berhasil dibuat!');
                } else {
                    throw new Error('Failed to create purchase');
                }
            } catch (error) {
                console.error('Error creating purchase:', error);
                this.showErrorNotification('Gagal membuat pembelian');
            }
        },
        
        resetForm() {
            this.purchaseData = {
                purchase_number: '',
                supplier_name: '',
                supplier_contact: '',
                invoice_number: '',
                purchase_date: '',
                due_date: '',
                notes: '',
                items: []
            };
            this.generatePurchaseNumber();
            this.setPurchaseDate();
        },
        
        viewPurchaseDetail(purchase) {
            this.selectedPurchase = purchase;
            this.showDetailModal = true;
        },
        
        async approvePurchase(purchaseId) {
            if (!confirm('Apakah Anda yakin ingin approve pembelian ini?')) return;
            
            try {
                const response = await fetch(`/admin/purchases/${purchaseId}/approve`, {
                    method: 'PUT',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content')
                    }
                });
                
                if (response.ok) {
                    await this.loadPurchases();
                    this.showDetailModal = false;
                    this.showSuccessNotification('Pembelian berhasil diapprove!');
                }
            } catch (error) {
                console.error('Error approving purchase:', error);
                this.showErrorNotification('Gagal approve pembelian');
            }
        },
        
        async rejectPurchase(purchaseId) {
            if (!confirm('Apakah Anda yakin ingin reject pembelian ini?')) return;
            
            try {
                const response = await fetch(`/admin/purchases/${purchaseId}/reject`, {
                    method: 'PUT',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content')
                    }
                });
                
                if (response.ok) {
                    await this.loadPurchases();
                    this.showDetailModal = false;
                    this.showSuccessNotification('Pembelian berhasil direject!');
                }
            } catch (error) {
                console.error('Error rejecting purchase:', error);
                this.showErrorNotification('Gagal reject pembelian');
            }
        },
        
        async completePurchase(purchaseId) {
            if (!confirm('Apakah Anda yakin ingin menandai pembelian ini sebagai completed?')) return;
            
            try {
                const response = await fetch(`/admin/purchases/${purchaseId}/complete`, {
                    method: 'PUT',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content')
                    }
                });
                
                if (response.ok) {
                    await this.loadPurchases();
                    this.showDetailModal = false;
                    this.showSuccessNotification('Pembelian berhasil ditandai sebagai completed!');
                }
            } catch (error) {
                console.error('Error completing purchase:', error);
                this.showErrorNotification('Gagal update status pembelian');
            }
        },

        async returnPurchase(purchaseId) {
            if (!confirm('Tandai pembelian ini sebagai Return (Paid)?')) return;
            try {
                const response = await fetch(`/admin/purchases/${purchaseId}/returned`, {
                    method: 'PUT',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content')
                    }
                });
                if (response.ok) {
                    await this.loadPurchases();
                    this.showSuccessNotification('Pembelian ditandai Return dan Paid');
                }
            } catch (error) {
                console.error('Error marking return:', error);
                this.showErrorNotification('Gagal set Return');
            }
        },
        
        getStatusClass(status) {
            const classes = {
                'pending': 'bg-yellow-100 text-yellow-800',
                'approved': 'bg-blue-100 text-blue-800',
                'rejected': 'bg-red-100 text-red-800',
                'completed': 'bg-green-100 text-green-800',
                'returned': 'bg-purple-100 text-purple-800'
            };
            return classes[status] || 'bg-gray-100 text-gray-800';
        },
        
        getStatusText(status) {
            const texts = {
                'pending': 'Pending',
                'approved': 'Approved',
                'rejected': 'Rejected',
                'completed': 'Completed',
                'returned': 'Returned'
            };
            return texts[status] || status;
        },
        
        getPaymentClass(status) {
            const classes = {
                'unpaid': 'bg-red-100 text-red-800',
                'partial': 'bg-orange-100 text-orange-800',
                'paid': 'bg-green-100 text-green-800'
            };
            return classes[status] || 'bg-gray-100 text-gray-800';
        },
        
        getPaymentText(status) {
            const texts = {
                'unpaid': 'Unpaid',
                'partial': 'Partial',
                'paid': 'Paid'
            };
            return texts[status] || status;
        },
        
        formatDate(dateString) {
            return new Date(dateString).toLocaleDateString('id-ID');
        },
        
        formatNumber(number) {
            return Number(number).toLocaleString('id-ID');
        },
        
        // Date filtering functions
        applyDateFilter() {
            // Trigger reactivity
        },
        
        clearDateFilter() {
            this.dateFilter = '';
            this.customStartDate = '';
            this.customEndDate = '';
        },
        
        getFilterStatusText() {
            switch (this.dateFilter) {
                case 'today': return 'Menampilkan pembelian hari ini';
                case 'week': return 'Menampilkan pembelian minggu ini';
                case 'month': return 'Menampilkan pembelian bulan ini';
                case 'year': return 'Menampilkan pembelian tahun ini';
                case 'custom':
                    if (this.customStartDate && this.customEndDate) {
                        const start = new Date(this.customStartDate).toLocaleDateString('id-ID');
                        const end = new Date(this.customEndDate).toLocaleDateString('id-ID');
                        return `Menampilkan pembelian dari ${start} sampai ${end}`;
                    }
                    return 'Menampilkan pembelian berdasarkan rentang tanggal custom';
                default: return '';
            }
        },
        
        applyDateFilterToPurchases(purchases) {
            if (!this.dateFilter) return purchases;
            const now = new Date();
            let startDate, endDate;
            
            switch (this.dateFilter) {
                case 'today':
                    startDate = new Date(now.getFullYear(), now.getMonth(), now.getDate());
                    endDate = new Date(now.getFullYear(), now.getMonth(), now.getDate(), 23, 59, 59);
                    break;
                case 'week':
                    const startOfWeek = new Date(now);
                    startOfWeek.setDate(now.getDate() - now.getDay());
                    startOfWeek.setHours(0, 0, 0, 0);
                    startDate = startOfWeek;
                    endDate = new Date(startOfWeek);
                    endDate.setDate(startOfWeek.getDate() + 6);
                    endDate.setHours(23, 59, 59, 999);
                    break;
                case 'month':
                    startDate = new Date(now.getFullYear(), now.getMonth(), 1);
                    endDate = new Date(now.getFullYear(), now.getMonth() + 1, 0, 23, 59, 59);
                    break;
                case 'year':
                    startDate = new Date(now.getFullYear(), 0, 1);
                    endDate = new Date(now.getFullYear(), 11, 31, 23, 59, 59);
                    break;
                case 'custom':
                    if (!this.customStartDate || !this.customEndDate) return purchases;
                    startDate = new Date(this.customStartDate);
                    endDate = new Date(this.customEndDate);
                    endDate.setHours(23, 59, 59, 999);
                    break;
                default: return purchases;
            }
            
            return purchases.filter(purchase => {
                const purchaseDate = new Date(purchase.purchase_date);
                return purchaseDate >= startDate && purchaseDate <= endDate;
            });
        },
        
        // Notification methods
        showSuccessNotification(message) {
            window.dispatchEvent(new CustomEvent('show-notification', {
                detail: { type: 'success', message: message }
            }));
        },
        
        showErrorNotification(message) {
            window.dispatchEvent(new CustomEvent('show-notification', {
                detail: { type: 'error', message: message }
            }));
        }
    }
}
</script>
@endsection
