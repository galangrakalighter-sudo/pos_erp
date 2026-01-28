<div x-data="{ open: true }" @mouseenter="open = true" @mouseleave="open = false" class="transition-all duration-2000 sticky top-0 z-40 h-screen bg-[#28C328] flex flex-col items-center py-6 relative rounded-br-[48px] shrink-0" :class="open ? 'w-64' : 'w-20'">
    <!-- Logo -->
    <div class="flex items-center gap-2 mb-8" :class="open ? 'justify-start' : 'justify-center'" x-data="{ logoUrl: '', mitraName: '' }" x-init="
        fetch('/client/identity', { headers: { 'Accept': 'application/json' }})
            .then(r=>r.json())
            .then(d=>{ logoUrl = (d.logo_url ? (d.logo_url + '?v=' + Date.now()) : '{{ asset('images/logo.png') }}'); mitraName = d.mitra_nama || 'GAFI'; })
            .catch(()=>{ logoUrl='{{ asset('images/logo.png') }}'; mitraName='GAFI'; });
        
        // Listen for updates from topbar
        window.addEventListener('updateSidebar', (e) => {
            if (e.detail.logoUrl) logoUrl = e.detail.logoUrl;
            if (e.detail.mitraName) mitraName = e.detail.mitraName;
        });
    ">
        <div class="bg-white rounded-full w-10 h-10 flex items-center justify-center overflow-hidden">
            <img :key="logoUrl" :src="logoUrl || '{{ asset('images/logo.png') }}'" alt="Logo" class="w-8 h-8 object-contain">
        </div>
        <span x-show="open" class="text-white font-extrabold text-lg tracking-wide" x-text="mitraName || 'GAFI'"></span>
    </div>
    <!-- Menu -->
    <nav class="flex-1 w-full overflow-y-auto">
        <ul class="space-y-2 w-full">
            <!-- Dashboard -->
            <li>
                <a href="{{ route('client.dashboard') }}" class="flex items-center py-3 transition group relative w-full" :class="open ? 'justify-start pl-4' : 'justify-center'">
                    <div
                        :class="open
                            ? ({{ request()->routeIs('client.dashboard') ? 'true' : 'false' }}
                                ? 'bg-white rounded-tl-3xl rounded-tr-xl rounded-bl-3xl shadow h-12 w-full flex items-center pr-6 pl-4'
                                : 'h-12 flex items-center')
                            : ({{ request()->routeIs('client.dashboard') ? 'true' : 'false' }}
                                ? 'bg-white rounded-tl-3xl rounded-tr-xl rounded-bl-3xl shadow h-12 w-12 flex items-center justify-center ml-auto'
                                : 'h-12 w-12 flex items-center justify-center mx-auto')">
                        <img src="{{ asset('icons/dashboard.png') }}" alt="Dashboard" class="w-5 h-5 object-contain"
                             :class="{{ request()->routeIs('client.dashboard') ? 'true' : 'false' }}" />
                        <span x-show="open" class="ml-3 font-bold"
                              :class="{{ request()->routeIs('client.dashboard') ? 'true' : 'false' }} ? 'text-[#28C328]' : 'text-white'">
                            Dashboard
                        </span>
                    </div>
                </a>
            </li>
            <!-- Cashier Management -->
            <li>
                <a href="{{ route('client.cashier') }}" class="flex items-center py-3 transition group relative w-full" :class="open ? 'justify-start pl-4' : 'justify-center'">
                    <div
                        :class="open
                            ? ({{ request()->routeIs('client.cashier') ? 'true' : 'false' }}
                                ? 'bg-white rounded-tl-3xl rounded-tr-xl rounded-bl-3xl shadow h-12 w-full flex items-center pr-6 pl-4'
                                : 'h-12 flex items-center')
                            : ({{ request()->routeIs('client.cashier') ? 'true' : 'false' }}
                                ? 'bg-white rounded-tl-3xl rounded-tr-xl rounded-bl-3xl shadow h-12 w-12 flex items-center justify-center ml-auto'
                                : 'h-12 w-12 flex items-center justify-center mx-auto')">
                        <img src="{{ asset('icons/cashier.png') }}" alt="Client" class="w-5 h-5 object-contain"
                             :class="{{ request()->routeIs('client.cashier') ? 'true' : 'false' }}" />
                        <span x-show="open" class="ml-3 font-bold"
                              :class="{{ request()->routeIs('client.cashier') ? 'true' : 'false' }} ? 'text-[#28C328]' : 'text-white'">
                            Cashier
                        </span>
                    </div>
                </a>
            </li>
            <!-- Stock Management -->
            <li>
                <a href="{{ route('client.stockc') }}" class="flex items-center py-3 transition group relative w-full" :class="open ? 'justify-start pl-4' : 'justify-center'">
                    <div
                        :class="open
                            ? ({{ request()->routeIs('client.stockc') ? 'true' : 'false' }}
                                ? 'bg-white rounded-tl-3xl rounded-tr-xl rounded-bl-3xl shadow h-12 w-full flex items-center pr-6 pl-4'
                                : 'h-12 flex items-center')
                            : ({{ request()->routeIs('client.stockc') ? 'true' : 'false' }}
                                ? 'bg-white rounded-tl-3xl rounded-tr-xl rounded-bl-3xl shadow h-12 w-12 flex items-center justify-center ml-auto'
                                : 'h-12 w-12 flex items-center justify-center mx-auto')">
                        <img src="{{ asset('icons/stock.png') }}" alt="Stock" class="w-5 h-5 object-contain"
                             :class="{{ request()->routeIs('client.stockc') ? 'true' : 'false' }}" />
                        <span x-show="open" class="ml-3 font-bold"
                              :class="{{ request()->routeIs('client.stockc') ? 'true' : 'false' }} ? 'text-[#28C328]' : 'text-white'">
                            Stock Management
                        </span>
                    </div>
                </a>
            </li>
            <!-- Sales History -->
            <li>
                <a href="{{ route('client.salesc') }}" class="flex items-center py-3 transition group relative w-full" :class="open ? 'justify-start pl-4' : 'justify-center'">
                    <div
                        :class="open
                            ? ({{ request()->routeIs('client.salesc') ? 'true' : 'false' }}
                                ? 'bg-white rounded-tl-3xl rounded-tr-xl rounded-bl-3xl shadow h-12 w-full flex items-center pr-6 pl-4'
                                : 'h-12 flex items-center')
                            : ({{ request()->routeIs('client.salesc') ? 'true' : 'false' }}
                                ? 'bg-white rounded-tl-3xl rounded-tr-xl rounded-bl-3xl shadow h-12 w-12 flex items-center justify-center ml-auto'
                                : 'h-12 w-12 flex items-center justify-center mx-auto')">
                        <img src="{{ asset('icons/sales.png') }}" alt="Sales" class="w-5 h-5 object-contain"
                             :class="{{ request()->routeIs('client.salesc') ? 'true' : 'false' }}" />
                        <span x-show="open" class="ml-3 font-bold"
                              :class="{{ request()->routeIs('client.salesc') ? 'true' : 'false' }} ? 'text-[#28C328]' : 'text-white'">
                            Sales History
                        </span>
                    </div>
                </a>
            </li>
            <li>
                <a href="{{ route('client.purchase') }}" class="flex items-center py-3 transition group relative w-full" :class="open ? 'justify-start pl-4' : 'justify-center'">
                    <div
                        :class="open
                            ? ({{ request()->routeIs('client.purchase') ? 'true' : 'false' }}
                                ? 'bg-white rounded-tl-3xl rounded-tr-xl rounded-bl-3xl shadow h-12 w-full flex items-center pr-6 pl-4'
                                : 'h-12 flex items-center')
                            : ({{ request()->routeIs('client.purchase') ? 'true' : 'false' }}
                                ? 'bg-white rounded-tl-3xl rounded-tr-xl rounded-bl-3xl shadow h-12 w-12 flex items-center justify-center ml-auto'
                                : 'h-12 w-12 flex items-center justify-center mx-auto')">
                        <svg class="w-5 h-5 object-contain" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                             :style="{{ request()->routeIs('client.purchase') ? 'true' : 'false' }} ? 'color: #132D21;' : 'color: #73FF73;'">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                        <span x-show="open" class="ml-3 font-bold"
                              :class="{{ request()->routeIs('client.purchase') ? 'true' : 'false' }} ? 'text-[#28C328]' : 'text-white'">
                            Purchase Order
                        </span>
                    </div>
                </a>
            </li>
        </ul>
    </nav>
    <!-- Logout -->
    <div class="absolute bottom-6 left-0 w-full px-4">
        <form method="POST" action="{{ route('logout') }}" id="logoutFormClient">
            @csrf
            <button type="button" onclick="showLogoutConfirmClient()" class="flex items-center py-3 px-4 rounded-xl text-white hover:bg-[#22a322] transition w-full justify-center gap-3 shadow-lg hover:shadow-xl transform hover:scale-105">
                <span class="w-5 h-5 flex items-center justify-center">
                    <img src="{{ asset('icons/logout.png') }}" alt="Logout" class="w-4 h-4 object-contain">
                </span>
                <span class="font-semibold">Keluar</span>
            </button>
        </form>
    </div>

    <!-- Modal Konfirmasi Logout Client -->
    <div id="logoutModalClient" class="fixed inset-0 z-50 overflow-y-auto hidden" x-cloak>
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">
                                Konfirmasi Keluar
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">
                                    Apakah Anda yakin ingin keluar dari aplikasi?
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button onclick="document.getElementById('logoutFormClient').submit()" type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:text-sm">
                        Ya, Keluar
                    </button>
                    <button onclick="hideLogoutConfirmClient()" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Batal
                    </button>
                </div>
            </div>
        </div>
    </div>
    <script>
    function showLogoutConfirmClient() {
        document.getElementById('logoutModalClient').classList.remove('hidden');
    }
    function hideLogoutConfirmClient() {
        document.getElementById('logoutModalClient').classList.add('hidden');
    }
    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('logoutModalClient');
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                hideLogoutConfirmClient();
            }
        });
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                hideLogoutConfirmClient();
            }
        });
    });
    </script>
</div>
