<style>[x-cloak] { display: none !important; }</style>
@extends('layouts.client')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div x-data="clientDashboard()" x-init="init()" class="space-y-6">

    <!-- KPI Cards (minimal) -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-xs uppercase tracking-wide text-gray-500">Transaksi Hari Ini</p>
            <p class="mt-1 text-2xl font-semibold text-gray-900" x-text="todaysOrders"></p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-xs uppercase tracking-wide text-gray-500">Omzet Hari Ini</p>
            <p class="mt-1 text-2xl font-semibold text-gray-900">Rp<span x-text="formatIDR(todaysRevenue)"></span></p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-xs uppercase tracking-wide text-gray-500">Item Unik</p>
            <p class="mt-1 text-2xl font-semibold text-gray-900" x-text="totalItems"></p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-xs uppercase tracking-wide text-gray-500">Total Stok (pcs)</p>
            <p class="mt-1 text-2xl font-semibold text-gray-900" x-text="formatQty(totalStock)"></p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-xs uppercase tracking-wide text-gray-500">Transaksi (Rentang)</p>
            <p class="mt-1 text-2xl font-semibold text-gray-900" x-text="rangeOrders"></p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-xs uppercase tracking-wide text-gray-500">Belum Dibayar</p>
            <p class="mt-1 text-2xl font-semibold text-gray-900" x-text="unpaidCount"></p>
        </div>
    </div>

    <!-- Loading banner removed for cleaner UX -->

    <div x-show="error" class="bg-red-50 border border-red-200 rounded-lg p-4">
        <div class="flex">
            <svg class="w-5 h-5 text-red-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
            </svg>
            <span class="text-red-700" x-text="error"></span>
        </div>
    </div>

    <!-- Chart and Settings -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6" x-show="!loading && !error">
        <div class="lg:col-span-2 bg-white rounded-xl border border-gray-200 p-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-2">
                <h3 class="text-lg font-semibold text-gray-800">Omzet Berdasarkan Periode</h3>
                <div class="flex items-center gap-3">
                    <div class="relative">
                        <select x-model="period" @change="saveSettings(); renderChart()" class="rounded-lg border border-gray-300 px-3 pr-8 py-2 text-sm text-gray-700 min-w-[7rem]" style="appearance:none;-webkit-appearance:none;-moz-appearance:none;background-image:none;">
                            <option value="week">7 Hari</option>
                            <option value="month">30 Hari</option>
                            <option value="year">12 Bulan</option>
                        </select>
                        <svg class="pointer-events-none absolute right-2 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </div>
                    <div class="flex items-center gap-2">
                        <label class="text-sm text-gray-500">Target</label>
                        <input type="number" x-model="targetRevenue" @input="saveSettings(); renderChart()" class="w-28 border border-gray-300 rounded-lg px-2 py-1 text-sm" placeholder="Rp">
                    </div>
                </div>
            </div>
            <div class="text-xs text-gray-400 text-right mb-1">Sumber: Database Sales Client</div>
            <canvas id="clientRevenueChart" class="w-full h-64"></canvas>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-800">Aktivitas Terakhir</h3>
                <a href="{{ route('client.salesc') }}" class="text-sm text-emerald-600 hover:underline">Lihat riwayat</a>
            </div>
            <div class="divide-y divide-gray-100">
                <template x-if="activities.length === 0">
                    <div class="text-sm text-gray-500 py-6 text-center">Belum ada aktivitas</div>
                </template>
                <template x-for="a in activities" :key="a.id">
                    <div class="py-3 flex items-center justify-between">
                        <div class="flex items-center gap-2 text-gray-700">
                            <span class="w-2 h-2 rounded-full" :class="a.status === 'completed' ? 'bg-green-500' : 'bg-orange-500'"></span>
                            <span x-text="a.text"></span>
                        </div>
                        <div class="text-xs text-gray-500" x-text="a.date"></div>
                    </div>
                </template>
            </div>
        </div>
    </div>
</div>

