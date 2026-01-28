@extends('layouts.admin')

@section('content')
<div class="bg-white rounded-xl shadow p-8" x-data="clientTable()">
    <h1 class="text-2xl font-bold text-[#28C328] mb-6">Client Management</h1>
    <div class="mb-6">
        <!-- Action bar -->
        <div class="flex flex-wrap items-center gap-2 justify-between">
            <div class="flex flex-1 gap-2">
                <!-- Search bar kiri -->
                <div class="w-72">
                    <div class="flex items-center border border-gray-300 rounded-lg px-4 py-1 bg-white">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-[#28C328] mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M11 19a8 8 0 100-16 8 8 0 000 16z" /></svg>
                        <input
                            type="text"
                            placeholder="Cari: Nama, ID Client, Sales, Alamat, Item..."
                            x-model="search"
                            class="flex-1 bg-transparent border-none outline-none text-gray-400 text-sm font-medium placeholder-gray-400 h-6"
                        >
                    </div>
                </div>
                
                <!-- Filter tanggal -->
                <div class="flex gap-2">
                    <select x-model="dateFilter" @change="applyDateFilter()" class="rounded-lg border border-gray-300 px-3 py-2 text-gray-700 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-[#28C328] focus:border-transparent">
                        <option value="">Semua Tanggal</option>
                        <option value="today">Hari Ini</option>
                        <option value="week">Minggu Ini</option>
                        <option value="month">Bulan Ini</option>
                        <option value="year">Tahun Ini</option>
                        <option value="custom">Custom Range</option>
                    </select>
                    
                    <!-- Custom date range inputs (muncul saat custom dipilih) -->
                    <div x-show="dateFilter === 'custom'" class="flex gap-2 items-center">
                        <input 
                            type="date" 
                            x-model="customStartDate" 
                            @change="applyDateFilter()"
                            class="rounded-lg border border-gray-300 px-3 py-2 text-gray-700 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-[#28C328] focus:border-transparent"
                            placeholder="Tanggal Mulai"
                        >
                        <span class="text-gray-500">sampai</span>
                        <input 
                            type="date" 
                            x-model="customEndDate" 
                            @change="applyDateFilter()"
                            class="rounded-lg border border-gray-300 px-3 py-2 text-gray-700 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-[#28C328] focus:border-transparent"
                            placeholder="Tanggal Akhir"
                        >
                    </div>
                    
                    <!-- Clear filter button (muncul saat ada filter aktif) -->
                    <button 
                        x-show="dateFilter" 
                        @click="clearDateFilter()" 
                        class="px-3 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition text-sm flex items-center gap-1"
                        title="Hapus Filter Tanggal"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        Clear
                    </button>
                </div>
            </div>
            <div class="flex gap-2 ml-auto">
                <button class="rounded-lg bg-[#28C328] px-4 py-2 text-white text-sm font-semibold flex items-center gap-2 hover:bg-[#22a322] transition" @click="exportExcel">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 2h8v4H8z" /></svg>
                    Excel
                </button>
                <button class="rounded-lg bg-[#28C328] px-4 py-2 text-white text-sm font-semibold flex items-center gap-2 hover:bg-[#22a322] transition" @click="exportPDF">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 20h9" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                    PDF
                </button>
                <button class="rounded-lg bg-[#28C328] px-4 py-2 text-white text-sm font-semibold flex items-center gap-2 hover:bg-[#22a322] transition" @click="showAddModal = true">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                    Tambahkan Client
                </button>
            </div>
        </div>
        <!-- Table -->
        <div class="overflow-x-auto mt-4">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="bg-[#28C328] text-white">
                        <th class="p-3 cursor-pointer select-none rounded-tl-xl" @click="sortBy('nama')">
                            <div class="flex items-center gap-1 justify-center">
                                <span>Nama Client</span>
                                <span class="flex flex-col">
                                    <svg :class="{'opacity-100': sortKey==='nama' && sortAsc, 'opacity-50': !(sortKey==='nama' && sortAsc)}" class="w-3 h-3" fill="white" viewBox="0 0 20 20"><path d="M10 6l-4 4h8l-4-4z"/></svg>
                                    <svg :class="{'opacity-100': sortKey==='nama' && !sortAsc, 'opacity-50': !(sortKey==='nama' && !sortAsc)}" class="w-3 h-3 -mt-1" fill="white" viewBox="0 0 20 20"><path d="M10 14l4-4H6l4 4z"/></svg>
                                </span>
                            </div>
                        </th>
                        <th class="p-3 cursor-pointer select-none" @click="sortBy('id')">
                            <div class="flex items-center gap-1 justify-center">
                                <span>ID Client</span>
                                <span class="flex flex-col">
                                    <svg :class="{'opacity-100': sortKey==='id' && sortAsc, 'opacity-50': !(sortKey==='id' && sortAsc)}" class="w-3 h-3" fill="white" viewBox="0 0 20 20"><path d="M10 6l-4 4h8l-4-4z"/></svg>
                                    <svg :class="{'opacity-100': sortKey==='id' && !sortAsc, 'opacity-50': !(sortKey==='id' && !sortAsc)}" class="w-3 h-3 -mt-1" fill="white" viewBox="0 0 20 20"><path d="M10 14l4-4H6l4 4z"/></svg>
                                </span>
                            </div>
                        </th>
                        <th class="p-3 cursor-pointer select-none" @click="sortBy('alamat')">
                            <div class="flex items-center gap-1 justify-center">
                                <span>Alamat</span>
                                <span class="flex flex-col">
                                    <svg :class="{'opacity-100': sortKey==='alamat' && sortAsc, 'opacity-50': !(sortKey==='alamat' && sortAsc)}" class="w-3 h-3" fill="white" viewBox="0 0 20 20"><path d="M10 6l-4 4h8l-4-4z"/></svg>
                                    <svg :class="{'opacity-100': sortKey==='alamat' && !sortAsc, 'opacity-50': !(sortKey==='alamat' && !sortAsc)}" class="w-3 h-3 -mt-1" fill="white" viewBox="0 0 20 20"><path d="M10 14l4-4H6l4 4z"/></svg>
                                </span>
                            </div>
                        </th>
                        <th class="p-3 cursor-pointer select-none" @click="sortBy('telepon')">
                            <div class="flex items-center gap-1 justify-center">
                                <span>Nomor Telepon</span>
                                <span class="flex flex-col">
                                    <svg :class="{'opacity-100': sortKey==='telepon' && sortAsc, 'opacity-50': !(sortKey==='telepon' && sortAsc)}" class="w-3 h-3" fill="white" viewBox="0 0 20 20"><path d="M10 6l-4 4h8l-4-4z"/></svg>
                                    <svg :class="{'opacity-100': sortKey==='telepon' && !sortAsc, 'opacity-50': !(sortKey==='telepon' && !sortAsc)}" class="w-3 h-3 -mt-1" fill="white" viewBox="0 0 20 20"><path d="M10 14l4-4H6l4 4z"/></svg>
                                </span>
                            </div>
                        </th>
                        <th class="p-3 cursor-pointer select-none" @click="sortBy('totalStok')">
                            <div class="flex items-center gap-1 justify-center">
                                <span>Stok Barang</span>
                                <span class="flex flex-col">
                                    <svg :class="{'opacity-100': sortKey==='totalStok' && sortAsc, 'opacity-50': !(sortKey==='totalStok' && sortAsc)}" class="w-3 h-3" fill="white" viewBox="0 0 20 20"><path d="M10 6l-4 4h8l-4-4z"/></svg>
                                    <svg :class="{'opacity-100': sortKey==='totalStok' && !sortAsc, 'opacity-50': !(sortKey==='totalStok' && !sortAsc)}" class="w-3 h-3 -mt-1" fill="white" viewBox="0 0 20 20"><path d="M10 14l4-4H6l4 4z"/></svg>
                                </span>
                            </div>
                        </th>
                        <th class="p-3 cursor-pointer select-none" @click="sortBy('bergabung')">
                            <div class="flex items-center gap-1 justify-center">
                                <span>Tanggal Bergabung</span>
                                <span class="flex flex-col">
                                    <svg :class="{'opacity-100': sortKey==='bergabung' && sortAsc, 'opacity-50': !(sortKey==='bergabung' && sortAsc)}" class="w-3 h-3" fill="white" viewBox="0 0 20 20"><path d="M10 6l-4 4h8l-4-4z"/></svg>
                                    <svg :class="{'opacity-100': sortKey==='bergabung' && !sortAsc, 'opacity-50': !(sortKey==='bergabung' && !sortAsc)}" class="w-3 h-3 -mt-1" fill="white" viewBox="0 0 20 20"><path d="M10 14l4-4H6l4 4z"/></svg>
                                </span>
                            </div>
                        </th>
                        <th class="p-3 cursor-pointer select-none" @click="sortBy('diperbaharui')">
                            <div class="flex items-center gap-1 justify-center">
                                <span>Diperbaharui</span>
                                <span class="flex flex-col">
                                    <svg :class="{'opacity-100': sortKey==='diperbaharui' && sortAsc, 'opacity-50': !(sortKey==='diperbaharui' && sortAsc)}" class="w-3 h-3" fill="white" viewBox="0 0 20 20"><path d="M10 6l-4 4h8l-4-4z"/></svg>
                                    <svg :class="{'opacity-100': sortKey==='diperbaharui' && !sortAsc, 'opacity-50': !(sortKey==='diperbaharui' && !sortAsc)}" class="w-3 h-3 -mt-1" fill="white" viewBox="0 0 20 20"><path d="M10 14l4-4H6l4 4z"/></svg>
                                </span>
                            </div>
                        </th>
                        <th class="p-3 text-center rounded-tr-xl">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    <template x-for="(client, idx) in paginatedClients" :key="client.id">
                        <tr>
                            <td class="p-3 text-center align-middle" x-text="client.nama"></td>
                            <td class="p-3 text-center align-middle" x-text="client.id"></td>
                            <td class="p-3 align-middle w-64" style="text-align:left;" x-text="client.alamat"></td>
                            <td class="p-3 text-center align-middle" x-text="client.telepon"></td>
                            <td class="p-3 text-center align-middle" x-text="getUniqueItemsCount(client) + ' item - ' + getTotalStokClient(client) + ' unit'"></td>
                            <td class="p-3 text-center align-middle" x-text="formatTanggalIndo(client.bergabung)"></td>
                            <td class="p-3 text-center align-middle" x-text="formatTanggalIndo(client.diperbaharui)"></td>
                            <td class="p-3 text-center align-middle">
                                <div class="relative">
                                    <button @click="openActionMenuIndex = openActionMenuIndex === idx ? null : idx" class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-gray-100">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><circle cx="12" cy="6" r="1.5"/><circle cx="12" cy="12" r="1.5"/><circle cx="12" cy="18" r="1.5"/></svg>
                                    </button>
                                    <div x-show="openActionMenuIndex === idx" x-transition class="absolute right-0 mt-2 w-32 bg-white rounded-xl shadow-lg border border-gray-100 z-10">
                                        <button @click="showDetail(client); openActionMenuIndex = null" class="block w-full text-left px-4 py-2 hover:bg-[#eafbe6] rounded-t-xl">Detail</button>
                                        <button @click="openEditModal(client, idx); openActionMenuIndex = null" class="block w-full text-left px-4 py-2 hover:bg-[#eafbe6]">Edit</button>
                                        <button @click="deleteClient(client); openActionMenuIndex = null" class="block w-full text-left px-4 py-2 hover:bg-[#ffeaea] text-red-600 rounded-b-xl">Hapus</button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
        
        <!-- Filter status indicator -->
        <div x-show="dateFilter" class="mt-4 p-3 bg-blue-50 border border-blue-200 rounded-lg">
            <div class="flex items-center gap-2 text-sm text-blue-700">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.207A1 1 0 013 6.5V4z" />
                </svg>
                <span x-text="getFilterStatusText()"></span>
                <span class="font-semibold" x-text="'(' + filteredClients.length + ' dari ' + clients.length + ' client)'"></span>
            </div>
        </div>
        
        <!-- Pagination -->
        <div class="flex justify-center mt-4">
            <nav class="flex items-center space-x-2">
                <button @click="prevPage" :disabled="currentPage === 1" class="w-8 h-8 flex items-center justify-center rounded-full border border-gray-300 text-gray-500 hover:bg-gray-100 disabled:opacity-50">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
                </button>
                
                <!-- First page -->
                <template x-if="totalPages > 0">
                    <button @click="goToPage(1)" :class="{'bg-[#28C328] text-white': currentPage === 1, 'bg-white text-gray-700': currentPage !== 1 }" class="w-8 h-8 flex items-center justify-center rounded-full border border-gray-300 font-semibold">
                        <span>1</span>
                    </button>
                </template>
                
                <!-- Ellipsis before current page -->
                <template x-if="currentPage > 3">
                    <span class="w-8 h-8 flex items-center justify-center text-gray-500">...</span>
                </template>
                
                <!-- Pages around current page -->
                <template x-for="page in getVisiblePages()" :key="page">
                    <template x-if="page !== 1 && page !== totalPages">
                        <button @click="goToPage(page)" :class="{'bg-[#28C328] text-white': currentPage === page, 'bg-white text-gray-700': currentPage !== page }" class="w-8 h-8 flex items-center justify-center rounded-full border border-gray-300 font-semibold">
                            <span x-text="page"></span>
                        </button>
                    </template>
                </template>
                
                <!-- Ellipsis after current page -->
                <template x-if="currentPage < totalPages - 2">
                    <span class="w-8 h-8 flex items-center justify-center text-gray-500">...</span>
                </template>
                
                <!-- Last page -->
                <template x-if="totalPages > 1">
                    <button @click="goToPage(totalPages)" :class="{'bg-[#28C328] text-white': currentPage === totalPages, 'bg-white text-gray-700': currentPage !== totalPages }" class="w-8 h-8 flex items-center justify-center rounded-full border border-gray-300 font-semibold">
                        <span x-text="totalPages"></span>
                    </button>
                </template>
                
                <button @click="nextPage" :disabled="currentPage === totalPages" class="w-8 h-8 flex items-center justify-center rounded-full border border-gray-300 text-gray-500 hover:bg-gray-100 disabled:opacity-50">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                </button>
            </nav>
        </div>
    </div>
    <!-- Modal Detail Client dengan desain yang lebih baik -->
    <div x-show="showDetailModal" x-transition class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 p-4" x-cloak @click.self="showDetailModal = false">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-4xl max-h-[90vh] flex flex-col" @click.stop>
            <div class="p-6 overflow-y-auto flex-1">
            <button @click="showDetailModal = false" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
            

            
            <!-- Informasi detail dalam grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Kolom kiri -->
                <div class="space-y-4">
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h3 class="font-semibold text-gray-700 mb-2 flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-[#28C328]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                            Alamat
                        </h3>
                        <p class="text-gray-600" x-text="detailClient.alamat"></p>
                    </div>
                    
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h3 class="font-semibold text-gray-700 mb-2 flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-[#28C328]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" /></svg>
                            Nomor Telepon
                        </h3>
                        <p class="text-gray-600" x-text="detailClient.telepon"></p>
                    </div>
                    
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h3 class="font-semibold text-gray-700 mb-2 flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-[#28C328]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                            Tanggal Bergabung
                        </h3>
                        <p class="text-gray-600" x-text="formatTanggalIndo(detailClient.bergabung)"></p>
                    </div>
                    
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h3 class="font-semibold text-gray-700 mb-2 flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-[#28C328]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            Terakhir Diperbaharui
                        </h3>
                        <p class="text-gray-600" x-text="formatTanggalIndo(detailClient.diperbaharui)"></p>
                    </div>
                </div>
                
                <!-- Kolom kanan -->
                <div class="space-y-4">
                    <!-- Nama Client -->
                    <div class="bg-gradient-to-r from-[#28C328] to-[#22a322] rounded-lg p-4 text-white">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                                <span class="text-white text-lg font-bold" x-text="detailClient.nama?.charAt(0)?.toUpperCase()"></span>
                            </div>
                            <div>
                                <h2 class="text-xl font-bold" x-text="detailClient.nama"></h2>
                                <p class="text-white text-opacity-80 text-sm" x-text="'ID: ' + detailClient.id"></p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h3 class="font-semibold text-gray-700 mb-2 flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-[#28C328]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg>
                            Stok yang Dimiliki Client
                        </h3>
                        <template x-if="detailClient.items && detailClient.items.length > 0">
                            <div class="space-y-3 max-h-48 overflow-y-auto">
                                <!-- Header untuk list stok -->
                                <div class="grid grid-cols-3 gap-2 text-xs font-semibold text-gray-600 border-b border-gray-200 pb-2 sticky top-0 bg-gray-50">
                                    <span>Nama Item</span>
                                    <span class="text-center">Jumlah</span>
                                    <span class="text-right">Total Nilai</span>
                                </div>
                                
                                <!-- List stok items (tampilkan semua item tanpa menggabungkan) -->
                                <template x-for="(item, index) in detailClient.items" :key="index">
                                    <div class="grid grid-cols-3 gap-2 text-sm border-b border-gray-100 pb-2 last:border-b-0">
                                        <span class="text-gray-800 font-medium">
                                            <span x-text="item.stokNama || item.nama || 'Nama tidak tersedia'"></span>
                                        </span>
                                        <span class="text-center font-semibold text-[#28C328]" x-text="(item.stokJumlah || item.jumlah || 0) + ' unit'"></span>
                                        <span class="text-right text-gray-600" x-text="'Rp ' + ((getItemPrice(item.stokId) * (item.stokJumlah || item.jumlah || 0))).toLocaleString('id-ID')"></span>
                                    </div>
                                </template>
                                
                                <!-- Total summary -->
                                <div class="pt-2 border-t border-gray-200 mt-3 sticky bottom-0 bg-gray-50">
                                    <div class="flex justify-between items-center text-sm">
                                        <span class="font-semibold text-gray-700">Total Stok:</span>
                                        <span class="font-bold text-[#28C328]" x-text="getTotalStokClient(detailClient) + ' unit'"></span>
                                    </div>
                                    <div class="flex justify-between items-center text-sm">
                                        <span class="font-semibold text-gray-700">Total Nilai:</span>
                                        <span class="font-bold text-[#28C328]" x-text="'Rp ' + getTotalValueClient(detailClient).toLocaleString('id-ID')"></span>
                                    </div>
                                </div>
                            </div>
                        </template>
                        <template x-if="!detailClient.items || detailClient.items.length === 0">
                            <div class="text-center py-4">
                                <div class="text-gray-400 text-sm" x-text="(detailClient.stokNama || 'Nama tidak tersedia') + ' - ' + (detailClient.stokJumlah || 0) + ' unit'"></div>
                                <div class="text-xs text-gray-300 mt-1">Data lama (akan diupdate otomatis)</div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
            
            <!-- History Pembelian -->
            <div class="mt-6 pt-4 border-t border-gray-200">
                <h3 class="text-lg font-bold text-gray-800 mb-3 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-[#28C328]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    History Pembelian
                </h3>
                <div class="bg-gray-50 rounded-xl p-4 max-h-48 overflow-y-auto">
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-xs">
                            <thead class="sticky top-0">
                                <tr class="bg-[#28C328] text-white">
                                    <th class="p-2 text-left">Tanggal</th>
                                    <th class="p-2 text-left">Item</th>
                                    <th class="p-2 text-left">Jumlah</th>
                                    <th class="p-2 text-left">Total Harga</th>
                                    <th class="p-2 text-left">Nama Sales</th>
                                    <th class="p-2 text-left">Status</th>
                                    <th class="p-2 text-left">Invoice</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <template x-for="history in clientHistory[detailClient.id] || []" :key="history.id">
                                    <tr>
                                        <td class="p-2" x-text="formatHistoryDate(history.timestamp)"></td>
                                        <td class="p-2" x-text="getHistoryItemName(history)"></td>
                                        <td class="p-2" x-text="getHistoryItemCount(history) + ' unit'"></td>
                                        <td class="p-2" x-text="'Rp ' + getHistoryTotalPrice(history).toLocaleString('id-ID')"></td>
                                        <td class="p-2" x-text="getHistorySalesName(history) || '-'"></td>
                                        <td class="p-2">
                                            <span class="px-2 py-1 rounded-full text-xs font-semibold" 
                                                  :class="getActionClass(history.action)" 
                                                  x-text="getActionText(history.action)"></span>
                                        </td>
                                        <td class="p-2">
                                            <button class="px-2 py-1 bg-[#28C328] text-white rounded text-[11px]" @click="openInvoiceFromHistory(history)">Lihat Invoice</button>
                                        </td>
                                    </tr>
                                </template>
                                <template x-if="(clientHistory[detailClient.id] || []).length === 0">
                                    <tr>
                                        <td colspan="7" class="p-4 text-center text-gray-500">
                                            Belum ada history pembelian
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Invoice list removed; handled via per-row action and modal -->
            
            </div>
            
            <!-- Footer dengan tombol aksi -->
            <div class="flex justify-end gap-3 p-6 border-t border-gray-200 bg-gray-50">
                <button @click="openEditModal(detailClient, getClientIndex(detailClient.id)); showDetailModal = false" class="px-6 py-2 bg-[#28C328] text-white rounded-lg hover:bg-[#22a322] transition-colors">
                    Edit Client
                </button>
                <button @click="showDetailModal = false" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">
                    Tutup
                </button>
            </div>
        </div>
    </div>
    <!-- Modal Tambah Client - Tahap 1 (Data Client) -->
    <div x-show="showAddModal" x-transition class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40" x-cloak @click.self="closeAddModal()">
        <div class="bg-white rounded-2xl shadow-xl p-6 w-full max-w-2xl mx-2 relative overflow-y-auto max-h-[70vh]" @click.stop>
            <button @click="closeAddModal()" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 text-2xl font-bold">&times;</button>
            <div class="text-center mb-6">
                <h2 class="text-xl font-bold text-[#28C328]">Tahap 1: Data Client</h2>
                <p class="text-gray-600 text-sm">Lengkapi informasi client terlebih dahulu</p>
            </div>
            <form class="grid grid-cols-1 md:grid-cols-2 gap-4" @submit.prevent="nextToStep2()">
                <div x-show="addErrorMsg" class="col-span-1 md:col-span-2 text-red-500 text-sm mb-2" x-text="addErrorMsg"></div>
                <div class="relative">
                    <label class="block font-semibold mb-2">Nama Client</label>
                    <input type="text" 
                           x-model="addNama" 
                           @input="selectedExistingClientNew = null; generateClientIdIfNeeded()"
                           @focus="showNamaSuggestionNew = true" 
                           @blur="setTimeout(() => showNamaSuggestionNew = false, 150)"
                           class="w-full rounded-lg border border-gray-300 px-4 py-2 text-gray-700 placeholder-gray-400" 
                           placeholder="Masukan Nama Client">
                    
                    <!-- Dropdown suggestion nama client -->
                    <div x-show="showNamaSuggestionNew" class="absolute left-0 right-0 bg-white border border-gray-200 rounded-lg shadow-lg z-20 max-h-48 overflow-y-auto mt-1">
                        <template x-for="client in filteredNamaClientsNew" :key="client.id">
                            <div @mousedown.prevent="selectExistingClientNew(client)" class="px-4 py-2 cursor-pointer hover:bg-[#eafbe6] border-b border-gray-100 last:border-b-0">
                                <div class="font-medium text-gray-800" x-text="client.nama"></div>
                                <div class="text-xs text-gray-500" x-text="'ID: ' + client.id + ' | ' + client.alamat"></div>
                            </div>
                        </template>
                        <template x-if="filteredNamaClientsNew.length === 0">
                            <div class="px-4 py-2 text-gray-400">Tidak ada client dengan nama tersebut</div>
                        </template>
                    </div>
                </div>
                <div>
                    <label class="block font-semibold mb-2">ID Client</label>
                    <input type="text" x-model="addId" class="w-full rounded-lg border border-gray-300 px-4 py-2 text-gray-700 placeholder-gray-400" placeholder="Masukan ID Client">
                </div>
                <div>
                    <label class="block font-semibold mb-2">Alamat</label>
                    <textarea x-model="addAlamat" rows="3" class="w-full rounded-lg border border-gray-300 px-4 py-2 text-gray-700 placeholder-gray-400 resize-none" placeholder="Masukan Alamat Lengkap"></textarea>
                </div>
                <div>
                    <label class="block font-semibold mb-2">Nomor Telepon</label>
                    <input type="text" x-model="addTelepon" class="w-full rounded-lg border border-gray-300 px-4 py-2 text-gray-700 placeholder-gray-400" placeholder="Masukan Nomor Telepon">
                </div>
                <div>
                    <label class="block font-semibold mb-2">Nama Sales</label>
                    <input type="text" x-model="addNamaSales" class="w-full rounded-lg border border-gray-300 px-4 py-2 text-gray-700 placeholder-gray-400" placeholder="Masukan Nama Sales">
                </div>
                <div>
                    <label class="block font-semibold mb-2">Tanggal Bergabung</label>
                    <input type="date" x-model="addBergabung" class="w-full rounded-lg border border-gray-300 px-4 py-2 text-gray-700">
                </div>
                <!-- Tombol bawah -->
                <div class="col-span-2 flex flex-col md:flex-row gap-2 mt-2">
                    <button type="submit" class="w-full rounded-lg bg-[#28C328] text-white font-semibold py-3 text-lg hover:bg-[#22a322] transition">Selanjutnya</button>
                </div>
                <div class="col-span-2 flex flex-col md:flex-row gap-2">
                    <button type="reset" @click.prevent="resetAddForm()" class="w-full rounded-lg bg-gray-200 text-gray-500 font-semibold py-3 text-lg">Reset</button>
                    <button type="button" @click="closeAddModal()" class="w-full rounded-lg bg-gray-200 text-gray-500 font-semibold py-3 text-lg">Batal</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Tambah Client - Tahap 2 (Data Items) -->
    <div x-show="showAddStep2Modal" x-transition class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40" x-cloak @click.self="backToStep1()">
        <div class="bg-white rounded-2xl shadow-xl p-6 w-full max-w-4xl mx-2 relative overflow-y-auto max-h-[80vh]" @click.stop>
            <button @click="backToStep1()" class="absolute top-4 left-4 text-gray-400 hover:text-gray-600 text-2xl font-bold">&larr;</button>
            <button @click="closeAddModal()" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 text-2xl font-bold">&times;</button>
            <div class="text-center mb-6">
                <h2 class="text-xl font-bold text-[#28C328]">Tahap 2: Data Items</h2>
                <p class="text-gray-600 text-sm">Pilih item yang akan dibeli client</p>
            </div>
            
            <!-- Informasi Client -->
            <div class="bg-gray-50 rounded-lg p-4 mb-6">
                <h3 class="font-semibold text-gray-800 mb-2">Informasi Client:</h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-2 text-sm">
                    <div><span class="text-gray-600">Nama:</span> <span class="font-semibold" x-text="addNama"></span></div>
                    <div><span class="text-gray-600">ID:</span> <span class="font-semibold" x-text="addId"></span></div>
                    <div><span class="text-gray-600">Telepon:</span> <span class="font-semibold" x-text="addTelepon"></span></div>
                    <div><span class="text-gray-600">Tanggal:</span> <span class="font-semibold" x-text="addBergabung"></span></div>
                </div>
            </div>

            <!-- Daftar Item Stok -->
            <div class="mb-6">
                <div class="flex items-center justify-between mb-3">
                    <div>
                        <h3 class="font-semibold text-gray-800">Pilih Item dari Stok:</h3>
                        <div class="text-sm text-gray-600 mt-1">
                            Menampilkan <span x-text="Math.min((stockItemCurrentPage - 1) * stockItemPerPage + 1, filteredStockItems.length)"></span> sampai 
                            <span x-text="Math.min(stockItemCurrentPage * stockItemPerPage, filteredStockItems.length)"></span> 
                            dari <span x-text="filteredStockItems.length"></span> item
                        </div>
                    </div>
                    <div class="flex items-center bg-gray-50 rounded-lg px-3 py-1 border w-48">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-gray-400 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 105 11a6 6 0 0012 0z" />
                        </svg>
                        <input type="text" x-model="stockItemSearch" class="flex-1 bg-transparent border-none outline-none text-sm" placeholder="Cari item..." />
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="bg-[#28C328] text-white">
                                <th class="p-2 text-left">Nama Item</th>
                                <th class="p-2 text-left">SKU</th>
                                <th class="p-2 text-left">Stok Tersedia</th>
                                <th class="p-2 text-left">Harga Satuan</th>
                                <th class="p-2 text-left">Quantity</th>
                                <th class="p-2 text-left">Subtotal</th>
                                <th class="p-2 text-left">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            <template x-for="item in paginatedStockItems" :key="item.id">
                                <tr>
                                    <td class="p-2" x-text="item.nama"></td>
                                    <td class="p-2 font-mono text-xs" x-text="item.sku"></td>
                                    <td class="p-2" x-text="Number(item.tersedia).toLocaleString('id-ID')"></td>
                                    <td class="p-2">Rp<span x-text="Number(item.harga).toLocaleString('id-ID')"></span></td>
                                    <td class="p-2">
                                        <input type="number" 
                                               x-model="item.selectedQuantity" 
                                               @input="updateItemSubtotal(item)"
                                               class="w-16 rounded border border-gray-300 px-2 py-1 text-sm" 
                                               min="0" 
                                               placeholder="0">
                                    </td>
                                    <td class="p-2 font-semibold">
                                        <span x-show="item.selectedQuantity > 0">Rp<span x-text="(Number(item.harga) * Number(item.selectedQuantity || 0)).toLocaleString('id-ID')"></span></span>
                                        <span x-show="item.selectedQuantity <= 0" class="text-gray-400">-</span>
                                    </td>
                                    <td class="p-2">
                                        <button type="button" 
                                                @click="addItemToCart(item)" 
                                                :disabled="!item.selectedQuantity || item.selectedQuantity <= 0"
                                                class="px-3 py-1 rounded text-xs font-semibold disabled:opacity-50 disabled:cursor-not-allowed"
                                                :class="item.selectedQuantity > 0 ? 'bg-[#28C328] text-white hover:bg-[#22a322]' : 'bg-gray-300 text-gray-500'">
                                            Tambah
                                        </button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination untuk Stock Items -->
                <div x-show="filteredStockItems.length > stockItemPerPage" class="flex justify-center mt-4">
                    <nav class="flex items-center space-x-2">
                        <button @click="prevStockPage()" :disabled="stockItemCurrentPage === 1" class="w-8 h-8 flex items-center justify-center rounded-full border border-gray-300 text-gray-500 hover:bg-gray-100 disabled:opacity-50">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
                        </button>
                        
                        <!-- First page -->
                        <template x-if="stockItemTotalPages > 0">
                            <button @click="goToStockPage(1)" :class="{'bg-[#28C328] text-white': stockItemCurrentPage === 1, 'bg-white text-gray-700': stockItemCurrentPage !== 1 }" class="w-8 h-8 flex items-center justify-center rounded-full border border-gray-300 font-semibold">
                                <span>1</span>
                            </button>
                        </template>
                        
                        <!-- Ellipsis before current page -->
                        <template x-if="stockItemCurrentPage > 3">
                            <span class="w-8 h-8 flex items-center justify-center text-gray-500">...</span>
                        </template>
                        
                        <!-- Pages around current page -->
                        <template x-for="page in getVisibleStockPages()" :key="page">
                            <template x-if="page !== 1 && page !== stockItemTotalPages">
                                <button @click="goToStockPage(page)" :class="{'bg-[#28C328] text-white': stockItemCurrentPage === page, 'bg-white text-gray-700': stockItemCurrentPage !== page }" class="w-8 h-8 flex items-center justify-center rounded-full border border-gray-300 font-semibold">
                                    <span x-text="page"></span>
                                </button>
                            </template>
                        </template>
                        
                        <!-- Ellipsis after current page -->
                        <template x-if="stockItemCurrentPage < stockItemTotalPages - 2">
                            <span class="w-8 h-8 flex items-center justify-center text-gray-500">...</span>
                        </template>
                        
                        <!-- Last page -->
                        <template x-if="stockItemTotalPages > 1">
                            <button @click="goToStockPage(stockItemTotalPages)" :class="{'bg-[#28C328] text-white': stockItemCurrentPage === stockItemTotalPages, 'bg-white text-gray-700': stockItemCurrentPage !== stockItemTotalPages }" class="w-8 h-8 flex items-center justify-center rounded-full border border-gray-300 font-semibold">
                                <span x-text="stockItemTotalPages"></span>
                            </button>
                        </template>
                        
                        <button @click="nextStockPage()" :disabled="stockItemCurrentPage === stockItemTotalPages" class="w-8 h-8 flex items-center justify-center rounded-full border border-gray-300 text-gray-500 hover:bg-gray-100 disabled:opacity-50">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                        </button>
                    </nav>
                </div>
            </div>

            <!-- Keranjang Items -->
            <div class="bg-green-50 rounded-lg p-4 mb-6">
                <h3 class="font-semibold text-green-800 mb-3">Keranjang Items:</h3>
                <div x-show="cartItems.length === 0" class="text-center text-gray-500 py-4">
                    Belum ada item yang dipilih
                </div>
                <div x-show="cartItems.length > 0" class="space-y-3">
                    <template x-for="(cartItem, idx) in cartItems" :key="cartItem.id">
                        <div class="grid grid-cols-12 gap-3 items-center bg-white rounded-lg p-3 border border-green-200">
                            <div class="col-span-6 flex items-center gap-3">
                                <div>
                                    <div class="font-semibold text-gray-800 leading-tight" x-text="cartItem.nama"></div>
                                    <div class="text-[11px] text-gray-500 uppercase tracking-wide">SKU: <span x-text="cartItem.sku"></span></div>
                                </div>
                            </div>
                            <div class="col-span-3 text-sm text-gray-600 text-right">
                                <span x-text="cartItem.selectedQuantity"></span>
                                <span class="text-gray-400">x</span>
                                <span>Rp<span x-text="Number(cartItem.harga).toLocaleString('id-ID')"></span></span>
                            </div>
                            <div class="col-span-2 text-right font-semibold text-green-800">
                                Rp<span x-text="(Number(cartItem.harga) * Number(cartItem.selectedQuantity)).toLocaleString('id-ID')"></span>
                            </div>
                            <div class="col-span-1 text-right">
                                <button @click="removeFromCart(idx)" class="text-red-500 hover:text-red-700">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </template>

                    <!-- Total Order Summary -->
                    <div class="bg-white rounded-xl border border-green-200 divide-y divide-green-100 mt-4">
                        <div class="flex justify-between items-center px-4 py-3">
                            <span class="font-semibold text-gray-700">Total Items:</span>
                            <span class="font-semibold text-gray-900" x-text="cartItems.length"></span>
                        </div>
                        <div class="flex justify-between items-center px-4 py-3">
                            <span class="font-semibold text-gray-700">Total Quantity:</span>
                            <span class="font-semibold text-gray-900" x-text="Number(cartTotalQuantity || 0).toLocaleString('id-ID')"></span>
                        </div>
                        <div class="flex justify-between items-center px-4 py-3">
                            <span class="font-semibold text-gray-700">Subtotal:</span>
                            <span class="font-semibold text-gray-900">Rp<span x-text="(cartSubtotal || 0).toLocaleString('id-ID')"></span></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Catatan -->
            <div class="bg-blue-50 rounded-lg p-4 mb-6">
                <h3 class="font-semibold text-blue-800 mb-3">Catatan</h3>
                <textarea x-model="addNotes" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm resize-none" rows="3" placeholder="Tambahkan catatan (opsional)"></textarea>
            </div>

            <!-- Diskon Reguler -->
            <div class="bg-blue-50 rounded-lg p-4 mb-6">
                <h3 class="font-semibold text-blue-800 mb-3">Pengaturan Diskon Reguler</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block font-semibold mb-2 text-sm">Tipe Diskon</label>
                        <select x-model="addDiskonTipe" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                            <option value="rupiah">Rupiah</option>
                            <option value="persen">Persen (%)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-semibold mb-2 text-sm">Nilai Diskon</label>
                        <input type="number" x-model="addDiskonNilai" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm" placeholder="0" min="0">
                    </div>
                </div>
                <div x-show="addDiskonNilai > 0" class="mt-2 text-sm">
                    <span class="text-blue-700">Diskon Reguler: </span>
                    <span x-show="addDiskonTipe === 'rupiah'" class="font-semibold text-blue-800">Rp <span x-text="Number(addDiskonNilai).toLocaleString('id-ID')"></span></span>
                    <span x-show="addDiskonTipe === 'persen'" class="font-semibold text-blue-800"><span x-text="addDiskonNilai"></span>%</span>
                </div>
            </div>

            <!-- Diskon Ball -->
            <div class="bg-purple-50 rounded-lg p-4 mb-6">
                <h3 class="font-semibold text-purple-800 mb-3">Pengaturan Diskon Ball</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block font-semibold mb-2 text-sm">Tipe Diskon Ball</label>
                        <select x-model="addDiskonBallTipe" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                            <option value="rupiah">Rupiah</option>
                            <option value="persen">Persen (%)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-semibold mb-2 text-sm">Nilai Diskon Ball</label>
                        <input type="number" x-model="addDiskonBallNilai" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm" placeholder="0" min="0">
                    </div>
                </div>
                <div x-show="addDiskonBallNilai > 0" class="mt-2 text-sm">
                    <span class="text-purple-700">Diskon Ball: </span>
                    <span x-show="addDiskonBallTipe === 'rupiah'" class="font-semibold text-purple-800">Rp <span x-text="Number(addDiskonBallNilai).toLocaleString('id-ID')"></span></span>
                    <span x-show="addDiskonBallTipe === 'persen'" class="font-semibold text-purple-800"><span x-text="addDiskonBallNilai"></span>%</span>
                </div>
            </div>

            <!-- Pengaturan Ongkir -->
            <div class="bg-orange-50 rounded-lg p-4 mb-6">
                <h3 class="font-semibold text-orange-800 mb-3">Pengaturan Ongkir</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-semibold mb-2 text-sm">Nama Ekspedisi</label>
                        <input type="text" x-model="addNamaEkspedisi" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm" placeholder="Contoh: JNE, SiCepat, J&T">
                    </div>
                    <div>
                        <label class="block font-semibold mb-2 text-sm">Biaya Ongkir</label>
                        <input type="number" x-model="addOngkir" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm" placeholder="0" min="0">
                    </div>
                </div>
                <div x-show="addOngkir > 0" class="mt-2 text-sm">
                    <span class="text-orange-700">Ongkir: </span>
                    <span class="font-semibold text-orange-800">Rp <span x-text="Number(addOngkir).toLocaleString('id-ID')"></span></span>
                    <span x-show="addNamaEkspedisi" class="text-orange-700 ml-2">via <span class="font-semibold" x-text="addNamaEkspedisi"></span></span>
                </div>
            </div>

            <!-- Total dan Tombol -->
            <div class="bg-gray-50 rounded-lg p-4 mb-6">
                <div class="flex justify-between items-center mb-2">
                    <span class="font-semibold text-gray-700">Total Items:</span>
                    <span class="font-semibold text-gray-800" x-text="cartItems.length"></span>
                </div>
                <div class="flex justify-between items-center mb-2">
                    <span class="font-semibold text-gray-700">Total Quantity:</span>
                    <span class="font-semibold text-gray-800" x-text="Number(cartTotalQuantity).toLocaleString('id-ID')"></span>
                </div>
                <div class="flex justify-between items-center mb-2">
                    <span class="font-semibold text-gray-700">Subtotal:</span>
                    <span class="font-semibold text-gray-800">Rp<span x-text="cartSubtotal.toLocaleString('id-ID')"></span></span>
                </div>
                <div x-show="addDiskonNilai > 0" class="flex justify-between items-center mb-2">
                    <span class="text-gray-600">Diskon Reguler:</span>
                    <span class="text-red-600">
                        <span x-show="addDiskonTipe === 'rupiah'">-Rp<span x-text="Number(addDiskonNilai).toLocaleString('id-ID')"></span></span>
                        <span x-show="addDiskonTipe === 'persen'">-<span x-text="addDiskonNilai"></span>%</span>
                    </span>
                </div>
                <div x-show="addDiskonBallNilai > 0" class="flex justify-between items-center mb-2">
                    <span class="text-gray-600">Diskon Ball:</span>
                    <span class="text-red-600">
                        <span x-show="addDiskonBallTipe === 'rupiah'">-Rp<span x-text="Number(addDiskonBallNilai).toLocaleString('id-ID')"></span></span>
                        <span x-show="addDiskonBallTipe === 'persen'">-<span x-text="addDiskonBallNilai"></span>%</span>
                    </span>
                </div>
                <div x-show="addOngkir > 0" class="flex justify-between items-center mb-2">
                    <span class="text-gray-600">Ongkir:</span>
                    <span class="text-green-600">+Rp<span x-text="Number(addOngkir).toLocaleString('id-ID')"></span></span>
                </div>
                <div class="flex justify-between items-center mb-4 pt-2 border-t border-gray-200">
                    <span class="text-lg font-bold text-gray-800">Total Bayar:</span>
                    <span class="text-xl font-bold text-[#28C328]">Rp<span x-text="cartTotalPayable.toLocaleString('id-ID')"></span></span>
                </div>
            </div>

            <!-- Tombol Aksi -->
            <div class="flex flex-col md:flex-row gap-4">
                <button type="button" @click="backToStep1()" class="flex-1 rounded-lg bg-gray-200 text-gray-700 font-semibold py-3 hover:bg-gray-300 transition">Kembali</button>
                <button type="button" @click="submitAddForm()" :disabled="cartItems.length === 0" class="flex-1 rounded-lg bg-[#28C328] text-white font-semibold py-3 hover:bg-[#22a322] transition disabled:opacity-50 disabled:cursor-not-allowed">Simpan Client</button>
            </div>
        </div>
    </div>

    <!-- Modal Tambah Client (Lama - untuk kompatibilitas) -->
    <div x-show="showAddModalOld" x-transition class="fixed inset-0 z-50 flex items-center justify-center" style="background:rgba(0,0,0,0.15);" x-cloak @click.self="closeAddModal()">
        <div class="relative bg-white rounded-2xl shadow-xl p-8 w-full max-w-3xl mx-4" style="min-width:340px;" @click.stop>
            <button @click="resetAddForm(); closeAddModal()" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 text-2xl font-bold">&times;</button>
            <form class="grid grid-cols-1 md:grid-cols-2 gap-8" @submit.prevent="submitAddForm()">
                <div x-show="addErrorMsg" class="col-span-1 md:col-span-2 text-red-500 text-sm mb-2" x-text="addErrorMsg"></div>
                <!-- Kiri -->
                <div class="flex flex-col gap-4">
                    <div class="relative">
                        <label class="block font-semibold mb-2">Nama Client</label>
                        <input 
                            type="text" 
                            x-model="addNama" 
                            @input="selectedExistingClient = null; generateClientIdIfNeeded()"
                            @focus="showNamaSuggestion = true" 
                            @blur="setTimeout(() => showNamaSuggestion = false, 150)"
                            class="w-full rounded-lg border border-gray-300 px-4 py-2 text-gray-700 placeholder-gray-400" 
                            placeholder="Masukan Nama Client"
                        >
                        <!-- Dropdown suggestion nama client -->
                        <div x-show="showNamaSuggestion" class="absolute left-0 right-0 bg-white border border-gray-200 rounded-lg shadow-lg z-20 max-h-48 overflow-y-auto mt-1">
                            <template x-for="client in filteredNamaClients" :key="client.id">
                                <div @mousedown.prevent="selectExistingClient(client)" class="px-4 py-2 cursor-pointer hover:bg-[#eafbe6] border-b border-gray-100 last:border-b-0">
                                    <div class="font-medium text-gray-800" x-text="client.nama"></div>
                                    <div class="text-xs text-gray-500" x-text="'ID: ' + client.id + ' | ' + client.alamat"></div>
                                </div>
                            </template>
                            <template x-if="filteredNamaClients.length === 0">
                                <div class="px-4 py-2 text-gray-400">Tidak ada client dengan nama tersebut</div>
                            </template>
                        </div>
                    </div>
                    <div>
                        <label class="block font-semibold mb-2">ID Client (Otomatis)</label>
                        <input type="text" x-model="addId" readonly class="w-full rounded-lg border border-gray-300 px-4 py-2 text-gray-700 bg-gray-50 cursor-not-allowed" placeholder="ID Client akan otomatis ter-generate">
                        <div x-show="addId" class="text-xs text-green-600 mt-1">✓ Client ID siap digunakan</div>
                    </div>
                    <div>
                        <label class="block font-semibold mb-2">Alamat</label>
                        <textarea x-model="addAlamat" rows="3" class="w-full rounded-lg border border-gray-300 px-4 py-2 text-gray-700 placeholder-gray-400 resize-none" placeholder="Masukan Alamat Lengkap"></textarea>
                    </div>
                </div>
                <!-- Kanan -->
                <div class="flex flex-col gap-4">
                    <div>
                        <label class="block font-semibold mb-2">Nomor Telepon</label>
                        <input type="text" x-model="addTelepon" class="w-full rounded-lg border border-gray-300 px-4 py-2 text-gray-700 placeholder-gray-400" placeholder="Masukan Nomor Telepon">
                    </div>
                    <div>
                        <label class="block font-semibold mb-2">Nama Sales</label>
                        <input type="text" x-model="addNamaSales" class="w-full rounded-lg border border-gray-300 px-4 py-2 text-gray-700 placeholder-gray-400" placeholder="Masukan Nama Sales">
                    </div>
                    <div>
                        <label class="block font-semibold mb-2">Item yang Dibeli</label>
                        <div class="space-y-3">
                            <!-- Dynamic items list -->
                            <template x-for="(item, index) in addItems" :key="index">
                                <div class="flex gap-2 items-end">
                                    <div class="flex-1">
                                        <select x-model="item.stokId" @change="onStokChange($event, index)" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-gray-700 bg-white text-sm">
                                            <option value="">Pilih Item</option>
                                            <template x-for="stok in availableStok" :key="stok.id">
                                                <option :value="stok.id" x-text="stok.nama + ' - Stok: ' + stok.tersedia + ' unit'"></option>
                                            </template>
                                        </select>
                                    </div>
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2">
                                            <input type="number" x-model="item.jumlah" min="1" class="flex-1 rounded-lg border border-gray-300 px-3 py-2 text-gray-700 placeholder-gray-400 text-sm" placeholder="Jumlah">
                                        </div>
                                    </div>
                                    <button type="button" @click="removeItem(index)" class="px-3 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition text-sm">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </template>
                            
                            <!-- Add new item button -->
                            <button type="button" @click="addNewItem()" class="w-full py-2 border-2 border-dashed border-gray-300 rounded-lg text-gray-500 hover:border-[#28C328] hover:text-[#28C328] transition flex items-center justify-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                Tambah Item Lain
                            </button>
                        </div>
                    </div>
                    <div>
                        <label class="block font-semibold mb-2">Tanggal Bergabung</label>
                        <input type="date" x-model="addBergabung" class="w-full rounded-lg border border-gray-300 px-4 py-2 text-gray-700">
                    </div>
                </div>
                <!-- Tombol bawah -->
                <div class="col-span-1 md:col-span-2 flex flex-col md:flex-row gap-4 mt-6">
                    <button type="submit" class="w-full rounded-lg bg-[#28C328] text-white font-semibold py-3 text-lg hover:bg-[#22a322] transition">Simpan</button>
                </div>
                <div class="col-span-1 md:col-span-2 flex flex-col md:flex-row gap-4">
                    <button type="reset" @click.prevent="resetAddForm()" class="w-full rounded-lg bg-gray-200 text-gray-500 font-semibold py-3 text-lg">Reset</button>
                    <button type="button" @click="resetAddForm(); closeAddModal()" class="w-full rounded-lg bg-gray-200 text-gray-500 font-semibold py-3 text-lg">Batal</button>
                </div>
            </form>
        </div>
    </div>
    <!-- Modal Edit Client -->
    <div x-show="showEditModal" x-transition class="fixed inset-0 z-50 flex items-center justify-center" style="background:rgba(0,0,0,0.15);" x-cloak @click.self="closeEditModal()">
        <div class="relative bg-white rounded-2xl shadow-xl p-8 w-full max-w-3xl mx-4" style="min-width:340px;" @click.stop>
            <button @click="resetEditForm(); closeEditModal()" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 text-2xl font-bold">&times;</button>
            <form class="grid grid-cols-1 md:grid-cols-2 gap-8" @submit.prevent="submitEditForm()">
                <div x-show="editErrorMsg" class="col-span-1 md:col-span-2 text-red-500 text-sm mb-2" x-text="editErrorMsg"></div>
                <!-- Kiri -->
                <div class="flex flex-col gap-4">
                    <div>
                        <label class="block font-semibold mb-2">Nama Client</label>
                        <input type="text" x-model="editNama" class="w-full rounded-lg border border-gray-300 px-4 py-2 text-gray-700 placeholder-gray-400" placeholder="Masukan Nama Client">
                    </div>
                    <div>
                        <label class="block font-semibold mb-2">ID Client</label>
                        <input type="text" x-model="editId" class="w-full rounded-lg border border-gray-300 px-4 py-2 text-gray-700 placeholder-gray-400" placeholder="Masukan ID Client">
                    </div>
                    <div>
                        <label class="block font-semibold mb-2">Alamat</label>
                        <textarea x-model="editAlamat" rows="3" class="w-full rounded-lg border border-gray-300 px-4 py-2 text-gray-700 placeholder-gray-400 resize-none" placeholder="Masukan Alamat Lengkap"></textarea>
                    </div>
                </div>
                <!-- Kanan -->
                <div class="flex flex-col gap-4">
                    <div>
                        <label class="block font-semibold mb-2">Nomor Telepon</label>
                        <input type="text" x-model="editTelepon" class="w-full rounded-lg border border-gray-300 px-4 py-2 text-gray-700 placeholder-gray-400" placeholder="Masukan Nomor Telepon">
                    </div>
                    <div>
                        <label class="block font-semibold mb-2">Nama Sales</label>
                        <input type="text" x-model="editNamaSales" class="w-full rounded-lg border border-gray-300 px-4 py-2 text-gray-700 placeholder-gray-400" placeholder="Masukan Nama Sales">
                    </div>
                    <div>
                        <label class="block font-semibold mb-2">Item yang Dibeli</label>
                        <div class="space-y-3">
                            <!-- Dynamic items list -->
                            <template x-for="(item, index) in editItems" :key="index">
                                <div class="flex gap-2 items-end">
                                    <div class="flex-1">
                                        <select x-model="item.stokId" @change="onEditStokChange($event, index)" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-gray-700 bg-white text-sm">
                                            <option value="">Pilih Item</option>
                                            <template x-for="stok in availableStok" :key="stok.id">
                                                <option :value="stok.id" :selected="stok.id == item.stokId" x-text="stok.nama + ' - Stok: ' + stok.tersedia + ' unit'"></option>
                                            </template>
                                        </select>
                                    </div>
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2">
                                            <input type="number" x-model="item.jumlah" min="1" class="flex-1 rounded-lg border border-gray-300 px-3 py-2 text-gray-700 placeholder-gray-400 text-sm" placeholder="Jumlah">
                                        </div>
                                    </div>
                                    <button type="button" @click="removeEditItem(index)" class="px-3 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition text-sm">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </template>
                            
                            <!-- Add new item button -->
                            <button type="button" @click="addNewEditItem()" class="w-full py-2 border-2 border-dashed border-gray-300 rounded-lg text-gray-500 hover:border-[#28C328] hover:text-[#28C328] transition flex items-center justify-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                Tambah Item Lain
                            </button>
                        </div>
                    </div>
                    <div>
                        <label class="block font-semibold mb-2">Tanggal Bergabung</label>
                        <input type="date" x-model="editBergabung" class="w-full rounded-lg border border-gray-300 px-4 py-2 text-gray-700">
                    </div>
                </div>
                <!-- Tombol bawah -->
                <div class="col-span-1 md:col-span-2 flex flex-col md:flex-row gap-4 mt-6">
                    <button type="submit" class="w-full rounded-lg bg-[#28C328] text-white font-semibold py-3 text-lg hover:bg-[#22a322] transition">Simpan</button>
                </div>
                <div class="col-span-1 md:col-span-2 flex flex-col md:flex-row gap-4">
                    <button type="reset" @click.prevent="resetEditForm()" class="w-full rounded-lg bg-gray-200 text-gray-500 font-semibold py-3 text-lg">Reset</button>
                    <button type="button" @click="resetEditForm(); closeEditModal()" class="w-full rounded-lg bg-gray-200 text-gray-500 font-semibold py-3 text-lg">Batal</button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Modal Konfirmasi Edit Stok -->
    <div x-show="showConfirmEditModal" x-transition class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40" x-cloak @click.self="showConfirmEditModal = false; confirmEditData = null">
        <div class="bg-white rounded-2xl shadow-xl p-8 w-full max-w-4xl mx-4 relative" @click.stop>
            <button @click="showConfirmEditModal = false; confirmEditData = null" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 text-2xl font-bold">&times;</button>
            
            <div class="text-center mb-6">
                <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-2">Konfirmasi Perubahan Stok Client</h3>
                <p class="text-gray-600">Perubahan stok akan mempengaruhi data di dashboard. Pastikan data yang dimasukkan sudah benar.</p>
            </div>
            
            <!-- Perubahan Stok -->
            <div class="bg-gray-50 rounded-xl p-4 mb-6">
                <h4 class="font-semibold text-gray-800 mb-3">Detail Perubahan Stok:</h4>
                <div class="space-y-3">
                    <template x-for="change in confirmEditData?.changes" :key="change.stokId">
                        <div class="bg-white rounded-lg p-3 border border-gray-200">
                            <div class="flex justify-between items-center mb-2">
                                <span class="font-semibold text-gray-800" x-text="change.stokNama"></span>
                                <span class="text-sm text-gray-500">ID: <span x-text="change.stokId"></span></span>
                            </div>
                            <div class="grid grid-cols-3 gap-4 text-sm">
                                <div>
                                    <span class="text-gray-600">Stok Lama:</span>
                                    <span class="font-semibold text-red-600 ml-1" x-text="change.stokLama + ' unit'"></span>
                                </div>
                                <div>
                                    <span class="text-gray-600">Stok Baru:</span>
                                    <span class="font-semibold text-green-600 ml-1" x-text="change.stokBaru + ' unit'"></span>
                                </div>
                                <div>
                                    <span class="text-gray-600">Selisih:</span>
                                    <span class="font-semibold" :class="change.selisih > 0 ? 'text-red-600' : 'text-green-600'" x-text="(change.selisih > 0 ? '+' : '') + change.selisih + ' unit'"></span>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
            
            <!-- Dampak pada Dashboard -->
            <div class="bg-blue-50 rounded-xl p-4 mb-6">
                <h4 class="font-semibold text-blue-800 mb-3">Dampak pada Dashboard:</h4>
                <div class="text-sm text-blue-700 space-y-1">
                    <template x-for="impact in confirmEditData?.dashboardImpact" :key="impact.stokId">
                        <div class="flex justify-between items-center">
                            <span x-text="impact.stokNama"></span>
                            <span class="font-semibold" x-text="'Stok: ' + impact.stokLama + ' → ' + impact.stokBaru"></span>
                        </div>
                    </template>
                </div>
            </div>
            
            <div class="flex gap-4">
                <button @click="showConfirmEditModal = false; confirmEditData = null" class="flex-1 rounded-lg bg-gray-200 text-gray-700 font-semibold py-3 hover:bg-gray-300 transition">
                    Batal
                </button>
                <button @click="confirmEditStok()" class="flex-1 rounded-lg bg-[#28C328] text-white font-semibold py-3 hover:bg-[#22a322] transition">
                    Konfirmasi Perubahan
                </button>
            </div>
        </div>
    </div>
    <!-- Modal Pilih Client Existing atau Baru -->
    <div x-show="showExistingChoiceModal" x-transition class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40" x-cloak @click.self="showExistingChoiceModal = false; existingCandidate = null">
        <div class="bg-white rounded-2xl shadow-xl p-8 w-full max-w-md mx-4 relative" @click.stop>
            <button @click="showExistingChoiceModal = false; existingCandidate = null" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 text-2xl font-bold">&times;</button>
            <div class="text-center mb-6">
                <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-2">Data client dengan nama yang sama ditemukan</h3>
                <p class="text-gray-600">Apakah ini client <span class="font-semibold" x-text="existingCandidate?.nama"></span> yang <span class="font-semibold">lama</span> (gabungkan data), atau <span class="font-semibold">baru</span> (buat data baru)?</p>
                <div class="mt-3 text-sm text-gray-500">ID Terdaftar: <span class="font-mono bg-gray-100 px-2 py-1 rounded" x-text="existingCandidate?.id"></span></div>
            </div>
            <div class="flex gap-4">
                <button @click="chooseExisting()" class="flex-1 rounded-lg bg-[#28C328] text-white font-semibold py-3 hover:bg-[#22a322] transition">Gunakan yang Lama (Gabungkan)</button>
                <button @click="createNewForSameName()" class="flex-1 rounded-lg bg-blue-600 text-white font-semibold py-3 hover:bg-blue-700 transition">Buat Client Baru</button>
            </div>
        </div>
    </div>
    
    <!-- Client Invoice Modal -->
    <div x-show="showClientInvoiceModal" x-transition class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40" x-cloak @click.self="showClientInvoiceModal = false">
        <div class="bg-white rounded-2xl shadow-xl p-0 w-full max-w-2xl mx-4 relative overflow-y-auto max-h-screen" @click.stop>
            <button @click="showClientInvoiceModal = false" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 text-2xl font-bold z-10">&times;</button>
            <div class="p-8">
                <!-- Header Company -->
                <div class="flex items-center gap-4 border-b pb-4 mb-6">
                    <img src="/images/logo.png" alt="Logo" class="w-16 h-16 rounded-full border object-contain bg-white">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-700">PT Golden Aroma Food Indonesia</h2>
                        <div class="text-xs text-gray-400" x-text="getAdminIdentity().address || 'Gerbang Kuning Gudang Bumbu, Jalan Ceuri no 51 Kampung Sindang Asih, Katapang, Pamentasan, Kabupaten Bandung, Jawa Barat 40921'"></div>
                        <div class="text-xs text-gray-400">Telp: <span x-text="getAdminIdentity().phone || '(021) 12345678'"></span> | Email: <span x-text="getAdminIdentity().email || 'info@gafi.co.id'"></span></div>
                    </div>
                    <div class="ml-auto text-right">
                        <div class="text-lg font-bold text-gray-700">INVOICE</div>
                        <div class="text-xs text-gray-500">Tanggal: <span x-text="currentInvoice ? (new Date(currentInvoice.timestamp)).toLocaleDateString('id-ID') : ''"></span></div>
                    </div>
                </div>
                <!-- Kepada -->
                <div class="mb-6">
                    <div class="font-semibold text-gray-700">Kepada:</div>
                    <div class="font-bold text-[#28C328] text-lg" x-text="detailClient ? detailClient.nama : ''"></div>
                    <div class="text-xs text-gray-500" x-text="detailClient ? detailClient.id : ''"></div>
                </div>
                <!-- Items Table -->
                <div class="overflow-x-auto mb-6">
                    <table class="min-w-full border text-sm">
                        <thead>
                            <tr class="bg-[#BAFFBA] text-gray-700">
                                <th class="py-2 px-4 border-b text-left">Nama Item</th>
                                <th class="py-2 px-4 border-b text-right">Harga</th>
                                <th class="py-2 px-4 border-b text-right">Qty</th>
                                <th class="py-2 px-4 border-b text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="it in (__lastInvoiceSourceHistory?.changes?.items || [])" :key="it.stock_item_id + '_' + it.jumlah">
                                <tr>
                                    <td class="py-2 px-4 border-b">
                                        <span x-text="(availableStok.find(s => s.id === it.stock_item_id)?.nama) || it.stock_item_id"></span>
                                    </td>
                                    <td class="py-2 px-4 border-b text-right">Rp<span x-text="Number(availableStok.find(s => s.id === it.stock_item_id)?.harga || 0).toLocaleString('id-ID')"></span></td>
                                    <td class="py-2 px-4 border-b text-right" x-text="Number(it.jumlah).toLocaleString('id-ID')"></td>
                                    <td class="py-2 px-4 border-b text-right">Rp<span x-text="(Number(availableStok.find(s => s.id === it.stock_item_id)?.harga || 0) * Number(it.jumlah || 0)).toLocaleString('id-ID')"></span></td>
                                </tr>
                            </template>
                        </tbody>
                        <tfoot>
                            <tr class="bg-gray-50">
                                <td class="py-2 px-4 border-t font-semibold text-gray-700" colspan="2">Total Quantity:</td>
                                <td class="py-2 px-4 border-t text-right font-bold" x-text="(__lastInvoiceSourceHistory?.changes?.items || []).reduce((sum, it) => sum + (Number(it.jumlah) || 0), 0)"></td>
                                <td class="py-2 px-4 border-t"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <!-- Summary & Export -->
                <div class="flex flex-col md:flex-row md:justify-between items-start md:items-center mb-2 gap-4">
                    <div class="space-y-1 text-sm text-gray-700">
                        <div>Subtotal: <span class="font-semibold">Rp<span x-text="Number(currentInvoice?.subtotal || 0).toLocaleString('id-ID')"></span></span></div>
                        <div>Diskon Reguler: <span class="font-semibold" x-text="currentInvoice && currentInvoice.diskonReg > 0 ? ('Rp ' + Number(currentInvoice.diskonReg).toLocaleString('id-ID')) : '-' "></span></div>
                        <div>Diskon Ball: <span class="font-semibold" x-text="currentInvoice && currentInvoice.diskonBall > 0 ? ('Rp ' + Number(currentInvoice.diskonBall).toLocaleString('id-ID')) : '-' "></span></div>
                        <div>Ongkir: <span class="font-semibold" x-text="formatOngkirWithExpedition(currentInvoice)"></span></div>
                        <div x-show="currentInvoice?.notes" class="text-gray-700">Catatan: <span class="font-semibold" x-text="currentInvoice?.notes"></span></div>
                    </div>
                    <div class="text-right space-y-1">
                        <div class="text-lg font-semibold text-[#28C328]">Total Bayar: <span class="text-2xl font-bold">Rp<span x-text="Number(currentInvoice?.total || 0).toLocaleString('id-ID')"></span></span></div>
                    </div>
                </div>
                <button class="rounded-lg bg-[#28C328] text-white font-semibold px-6 py-2 text-sm mt-4 w-full md:w-auto" @click="exportClientInvoicePDF(currentInvoice)">Export PDF</button>
            </div>
        </div>
    </div>
</div>
@endsection

<script>
function clientTable() {
    return {
        clients: [],
        availableStok: [],
        search: '',
        dateFilter: '',
        customStartDate: '',
        customEndDate: '',
        sortKey: '',
        sortAsc: true,
        sortCount: 0,
        currentPage: 1,
        perPage: 10,
        showDetailModal: false,
        detailClient: {},
        showAddModal: false,
        showAddStep2Modal: false,
        showEditModal: false,
        showConfirmEditModal: false,
        confirmEditData: null,
        editNama: '',
        editId: '',
        editAlamat: '',
        editTelepon: '',
        editNamaSales: '',
        editItems: [{ stokId: '', jumlah: '', maxStok: 0 }],
        editErrorMsg: '',
        editIndex: null,
        addNama: '',
        addId: '',
        addAlamat: '',
        addTelepon: '',
        addNamaSales: '',
        addItems: [{ stokId: '', jumlah: '', maxStok: 0 }],
        addBergabung: '',
        addDiperbaharui: '',
        addErrorMsg: '',
        showNamaSuggestion: false,
        selectedExistingClient: null,
        showExistingChoiceModal: false,
        existingCandidate: null,
        // Variabel untuk modal baru
        stockItemSearch: '',
        cartItems: [],
        stockItemCurrentPage: 1,
        stockItemPerPage: 10,
        // Variabel untuk autocomplete nama client
        showNamaSuggestionNew: false,
        selectedExistingClientNew: null,
        // Diskon & ongkir (mengikuti struktur sales)
        addDiskonTipe: 'rupiah',
        addDiskonNilai: 0,
        addDiskonBallTipe: 'rupiah',
        addDiskonBallNilai: 0,
        addNamaEkspedisi: '',
        addOngkir: 0,
        addNotes: '',
        // Invoice modal state
        showClientInvoiceModal: false,
        currentInvoice: null,
        clientHistory: {},
        adminIdentityCache: null, // Cache untuk data identitas admin
        async init() {
            await this.fetchClients();
            await this.fetchStockItems();
            this.loadAdminIdentity();
            
            // Watch for search changes to reset pagination
            this.$watch('stockItemSearch', () => {
                this.stockItemCurrentPage = 1;
            });
        },
        
        // Load admin identity data
        loadAdminIdentity() {
            fetch('/admin/identity', {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (!data.error) {
                    this.adminIdentityCache = {
                        phone: data.telepon || '(021) 12345678',
                        address: data.alamat || 'Gerbang Kuning Gudang Bumbu, Jalan Ceuri no 51 Kampung Sindang Asih, Katapang, Pamentasan, Kabupaten Bandung, Jawa Barat 40921',
                        email: data.email || 'info@gafi.co.id',
                        bank: data.bank || 'BCA',
                        account: data.no_rekening || '1234567890'
                    };
                }
            })
            .catch(error => {
                console.error('Error loading admin identity:', error);
            });
        },
        
        // Admin identity helpers (using database API)
        getAdminIdentity() {
            // Return cached data if available
            if (this.adminIdentityCache) {
                return this.adminIdentityCache;
            }
            
            // Default values
            const defaultIdentity = {
                phone: '(021) 12345678',
                address: 'Gerbang Kuning Gudang Bumbu, Jalan Ceuri no 51 Kampung Sindang Asih, Katapang, Pamentasan, Kabupaten Bandung, Jawa Barat 40921',
                email: 'info@gafi.co.id',
                bank: 'BCA',
                account: '1234567890'
            };
            
            // Fetch from API asynchronously
            fetch('/admin/identity', {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (!data.error) {
                    this.adminIdentityCache = {
                        phone: data.telepon || defaultIdentity.phone,
                        address: data.alamat || defaultIdentity.address,
                        email: data.email || defaultIdentity.email,
                        bank: data.bank || defaultIdentity.bank,
                        account: data.no_rekening || defaultIdentity.account
                    };
                } else {
                    this.adminIdentityCache = defaultIdentity;
                }
            })
            .catch(error => {
                console.error('Error loading admin identity:', error);
                this.adminIdentityCache = defaultIdentity;
            });
            
            // Return default values while loading
            return defaultIdentity;
        },
        // Build invoice rows from history entries that contain items
        getInvoiceRows(client) {
            const list = this.clientHistory[client.id] || [];
            return list
                .filter(h => Array.isArray(h?.changes?.items) && h.changes.items.length > 0)
                .map((h, idx) => {
                    const items = h.changes.items;
                    const subtotal = items.reduce((t, it) => {
                        const stockItem = this.availableStok.find(s => s.id === it.stock_item_id);
                        const price = stockItem ? Number(stockItem.harga) : 0;
                        return t + (price * Number(it.jumlah || 0));
                    }, 0);
                    const diskonReg = (() => {
                        const tipe = h.changes.diskon_tipe;
                        const nilai = Number(h.changes.diskon_nilai || 0);
                        if (!nilai) return 0;
                        if (tipe === 'persen') return Math.round(subtotal * nilai / 100);
                        return Math.min(nilai, subtotal);
                    })();
                    const diskonBall = Number(h.changes.diskon_ball_nilai || 0) > 0 ?
                        (h.changes.diskon_ball_tipe === 'persen' ? Math.round(subtotal * Number(h.changes.diskon_ball_nilai)/100) : Math.min(Number(h.changes.diskon_ball_nilai), subtotal))
                        : 0;
                    const ongkir = Number(h.changes.ongkir || 0);
                    const totalQty = items.reduce((t, it) => Number(t) + Number(it.jumlah || 0), 0);
                    const total = Math.max(0, subtotal - diskonReg - diskonBall + ongkir);
                    return {
                        id: 'inv_' + (h.id || idx),
                        timestamp: h.timestamp,
                        itemsCount: items.length,
                        totalQty,
                        subtotal,
                        diskonReg,
                        diskonBall,
                        ongkir,
                        total,
                        notes: h.changes.notes || '',
                        nama_sales: h.changes.nama_sales || '',
                        nama_ekspedisi: h.changes.nama_ekspedisi || ''
                    };
                });
        },
        async exportClientInvoicePDF(inv) {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF();
            const pageWidth = doc.internal.pageSize.getWidth();
            const marginX = 14;

            // Try load logo and draw header identity
            const loadImageAsDataURL = async (url) => {
                try {
                    const res = await fetch(url, { cache: 'no-store' });
                    if (!res.ok) return null;
                    const blob = await res.blob();
                    return await new Promise(resolve => {
                        const reader = new FileReader();
                        reader.onload = () => resolve(reader.result);
                        reader.readAsDataURL(blob);
                    });
                } catch (_) { return null; }
            };

            let y = 14;
            const logoData = await loadImageAsDataURL('/images/logo.png');
            if (logoData) {
                try { doc.addImage(logoData, 'PNG', marginX, y - 2, 20, 20); } catch(_) {}
            }
            // Company name & address
            doc.setFontSize(12); doc.setTextColor(60); doc.setFont(undefined, 'bold');
            doc.text('PT Golden Aroma Food Indonesia', marginX + 26, y + 2);
            doc.setFont(undefined, 'normal');
            doc.setFontSize(8);
            
            // Ambil alamat dari database
            const adminIdent = this.getAdminIdentity();
            const companyAddress = adminIdent.address || 'Gerbang Kuning Gudang Bumbu, Jalan Ceuri no 51 Kampung Sindang Asih \nKatapang, Pamentasan, Kabupaten Bandung, Jawa Barat 40921';
            // Batasi lebar alamat agar tidak menabrak blok kanan
            const addrMaxWidth = 200; // lebih sempit supaya tidak menabrak blok kanan
            const addrLines = doc.splitTextToSize(companyAddress, addrMaxWidth);
            doc.text(addrLines, marginX + 26, y + 8);
            const phone = adminIdent.phone || '';
            const email = adminIdent.email || '';
            const contactY = y + 8 + (addrLines.length - 1) * 6 + 6;
            doc.text(`Telp: ${phone || '-'}  |  Email: ${email || '-'}`, marginX + 26, contactY);

            // Invoice title and date (blok kanan)
            doc.setFontSize(14); doc.setFont(undefined, 'bold');
            doc.text('INVOICE', pageWidth - marginX, y + 2, { align: 'right' });
            doc.setFont(undefined, 'normal'); doc.setFontSize(9);
            doc.text('Tanggal: ' + (new Date(inv.timestamp)).toLocaleDateString('id-ID'), pageWidth - marginX, y + 10, { align: 'right' });

            // Turunkan Y sesuai tinggi header kiri/kanan (beri ruang ekstra)
            y = Math.max(contactY, y + 16) + 12;
            // Kepada
            doc.setFontSize(11); doc.text('Kepada:', marginX, y); doc.setFontSize(12); doc.setTextColor(40,195,40);
            doc.text(this.detailClient.nama || '', marginX + 22, y);
            doc.setTextColor(60);
            doc.setFontSize(9); doc.text(String(this.detailClient.id || ''), marginX + 22, y + 6);
            y += 12;

            // Items source: use last opened history to avoid mismatch id
            const srcHist = this.__lastInvoiceSourceHistory;
            const items = Array.isArray(srcHist?.changes?.items) ? srcHist.changes.items : [];
            const body = items.map(it => {
                const stockItem = this.availableStok.find(s => s.id === it.stock_item_id);
                const price = stockItem ? Number(stockItem.harga) : 0;
                const qty = Number(it.jumlah || 0);
                const line = price * qty;
                return [String(stockItem ? stockItem.nama : it.stock_item_id), 'Rp' + price.toLocaleString('id-ID'), String(qty), 'Rp' + line.toLocaleString('id-ID')];
            });
            doc.autoTable({
                head: [['Nama Item','Harga','Qty','Subtotal']],
                body,
                startY: y,
                styles: { fontSize: 9, cellPadding: 3, lineWidth: 0.1, lineColor: [200,200,200] },
                headStyles: { fillColor: [186,255,186], textColor: 60, lineWidth: 0.1, lineColor: [200,200,200] },
                alternateRowStyles: { fillColor: [245, 250, 245] },
                columnStyles: { 0:{cellWidth:'auto'}, 1:{halign:'right', cellWidth:32}, 2:{halign:'right', cellWidth:18}, 3:{halign:'right', cellWidth:38} },
                margin: { left: marginX, right: marginX }
            });
            const finalY = doc.lastAutoTable ? doc.lastAutoTable.finalY : y + 10;
            let cur = finalY + 6;
            const rightX = pageWidth - marginX;
            doc.setFontSize(10); doc.setTextColor(60);
            doc.text('Subtotal', rightX - 40, cur, { align: 'right' });
            doc.text('Rp' + Number(inv.subtotal).toLocaleString('id-ID'), rightX, cur, { align: 'right' });
            doc.text('Diskon Reg', rightX - 40, cur + 6, { align: 'right' });
            doc.text(inv.diskonReg > 0 ? ('Rp' + Number(inv.diskonReg).toLocaleString('id-ID')) : '-', rightX, cur + 6, { align: 'right' });
            doc.text('Diskon Ball', rightX - 40, cur + 12, { align: 'right' });
            doc.text(inv.diskonBall > 0 ? ('Rp' + Number(inv.diskonBall).toLocaleString('id-ID')) : '-', rightX, cur + 12, { align: 'right' });
            doc.text('Ongkir', rightX - 40, cur + 18, { align: 'right' });
            let ongkirText = '-';
            if (inv.ongkir > 0) {
                ongkirText = 'Rp' + Number(inv.ongkir).toLocaleString('id-ID');
            } else if (inv.ongkir === 0) {
                ongkirText = 'Free Ongkir';
            }
            doc.text(ongkirText, rightX, cur + 18, { align: 'right' });
            doc.setFontSize(12); doc.setTextColor(40,195,40);
            const totalYOffset = 28;
            doc.text('Total', rightX - 40, cur + totalYOffset, { align: 'right' });
            doc.setFontSize(16);
            doc.text('Rp' + Number(inv.total).toLocaleString('id-ID'), rightX, cur + totalYOffset, { align: 'right' });

            // Notes box
            if (inv?.notes) {
                const notesY = cur + totalYOffset + 12;
                doc.setFontSize(9); doc.setTextColor(80);
                doc.text('Catatan:', marginX, notesY);
                doc.setFontSize(9); doc.setTextColor(40);
                const split = doc.splitTextToSize(String(inv.notes), pageWidth - marginX * 2);
                doc.text(split, marginX, notesY + 6);
            }
            doc.save('invoice_' + (this.detailClient.id || 'client') + '.pdf');
        },
        async fetchClients() {
            try {
                const res = await fetch('/admin/clients', { headers: { 'Accept': 'application/json' } });
                if (res.ok) {
                    this.clients = await res.json();
                    // Debug: log data yang di-load
                    console.log('Clients loaded:', this.clients.map(c => ({
                        nama: c.nama,
                        nama_sales: c.nama_sales,
                        id: c.id
                    })));
                    
                    // Load histories untuk semua client setelah data client di-load
                    await this.loadAllClientHistories();
                }
            } catch (e) {
                console.error('Gagal fetch clients:', e);
            }
        },
        async fetchStockItems() {
            try {
                const res = await fetch('/admin/stock-items', { headers: { 'Accept': 'application/json' } });
                if (res.ok) {
                    this.availableStok = await res.json();
                }
            } catch (e) {
                console.error('Gagal fetch stock items:', e);
            }
        },
        async loadAllClientHistories() {
            try {
                // Load history untuk semua client secara paralel
                const promises = this.clients.map(async (client) => {
                    try {
                        const res = await fetch(`/admin/clients/${client.id}/history`, { 
                            headers: { 'Accept': 'application/json' } 
                        });
                        if (res.ok) {
                            const histories = await res.json();
                            this.clientHistory[client.id] = histories;
                        }
                    } catch (e) {
                        console.error(`Gagal load history untuk client ${client.nama}:`, e);
                    }
                });
                
                await Promise.all(promises);
                console.log('All client histories loaded:', this.clientHistory);
            } catch (e) {
                console.error('Gagal load all client histories:', e);
            }
        },
        sortBy(key) {
            if (this.sortKey !== key) {
                this.sortKey = key;
                this.sortAsc = true;
                this.sortCount = 1;
            } else if (this.sortAsc) {
                this.sortAsc = false;
                this.sortCount = 2;
            } else {
                this.sortKey = '';
                this.sortAsc = true;
                this.sortCount = 0;
            }
        },
        
        // Date filter functions
        applyDateFilter() {
            // Reset to first page when filter changes
            this.currentPage = 1;
        },
        
        clearDateFilter() {
            this.dateFilter = '';
            this.customStartDate = '';
            this.customEndDate = '';
            this.currentPage = 1;
        },
        
        getFilterStatusText() {
            switch (this.dateFilter) {
                case 'today':
                    return 'Menampilkan client yang bergabung hari ini';
                case 'week':
                    return 'Menampilkan client yang bergabung minggu ini';
                case 'month':
                    return 'Menampilkan client yang bergabung bulan ini';
                case 'year':
                    return 'Menampilkan client yang bergabung tahun ini';
                case 'custom':
                    if (this.customStartDate && this.customEndDate) {
                        const start = new Date(this.customStartDate).toLocaleDateString('id-ID');
                        const end = new Date(this.customEndDate).toLocaleDateString('id-ID');
                        return `Menampilkan client yang bergabung dari ${start} sampai ${end}`;
                    }
                    return 'Menampilkan client berdasarkan rentang tanggal custom';
                default:
                    return '';
            }
        },
        
        applyDateFilterToClients(clients) {
            if (!this.dateFilter) return clients;
            
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
                    startDate = new Date(startOfWeek.getFullYear(), startOfWeek.getMonth(), startOfWeek.getDate());
                    endDate = new Date(now.getFullYear(), now.getMonth(), now.getDate(), 23, 59, 59);
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
                    if (!this.customStartDate || !this.customEndDate) return clients;
                    startDate = new Date(this.customStartDate);
                    endDate = new Date(this.customEndDate);
                    endDate.setHours(23, 59, 59, 999);
                    break;
                default:
                    return clients;
            }
            
            return clients.filter(client => {
                const clientDate = new Date(client.bergabung);
                return clientDate >= startDate && clientDate <= endDate;
            });
        },
        get filteredClients() {
            const search = this.search.toLowerCase();
            let filtered = this.clients.filter(client => {
                // Cari di semua field yang relevan
                const searchableFields = [
                    client.nama,
                    client.id, // client_id
                    client.alamat,
                    client.telepon,
                    client.nama_sales,
                    client.nama_ekspedisi,
                    client.notes
                ];
                
                // Cari di items juga
                if (client.items && client.items.length > 0) {
                    client.items.forEach(item => {
                        searchableFields.push(item.stokNama);
                    });
                }
                
                // Cari di history pembelian untuk nama sales lama
                if (this.clientHistory && this.clientHistory[client.id]) {
                    const histories = this.clientHistory[client.id];
                    histories.forEach(history => {
                        if (history.changes && history.changes.nama_sales) {
                            searchableFields.push(history.changes.nama_sales);
                        }
                    });
                }
                
                // Debug: log untuk troubleshooting
                if (search && search.includes('sm-01')) {
                    console.log('Debug search for sm-01:', {
                        client: client.nama,
                        nama_sales: client.nama_sales,
                        searchableFields: searchableFields,
                        histories: this.clientHistory ? this.clientHistory[client.id] : 'not loaded',
                        matches: searchableFields.some(field => 
                            field && String(field).toLowerCase().includes(search)
                        )
                    });
                }
                
                return searchableFields.some(field => 
                    field && String(field).toLowerCase().includes(search)
                );
            });
            
            // Apply date filter
            if (this.dateFilter) {
                filtered = this.applyDateFilterToClients(filtered);
            }
            
            return filtered;
        },
        get sortedClients() {
            if (!this.sortKey) return this.filteredClients;
            return this.filteredClients.slice().sort((a, b) => {
                let valA, valB;
                
                // Sorting khusus untuk kolom totalStok
                if (this.sortKey === 'totalStok') {
                    valA = this.getTotalStokClient(a);
                    valB = this.getTotalStokClient(b);
                }
                // Sorting khusus untuk kolom diperbaharui (tanggal)
                else if (this.sortKey === 'diperbaharui') {
                    valA = new Date(a[this.sortKey]);
                    valB = new Date(b[this.sortKey]);
                } else {
                    valA = a[this.sortKey];
                    valB = b[this.sortKey];
                    if (typeof valA === 'string') valA = valA.toLowerCase();
                    if (typeof valB === 'string') valB = valB.toLowerCase();
                }
                
                if (valA < valB) return this.sortAsc ? -1 : 1;
                if (valA > valB) return this.sortAsc ? 1 : -1;
                return 0;
            });
        },
        get paginatedClients() {
            const start = (this.currentPage - 1) * this.perPage;
            return this.sortedClients.slice(start, start + this.perPage);
        },
        get totalPages() {
            return Math.max(1, Math.ceil(this.sortedClients.length / this.perPage));
        },
        getVisiblePages() {
            const total = this.totalPages;
            const current = this.currentPage;
            const pages = [];
            
            if (total <= 7) {
                // Show all pages if 7 or fewer
                for (let i = 1; i <= total; i++) {
                    pages.push(i);
                }
            } else {
                // Always show first and last page
                pages.push(1);
                pages.push(total);
                
                // Show pages around current page
                const start = Math.max(2, current - 1);
                const end = Math.min(total - 1, current + 1);
                
                for (let i = start; i <= end; i++) {
                    if (!pages.includes(i)) {
                        pages.push(i);
                    }
                }
            }
            
            return pages.sort((a, b) => a - b);
        },
        
        // Computed properties untuk modal baru
        get filteredStockItems() {
            if (!this.stockItemSearch) return this.availableStok;
            const search = this.stockItemSearch.toLowerCase();
            return this.availableStok.filter(item => 
                item.nama.toLowerCase().includes(search) || 
                item.sku.toLowerCase().includes(search)
            );
        },
        get paginatedStockItems() {
            const start = (this.stockItemCurrentPage - 1) * this.stockItemPerPage;
            return this.filteredStockItems.slice(start, start + this.stockItemPerPage);
        },
        get stockItemTotalPages() {
            return Math.max(1, Math.ceil(this.filteredStockItems.length / this.stockItemPerPage));
        },
        getVisibleStockPages() {
            const total = this.stockItemTotalPages;
            const current = this.stockItemCurrentPage;
            const pages = [];
            
            if (total <= 7) {
                // Show all pages if 7 or fewer
                for (let i = 1; i <= total; i++) {
                    pages.push(i);
                }
            } else {
                // Always show first and last page
                pages.push(1);
                pages.push(total);
                
                // Show pages around current page
                const start = Math.max(2, current - 1);
                const end = Math.min(total - 1, current + 1);
                
                for (let i = start; i <= end; i++) {
                    if (!pages.includes(i)) {
                        pages.push(i);
                    }
                }
            }
            
            return pages.sort((a, b) => a - b);
        },
        
        get cartTotalQuantity() {
            return this.cartItems.reduce((total, item) => Number(total) + Number(item.selectedQuantity || 0), 0);
        },
        
        get cartTotalValue() {
            return this.cartItems.reduce((total, item) => 
                total + (Number(item.harga) * Number(item.selectedQuantity || 0)), 0
            );
        },
        
        // Perhitungan total seperti sales (subtotal - diskon + ongkir)
        get cartSubtotal() {
            return this.cartTotalValue;
        },
        calculateDiscount(subtotal) {
            if (!this.addDiskonNilai || this.addDiskonNilai <= 0) return 0;
            if (this.addDiskonTipe === 'rupiah') return Math.min(Number(this.addDiskonNilai)||0, subtotal);
            if (this.addDiskonTipe === 'persen') return Math.round(subtotal * (Number(this.addDiskonNilai)||0) / 100);
            return 0;
        },
        calculateDiscountBall(subtotal) {
            if (!this.addDiskonBallNilai || this.addDiskonBallNilai <= 0) return 0;
            if (this.addDiskonBallTipe === 'rupiah') return Math.min(Number(this.addDiskonBallNilai)||0, subtotal);
            if (this.addDiskonBallTipe === 'persen') return Math.round(subtotal * (Number(this.addDiskonBallNilai)||0) / 100);
            return 0;
        },
        get cartTotalPayable() {
            const subtotal = this.cartSubtotal;
            const diskon = this.calculateDiscount(subtotal);
            const diskonBall = this.calculateDiscountBall(subtotal);
            const ongkir = Number(this.addOngkir) || 0;
            const total = subtotal - diskon - diskonBall + ongkir;
            return total > 0 ? total : 0;
        },

        formatOngkirWithExpedition(invoice) {
            if (invoice && invoice.ongkir !== undefined && invoice.ongkir !== null) {
                const ongkirValue = Number(invoice.ongkir);
                if (ongkirValue > 0) {
                    return `Rp ${ongkirValue.toLocaleString('id-ID')}`;
                } else if (ongkirValue === 0) {
                    return 'Free Ongkir';
                }
            }
            return '-';
        },
        
        // Computed property untuk autocomplete nama client
        get filteredNamaClientsNew() {
            const q = this.addNama.toLowerCase();
            return this.clients
                .filter(client => !q || client.nama.toLowerCase().includes(q))
                .slice(0, 10); // Batasi maksimal 10 suggestion
        },
        prevPage() { if (this.currentPage > 1) this.currentPage--; },
        nextPage() { if (this.currentPage < this.totalPages) this.currentPage++; },
        goToPage(page) { this.currentPage = page; },
        async showDetail(client) {
            // Fetch data client terbaru dari backend untuk memastikan data akurat
            try {
                const res = await fetch(`/admin/clients/${client.id}`, { headers: { 'Accept': 'application/json' } });
                if (res.ok) {
                    const freshClientData = await res.json();
                    this.detailClient = freshClientData;
                } else {
                    // Fallback ke data yang ada jika fetch gagal
                    this.detailClient = client;
                }
            } catch (e) {
                console.error('Gagal fetch detail client:', e);
                // Fallback ke data yang ada jika fetch gagal
            this.detailClient = client;
            }
            
            this.showDetailModal = true;
            
            // Load history
            await this.getClientHistory(client.id);
        },
        editClient(client) {
            alert('Edit client: ' + client.nama);
        },
        async deleteClient(client) {
            if (confirm('Hapus client: ' + client.nama + '?')) {
                try {
                    const res = await fetch(`/admin/clients/${client.id}`, {
                        method: 'DELETE',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content')
                        }
                    });
                    
                    if (!res.ok) {
                        alert('Gagal menghapus client.');
                        return;
                    }
                    
                this.clients = this.clients.filter(c => c.id !== client.id);
                } catch (e) {
                    alert('Gagal terhubung ke server.');
                }
            }
        },
        exportExcel() {
            // Implementasi export Excel - memecah setiap client menjadi beberapa baris (satu baris per item)
            const excelData = [];
            
            // Loop setiap client yang sudah di-filter dan di-sort
            this.sortedClients.forEach(client => {
                if (client.items && client.items.length > 0) {
                    // Jika client punya items array, buat satu baris per item
                    client.items.forEach(item => {
                        const itemName = item.stokNama || item.nama || 'Nama tidak tersedia';
                        const quantity = Number(item.stokJumlah || item.jumlah || 0);
                        const price = this.getItemPrice(item.stokId);
                        const totalValue = price * quantity;
                        
                        excelData.push({
                            'Nama Client': client.nama || '',
                            'ID Client': client.id || '',
                            'Alamat': client.alamat || '',
                            'Telepon': client.telepon || '',
                            'Nama Sales': client.nama_sales || '-',
                            'Tanggal Bergabung': this.formatTanggalIndo(client.bergabung),
                            'Diperbaharui': this.formatTanggalIndo(client.diperbaharui),
                            'Nama Item': itemName,
                            'Quantity': quantity,
                            'Harga Satuan': 'Rp ' + price.toLocaleString('id-ID'),
                            'Total Nilai Item': 'Rp ' + totalValue.toLocaleString('id-ID')
                        });
                    });
                } else {
                    // Fallback untuk data lama yang tidak punya items array
                    const itemName = client.stokNama || 'Nama tidak tersedia';
                    const quantity = Number(client.stokJumlah || 0);
                    const price = this.getItemPrice(client.stokId);
                    const totalValue = price * quantity;
                    
                    excelData.push({
                        'Nama Client': client.nama || '',
                        'ID Client': client.id || '',
                        'Alamat': client.alamat || '',
                        'Telepon': client.telepon || '',
                        'Nama Sales': client.nama_sales || '-',
                        'Tanggal Bergabung': this.formatTanggalIndo(client.bergabung),
                        'Diperbaharui': this.formatTanggalIndo(client.diperbaharui),
                        'Nama Item': itemName,
                        'Quantity': quantity,
                        'Harga Satuan': 'Rp ' + price.toLocaleString('id-ID'),
                        'Total Nilai Item': 'Rp ' + totalValue.toLocaleString('id-ID')
                    });
                }
            });
            
            // Hitung total quantity dan total nilai
            let totalQuantity = 0;
            let totalNilai = 0;
            excelData.forEach(row => {
                totalQuantity += Number(row.Quantity) || 0;
                const nilai = Number(row['Total Nilai Item'].replace(/[^\d]/g, '')) || 0;
                totalNilai += nilai;
            });
            
            // Tambahkan row total
            excelData.push({
                'Nama Client': '',
                'ID Client': '',
                'Alamat': '',
                'Telepon': '',
                'Nama Sales': '',
                'Tanggal Bergabung': '',
                'Diperbaharui': '',
                'Nama Item': '',
                'Quantity': `TOTAL: ${totalQuantity} unit`,
                'Harga Satuan': '',
                'Total Nilai Item': 'Rp ' + totalNilai.toLocaleString('id-ID')
            });
            
            const worksheet = XLSX.utils.json_to_sheet(excelData);
            // Set column widths
            worksheet['!cols'] = [
                { wch: 25 },  // Nama Client
                { wch: 18 },  // ID Client
                { wch: 40 },  // Alamat
                { wch: 18 },  // Telepon
                { wch: 20 },  // Nama Sales
                { wch: 18 },  // Tanggal Bergabung
                { wch: 18 },  // Diperbaharui
                { wch: 30 },  // Nama Item
                { wch: 12 },  // Quantity
                { wch: 18 },  // Harga Satuan
                { wch: 18 }   // Total Nilai Item
            ];
            
            worksheet['!freeze'] = { xSplit: 0, ySplit: 1 };
            worksheet['!autofilter'] = { ref: 'A1:K1' };
            const workbook = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(workbook, worksheet, 'Client Data');
            XLSX.writeFile(workbook, 'client_data.xlsx');
        },
        exportPDF() {
            // Export hanya data yang sedang tampil (terfilter + tersortir)
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF('p', 'pt');
            const tableColumn = ["Nama", "ID Client", "Alamat", "Telepon", "Nama Sales", "Bergabung", "Diperbaharui", "Jumlah Item", "Total Stok", "Total Nilai"];
            const rowsSource = this.sortedClients; // sudah termasuk filter & sort
            const tableRows = rowsSource.map(c => [
                c.nama,
                c.id,
                c.alamat,
                c.telepon,
                c.nama_sales || '-',
                this.formatTanggalIndo(c.bergabung),
                this.formatTanggalIndo(c.diperbaharui),
                this.getUniqueItemsCount(c).toString(),
                this.getTotalStokClient(c).toString(),
                'Rp ' + this.getTotalValueClient(c).toLocaleString('id-ID')
            ]);

            doc.autoTable({
                head: [tableColumn],
                body: tableRows,
                startY: 60,
                styles: { fontSize: 8, cellPadding: 3 },
                headStyles: { fillColor: [40, 195, 40], textColor: 255 },
                margin: { left: 40, right: 40 }
            });
            doc.save('client_data.pdf');
        },
        closeAddModal() { 
            this.showAddModal = false; 
            this.showAddStep2Modal = false;
            this.resetAddForm(); 
            this.addErrorMsg = ''; 
        },
        
        // Fungsi untuk modal baru
        nextToStep2() {
            if (!this.addNama) { this.addErrorMsg = 'Field Nama Client wajib diisi.'; return; }
            if (!this.addId) { this.addErrorMsg = 'Field ID Client wajib diisi.'; return; }
            if (!this.addAlamat) { this.addErrorMsg = 'Field Alamat wajib diisi.'; return; }
            if (!this.addTelepon) { this.addErrorMsg = 'Field Nomor Telepon wajib diisi.'; return; }
            if (!this.addBergabung) { this.addErrorMsg = 'Field Tanggal Bergabung wajib diisi.'; return; }
            
            this.showAddModal = false;
            this.showAddStep2Modal = true;
            this.addErrorMsg = '';
            // Reset pagination stock items
            this.stockItemCurrentPage = 1;
        },
        
        backToStep1() {
            this.showAddStep2Modal = false;
            this.showAddModal = true;
        },
        
        updateItemSubtotal(item) {
            // Fungsi untuk update subtotal item (bisa ditambahkan logika jika diperlukan)
        },
        
        addItemToCart(item) {
            if (!item.selectedQuantity || item.selectedQuantity <= 0) return;
            
            // Cek apakah item sudah ada di cart
            const existingItem = this.cartItems.find(cartItem => cartItem.id === item.id);
            if (existingItem) {
                existingItem.selectedQuantity += item.selectedQuantity;
            } else {
                this.cartItems.push({
                    id: item.id,
                    nama: item.nama,
                    sku: item.sku,
                    harga: item.harga,
                    selectedQuantity: item.selectedQuantity
                });
            }
            
            // Reset quantity di item
            item.selectedQuantity = 0;
        },
        
        removeFromCart(index) {
            this.cartItems.splice(index, 1);
        },
        
        // Pagination functions untuk stock items
        prevStockPage() { 
            if (this.stockItemCurrentPage > 1) this.stockItemCurrentPage--; 
        },
        nextStockPage() { 
            if (this.stockItemCurrentPage < this.stockItemTotalPages) this.stockItemCurrentPage++; 
        },
        goToStockPage(page) { 
            this.stockItemCurrentPage = page; 
        },
        
        // Fungsi untuk autocomplete nama client
        selectExistingClientNew(client) {
            this.addNama = client.nama;
            // Gunakan client_id (string) bukan id numerik saat mengisi ID Client
            this.addId = client.client_id || client.id;
            this.addAlamat = client.alamat;
            this.addTelepon = client.telepon;
            this.addNamaSales = client.nama_sales || '';
            this.addBergabung = client.bergabung;
            this.selectedExistingClientNew = client;
            this.showNamaSuggestionNew = false;
        },
        resetAddForm() {
            this.addNama = '';
            this.addId = '';
            this.addAlamat = '';
            this.addTelepon = '';
            this.addNamaSales = '';
            this.addItems = [{ stokId: '', jumlah: '', maxStok: 0 }];
            this.addBergabung = '';
            this.addDiperbaharui = '';
            this.addErrorMsg = '';
            this.selectedExistingClient = null;
            this.showNamaSuggestion = false;
            // Reset variabel modal baru
            this.stockItemSearch = '';
            this.cartItems = [];
            this.addDiskonTipe = 'rupiah';
            this.addDiskonNilai = 0;
            this.addDiskonBallTipe = 'rupiah';
            this.addDiskonBallNilai = 0;
            this.addNamaEkspedisi = '';
            this.addOngkir = 0;
            this.addNotes = '';
            // Reset variabel autocomplete
            this.showNamaSuggestionNew = false;
            this.selectedExistingClientNew = null;
            // Reset selectedQuantity untuk semua stock items
            this.availableStok.forEach(item => {
                item.selectedQuantity = 0;
            });
        },
        onStokChange(event, index) {
            const stokId = parseInt(event.target.value);
            const selectedStok = this.availableStok.find(s => s.id === stokId);
            if (selectedStok) {
                this.addItems[index].maxStok = selectedStok.tersedia;
                this.addItems[index].jumlah = '';
            } else {
                this.addItems[index].maxStok = 0;
                this.addItems[index].jumlah = '';
            }
        },
        async submitAddForm() {
            if (!this.addNama) { this.addErrorMsg = 'Field Nama Client wajib diisi.'; return; }
            if (!this.addId) { this.addErrorMsg = 'Field ID Client wajib diisi.'; return; }
            if (!this.addAlamat) { this.addErrorMsg = 'Field Alamat wajib diisi.'; return; }
            if (!this.addTelepon) { this.addErrorMsg = 'Field Nomor Telepon wajib diisi.'; return; }
            if (!this.addBergabung) { this.addErrorMsg = 'Field Tanggal Bergabung wajib diisi.'; return; }
            
            // Validasi items dari cart
            if (this.cartItems.length === 0) { this.addErrorMsg = 'Minimal satu item harus dipilih.'; return; }
            
            // Validasi jumlah pembelian
            for (let item of this.cartItems) {
                if (!item.selectedQuantity || item.selectedQuantity < 1) { 
                    this.addErrorMsg = 'Jumlah pembelian harus lebih dari 0.'; 
                    return; 
                }
            }
            
            // Jika user TIDAK memilih client existing manapun, validasi keunikan client_id
            if (!this.selectedExistingClient && !this.selectedExistingClientNew) {
                const isClientIdUnique = await this.validateClientIdUnique(this.addId);
                if (!isClientIdUnique) {
                    this.generateUniqueClientId();
                    this.addErrorMsg = 'Client ID sudah ada, telah di-generate ulang. Silakan cek dan simpan lagi.';
                    return;
                }
            }
            
            try {
                const payload = {
                    nama: this.addNama.trim(),
                    client_id: this.addId.trim(),
                    alamat: this.addAlamat.trim(),
                    telepon: this.addTelepon.trim(),
                    nama_sales: this.addNamaSales.trim(),
                    tanggal_bergabung: this.addBergabung,
                    // Tambahan seperti di sales
                    diskon_tipe: this.addDiskonTipe || null,
                    diskon_nilai: Number(this.addDiskonNilai) || 0,
                    diskon_ball_tipe: this.addDiskonBallTipe || null,
                    diskon_ball_nilai: Number(this.addDiskonBallNilai) || 0,
                    nama_ekspedisi: this.addNamaEkspedisi || null,
                    ongkir: Number(this.addOngkir) || 0,
                    notes: this.addNotes || '',
                    items: this.cartItems.map(item => ({
                        stock_item_id: parseInt(item.id),
                        jumlah: parseInt(item.selectedQuantity)
                    }))
                };

                // SELALU gunakan POST ke endpoint store.
                // Backend store() sudah menangani: jika client_id sudah ada, dia akan MERGE/append items,
                // bukan mengganti seluruh items.
                const res = await fetch('/admin/clients', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content')
                    },
                    body: JSON.stringify(payload)
                });

                if (!res.ok) {
                    const err = await res.json().catch(() => ({}));
                    this.addErrorMsg = err.message || 'Gagal menyimpan client.';
                    return;
                }
                
                const saved = await res.json();
                
                // Cek apakah ini update client yang sudah ada atau client baru
                // Gunakan client_id untuk pencarian karena backend mengembalikan client_id sebagai 'id'
                const existingIndex = this.clients.findIndex(c => c.id === saved.id);
                if (existingIndex !== -1) {
                    // Update client yang sudah ada
                    this.clients.splice(existingIndex, 1, saved);
                } else {
                    // Tambah client baru
                    this.clients.unshift(saved);
                }
                
                // Force refresh untuk memastikan data ter-update
                await this.fetchClients();
                
                // Jika modal detail terbuka untuk client ini, update detail dan history
                if (this.showDetailModal && (this.detailClient.id === saved.id || this.detailClient.id === saved.client_id)) {
                    // Fetch ulang data client yang lengkap dari backend
                    try {
                        const clientId = saved.id || saved.client_id;
                        const resDetail = await fetch(`/admin/clients/${clientId}`, { headers: { 'Accept': 'application/json' } });
                        if (resDetail.ok) {
                            const detailData = await resDetail.json();
                            this.detailClient = detailData;
                        } else {
                            this.detailClient = saved;
                        }
                    } catch (e) {
                        this.detailClient = saved;
                    }
                    
                    // Clear history cache dan fetch ulang
                    const clientId = saved.id || saved.client_id;
                    delete this.clientHistory[clientId];
                    await this.getClientHistory(clientId);
                }
            
            this.resetAddForm();
            this.closeAddModal();
            } catch (e) {
                this.addErrorMsg = 'Gagal terhubung ke server.';
            }
        },
        openEditModal(client, idx) {
            this.editIndex = idx;
            this.editNama = client.nama;
            this.editId = client.id;
            this.editAlamat = client.alamat;
            this.editTelepon = client.telepon;
            this.editNamaSales = client.nama_sales || '';
            
            // Handle multiple items - gunakan data yang sudah ada di client
            if (client.items && client.items.length > 0) {
                this.editItems = client.items.map(item => ({
                    stokId: item.stokId.toString(),
                    jumlah: item.stokJumlah.toString(),
                    maxStok: this.availableStok.find(s => s.id === item.stokId)?.tersedia || 0
                }));
            } else {
                // Fallback untuk data lama
                this.editItems = [{
                    stokId: client.stokId ? client.stokId.toString() : '',
                    jumlah: client.stokJumlah ? client.stokJumlah.toString() : '',
                    maxStok: this.availableStok.find(s => s.id === client.stokId)?.tersedia || 0
                }];
            }
            
            this.editBergabung = client.bergabung;
            this.showEditModal = true;
            this.editErrorMsg = '';
        },
        closeEditModal() { this.showEditModal = false; this.resetEditForm(); this.editErrorMsg = ''; },
        resetEditForm() {
            this.editNama = '';
            this.editId = '';
            this.editAlamat = '';
            this.editTelepon = '';
            this.editNamaSales = '';
            this.editItems = [{ stokId: '', jumlah: '', maxStok: 0 }];
            this.editBergabung = '';
            this.editErrorMsg = '';
            this.editIndex = null;
        },

        formatTanggalIndo(dateStr) {
            if (!dateStr) return '-';
            const bulan = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
            const [year, month, day] = dateStr.split('-');
            return Number(day) + ' ' + bulan[Number(month)-1] + ' ' + year;
        },
        openActionMenuIndex: null,
        getClientIndex(clientId) {
            return this.clients.findIndex(c => c.id === clientId);
        },
        
        // Functions untuk multiple items
        addNewItem() {
            this.addItems.push({ stokId: '', jumlah: '', maxStok: 0 });
        },
        
        removeItem(index) {
            if (this.addItems.length > 1) {
                this.addItems.splice(index, 1);
            }
        },
        
        addNewEditItem() {
            this.editItems.push({ stokId: '', jumlah: '', maxStok: 0 });
        },
        
        removeEditItem(index) {
            if (this.editItems.length > 1) {
                this.editItems.splice(index, 1);
            }
        },
        
        onEditStokChange(event, index) {
            const stokId = parseInt(event.target.value);
            const selectedStok = this.availableStok.find(s => s.id === stokId);
            if (selectedStok) {
                this.editItems[index].maxStok = selectedStok.tersedia;
                this.editItems[index].jumlah = '';
            } else {
                this.editItems[index].maxStok = 0;
                this.editItems[index].jumlah = '';
            }
        },
        
        // Functions untuk konfirmasi edit dan history
        clientHistory: {},
        async getClientHistory(clientId) {
            if (!this.clientHistory[clientId]) {
                try {
                    const res = await fetch(`/admin/clients/${clientId}/history`, { headers: { 'Accept': 'application/json' } });
                    if (res.ok) {
                        this.clientHistory[clientId] = await res.json();
                    } else {
                        this.clientHistory[clientId] = [];
                    }
                } catch (e) {
                    this.clientHistory[clientId] = [];
                }
            }
            return this.clientHistory[clientId] || [];
        },
        
        async submitEditForm() {
            if (!this.editNama) { this.editErrorMsg = 'Field Nama Client wajib diisi.'; return; }
            if (!this.editId) { this.editErrorMsg = 'Field ID Client wajib diisi.'; return; }
            if (!this.editAlamat) { this.editErrorMsg = 'Field Alamat wajib diisi.'; return; }
            if (!this.editTelepon) { this.editErrorMsg = 'Field Nomor Telepon wajib diisi.'; return; }
            if (!this.editBergabung) { this.editErrorMsg = 'Field Tanggal Bergabung wajib diisi.'; return; }
            
            // Validasi items
            const validItems = this.editItems.filter(item => item.stokId && item.jumlah);
            if (validItems.length === 0) { this.editErrorMsg = 'Minimal satu item harus dipilih.'; return; }
            
            // Validasi jumlah pembelian (tanpa batas stok maksimum / support preorder)
            for (let item of validItems) {
                if (!item.jumlah || item.jumlah < 1) { 
                    this.editErrorMsg = 'Jumlah pembelian harus lebih dari 0.'; 
                    return; 
                }
            }
            
            try {
                const payload = {
                    nama: this.editNama.trim(),
                    client_id: this.editId.trim(),
                    alamat: this.editAlamat.trim(),
                    telepon: this.editTelepon.trim(),
                    nama_sales: this.editNamaSales.trim(),
                    tanggal_bergabung: this.editBergabung,
                    items: validItems.map(item => ({
                        stock_item_id: parseInt(item.stokId),
                        jumlah: parseInt(item.jumlah)
                    }))
                };

            const originalClient = this.clients[this.editIndex];
                const res = await fetch(`/admin/clients/${originalClient.id}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content')
                    },
                    body: JSON.stringify(payload)
                });
                
                if (!res.ok) {
                    const err = await res.json().catch(() => ({}));
                    this.editErrorMsg = err.message || 'Gagal menyimpan perubahan.';
                    return;
                }
                
                const updated = await res.json();
                this.clients.splice(this.editIndex, 1, updated);
                
                // Jika modal detail terbuka untuk client ini, update detail dan history
                if (this.showDetailModal && (this.detailClient.id === updated.id || this.detailClient.id === updated.client_id)) {
                    // Fetch ulang data client yang lengkap dari backend
                    try {
                        const clientId = updated.id || updated.client_id;
                        const resDetail = await fetch(`/admin/clients/${clientId}`, { headers: { 'Accept': 'application/json' } });
                        if (resDetail.ok) {
                            const detailData = await resDetail.json();
                            this.detailClient = detailData;
                        } else {
                            this.detailClient = updated;
                        }
                    } catch (e) {
                        this.detailClient = updated;
                    }
                    
                    // Clear history cache dan fetch ulang
                    const clientId = updated.id || updated.client_id;
                    delete this.clientHistory[clientId];
                    await this.getClientHistory(clientId);
                }
                
            this.resetEditForm();
            this.closeEditModal();
            } catch (e) {
                this.editErrorMsg = 'Gagal terhubung ke server.';
            }
        },
        
        confirmEditStok() {
            // Fungsi ini sudah tidak diperlukan karena edit langsung ke database
            // Bisa dihapus atau dijadikan placeholder
        },
        
        // Functions untuk autocomplete nama client
        get filteredNamaClients() {
            const q = this.addNama.toLowerCase();
            return this.clients
                .filter(client => !q || client.nama.toLowerCase().includes(q))
                .slice(0, 10); // Batasi maksimal 10 suggestion
        },
        
        selectExistingClient(client) {
            // Jika nama dan ID existing cocok dengan input sekarang, minta konfirmasi lama/baru
            this.existingCandidate = client;
            this.showExistingChoiceModal = true;
        },

        // Pengguna memilih "Gunakan yang Lama"
        chooseExisting() {
            const client = this.existingCandidate;
            if (!client) { this.showExistingChoiceModal = false; return; }
            this.addNama = client.nama;
            this.addId = client.client_id || client.id;
            this.addAlamat = client.alamat;
            this.addTelepon = client.telepon;
            this.addNamaSales = client.nama_sales || '';
            this.addBergabung = client.bergabung;
            this.selectedExistingClient = client; // merge mode
            this.addItems = [{ stokId: '', jumlah: '', maxStok: 0 }];
            this.showNamaSuggestion = false;
            this.showExistingChoiceModal = false;
            this.existingCandidate = null;
        },

        // Pengguna memilih "Buat Client Baru"
        createNewForSameName() {
            const client = this.existingCandidate;
            if (!client) { this.showExistingChoiceModal = false; return; }
            // tetap gunakan nama yang sama, tetapi jangan gunakan ID lama
            this.selectedExistingClient = null; // create new
            this.generateUniqueClientId();
            this.showNamaSuggestion = false;
            this.showExistingChoiceModal = false;
            this.existingCandidate = null;
        },
        
        // Function untuk menghitung total stok client
        getTotalStokClient(client) {
            if (client.items && client.items.length > 0) {
                return client.items.reduce((total, item) => total + Number(item.stokJumlah || item.jumlah || 0), 0);
            } else {
                // Fallback untuk data lama
                return Number(client.stokJumlah || 0);
            }
        },
        
        // Function untuk mendapatkan jumlah jenis item (unique items)
        getUniqueItemsCount(client) {
            if (client.items && client.items.length > 0) {
                return client.items.length;
            } else {
                // Fallback untuk data lama
                return client.stokId ? 1 : 0;
            }
        },
        
        // Function untuk mendapatkan harga item
        getItemPrice(stokId) {
            if (!stokId) return 0;
            const stokItem = this.availableStok.find(s => s.id === parseInt(stokId));
            return stokItem ? stokItem.harga : 0;
        },
        
        // Function untuk menghitung total nilai client
        getTotalValueClient(client) {
            if (client.items && client.items.length > 0) {
                return client.items.reduce((total, item) => {
                    const price = this.getItemPrice(item.stokId);
                    const quantity = Number(item.stokJumlah || item.jumlah || 0);
                    return total + (price * quantity);
                }, 0);
            } else {
                // Fallback untuk data lama
                const price = this.getItemPrice(client.stokId);
                return price * Number(client.stokJumlah || 0);
            }
        },
        
        // Function untuk menggabungkan item dengan nama yang sama
        getMergedItems(items) {
            if (!items || items.length === 0) return [];
            
            const mergedMap = new Map();
            
            items.forEach(item => {
                const itemName = item.stokNama || item.nama || 'Nama tidak tersedia';
                const itemJumlah = Number(item.stokJumlah || item.jumlah || 0);
                const itemPrice = this.getItemPrice(item.stokId);
                
                if (mergedMap.has(itemName)) {
                    // Update existing item
                    const existing = mergedMap.get(itemName);
                    existing.totalJumlah += itemJumlah;
                    existing.totalNilai += (itemPrice * itemJumlah);
                } else {
                    // Create new merged item
                    mergedMap.set(itemName, {
                        stokNama: itemName,
                        totalJumlah: itemJumlah,
                        totalNilai: itemPrice * itemJumlah
                    });
                }
            });
            
            return Array.from(mergedMap.values());
        },

        // Helper functions untuk history
        formatHistoryDate(timestamp) {
            const date = new Date(timestamp);
            return date.toLocaleDateString('id-ID', { 
                year: 'numeric', 
                month: 'short', 
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        },

        getHistoryItemName(history) {
            if (history.changes && history.changes.items) {
                const items = history.changes.items;
                if (Array.isArray(items) && items.length > 0) {
                    const itemNames = items.map(item => {
                        const stockItem = this.availableStok.find(s => s.id === item.stock_item_id);
                        return stockItem ? stockItem.nama : 'Item tidak ditemukan';
                    });
                    return itemNames.join(', ');
                }
            }
            return history.changes?.nama || 'N/A';
        },

        getHistoryItemCount(history) {
            if (history.changes && history.changes.items) {
                const items = history.changes.items;
                if (Array.isArray(items)) {
                    return items.reduce((total, item) => total + (item.jumlah || 0), 0);
                }
            }
            return 0;
        },

        getHistoryTotalPrice(history) {
            if (history.changes && history.changes.items) {
                const items = history.changes.items;
                if (Array.isArray(items)) {
                    return items.reduce((total, item) => {
                        const stockItem = this.availableStok.find(s => s.id === item.stock_item_id);
                        return total + (stockItem ? stockItem.harga * item.jumlah : 0);
                    }, 0);
                }
            }
            return 0;
        },
        openInvoiceFromHistory(history) {
            const items = Array.isArray(history?.changes?.items) ? history.changes.items : [];
            const subtotal = items.reduce((t, it) => {
                const stockItem = this.availableStok.find(s => s.id === it.stock_item_id);
                const price = stockItem ? Number(stockItem.harga) : 0;
                return t + (price * Number(it.jumlah || 0));
            }, 0);
            const tipe = history?.changes?.diskon_tipe;
            const nilai = Number(history?.changes?.diskon_nilai || 0);
            const diskonReg = nilai ? (tipe === 'persen' ? Math.round(subtotal * nilai / 100) : Math.min(nilai, subtotal)) : 0;
            const diskonBall = Number(history?.changes?.diskon_ball_nilai || 0) > 0 ?
                (history?.changes?.diskon_ball_tipe === 'persen' ? Math.round(subtotal * Number(history?.changes?.diskon_ball_nilai)/100) : Math.min(Number(history?.changes?.diskon_ball_nilai), subtotal))
                : 0;
            const inv = {
                id: 'hist_inv_' + (history.id || Date.now()),
                timestamp: history.timestamp,
                itemsCount: items.length,
                totalQty: items.reduce((t, it) => Number(t) + Number(it.jumlah || 0), 0),
                subtotal,
                diskonReg,
                diskonBall,
                ongkir: Number(history?.changes?.ongkir || 0),
                total: Math.max(0, subtotal - diskonReg - diskonBall + Number(history?.changes?.ongkir || 0)),
                notes: history?.changes?.notes || '',
                nama_sales: history?.changes?.nama_sales || '',
                nama_ekspedisi: history?.changes?.nama_ekspedisi || ''
            };
            this.__lastInvoiceSourceHistory = history;
            this.currentInvoice = inv;
            this.showClientInvoiceModal = true;
        },
        getActionClass(action) {
            switch(action) {
                case 'Client Baru Dibuat':
                    return 'bg-green-100 text-green-800';
                case 'Client Diperbaharui':
                    return 'bg-blue-100 text-blue-800';
                case 'Stok Ditambahkan':
                    return 'bg-yellow-100 text-yellow-800';
                case 'Client Dihapus':
                    return 'bg-red-100 text-red-800';
                default:
                    return 'bg-gray-100 text-gray-800';
            }
        },

        getActionText(action) {
            switch(action) {
                case 'Client Baru Dibuat':
                    return 'Dibuat';
                case 'Client Diperbaharui':
                    return 'Diupdate';
                case 'Stok Ditambahkan':
                    return 'Stok Ditambah';
                case 'Client Dihapus':
                    return 'Dihapus';
                default:
                    return action;
            }
        },
        
        // Method untuk mendapatkan nama sales dari history
        getHistorySalesName(history) {
            if (history.changes && history.changes.nama_sales) {
                return history.changes.nama_sales;
            }
            return '-';
        },
        
        // Generate client_id otomatis dari nama
        generateClientId() {
            if (!this.addNama.trim()) {
                this.addId = '';
                return;
            }
            
            // Generate client_id dengan format: Nama-XXX (angka acak 100-999)
            const randomNumber = Math.floor(Math.random() * 900) + 100; // 100-999
            this.addId = `${this.addNama.trim()}-${randomNumber}`;
        },
        
        // Hanya generate ID jika belum memilih client existing
        generateClientIdIfNeeded() {
            if (this.selectedExistingClient || this.selectedExistingClientNew) return; // jangan ubah ID yang sudah dipilih
            this.generateClientId();
        },
        
        // Validasi apakah client_id sudah ada di database
        async validateClientIdUnique(clientId) {
            try {
                const res = await fetch(`/admin/validate-client-id?client_id=${encodeURIComponent(clientId)}`, {
                    headers: { 'Accept': 'application/json' }
                });
                
                if (res.ok) {
                    const data = await res.json();
                    return data.unique; // true jika unik, false jika sudah ada
                }
                
                return false; // Jika error, anggap tidak unik
            } catch (e) {
                console.error('Error validating client_id:', e);
                return false;
            }
        },
        
        // Generate client_id baru yang unik
        generateUniqueClientId() {
            if (!this.addNama.trim()) return;
            
            let attempts = 0;
            let newClientId = '';
            const maxAttempts = 50; // Maksimal 50 percobaan untuk menghindari infinite loop
            
            // Cari client_id yang unik dengan angka acak
            do {
                const randomNumber = Math.floor(Math.random() * 900) + 100; // 100-999
                newClientId = `${this.addNama.trim()}-${randomNumber}`;
                attempts++;
                
                // Cek apakah sudah ada di list client yang sedang ditampilkan
                const existsInList = this.clients.some(client => client.client_id === newClientId);
                if (!existsInList) {
                    break;
                }
            } while (attempts < maxAttempts);
            
            // Jika masih belum unik setelah 50 percobaan, gunakan timestamp
            if (attempts >= maxAttempts) {
                const timestamp = Date.now().toString().slice(-3); // Ambil 3 digit terakhir dari timestamp
                newClientId = `${this.addNama.trim()}-${timestamp}`;
            }
            
            this.addId = newClientId;
        },
    }
}
</script>