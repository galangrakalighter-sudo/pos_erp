<!-- Help System Component -->
<div x-data="helpSystem()" x-init="init()" class="help-system">
    <!-- Floating Help Button -->
    <button @click="toggleHelp()" 
            class="fixed bottom-6 right-6 z-50 bg-[#28C328] hover:bg-[#22A022] text-white rounded-full w-12 h-12 shadow-lg transition-all duration-200 flex items-center justify-center group"
            :class="{ 'rotate-45': showHelp }"
            title="Bantuan / Help">
        <svg class="w-6 h-6 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
    </button>

    <!-- Help Modal -->
    <div x-show="showHelp" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="fixed inset-0 z-50 overflow-y-auto"
         style="display: none;">
        
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-black bg-opacity-20 transition-opacity z-40" @click="closeHelp()"></div>
        
        <!-- Modal Content -->
        <div class="flex items-center justify-center min-h-screen p-4 relative z-50">
            <div class="bg-white rounded-xl shadow-2xl max-w-4xl w-full max-h-[85vh] flex flex-col">
                <!-- Header -->
                <div class="bg-gradient-to-r from-[#28C328] to-[#22A022] text-white p-6 flex-shrink-0">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <div>
                                <h2 class="text-xl font-bold" x-text="currentPage.title || 'Panduan Sistem'"></h2>
                                <p class="text-sm opacity-90" x-text="currentPage.subtitle || 'Bantuan penggunaan'"></p>
                            </div>
                        </div>
                        <button @click="closeHelp()" class="text-white hover:text-gray-200 transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Language Toggle -->
                <div class="bg-gray-50 px-6 py-3 border-b flex-shrink-0">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <button @click="setLanguage('id')" 
                                    class="px-3 py-1 rounded-md text-sm font-medium transition-colors"
                                    :class="language === 'id' ? 'bg-[#28C328] text-white' : 'bg-white text-gray-600 hover:bg-gray-100'">
                                🇮🇩 Indonesia
                            </button>
                            <button @click="setLanguage('en')" 
                                    class="px-3 py-1 rounded-md text-sm font-medium transition-colors"
                                    :class="language === 'en' ? 'bg-[#28C328] text-white' : 'bg-white text-gray-600 hover:bg-gray-100'">
                                🇺🇸 English
                            </button>
                        </div>
                        <button @click="skipHelp()" class="text-sm text-gray-500 hover:text-gray-700 transition-colors">
                            <span x-text="language === 'id' ? 'Lewati' : 'Skip'"></span>
                        </button>
                    </div>
                </div>

                <!-- Content -->
                <div class="p-6 overflow-y-auto flex-1 min-h-0">
                    <div class="space-y-6">
                        <template x-for="(section, index) in currentPage.sections || []" :key="index">
                            <div class="help-section">
                                <div class="flex items-start gap-4">
                                    <div class="flex-shrink-0 w-10 h-10 bg-[#28C328] bg-opacity-10 rounded-lg flex items-center justify-center">
                                        <svg class="w-5 h-5 text-[#28C328]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" x-attr:d="section.icon"></path>
                                        </svg>
                                    </div>
                                    <div class="flex-1">
                                        <h3 class="text-lg font-semibold text-gray-800 mb-2" x-text="section.title"></h3>
                                        <div class="text-gray-600 leading-relaxed space-y-3" x-html="section.content"></div>
                                    </div>
                                </div>
                            </div>
                        </template>
                        
                        <!-- Fallback content if no sections -->
                        <template x-if="!currentPage.sections || currentPage.sections.length === 0">
                            <div class="text-center py-8">
                                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-800 mb-2">Panduan Sedang Dimuat</h3>
                                <p class="text-gray-600">Silakan tunggu sebentar...</p>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Footer -->
                <div class="bg-gray-50 px-6 py-4 border-t flex-shrink-0">
                    <div class="flex items-center justify-between">
                        <div class="text-sm text-gray-500">
                            <span x-text="language === 'id' ? 'Panduan ini akan membantu Anda menggunakan sistem dengan lebih efektif.' : 'This guide will help you use the system more effectively.'"></span>
                        </div>
                        <button @click="closeHelp()" 
                                class="bg-[#28C328] hover:bg-[#22A022] text-white px-6 py-2 rounded-lg font-medium transition-colors">
                            <span x-text="language === 'id' ? 'Mengerti' : 'Got it'"></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function helpSystem() {
    return {
        showHelp: false,
        language: localStorage.getItem('help_language') || 'id',
        currentPage: {},
        
        init() {
            this.loadLanguagePreference();
            this.loadPageHelp();
        },
        
        toggleHelp() {
            this.showHelp = !this.showHelp;
            if (this.showHelp) {
                document.body.style.overflow = 'hidden';
                document.documentElement.style.overflow = 'hidden';
                // Reload page help when opening modal
                this.loadPageHelp();
            } else {
                document.body.style.overflow = '';
                document.documentElement.style.overflow = '';
            }
        },
        
        closeHelp() {
            this.showHelp = false;
            document.body.style.overflow = '';
            document.documentElement.style.overflow = '';
        },
        
        setLanguage(lang) {
            this.language = lang;
            localStorage.setItem('help_language', lang);
            this.loadPageHelp();
        },
        
        skipHelp() {
            this.closeHelp();
            // Mark as skipped for this session
            sessionStorage.setItem('help_skipped', 'true');
        },
        
        loadLanguagePreference() {
            const savedLang = localStorage.getItem('help_language');
            if (savedLang) {
                this.language = savedLang;
            }
        },
        
        loadPageHelp() {
            const currentPath = window.location.pathname;
            const pageData = this.getPageHelp(currentPath);
            this.currentPage = pageData[this.language] || pageData.id;
        },
        
        getPageHelp(path) {
            const helpData = {
                '/admin/overview': {
                    id: {
                        title: 'Dashboard Admin',
                        subtitle: 'Panduan lengkap untuk mengelola sistem GAFI',
                        sections: [
                            {
                                title: 'Kartu KPI',
                                icon: 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z',
                                content: `
                                    <p>Kartu KPI menampilkan informasi penting dalam format yang mudah dibaca:</p>
                                    <ul class="list-disc list-inside space-y-1 mt-2">
                                        <li><strong>Total Client:</strong> Jumlah mitra yang terdaftar</li>
                                        <li><strong>Total Item Stok:</strong> Jumlah produk dalam sistem</li>
                                        <li><strong>Item Low Stock:</strong> Produk dengan stok di bawah batas minimum</li>
                                        <li><strong>Transaksi (Rentang):</strong> Jumlah transaksi dalam periode yang dipilih</li>
                                        <li><strong>Omzet (Rentang):</strong> Total pendapatan dalam periode yang dipilih</li>
                                        <li><strong>Belum Dibayar:</strong> Transaksi yang masih pending pembayaran</li>
                                    </ul>
                                `
                            },
                            {
                                title: 'Filter Periode',
                                icon: 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',
                                content: `
                                    <p>Gunakan filter periode untuk melihat data dalam rentang waktu tertentu:</p>
                                    <ul class="list-disc list-inside space-y-1 mt-2">
                                        <li><strong>Minggu:</strong> Data 7 hari terakhir</li>
                                        <li><strong>Bulan:</strong> Data 30 hari terakhir</li>
                                        <li><strong>Tahun:</strong> Data 12 bulan terakhir</li>
                                    </ul>
                                    <p class="mt-3">Filter ini akan mempengaruhi grafik omzet dan kartu KPI.</p>
                                `
                            },
                            {
                                title: 'Threshold Low Stock',
                                icon: 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z',
                                content: `
                                    <p>Atur batas minimum stok untuk mendapatkan notifikasi:</p>
                                    <ul class="list-disc list-inside space-y-1 mt-2">
                                        <li><strong>Admin low stock threshold:</strong> Batas untuk stok admin/pusat</li>
                                        <li><strong>Client low stock threshold:</strong> Batas untuk stok mitra/client</li>
                                    </ul>
                                    <p class="mt-3">Sistem akan menampilkan peringatan ketika stok turun di bawah batas yang ditentukan.</p>
                                `
                            },
                            {
                                title: 'Target Omzet',
                                icon: 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6',
                                content: `
                                    <p>Setel target omzet untuk monitoring performa:</p>
                                    <ul class="list-disc list-inside space-y-1 mt-2">
                                        <li>Masukkan target dalam format angka (tanpa titik atau koma)</li>
                                        <li>Target akan ditampilkan sebagai garis putus-putus pada grafik</li>
                                        <li>Bandingkan omzet aktual dengan target yang telah ditentukan</li>
                                    </ul>
                                `
                            }
                        ]
                    },
                    en: {
                        title: 'Admin Dashboard',
                        subtitle: 'Complete guide for managing GAFI system',
                        sections: [
                            {
                                title: 'KPI Cards',
                                icon: 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z',
                                content: `
                                    <p>KPI cards display important information in an easy-to-read format:</p>
                                    <ul class="list-disc list-inside space-y-1 mt-2">
                                        <li><strong>Total Clients:</strong> Number of registered partners</li>
                                        <li><strong>Total Stock Items:</strong> Number of products in the system</li>
                                        <li><strong>Low Stock Items:</strong> Products with stock below minimum threshold</li>
                                        <li><strong>Transactions (Range):</strong> Number of transactions in selected period</li>
                                        <li><strong>Revenue (Range):</strong> Total income in selected period</li>
                                        <li><strong>Unpaid:</strong> Transactions still pending payment</li>
                                    </ul>
                                `
                            },
                            {
                                title: 'Period Filter',
                                icon: 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',
                                content: `
                                    <p>Use period filter to view data within specific time ranges:</p>
                                    <ul class="list-disc list-inside space-y-1 mt-2">
                                        <li><strong>Week:</strong> Last 7 days data</li>
                                        <li><strong>Month:</strong> Last 30 days data</li>
                                        <li><strong>Year:</strong> Last 12 months data</li>
                                    </ul>
                                    <p class="mt-3">This filter will affect the revenue chart and KPI cards.</p>
                                `
                            },
                            {
                                title: 'Low Stock Threshold',
                                icon: 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z',
                                content: `
                                    <p>Set minimum stock limits to receive notifications:</p>
                                    <ul class="list-disc list-inside space-y-1 mt-2">
                                        <li><strong>Admin low stock threshold:</strong> Limit for admin/central stock</li>
                                        <li><strong>Client low stock threshold:</strong> Limit for partner/client stock</li>
                                    </ul>
                                    <p class="mt-3">The system will display warnings when stock falls below the specified limit.</p>
                                `
                            },
                            {
                                title: 'Revenue Target',
                                icon: 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6',
                                content: `
                                    <p>Set revenue targets for performance monitoring:</p>
                                    <ul class="list-disc list-inside space-y-1 mt-2">
                                        <li>Enter target in number format (without dots or commas)</li>
                                        <li>Target will be displayed as a dashed line on the chart</li>
                                        <li>Compare actual revenue with the set target</li>
                                    </ul>
                                `
                            }
                        ]
                    }
                },
                '/admin/dashboard': {
                    id: {
                        title: 'Manajemen Stok Admin',
                        subtitle: 'Kelola produk dan persediaan pusat',
                        sections: [
                            {
                                title: 'Daftar Produk',
                                icon: 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4',
                                content: `
                                    <p>Halaman ini menampilkan semua produk yang dikelola oleh admin/pusat:</p>
                                    <ul class="list-disc list-inside space-y-1 mt-2">
                                        <li><strong>Nama Produk:</strong> Nama lengkap produk</li>
                                        <li><strong>SKU:</strong> Kode unik produk</li>
                                        <li><strong>Tersedia:</strong> Jumlah stok saat ini</li>
                                        <li><strong>Harga:</strong> Harga jual produk</li>
                                        <li><strong>ID Client:</strong> ID unik untuk identifikasi client</li>
                                    </ul>
                                `
                            },
                            {
                                title: 'Tambah Produk Baru',
                                icon: 'M12 6v6m0 0v6m0-6h6m-6 0H6',
                                content: `
                                    <p>Untuk menambahkan produk baru:</p>
                                    <ol class="list-decimal list-inside space-y-1 mt-2">
                                        <li>Klik tombol "Tambah Item" di bagian atas</li>
                                        <li>Isi nama produk, stok tersedia, dan harga</li>
                                        <li>ID Client akan di-generate otomatis</li>
                                        <li>Klik "Simpan" untuk menyimpan produk</li>
                                    </ol>
                                `
                            },
                            {
                                title: 'Edit dan Hapus',
                                icon: 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z',
                                content: `
                                    <p>Mengelola produk yang sudah ada:</p>
                                    <ul class="list-disc list-inside space-y-1 mt-2">
                                        <li><strong>Edit:</strong> Klik ikon pensil untuk mengubah data produk</li>
                                        <li><strong>Hapus:</strong> Klik ikon tempat sampah untuk menghapus produk</li>
                                        <li><strong>Detail:</strong> Klik pada baris untuk melihat detail lengkap</li>
                                    </ul>
                                `
                            }
                        ]
                    },
                    en: {
                        title: 'Admin Stock Management',
                        subtitle: 'Manage central products and inventory',
                        sections: [
                            {
                                title: 'Product List',
                                icon: 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4',
                                content: `
                                    <p>This page displays all products managed by admin/central:</p>
                                    <ul class="list-disc list-inside space-y-1 mt-2">
                                        <li><strong>Product Name:</strong> Full product name</li>
                                        <li><strong>SKU:</strong> Unique product code</li>
                                        <li><strong>Available:</strong> Current stock quantity</li>
                                        <li><strong>Price:</strong> Product selling price</li>
                                        <li><strong>Client ID:</strong> Unique ID for client identification</li>
                                    </ul>
                                `
                            },
                            {
                                title: 'Add New Product',
                                icon: 'M12 6v6m0 0v6m0-6h6m-6 0H6',
                                content: `
                                    <p>To add a new product:</p>
                                    <ol class="list-decimal list-inside space-y-1 mt-2">
                                        <li>Click "Add Item" button at the top</li>
                                        <li>Fill in product name, available stock, and price</li>
                                        <li>Client ID will be auto-generated</li>
                                        <li>Click "Save" to store the product</li>
                                    </ol>
                                `
                            },
                            {
                                title: 'Edit and Delete',
                                icon: 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z',
                                content: `
                                    <p>Managing existing products:</p>
                                    <ul class="list-disc list-inside space-y-1 mt-2">
                                        <li><strong>Edit:</strong> Click pencil icon to modify product data</li>
                                        <li><strong>Delete:</strong> Click trash icon to remove product</li>
                                        <li><strong>Detail:</strong> Click on row to view complete details</li>
                                    </ul>
                                `
                            }
                        ]
                    }
                },
                '/client/dashboard': {
                    id: {
                        title: 'Dashboard Mitra',
                        subtitle: 'Panduan lengkap untuk mitra GAFI',
                        sections: [
                            {
                                title: 'Kartu KPI',
                                icon: 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z',
                                content: `
                                    <p>Kartu KPI menampilkan performa bisnis Anda:</p>
                                    <ul class="list-disc list-inside space-y-1 mt-2">
                                        <li><strong>Transaksi Hari Ini:</strong> Jumlah transaksi hari ini</li>
                                        <li><strong>Omzet Hari Ini:</strong> Total pendapatan hari ini</li>
                                        <li><strong>Item Unik:</strong> Jumlah produk yang Anda kelola</li>
                                        <li><strong>Total Stok (pcs):</strong> Total stok semua produk</li>
                                        <li><strong>Transaksi (Rentang):</strong> Transaksi dalam periode yang dipilih</li>
                                        <li><strong>Belum Dibayar:</strong> Transaksi yang masih pending</li>
                                    </ul>
                                `
                            },
                            {
                                title: 'Grafik Omzet',
                                icon: 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6',
                                content: `
                                    <p>Grafik omzet membantu Anda memantau performa penjualan:</p>
                                    <ul class="list-disc list-inside space-y-1 mt-2">
                                        <li><strong>Filter Periode:</strong> Pilih 7 Hari, 30 Hari, atau 12 Bulan</li>
                                        <li><strong>Target Omzet:</strong> Setel target untuk monitoring performa</li>
                                        <li><strong>Garis Target:</strong> Bandingkan omzet aktual dengan target</li>
                                        <li><strong>Data Real-time:</strong> Grafik update otomatis saat ada transaksi baru</li>
                                    </ul>
                                `
                            },
                            {
                                title: 'Aktivitas Terakhir',
                                icon: 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
                                content: `
                                    <p>Monitor aktivitas transaksi terbaru:</p>
                                    <ul class="list-disc list-inside space-y-1 mt-2">
                                        <li><strong>Riwayat Transaksi:</strong> Lihat transaksi terbaru</li>
                                        <li><strong>Detail Waktu:</strong> Tanggal dan waktu transaksi</li>
                                        <li><strong>Quick Access:</strong> Klik "Lihat riwayat" untuk detail lengkap</li>
                                    </ul>
                                `
                            }
                        ]
                    },
                    en: {
                        title: 'Partner Dashboard',
                        subtitle: 'Complete guide for GAFI partners',
                        sections: [
                            {
                                title: 'KPI Cards',
                                icon: 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z',
                                content: `
                                    <p>KPI cards display your business performance:</p>
                                    <ul class="list-disc list-inside space-y-1 mt-2">
                                        <li><strong>Today's Transactions:</strong> Number of transactions today</li>
                                        <li><strong>Today's Revenue:</strong> Total income today</li>
                                        <li><strong>Unique Items:</strong> Number of products you manage</li>
                                        <li><strong>Total Stock (pcs):</strong> Total stock of all products</li>
                                        <li><strong>Transactions (Range):</strong> Transactions in selected period</li>
                                        <li><strong>Unpaid:</strong> Transactions still pending</li>
                                    </ul>
                                `
                            },
                            {
                                title: 'Revenue Chart',
                                icon: 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6',
                                content: `
                                    <p>Revenue chart helps you monitor sales performance:</p>
                                    <ul class="list-disc list-inside space-y-1 mt-2">
                                        <li><strong>Period Filter:</strong> Choose 7 Days, 30 Days, or 12 Months</li>
                                        <li><strong>Revenue Target:</strong> Set target for performance monitoring</li>
                                        <li><strong>Target Line:</strong> Compare actual revenue with target</li>
                                        <li><strong>Real-time Data:</strong> Chart updates automatically with new transactions</li>
                                    </ul>
                                `
                            },
                            {
                                title: 'Recent Activities',
                                icon: 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
                                content: `
                                    <p>Monitor latest transaction activities:</p>
                                    <ul class="list-disc list-inside space-y-1 mt-2">
                                        <li><strong>Transaction History:</strong> View latest transactions</li>
                                        <li><strong>Time Details:</strong> Date and time of transactions</li>
                                        <li><strong>Quick Access:</strong> Click "View history" for complete details</li>
                                    </ul>
                                `
                            }
                        ]
                    }
                },
                '/client/cashier': {
                    id: {
                        title: 'Sistem Kasir (POS)',
                        subtitle: 'Panduan lengkap untuk transaksi penjualan',
                        sections: [
                            {
                                title: 'Daftar Produk',
                                icon: 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4',
                                content: `
                                    <p>Bagian kiri menampilkan semua produk yang tersedia:</p>
                                    <ul class="list-disc list-inside space-y-1 mt-2">
                                        <li><strong>Pencarian:</strong> Ketik nama produk untuk mencari cepat</li>
                                        <li><strong>Kategori:</strong> Filter berdasarkan kategori produk</li>
                                        <li><strong>Stok:</strong> Lihat stok tersedia di setiap produk</li>
                                        <li><strong>Tambah ke Keranjang:</strong> Klik produk untuk menambahkan ke keranjang</li>
                                    </ul>
                                `
                            },
                            {
                                title: 'Keranjang Belanja',
                                icon: 'M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m6-5v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6m6 0V9a2 2 0 00-2-2H9a2 2 0 00-2 2v4.01',
                                content: `
                                    <p>Bagian tengah adalah keranjang belanja:</p>
                                    <ul class="list-disc list-inside space-y-1 mt-2">
                                        <li><strong>Daftar Item:</strong> Produk yang dipilih untuk dibeli</li>
                                        <li><strong>Kuantitas:</strong> Gunakan +/- untuk mengubah jumlah</li>
                                        <li><strong>Harga:</strong> Harga per item dan total</li>
                                        <li><strong>Hapus:</strong> Klik ikon tempat sampah untuk menghapus item</li>
                                    </ul>
                                `
                            },
                            {
                                title: 'Pembayaran',
                                icon: 'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z',
                                content: `
                                    <p>Bagian kanan untuk proses pembayaran:</p>
                                    <ul class="list-disc list-inside space-y-1 mt-2">
                                        <li><strong>Total Belanja:</strong> Total harga semua item</li>
                                        <li><strong>Diskon:</strong> Masukkan diskon jika ada</li>
                                        <li><strong>Total Bayar:</strong> Total setelah diskon</li>
                                        <li><strong>Bayar:</strong> Klik untuk menyelesaikan transaksi</li>
                                        <li><strong>Reset:</strong> Klik untuk mengosongkan keranjang</li>
                                    </ul>
                                `
                            },
                            {
                                title: 'Tips Penggunaan',
                                icon: 'M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z',
                                content: `
                                    <p>Beberapa tips untuk penggunaan yang efisien:</p>
                                    <ul class="list-disc list-inside space-y-1 mt-2">
                                        <li><strong>Keyboard Shortcut:</strong> Gunakan Enter untuk menambah item ke keranjang</li>
                                        <li><strong>Quick Search:</strong> Ketik langsung untuk mencari produk</li>
                                        <li><strong>Stock Alert:</strong> Sistem akan memperingatkan jika stok tidak cukup</li>
                                        <li><strong>Auto Save:</strong> Transaksi otomatis tersimpan ke riwayat</li>
                                    </ul>
                                `
                            }
                        ]
                    },
                    en: {
                        title: 'Cashier System (POS)',
                        subtitle: 'Complete guide for sales transactions',
                        sections: [
                            {
                                title: 'Product List',
                                icon: 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4',
                                content: `
                                    <p>The left section displays all available products:</p>
                                    <ul class="list-disc list-inside space-y-1 mt-2">
                                        <li><strong>Search:</strong> Type product name for quick search</li>
                                        <li><strong>Category:</strong> Filter by product category</li>
                                        <li><strong>Stock:</strong> View available stock for each product</li>
                                        <li><strong>Add to Cart:</strong> Click product to add to cart</li>
                                    </ul>
                                `
                            },
                            {
                                title: 'Shopping Cart',
                                icon: 'M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m6-5v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6m6 0V9a2 2 0 00-2-2H9a2 2 0 00-2 2v4.01',
                                content: `
                                    <p>The middle section is the shopping cart:</p>
                                    <ul class="list-disc list-inside space-y-1 mt-2">
                                        <li><strong>Item List:</strong> Products selected for purchase</li>
                                        <li><strong>Quantity:</strong> Use +/- to change quantity</li>
                                        <li><strong>Price:</strong> Price per item and total</li>
                                        <li><strong>Remove:</strong> Click trash icon to remove item</li>
                                    </ul>
                                `
                            },
                            {
                                title: 'Payment',
                                icon: 'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z',
                                content: `
                                    <p>The right section for payment process:</p>
                                    <ul class="list-disc list-inside space-y-1 mt-2">
                                        <li><strong>Total Purchase:</strong> Total price of all items</li>
                                        <li><strong>Discount:</strong> Enter discount if any</li>
                                        <li><strong>Total Payment:</strong> Total after discount</li>
                                        <li><strong>Pay:</strong> Click to complete transaction</li>
                                        <li><strong>Reset:</strong> Click to empty cart</li>
                                    </ul>
                                `
                            },
                            {
                                title: 'Usage Tips',
                                icon: 'M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z',
                                content: `
                                    <p>Some tips for efficient usage:</p>
                                    <ul class="list-disc list-inside space-y-1 mt-2">
                                        <li><strong>Keyboard Shortcut:</strong> Use Enter to add item to cart</li>
                                        <li><strong>Quick Search:</strong> Type directly to search products</li>
                                        <li><strong>Stock Alert:</strong> System will warn if stock is insufficient</li>
                                        <li><strong>Auto Save:</strong> Transactions automatically saved to history</li>
                                    </ul>
                                `
                            }
                        ]
                    }
                },
                '/client/stockc': {
                    id: {
                        title: 'Manajemen Stok Mitra',
                        subtitle: 'Kelola produk dan persediaan Anda',
                        sections: [
                            {
                                title: 'Daftar Produk',
                                icon: 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4',
                                content: `
                                    <p>Halaman ini menampilkan semua produk yang Anda kelola:</p>
                                    <ul class="list-disc list-inside space-y-1 mt-2">
                                        <li><strong>Produk GAFI:</strong> Produk yang dibeli dari admin/pusat (tidak bisa diedit/dihapus)</li>
                                        <li><strong>Produk Umum:</strong> Produk yang Anda tambahkan sendiri (bisa diedit/dihapus)</li>
                                        <li><strong>Stok Tersedia:</strong> Jumlah stok saat ini dalam satuan pcs</li>
                                        <li><strong>Harga Jual:</strong> Harga yang Anda tetapkan untuk customer</li>
                                        <li><strong>SKU:</strong> Kode unik produk (auto-generate)</li>
                                        <li><strong>Kategori:</strong> GAFI atau Umum (hanya untuk produk yang Anda tambahkan)</li>
                                        <li><strong>Diperbaharui:</strong> Tanggal terakhir update stok</li>
                                    </ul>
                                `
                            },
                            {
                                title: 'Tambah Produk Baru',
                                icon: 'M12 6v6m0 0v6m0-6h6m-6 0H6',
                                content: `
                                    <p>Untuk menambahkan produk baru:</p>
                                    <ol class="list-decimal list-inside space-y-1 mt-2">
                                        <li>Klik tombol "Tambah Item" di bagian atas</li>
                                        <li>Isi nama produk (akan muncul autocomplete jika sudah ada)</li>
                                        <li>SKU akan di-generate otomatis berdasarkan nama dan kategori</li>
                                        <li>Pilih kategori (Umum saja untuk input manual)</li>
                                        <li>Isi stok tersedia dan harga jual</li>
                                        <li>Klik "Simpan" untuk menyimpan</li>
                                    </ol>
                                    <p class="mt-3"><strong>Catatan:</strong> Jika nama produk sudah ada, stok akan ditambahkan ke produk yang sudah ada.</p>
                                `
                            },
                            {
                                title: 'Split Item (GAFI)',
                                icon: 'M4 6h16M4 10h16M4 14h16M4 18h16',
                                content: `
                                    <p>Fitur khusus untuk produk GAFI:</p>
                                    <ul class="list-disc list-inside space-y-1 mt-2">
                                        <li><strong>Split Item:</strong> Membagi produk GAFI menjadi ukuran yang lebih kecil</li>
                                        <li><strong>Gramasi:</strong> Masukkan ukuran dalam gram (misal: 750g)</li>
                                        <li><strong>Jumlah Split:</strong> Berapa item baru yang akan dibuat</li>
                                        <li><strong>Harga:</strong> Harga untuk item yang di-split (opsional)</li>
                                        <li><strong>Auto Deduct:</strong> Stok induk akan berkurang otomatis</li>
                                        <li><strong>Konversi:</strong> 1kg = 1000g, sistem otomatis menghitung</li>
                                        <li><strong>Preview:</strong> Sistem menampilkan preview hasil split sebelum konfirmasi</li>
                                    </ul>
                                    <p class="mt-3"><strong>Contoh:</strong> 1kg Bumbu Cabai → 2 item @ 500g (stok induk berkurang 1 pcs)</p>
                                `
                            },
                            {
                                title: 'Manajemen Stok',
                                icon: 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z',
                                content: `
                                    <p>Mengelola produk yang sudah ada:</p>
                                    <ul class="list-disc list-inside space-y-1 mt-2">
                                        <li><strong>Edit:</strong> Klik ikon pensil untuk mengubah data (kecuali GAFI)</li>
                                        <li><strong>Hapus:</strong> Klik ikon tempat sampah untuk menghapus (kecuali GAFI)</li>
                                        <li><strong>Detail:</strong> Klik pada baris untuk melihat detail lengkap</li>
                                        <li><strong>Low Stock Alert:</strong> Produk dengan stok rendah akan ditandai merah</li>
                                        <li><strong>Split Item:</strong> Khusus untuk produk GAFI (tombol di menu aksi)</li>
                                        <li><strong>Auto-complete:</strong> Sistem mengenali produk yang sudah ada</li>
                                    </ul>
                                    <p class="mt-3"><strong>Pembatasan:</strong> Produk GAFI tidak bisa diedit/dihapus, hanya bisa di-split.</p>
                                `
                            },
                            {
                                title: 'Tips Penggunaan',
                                icon: 'M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z',
                                content: `
                                    <p>Beberapa tips untuk manajemen stok yang efektif:</p>
                                    <ul class="list-disc list-inside space-y-1 mt-2">
                                        <li><strong>Monitor Low Stock:</strong> Perhatikan produk dengan stok rendah (ditandai merah)</li>
                                        <li><strong>Split GAFI:</strong> Gunakan fitur split untuk memenuhi permintaan customer</li>
                                        <li><strong>Update Harga:</strong> Sesuaikan harga jual dengan kondisi pasar</li>
                                        <li><strong>Backup Data:</strong> Export data secara berkala untuk backup</li>
                                        <li><strong>Auto-complete:</strong> Manfaatkan fitur autocomplete untuk efisiensi</li>
                                    </ul>
                                `
                            }
                        ]
                    },
                    en: {
                        title: 'Partner Stock Management',
                        subtitle: 'Manage your products and inventory',
                        sections: [
                            {
                                title: 'Product List',
                                icon: 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4',
                                content: `
                                    <p>This page displays all products you manage:</p>
                                    <ul class="list-disc list-inside space-y-1 mt-2">
                                        <li><strong>GAFI Products:</strong> Products purchased from admin/central (cannot be edited/deleted)</li>
                                        <li><strong>General Products:</strong> Products you added yourself (can be edited/deleted)</li>
                                        <li><strong>Available Stock:</strong> Current stock quantity in pieces</li>
                                        <li><strong>Selling Price:</strong> Price you set for customers</li>
                                        <li><strong>SKU:</strong> Unique product code (auto-generated)</li>
                                        <li><strong>Category:</strong> GAFI or General (only for products you added)</li>
                                        <li><strong>Last Updated:</strong> Date of last stock update</li>
                                    </ul>
                                `
                            },
                            {
                                title: 'Add New Product',
                                icon: 'M12 6v6m0 0v6m0-6h6m-6 0H6',
                                content: `
                                    <p>To add a new product:</p>
                                    <ol class="list-decimal list-inside space-y-1 mt-2">
                                        <li>Click "Add Item" button at the top</li>
                                        <li>Fill in product name (autocomplete will appear if exists)</li>
                                        <li>SKU will be auto-generated based on name and category</li>
                                        <li>Select category (General only for manual input)</li>
                                        <li>Fill in available stock and selling price</li>
                                        <li>Click "Save" to store</li>
                                    </ol>
                                    <p class="mt-3"><strong>Note:</strong> If product name already exists, stock will be added to existing product.</p>
                                `
                            },
                            {
                                title: 'Split Item (GAFI)',
                                icon: 'M4 6h16M4 10h16M4 14h16M4 18h16',
                                content: `
                                    <p>Special feature for GAFI products:</p>
                                    <ul class="list-disc list-inside space-y-1 mt-2">
                                        <li><strong>Split Item:</strong> Divide GAFI products into smaller sizes</li>
                                        <li><strong>Grams:</strong> Enter size in grams (e.g., 750g)</li>
                                        <li><strong>Split Quantity:</strong> How many new items to create</li>
                                        <li><strong>Price:</strong> Price for split items (optional)</li>
                                        <li><strong>Auto Deduct:</strong> Parent stock will decrease automatically</li>
                                        <li><strong>Conversion:</strong> 1kg = 1000g, system calculates automatically</li>
                                        <li><strong>Preview:</strong> System shows split result preview before confirmation</li>
                                    </ul>
                                    <p class="mt-3"><strong>Example:</strong> 1kg Chili Spice → 2 items @ 500g (parent stock decreases by 1 pcs)</p>
                                `
                            },
                            {
                                title: 'Stock Management',
                                icon: 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z',
                                content: `
                                    <p>Managing existing products:</p>
                                    <ul class="list-disc list-inside space-y-1 mt-2">
                                        <li><strong>Edit:</strong> Click pencil icon to modify data (except GAFI)</li>
                                        <li><strong>Delete:</strong> Click trash icon to remove (except GAFI)</li>
                                        <li><strong>Detail:</strong> Click on row to view complete details</li>
                                        <li><strong>Low Stock Alert:</strong> Products with low stock will be marked red</li>
                                        <li><strong>Split Item:</strong> Special for GAFI products (button in action menu)</li>
                                        <li><strong>Auto-complete:</strong> System recognizes existing products</li>
                                    </ul>
                                    <p class="mt-3"><strong>Restriction:</strong> GAFI products cannot be edited/deleted, only split.</p>
                                `
                            },
                            {
                                title: 'Usage Tips',
                                icon: 'M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z',
                                content: `
                                    <p>Some tips for effective stock management:</p>
                                    <ul class="list-disc list-inside space-y-1 mt-2">
                                        <li><strong>Monitor Low Stock:</strong> Pay attention to products with low stock (marked red)</li>
                                        <li><strong>Split GAFI:</strong> Use split feature to meet customer demands</li>
                                        <li><strong>Update Prices:</strong> Adjust selling prices according to market conditions</li>
                                        <li><strong>Backup Data:</strong> Export data regularly for backup</li>
                                        <li><strong>Auto-complete:</strong> Take advantage of autocomplete feature for efficiency</li>
                                    </ul>
                                `
                            }
                        ]
                    }
                },
                '/client/salesc': {
                    id: {
                        title: 'Riwayat Penjualan Mitra',
                        subtitle: 'Kelola dan lihat riwayat transaksi penjualan',
                        sections: [
                            {
                                title: 'Daftar Transaksi',
                                icon: 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
                                content: `
                                    <p>Halaman ini menampilkan semua transaksi penjualan Anda:</p>
                                    <ul class="list-disc list-inside space-y-1 mt-2">
                                        <li><strong>ID Pesanan:</strong> Nomor unik transaksi (format: NOxxx)</li>
                                        <li><strong>Tanggal & Waktu:</strong> Kapan transaksi dilakukan (format: YYYY-MM-DD HH:mm)</li>
                                        <li><strong>Total:</strong> Total nilai transaksi dalam format rupiah</li>
                                        <li><strong>Metode Pembayaran:</strong> Cara pembayaran yang digunakan customer</li>
                                        <li><strong>Status:</strong> Status pembayaran (Selesai/Belum dibayar/Dibatalkan)</li>
                                        <li><strong>Aksi:</strong> Lihat detail, edit, atau hapus transaksi</li>
                                    </ul>
                                `
                            },
                            {
                                title: 'Filter dan Pencarian',
                                icon: 'M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z',
                                content: `
                                    <p>Gunakan filter untuk menemukan transaksi tertentu:</p>
                                    <ul class="list-disc list-inside space-y-1 mt-2">
                                        <li><strong>Pencarian:</strong> Ketik ID pesanan atau nama customer</li>
                                        <li><strong>Filter Periode:</strong> Pilih periode tanggal (hari ini, minggu ini, bulan ini)</li>
                                        <li><strong>Filter Status:</strong> Pilih status pembayaran (Semua/Selesai/Belum dibayar)</li>
                                        <li><strong>Sort:</strong> Urutkan berdasarkan kolom tertentu (ID, Tanggal, Total)</li>
                                        <li><strong>Reset Filter:</strong> Klik tombol reset untuk menghapus semua filter</li>
                                    </ul>
                                `
                            },
                            {
                                title: 'Detail Transaksi',
                                icon: 'M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z',
                                content: `
                                    <p>Lihat detail lengkap transaksi:</p>
                                    <ul class="list-disc list-inside space-y-1 mt-2">
                                        <li><strong>Informasi Customer:</strong> Nama, alamat, kontak customer</li>
                                        <li><strong>Daftar Item:</strong> Produk yang dibeli dengan detail (nama, qty, harga, subtotal)</li>
                                        <li><strong>Perhitungan:</strong> Subtotal, diskon, total akhir, kembalian</li>
                                        <li><strong>Status Pembayaran:</strong> Sudah dibayar atau belum</li>
                                        <li><strong>Timeline Transaksi:</strong> Waktu pemesanan, penerbitan invoice, pembayaran</li>
                                        <li><strong>Waktu Transaksi:</strong> Tanggal dan jam transaksi yang akurat</li>
                                    </ul>
                                `
                            },
                            {
                                title: 'Invoice dan Export',
                                icon: 'M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
                                content: `
                                    <p>Generate invoice dan export data transaksi:</p>
                                    <ul class="list-disc list-inside space-y-1 mt-2">
                                        <li><strong>Preview Invoice:</strong> Lihat invoice dalam modal sebelum print</li>
                                        <li><strong>Print Invoice:</strong> Cetak invoice untuk customer</li>
                                        <li><strong>Export Excel:</strong> Download data dalam format Excel (.xlsx)</li>
                                        <li><strong>Export PDF:</strong> Download data dalam format PDF</li>
                                        <li><strong>Identitas Mitra:</strong> Invoice menggunakan data identitas dari pengaturan</li>
                                        <li><strong>Logo Perusahaan:</strong> Logo GAFI otomatis muncul di invoice</li>
                                    </ul>
                                `
                            },
                            {
                                title: 'Manajemen Transaksi',
                                icon: 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z',
                                content: `
                                    <p>Mengelola transaksi yang sudah ada:</p>
                                    <ul class="list-disc list-inside space-y-1 mt-2">
                                        <li><strong>Edit Transaksi:</strong> Ubah detail transaksi jika diperlukan</li>
                                        <li><strong>Hapus Transaksi:</strong> Hapus transaksi yang tidak valid</li>
                                        <li><strong>Update Status:</strong> Ubah status pembayaran</li>
                                        <li><strong>Backup Data:</strong> Export data secara berkala untuk backup</li>
                                        <li><strong>Riwayat Lengkap:</strong> Semua transaksi tersimpan dengan aman</li>
                                    </ul>
                                `
                            }
                        ]
                    },
                    en: {
                        title: 'Partner Sales History',
                        subtitle: 'Manage and view sales transaction history',
                        sections: [
                            {
                                title: 'Transaction List',
                                icon: 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
                                content: `
                                    <p>This page displays all your sales transactions:</p>
                                    <ul class="list-disc list-inside space-y-1 mt-2">
                                        <li><strong>Order ID:</strong> Unique transaction number (format: NOxxx)</li>
                                        <li><strong>Date & Time:</strong> When transaction was made (format: YYYY-MM-DD HH:mm)</li>
                                        <li><strong>Total:</strong> Total transaction value in currency format</li>
                                        <li><strong>Payment Method:</strong> Payment method used by customer</li>
                                        <li><strong>Status:</strong> Payment status (Completed/Unpaid/Cancelled)</li>
                                        <li><strong>Actions:</strong> View details, edit, or delete transaction</li>
                                    </ul>
                                `
                            },
                            {
                                title: 'Filter and Search',
                                icon: 'M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z',
                                content: `
                                    <p>Use filters to find specific transactions:</p>
                                    <ul class="list-disc list-inside space-y-1 mt-2">
                                        <li><strong>Search:</strong> Type order ID or customer name</li>
                                        <li><strong>Period Filter:</strong> Select date period (today, this week, this month)</li>
                                        <li><strong>Status Filter:</strong> Select payment status (All/Completed/Unpaid)</li>
                                        <li><strong>Sort:</strong> Sort by specific column (ID, Date, Total)</li>
                                        <li><strong>Reset Filter:</strong> Click reset button to clear all filters</li>
                                    </ul>
                                `
                            },
                            {
                                title: 'Transaction Details',
                                icon: 'M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z',
                                content: `
                                    <p>View complete transaction details:</p>
                                    <ul class="list-disc list-inside space-y-1 mt-2">
                                        <li><strong>Customer Information:</strong> Name, address, contact</li>
                                        <li><strong>Item List:</strong> Purchased products with details (name, qty, price, subtotal)</li>
                                        <li><strong>Calculation:</strong> Subtotal, discount, final total, change</li>
                                        <li><strong>Payment Status:</strong> Paid or unpaid</li>
                                        <li><strong>Transaction Timeline:</strong> Order time, invoice issuance, payment</li>
                                        <li><strong>Transaction Time:</strong> Accurate date and time of transaction</li>
                                    </ul>
                                `
                            },
                            {
                                title: 'Invoice and Export',
                                icon: 'M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
                                content: `
                                    <p>Generate invoice and export transaction data:</p>
                                    <ul class="list-disc list-inside space-y-1 mt-2">
                                        <li><strong>Preview Invoice:</strong> View invoice in modal before printing</li>
                                        <li><strong>Print Invoice:</strong> Print invoice for customer</li>
                                        <li><strong>Export Excel:</strong> Download data in Excel format (.xlsx)</li>
                                        <li><strong>Export PDF:</strong> Download data in PDF format</li>
                                        <li><strong>Partner Identity:</strong> Invoice uses identity data from settings</li>
                                        <li><strong>Company Logo:</strong> GAFI logo automatically appears on invoice</li>
                                    </ul>
                                `
                            },
                            {
                                title: 'Transaction Management',
                                icon: 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z',
                                content: `
                                    <p>Managing existing transactions:</p>
                                    <ul class="list-disc list-inside space-y-1 mt-2">
                                        <li><strong>Edit Transaction:</strong> Modify transaction details if needed</li>
                                        <li><strong>Delete Transaction:</strong> Remove invalid transactions</li>
                                        <li><strong>Update Status:</strong> Change payment status</li>
                                        <li><strong>Backup Data:</strong> Export data regularly for backup</li>
                                        <li><strong>Complete History:</strong> All transactions stored securely</li>
                                    </ul>
                                `
                            }
                        ]
                    }
                },
                '/admin/client': {
                    id: {
                        title: 'Manajemen Client',
                        subtitle: 'Kelola data mitra/client',
                        sections: [
                            {
                                title: 'Daftar Client',
                                icon: 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z',
                                content: `
                                    <p>Halaman ini menampilkan semua client/mitra yang terdaftar:</p>
                                    <ul class="list-disc list-inside space-y-1 mt-2">
                                        <li><strong>ID Client:</strong> Nomor identifikasi unik client</li>
                                        <li><strong>Nama Client:</strong> Nama lengkap mitra</li>
                                        <li><strong>Alamat:</strong> Alamat lengkap client</li>
                                        <li><strong>Nomor Telepon:</strong> Kontak client</li>
                                        <li><strong>Tanggal Bergabung:</strong> Kapan client bergabung</li>
                                        <li><strong>Item yang Dibeli:</strong> Produk yang pernah dibeli client</li>
                                    </ul>
                                `
                            },
                            {
                                title: 'Tambah Client Baru',
                                icon: 'M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z',
                                content: `
                                    <p>Untuk menambahkan client baru:</p>
                                    <ol class="list-decimal list-inside space-y-1 mt-2">
                                        <li>Klik tombol "Tambah Client" di bagian atas</li>
                                        <li>Isi nama lengkap client</li>
                                        <li>Masukkan ID client yang unik</li>
                                        <li>Isi alamat lengkap client</li>
                                        <li>Masukkan nomor telepon</li>
                                        <li>Setel tanggal bergabung</li>
                                        <li>Pilih item yang dibeli oleh client</li>
                                        <li>Klik "Simpan" untuk menyimpan data</li>
                                    </ol>
                                `
                            },
                            {
                                title: 'Detail Client',
                                icon: 'M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z',
                                content: `
                                    <p>Melihat detail lengkap client:</p>
                                    <ul class="list-disc list-inside space-y-1 mt-2">
                                        <li><strong>Informasi Pribadi:</strong> Nama, ID client, alamat, telepon</li>
                                        <li><strong>Tanggal Bergabung:</strong> Kapan client bergabung dengan sistem</li>
                                        <li><strong>Riwayat Pembelian:</strong> Produk yang pernah dibeli</li>
                                        <li><strong>Data Transaksi:</strong> Total pembelian dan riwayat transaksi</li>
                                        <li><strong>Status Pembelian:</strong> Status item yang dibeli</li>
                                    </ul>
                                `
                            }
                        ]
                    },
                    en: {
                        title: 'Client Management',
                        subtitle: 'Manage partner/client data',
                        sections: [
                            {
                                title: 'Client List',
                                icon: 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z',
                                content: `
                                    <p>This page displays all registered clients/partners:</p>
                                    <ul class="list-disc list-inside space-y-1 mt-2">
                                        <li><strong>Client ID:</strong> Unique client identification number</li>
                                        <li><strong>Client Name:</strong> Full partner name</li>
                                        <li><strong>Address:</strong> Complete client address</li>
                                        <li><strong>Phone Number:</strong> Client contact</li>
                                        <li><strong>Join Date:</strong> When client joined</li>
                                        <li><strong>Purchased Items:</strong> Products previously purchased by client</li>
                                    </ul>
                                `
                            },
                            {
                                title: 'Add New Client',
                                icon: 'M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z',
                                content: `
                                    <p>To add a new client:</p>
                                    <ol class="list-decimal list-inside space-y-1 mt-2">
                                        <li>Click "Add Client" button at the top</li>
                                        <li>Fill in client's full name</li>
                                        <li>Enter unique client ID</li>
                                        <li>Fill in complete client address</li>
                                        <li>Enter phone number</li>
                                        <li>Set join date</li>
                                        <li>Select items purchased by client</li>
                                        <li>Click "Save" to store data</li>
                                    </ol>
                                `
                            },
                            {
                                title: 'Client Details',
                                icon: 'M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z',
                                content: `
                                    <p>View complete client details:</p>
                                    <ul class="list-disc list-inside space-y-1 mt-2">
                                        <li><strong>Personal Information:</strong> Name, client ID, address, phone</li>
                                        <li><strong>Join Date:</strong> When client joined the system</li>
                                        <li><strong>Purchase History:</strong> Products previously purchased</li>
                                        <li><strong>Transaction Data:</strong> Total purchases and transaction history</li>
                                        <li><strong>Purchase Status:</strong> Status of purchased items</li>
                                    </ul>
                                `
                            }
                        ]
                    }
                },
                '/admin/sales': {
                    id: {
                        title: 'Riwayat Penjualan Admin',
                        subtitle: 'Kelola dan lihat riwayat transaksi pusat',
                        sections: [
                            {
                                title: 'Daftar Transaksi',
                                icon: 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
                                content: `
                                    <p>Halaman ini menampilkan semua transaksi penjualan pusat:</p>
                                    <ul class="list-disc list-inside space-y-1 mt-2">
                                        <li><strong>ID Transaksi:</strong> Nomor unik transaksi</li>
                                        <li><strong>Client:</strong> Nama client yang membeli</li>
                                        <li><strong>Tanggal & Waktu:</strong> Kapan transaksi dilakukan</li>
                                        <li><strong>Total:</strong> Total nilai transaksi</li>
                                        <li><strong>Status:</strong> Status pembayaran transaksi</li>
                                    </ul>
                                `
                            },
                            {
                                title: 'Filter dan Pencarian',
                                icon: 'M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z',
                                content: `
                                    <p>Gunakan filter untuk menemukan transaksi tertentu:</p>
                                    <ul class="list-disc list-inside space-y-1 mt-2">
                                        <li><strong>Pencarian:</strong> Ketik ID transaksi atau nama client</li>
                                        <li><strong>Filter Status:</strong> Pilih status pembayaran</li>
                                        <li><strong>Filter Tanggal:</strong> Pilih rentang tanggal</li>
                                        <li><strong>Filter Client:</strong> Pilih client tertentu</li>
                                    </ul>
                                `
                            },
                            {
                                title: 'Detail Transaksi',
                                icon: 'M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z',
                                content: `
                                    <p>Lihat detail lengkap transaksi:</p>
                                    <ul class="list-disc list-inside space-y-1 mt-2">
                                        <li><strong>Informasi Client:</strong> Nama, email, kontak</li>
                                        <li><strong>Daftar Produk:</strong> Produk yang dibeli dengan detail</li>
                                        <li><strong>Perhitungan:</strong> Subtotal, diskon, total akhir</li>
                                        <li><strong>Status Pembayaran:</strong> Sudah dibayar atau belum</li>
                                        <li><strong>Invoice:</strong> Generate dan download invoice</li>
                                    </ul>
                                `
                            },
                            {
                                title: 'Export Data',
                                icon: 'M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
                                content: `
                                    <p>Export data transaksi:</p>
                                    <ul class="list-disc list-inside space-y-1 mt-2">
                                        <li><strong>Export Excel:</strong> Download data dalam format Excel</li>
                                        <li><strong>Export PDF:</strong> Download data dalam format PDF</li>
                                    </ul>
                                `
                            }
                        ]
                    },
                    en: {
                        title: 'Admin Sales History',
                        subtitle: 'Manage and view central transaction history',
                        sections: [
                            {
                                title: 'Transaction List',
                                icon: 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
                                content: `
                                    <p>This page displays all central sales transactions:</p>
                                    <ul class="list-disc list-inside space-y-1 mt-2">
                                        <li><strong>Transaction ID:</strong> Unique transaction number</li>
                                        <li><strong>Client:</strong> Name of purchasing client</li>
                                        <li><strong>Date & Time:</strong> When transaction was made</li>
                                        <li><strong>Total:</strong> Total transaction value</li>
                                        <li><strong>Status:</strong> Payment status of transaction</li>
                                    </ul>
                                `
                            },
                            {
                                title: 'Filter and Search',
                                icon: 'M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z',
                                content: `
                                    <p>Use filters to find specific transactions:</p>
                                    <ul class="list-disc list-inside space-y-1 mt-2">
                                        <li><strong>Search:</strong> Type transaction ID or client name</li>
                                        <li><strong>Status Filter:</strong> Select payment status</li>
                                        <li><strong>Date Filter:</strong> Choose date range</li>
                                        <li><strong>Client Filter:</strong> Select specific client</li>
                                    </ul>
                                `
                            },
                            {
                                title: 'Transaction Details',
                                icon: 'M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z',
                                content: `
                                    <p>View complete transaction details:</p>
                                    <ul class="list-disc list-inside space-y-1 mt-2">
                                        <li><strong>Client Information:</strong> Name, email, contact</li>
                                        <li><strong>Product List:</strong> Purchased products with details</li>
                                        <li><strong>Calculation:</strong> Subtotal, discount, final total</li>
                                        <li><strong>Payment Status:</strong> Paid or unpaid</li>
                                        <li><strong>Invoice:</strong> Generate and download invoice</li>
                                    </ul>
                                `
                            },
                            {
                                title: 'Export Data',
                                icon: 'M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
                                content: `
                                    <p>Export transaction data:</p>
                                    <ul class="list-disc list-inside space-y-1 mt-2">
                                        <li><strong>Export Excel:</strong> Download data in Excel format</li>
                                        <li><strong>Export PDF:</strong> Download data in PDF format</li>
                                    </ul>
                                `
                            }
                        ]
                    }
                }
                // Add more pages here...
            };
            
            return helpData[path] || {
                id: {
                    title: 'Panduan Halaman',
                    subtitle: 'Panduan penggunaan sistem',
                    sections: [{
                        title: 'Informasi Umum',
                        icon: 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
                        content: '<p>Panduan untuk halaman ini sedang dalam pengembangan.</p>'
                    }]
                },
                en: {
                    title: 'Page Guide',
                    subtitle: 'System usage guide',
                    sections: [{
                        title: 'General Information',
                        icon: 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
                        content: '<p>Guide for this page is under development.</p>'
                    }]
                }
            };
        }
    }
}
</script>