<script>
function clientDashboard() {
    return {
        // data
        period: localStorage.getItem('client_dash_period') || 'month',
        targetRevenue: Number(localStorage.getItem('client_dash_target') || 0),
        chart: null,
        loading: false,
        error: null,

        // KPI data from database
        todaysRevenue: 0,
        todaysOrders: 0,
        totalItems: 0,
        totalStock: 0,
        rangeOrders: 0,
        unpaidCount: 0,
        activities: [],
        revenueData: [],

        init() {
            this.loadDashboardData();
            this.renderChart();
            
            // Auto-refresh removed to avoid redundant fetches
        },

        async loadDashboardData() {
            this.loading = true;
            this.error = null;
            
            try {
                const response = await fetch('/client/dashboard-data', {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (!response.ok) {
                    if (response.status === 401) {
                        throw new Error('Sesi telah berakhir. Silakan login ulang.');
                    } else if (response.status === 500) {
                        throw new Error('Terjadi kesalahan server. Silakan coba lagi.');
                    } else {
                        throw new Error(`HTTP ${response.status}: Gagal memuat data dashboard`);
                    }
                }

                const result = await response.json();
                
                if (result.success && result.data) {
                    // Update KPI data
                    this.todaysOrders = result.data.kpi.todaysOrders || 0;
                    this.todaysRevenue = result.data.kpi.todaysRevenue || 0;
                    this.totalItems = result.data.kpi.totalItems || 0;
                    this.totalStock = result.data.kpi.totalStock || 0;
                    this.rangeOrders = result.data.kpi.rangeOrders || 0;
                    this.unpaidCount = result.data.kpi.unpaidCount || 0;
                    
                    // Update activities
                    this.activities = result.data.activities || [];
                    
                    // Update revenue data for chart
                    this.revenueData = result.data.revenueData || [];
                    
                    // Re-render chart with new data
                    this.renderChart();
                } else {
                    throw new Error(result.message || 'Gagal memuat data dashboard');
                }
            } catch (error) {
                console.error('Error loading dashboard data:', error);
                this.error = 'Gagal memuat data dashboard: ' + error.message;
            } finally {
                this.loading = false;
            }
        },

        saveSettings() {
            localStorage.setItem('client_dash_period', this.period);
            localStorage.setItem('client_dash_target', String(this.targetRevenue || 0));
        },

        buildSeries() {
            // Use revenue data from database
            if (this.revenueData && this.revenueData.length > 0) {
                const labels = this.revenueData.map(item => item.label);
                const data = this.revenueData.map(item => Number(item.value));
                return { labels, data };
            }
            
            // Fallback to empty data
            return { labels: [], data: [] };
        },

        renderChart() {
            const ctx = document.getElementById('clientRevenueChart');
            if (!ctx) return;
            
            const { labels, data } = this.buildSeries();
            const gradient = ctx.getContext('2d').createLinearGradient(0, 0, 0, 240);
            gradient.addColorStop(0, 'rgba(16,185,129,0.35)');
            gradient.addColorStop(1, 'rgba(16,185,129,0.02)');

            if (this.chart) this.chart.destroy();
            this.chart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels,
                    datasets: [{
                        label: 'Omzet',
                        data,
                        fill: true,
                        backgroundColor: gradient,
                        borderColor: '#10B981',
                        pointRadius: 2,
                        borderWidth: 2,
                        tension: 0.35
                    }, ...(this.targetRevenue > 0 ? [{
                        label: 'Target',
                        data: new Array(labels.length).fill(this.targetRevenue),
                        borderColor: '#F59E0B',
                        borderDash: [6,6],
                        pointRadius: 0,
                        fill: false
                    }] : [])]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: true },
                        tooltip: {
                            callbacks: {
                                label: (ctx) => {
                                    const v = ctx.parsed.y || 0;
                                    return ctx.dataset.label + ': Rp' + this.formatIDR(v);
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            grid: { color: 'rgba(0,0,0,0.06)' },
                            ticks: {
                                callback: (v) => 'Rp' + this.formatIDR(v)
                            }
                        },
                        x: {
                            grid: { display: false }
                        }
                    }
                }
            });
        },

        formatIDR(num) {
            try { return Number(num || 0).toLocaleString('id-ID'); } catch { return '0'; }
        },

        formatQty(val) {
            const num = Number(val || 0);
            return Number.isInteger(num) ? String(num) : num.toLocaleString('id-ID', { maximumFractionDigits: 2, minimumFractionDigits: 0 });
        }
    }
}
</script>
@endsection 