<div class="w-full bg-white shadow flex items-center justify-between px-8 py-4 sticky top-0 z-10" x-data="{
    time: '',
    date: '',
    showIdentityModal: false,
    showLowStockModal: false,
    identity: { mitraName: '', phone: '', address: '', email: '', bank: '', account: '', logoUrl: '', logoFile: null },
    lowStockData: [],
    lowStockCount: 0,
    lowStockThreshold: Number(localStorage.getItem('gafi_overview_clientLowStockThreshold')) || 10,
    updateTime() {
        const now = new Date();
        const options = { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' };
        this.time = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', hour12: true });
        this.date = now.toLocaleDateString('id-ID', options);
    },
    loadIdentity() {
        fetch('/client/identity', {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                this.identity = { mitraName: '', phone: '', address: '', email: '', bank: '', account: '', logoUrl: '', logoFile: null };
            } else {
                this.identity = {
                    mitraName: data.mitra_nama || '',
                    phone: data.telepon || '',
                    address: data.alamat || '',
                    email: data.email || '',
                    bank: data.bank || '',
                    account: data.no_rekening || '',
                    logoUrl: data.logo_url ? (data.logo_url + '?v=' + Date.now()) : ''
                };
            }
        })
        .catch(() => {
            this.identity = { mitraName: '', phone: '', address: '', email: '', bank: '', account: '', logoUrl: '', logoFile: null };
        });
    },
    saveIdentity() {
        const formData = new FormData();
        formData.append('mitra_nama', this.identity.mitraName || '');
        formData.append('telepon', this.identity.phone || '');
        formData.append('alamat', this.identity.address || '');
        formData.append('email', this.identity.email || '');
        formData.append('bank', this.identity.bank || '');
        formData.append('no_rekening', this.identity.account || '');
        if (this.identity.logoFile) {
            formData.append('logo', this.identity.logoFile);
        }
        fetch('/client/identity', {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content')
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Data identitas berhasil disimpan!');
                if (data.logo_url) {
                    this.identity.logoUrl = data.logo_url + '?v=' + Date.now();
                }
                if (data.mitra_nama) {
                    this.identity.mitraName = data.mitra_nama;
                }
                
                // Update sidebar secara real-time
                this.updateSidebar(data.logo_url, data.mitra_nama);
                
                this.showIdentityModal = false;
            } else {
                alert('Gagal menyimpan data identitas: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(() => {
            alert('Gagal menyimpan data identitas. Silakan coba lagi.');
        });
    },
            fetchLowStockNotif() {
            // Ambil threshold terbaru dari localStorage
            this.lowStockThreshold = Number(localStorage.getItem('gafi_overview_clientLowStockThreshold')) || 10;
            fetch(`/client/low-stock-notif?threshold=${this.lowStockThreshold}`)
                .then(res => res.json())
                .then(data => {
                    // Validasi dan bersihkan data
                    const rawData = data.lowStockNotif || [];
                    this.lowStockData = rawData.map((item, index) => ({
                        id: item.id, // ID notifikasi untuk dismiss
                        itemName: item.itemName || item.nama || `Item ${index + 1}`,
                        sku: item.sku || `SKU-${index + 1}`,
                        stock: item.stock || item.tersedia || 0,
                        threshold: item.threshold || 10,
                        category: item.category || 'Unknown',
                        source: item.source || 'unknown',
                        nama: item.nama || item.itemName || `Item ${index + 1}`,
                        tersedia: item.tersedia || item.stock || 0
                    }));
                    
                    this.lowStockCount = this.lowStockData.length;
                })
                .catch((error) => {
                    this.lowStockData = [];
                    this.lowStockCount = 0;
                });
        },
        dismissNotification(notificationId) {
            fetch(`/client/dismiss-notification/${notificationId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Hapus notifikasi dari list
                    this.lowStockData = this.lowStockData.filter(item => item.id !== notificationId);
                    this.lowStockCount = this.lowStockData.length;
                    alert('Notifikasi berhasil dihilangkan');
                } else {
                    alert('Gagal menghilangkan notifikasi: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error dismissing notification:', error);
                alert('Gagal menghilangkan notifikasi. Silakan coba lagi.');
            });
        },
    openLowStockDetails() {
        this.fetchLowStockNotif();
        this.showLowStockModal = true;
    },
    updateSidebar(logoUrl, mitraName) {
        // Dispatch custom event untuk update sidebar
        window.dispatchEvent(new CustomEvent('updateSidebar', {
            detail: {
                logoUrl: logoUrl ? logoUrl + '?v=' + Date.now() : null,
                mitraName: mitraName || 'GAFI'
            }
        }));
    }
}"
    x-init="updateTime(); setInterval(updateTime, 1000); loadIdentity(); fetchLowStockNotif(); setInterval(fetchLowStockNotif, 30000);">
    <!-- Left: Tempat icon/logo/menu -->
    <div class="flex items-center gap-3 min-w-[120px]">
        <img :key="identity.logoUrl" :src="identity.logoUrl || '{{ asset('images/logo.png') }}'" alt="Logo" class="h-8 w-auto" />
        <span><!-- Tambah icon lain di sini --></span>
    </div>
    <!-- Center: Judul halaman/opsional -->
    <div class="flex-1 text-center font-semibold text-lg text-gray-700">
        <span><!-- Judul Halaman --></span>
    </div>
    <!-- Right: Jam, tanggal, notifikasi, profile -->
    <div class="flex items-center gap-6">
        <div class="flex items-center gap-2 bg-gray-100 px-4 py-1 rounded shadow text-xs">
            <img src="{{ asset('icons/clock.png') }}" alt="Clock" class="w-4 h-4 mr-1" />
            <span class="font-bold" x-text="time"></span>
            <span>|</span>
            <span x-text="date"></span>
        </div>
        <!-- Notifikasi icon pakai notif.png dengan low stock counter -->
        <div class="relative">
            <span class="w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center cursor-pointer hover:bg-gray-300 transition-colors" @click="openLowStockDetails()">
                <img src="{{ asset('icons/notif.png') }}" alt="Notif" class="w-5 h-5" />
                <!-- Low stock notification badge -->
                <template x-if="lowStockCount > 0">
                    <span class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 text-white text-xs rounded-full flex items-center justify-center font-bold" x-text="lowStockCount > 99 ? '99+' : lowStockCount"></span>
                </template>
            </span>
        </div>
        <!-- Profile icon -->
        <span class="w-8 h-8 bg-[#28C328] rounded-full flex items-center justify-center text-white cursor-pointer" @click="showIdentityModal = true">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A9 9 0 1112 21a9 9 0 01-6.879-3.196z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
        </span>
    </div>
    <!-- Modal Pengaturan Identitas Mitra -->
    <div x-show="showIdentityModal" x-transition class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40 p-4" x-cloak>
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-xl mx-4 relative overflow-hidden">
            <div class="bg-[#28C328] text-white px-6 py-4 flex items-center justify-between">
                <div class="font-semibold">Pengaturan Identitas Mitra</div>
                <button @click="showIdentityModal = false" class="w-8 h-8 rounded-full bg-white/20 hover:bg-white/30 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Mitra (opsional)</label>
                    <input type="text" x-model="identity.mitraName" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-[#28C328] focus:border-[#28C328]" placeholder="Contoh: Toko Sumber Rezeki" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Telepon</label>
                    <input type="text" x-model="identity.phone" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-[#28C328] focus:border-[#28C328]" placeholder="Contoh: 0812-3456-7890" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" x-model="identity.email" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-[#28C328] focus:border-[#28C328]" placeholder="Contoh: mitra@gafi.co.id" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Alamat</label>
                    <textarea x-model="identity.address" rows="3" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-[#28C328] focus:border-[#28C328]" placeholder="Alamat lengkap mitra"></textarea>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Bank</label>
                        <input type="text" x-model="identity.bank" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-[#28C328] focus:border-[#28C328]" placeholder="Contoh: BCA / BRI / Mandiri" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">No Rekening</label>
                        <input type="text" x-model="identity.account" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-[#28C328] focus:border-[#28C328]" placeholder="Contoh: 1234567890" />
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Logo Mitra</label>
                    <input type="file" accept="image/*" @change="(e)=>{ const f = e.target.files[0]; if(f){ identity.logoFile = f; } }" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-[#28C328] focus:border-[#28C328]" />
                    <p class="text-xs text-gray-500 mt-1">Format: JPG/PNG, maks 1MB</p>
                </div>
            </div>
            <div class="px-6 pb-6">
                <button @click="saveIdentity()" class="w-full md:w-auto bg-[#28C328] hover:bg-[#22a322] text-white font-semibold px-6 py-2 rounded-lg">Simpan</button>
            </div>
        </div>
    </div>
    
    <!-- Modal Notifikasi Low Stock -->
    <div x-show="showLowStockModal" x-transition class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40 p-4" x-cloak>
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-xl mx-4 relative overflow-hidden">
            <div class="bg-red-500 text-white px-6 py-4 flex items-center justify-between">
                <div class="font-semibold">Low Stock Alert</div>
                <button @click="showLowStockModal = false" class="w-8 h-8 rounded-full bg-white/20 hover:bg-white/30 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>
            <div class="p-6">
                <div class="mb-4">
                    <div class="text-sm text-gray-600 mb-2">Total item low stock: <span class="font-semibold text-red-600" x-text="lowStockCount"></span></div>
                    <div class="text-xs text-gray-500 space-y-1">
                        <div>• Item GAFI: Threshold <span class="font-semibold" x-text="Number(localStorage.getItem('gafi_overview_clientLowStockThreshold') || 10)"></span></div>
                        <div>• Item Umum: Threshold <span class="font-semibold">10</span></div>
                    </div>
                </div>
                
                <div class="space-y-3 max-h-64 overflow-y-auto">
                    <template x-if="lowStockData.length === 0">
                        <div class="text-center text-gray-500 py-8">Tidak ada item low stock</div>
                    </template>
                    <template x-for="(item, index) in lowStockData" :key="index">
                        <div class="border border-gray-200 rounded-lg p-3">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg flex items-center justify-center" :class="item.category === 'GAFI' ? 'bg-blue-100' : 'bg-red-100'">
                                        <svg class="w-4 h-4" :class="item.category === 'GAFI' ? 'text-blue-600' : 'text-red-600'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="font-semibold text-gray-800 text-sm" x-text="item.itemName || item.nama || 'Unknown Item'"></div>
                                        <div class="text-xs text-gray-500 font-mono" x-text="item.sku || 'No SKU'"></div>
                                        <div class="text-xs" :class="item.category === 'GAFI' ? 'text-blue-600' : 'text-gray-500'">
                                            <span x-text="item.category || 'Unknown'"></span> • Threshold: <span x-text="item.threshold || 0"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <div class="text-right">
                                        <div class="text-lg font-bold text-red-600" x-text="item.stock || item.tersedia || 0"></div>
                                    </div>
                                    <!-- Tombol dismiss -->
                                    <button @click="dismissNotification(item.id)" 
                                            class="w-5 h-5 bg-red-100 hover:bg-red-200 rounded-full flex items-center justify-center transition-colors"
                                            title="Hilangkan notifikasi">
                                        <svg class="w-3 h-3 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
                
                <div class="mt-6 pt-4 border-t border-gray-200">
                    <div class="flex justify-end gap-3">
                        <button @click="showLowStockModal = false" class="px-4 py-2 text-gray-600 hover:text-gray-800 font-medium">Tutup</button>
                        <a href="{{ route('client.stockc') }}" class="px-4 py-2 bg-[#28C328] hover:bg-[#22a322] text-white rounded-lg font-medium">Lihat Stok</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
