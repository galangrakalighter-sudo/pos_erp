<div class="w-full bg-white shadow flex items-center justify-between px-8 py-4 sticky top-0 z-10"
    x-data="{
        time: '',
        date: '',
        showIdentityModal: false,
        showLowStockModal: false,
        showNotification: false,
        notificationMessage: '',
        notificationType: 'success',
        identity: { phone: '', address: '', email: '', bank: '', account: '' },
        lowStockData: [],
        lowStockCount: 0,
        lowStockThreshold: Number(localStorage.getItem('gafi_overview_lowStockThreshold')) || 10,
        clientLowStockThreshold: Number(localStorage.getItem('gafi_overview_clientLowStockThreshold')) || 10,
        updateTime() {
            const now = new Date();
            const options = { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' };
            this.time = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', hour12: true });
            this.date = now.toLocaleDateString('id-ID', options);
        },
        loadIdentity() {
            fetch('/admin/identity', {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    console.error('Error loading identity:', data.error);
                    this.identity = { phone: '', address: '', email: '', bank: '', account: '' };
                } else {
                    this.identity = {
                        phone: data.telepon || '',
                        address: data.alamat || '',
                        email: data.email || '',
                        bank: data.bank || '',
                        account: data.no_rekening || ''
                    };
                }
            })
            .catch(error => {
                console.error('Error loading identity:', error);
                this.identity = { phone: '', address: '', email: '', bank: '', account: '' };
            });
        },
        saveIdentity() {
            const payload = {
                telepon: this.identity.phone || '',
                alamat: this.identity.address || '',
                email: this.identity.email || '',
                bank: this.identity.bank || '',
                no_rekening: this.identity.account || ''
            };
            fetch('/admin/identity', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content')
                },
                body: JSON.stringify(payload)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    this.showSuccessNotification('Data identitas berhasil disimpan!');
                    this.showIdentityModal = false;
                } else {
                    this.showErrorNotification('Gagal menyimpan data identitas: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error saving identity:', error);
                this.showErrorNotification('Gagal menyimpan data identitas. Silakan coba lagi.');
            });
        },
        fetchLowStockNotif() {
            // Ambil threshold terbaru dari localStorage SETIAP fetch
            this.lowStockThreshold = Number(localStorage.getItem('gafi_overview_lowStockThreshold')) || 10;
            this.clientLowStockThreshold = Number(localStorage.getItem('gafi_overview_clientLowStockThreshold')) || 10;
            const adminThreshold = this.lowStockThreshold;
            const clientThreshold = this.clientLowStockThreshold;
            fetch(`/admin/overview-data?threshold=${adminThreshold}&clientThreshold=${clientThreshold}`)
                .then(res => res.json())
                .then(data => {
                    this.lowStockData = (data.lowStockNotif || []).map(item => ({
                        // Pastikan semua field tersedia agar tidak undefined di template
                        id: item.id, // ID notifikasi untuk dismiss
                        type: item.tipe || item.type || 'admin',
                        clientName: (item.tipe || item.type) === 'admin' ? 'Admin/Pusat' : (item.client_nama || item.clientName || 'Client'),
                        itemName: item.nama || item.itemName || 'Tanpa Nama',
                        sku: item.sku || (item.id ? String(item.id) : ''),
                        stock: Number(item.tersedia ?? item.stock ?? 0),
                        threshold: (item.tipe || item.type) === 'admin' ? adminThreshold : clientThreshold,
                        category: (item.tipe || item.type) === 'admin' ? 'Admin' : 'GAFI'
                    }));
                    this.lowStockCount = this.lowStockData.length;
                })
                .catch(err => {
                    this.lowStockData = [];
                    this.lowStockCount = 0;
                });
        },
        dismissNotification(notificationId) {
            fetch(`/admin/dismiss-notification/${notificationId}`, {
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
                    this.showSuccessNotification('Notifikasi berhasil dihilangkan');
                } else {
                    this.showErrorNotification('Gagal menghilangkan notifikasi: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error dismissing notification:', error);
                this.showErrorNotification('Gagal menghilangkan notifikasi. Silakan coba lagi.');
            });
        },
        openClientDetail() {
            this.fetchLowStockNotif();
            this.showLowStockModal = true;
        },
        
        showSuccessNotification(message) {
            this.notificationType = 'success';
            this.notificationMessage = message;
            this.showNotification = true;
            setTimeout(() => {
                this.showNotification = false;
            }, 4000);
        },
        
        showErrorNotification(message) {
            this.notificationType = 'error';
            this.notificationMessage = message;
            this.showNotification = true;
            setTimeout(() => {
                this.showNotification = false;
            }, 5000);
        },
    }"
    x-init="updateTime(); setInterval(updateTime, 1000); loadIdentity(); fetchLowStockNotif(); setInterval(fetchLowStockNotif, 30000);">

    <!-- Left: Tempat icon/logo/menu -->
    <div class="flex items-center gap-3 min-w-[120px]">
        <img src="{{ asset('images/logo.png') }}" alt="Logo" class="h-8 w-auto" />
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
            <span class="w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center cursor-pointer hover:bg-gray-300 transition-colors" @click="openClientDetail()">
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
    <!-- Modal Pengaturan Identitas Admin -->
    <div x-show="showIdentityModal" x-transition class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40 p-4" x-cloak>
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-xl mx-4 relative overflow-hidden">
            <div class="bg-[#28C328] text-white px-6 py-4 flex items-center justify-between">
                <div class="font-semibold">Pengaturan Identitas Admin</div>
                <button @click="showIdentityModal = false" class="w-8 h-8 rounded-full bg-white/20 hover:bg-white/30 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Telepon</label>
                    <input type="text" x-model="identity.phone" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-[#28C328] focus:border-[#28C328]" placeholder="Contoh: 021-12345678" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" x-model="identity.email" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-[#28C328] focus:border-[#28C328]" placeholder="Contoh: admin@gafi.co.id" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Alamat</label>
                    <textarea x-model="identity.address" rows="3" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-[#28C328] focus:border-[#28C328]" placeholder="Alamat kantor pusat"></textarea>
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
            </div>
            <div class="px-6 pb-6">
                <button @click="saveIdentity()" class="w-full md:w-auto bg-[#28C328] hover:bg-[#22a322] text-white font-semibold px-6 py-2 rounded-lg">Simpan</button>
            </div>
        </div>
    </div>
    
    <!-- Modal Notifikasi Low Stock -->
    <div x-show="showLowStockModal" x-transition class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40 p-4" x-cloak>
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-2xl mx-4 relative overflow-hidden">
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
                        <div>• Admin Items: Threshold <span class="font-semibold" x-text="lowStockThreshold"></span></div>
                        <div>• Client GAFI Items: Threshold <span class="font-semibold" x-text="clientLowStockThreshold"></span></div>
                    </div>
                </div>
                
                <div class="space-y-3 max-h-96 overflow-y-auto">
                    <template x-if="lowStockData.length === 0">
                        <div class="text-center text-gray-500 py-8">Tidak ada item low stock</div>
                    </template>
                    <template x-for="(item, index) in lowStockData" :key="index">
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="flex items-center justify-between mb-2">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="font-semibold text-gray-800" x-text="item.itemName"></div>
                                        <div class="text-sm text-gray-500 font-mono" x-text="item.sku"></div>
                                        <div class="text-xs" :class="item.category === 'GAFI' ? 'text-blue-600' : 'text-gray-500'" x-text="item.category"></div>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3">
                                    <div class="text-right">
                                        <div class="text-lg font-bold text-red-600" x-text="item.stock"></div>
                                        <div class="text-xs text-gray-500" x-text="item.clientName"></div>
                                    </div>
                                    <!-- Tombol dismiss -->
                                    <button @click="dismissNotification(item.id)" 
                                            class="w-6 h-6 bg-red-100 hover:bg-red-200 rounded-full flex items-center justify-center transition-colors"
                                            title="Hilangkan notifikasi">
                                        <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                        <a href="{{ route('admin.dashboard') }}" class="px-4 py-2 bg-[#28C328] hover:bg-[#22a322] text-white rounded-lg font-medium">Kelola Stok</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Toast Notification -->
    <div x-show="showNotification" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform translate-y-2"
         x-transition:enter-end="opacity-100 transform translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 transform translate-y-0"
         x-transition:leave-end="opacity-0 transform translate-y-2"
         class="fixed top-24 right-8 z-50 max-w-sm w-full"
         x-cloak>
        <div class="rounded-lg shadow-lg border-l-4 p-4" 
             :class="notificationType === 'success' ? 'bg-green-50 border-green-400 text-green-800' : 'bg-red-50 border-red-400 text-red-800'">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <template x-if="notificationType === 'success'">
                        <svg class="w-5 h-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                    </template>
                    <template x-if="notificationType === 'error'">
                        <svg class="w-5 h-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                    </template>
                </div>
                <div class="ml-3 flex-1">
                    <p class="text-sm font-medium" x-text="notificationMessage"></p>
                </div>
                <div class="ml-4 flex-shrink-0">
                    <button @click="showNotification = false" 
                            class="inline-flex text-gray-400 hover:text-gray-600 focus:outline-none focus:text-gray-600 transition ease-in-out duration-150">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

