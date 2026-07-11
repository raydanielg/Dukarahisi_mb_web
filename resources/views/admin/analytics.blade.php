@extends('layouts.dashboard')

@section('title', 'Analytics - ' . config('app.name', 'Elimu Store'))
@section('page_title', 'Analytics & Reports')

@section('content')
<div class="space-y-6">
    {{-- Period Filter --}}
    <div class="hover-lift dashboard-card flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white rounded-xl border border-gray-100 p-4">
        <div>
            <h2 class="text-lg font-bold text-gray-800">Analytics Dashboard</h2>
            <p class="text-sm text-gray-500">Track performance, sales, and user growth</p>
        </div>
        <form method="GET" class="flex items-center gap-2">
            <select name="period" onchange="this.form.submit()" class="px-4 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none">
                <option value="7" {{ $period == '7' ? 'selected' : '' }}>Last 7 Days</option>
                <option value="30" {{ $period == '30' ? 'selected' : '' }}>Last 30 Days</option>
                <option value="90" {{ $period == '90' ? 'selected' : '' }}>Last 90 Days</option>
                <option value="365" {{ $period == '365' ? 'selected' : '' }}>Last 12 Months</option>
            </select>
        </form>
    </div>

    {{-- KPI Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
        <div class="kpi-card dashboard-card bg-gradient-to-br from-emerald-600 to-emerald-700 rounded-xl border border-emerald-500 p-4 text-white relative overflow-hidden hover:shadow-lg">
            <div class="absolute top-0 right-0 w-16 h-16 bg-white/10 rounded-full -mr-8 -mt-8"></div>
            <div class="relative z-10">
                <span class="text-[10px] font-medium text-emerald-100">Total Revenue</span>
                <p class="text-xl font-bold tracking-tight mt-1">TZS {{ number_format($totalRevenue, 0) }}</p>
                <p class="text-[10px] text-emerald-200 mt-1">+TZS {{ number_format($periodRevenue, 0) }} in period</p>
            </div>
        </div>

        <div class="kpi-card dashboard-card bg-gradient-to-br from-amber-400 to-amber-500 rounded-xl border border-amber-300 p-4 text-white relative overflow-hidden hover:shadow-lg">
            <div class="absolute top-0 right-0 w-16 h-16 bg-white/10 rounded-full -mr-8 -mt-8"></div>
            <div class="relative z-10">
                <span class="text-[10px] font-medium text-amber-50">Total Orders</span>
                <p class="text-xl font-bold tracking-tight mt-1">{{ number_format($totalOrders) }}</p>
                <p class="text-[10px] text-amber-100 mt-1">+{{ number_format($periodOrders) }} in period</p>
            </div>
        </div>

        <div class="kpi-card dashboard-card bg-gradient-to-br from-sky-500 to-sky-600 rounded-xl border border-sky-400 p-4 text-white relative overflow-hidden hover:shadow-lg">
            <div class="absolute top-0 right-0 w-16 h-16 bg-white/10 rounded-full -mr-8 -mt-8"></div>
            <div class="relative z-10">
                <span class="text-[10px] font-medium text-sky-100">Total Users</span>
                <p class="text-xl font-bold tracking-tight mt-1">{{ number_format($totalUsers) }}</p>
                <p class="text-[10px] text-sky-200 mt-1">+{{ number_format($periodNewUsers) }} in period</p>
            </div>
        </div>

        <div class="kpi-card dashboard-card bg-gradient-to-br from-violet-500 to-violet-600 rounded-xl border border-violet-400 p-4 text-white relative overflow-hidden hover:shadow-lg">
            <div class="absolute top-0 right-0 w-16 h-16 bg-white/10 rounded-full -mr-8 -mt-8"></div>
            <div class="relative z-10">
                <span class="text-[10px] font-medium text-violet-100">Paid Orders</span>
                <p class="text-xl font-bold tracking-tight mt-1">{{ number_format($totalPaidOrders) }}</p>
                <p class="text-[10px] text-violet-200 mt-1">+{{ number_format($periodPaidOrders) }} in period</p>
            </div>
        </div>
    </div>

    {{-- Main Charts --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Revenue Trend --}}
        <div class="hover-lift dashboard-card lg:col-span-2 bg-white rounded-xl border border-gray-100 p-5">
            <h3 class="text-sm font-semibold text-gray-900 mb-4">Revenue Trend (Last {{ $period }} Days)</h3>
            <div class="chart-container" style="height: 320px;">
                <canvas id="revenueTrendChart"></canvas>
            </div>
        </div>

        {{-- Order Status Doughnut --}}
        <div class="hover-lift dashboard-card bg-white rounded-xl border border-gray-100 p-5">
            <h3 class="text-sm font-semibold text-gray-900 mb-4">Order Status</h3>
            <div class="chart-container flex items-center justify-center" style="height: 256px;">
                <canvas id="orderStatusChart"></canvas>
            </div>
            <div class="mt-4 grid grid-cols-2 gap-2 text-xs">
                <div class="flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-emerald-500"></span>Paid</div>
                <div class="flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-amber-400"></span>Pending</div>
                <div class="flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-red-500"></span>Failed</div>
                <div class="flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-gray-400"></span>Cancelled</div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- User Growth --}}
        <div class="hover-lift dashboard-card bg-white rounded-xl border border-gray-100 p-5">
            <h3 class="text-sm font-semibold text-gray-900 mb-4">User Growth (6 Months)</h3>
            <div class="chart-container" style="height: 256px;">
                <canvas id="userGrowthChart"></canvas>
            </div>
        </div>

        {{-- Orders Activity --}}
        <div class="hover-lift dashboard-card bg-white rounded-xl border border-gray-100 p-5">
            <h3 class="text-sm font-semibold text-gray-900 mb-4">Daily Orders</h3>
            <div class="chart-container" style="height: 256px;">
                <canvas id="dailyOrdersChart"></canvas>
            </div>
        </div>

        {{-- Payment Status --}}
        <div class="hover-lift dashboard-card bg-white rounded-xl border border-gray-100 p-5">
            <h3 class="text-sm font-semibold text-gray-900 mb-4">Payment Status</h3>
            <div class="chart-container flex items-center justify-center" style="height: 256px;">
                <canvas id="paymentStatusChart"></canvas>
            </div>
        </div>
    </div>

    {{-- Top Performance Tables --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Top Selling Notes --}}
        <div class="hover-lift bg-white rounded-xl border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h3 class="text-sm font-semibold text-gray-900">Top Selling Notes</h3>
            </div>
            <div class="overflow-x-auto max-h-80 overflow-y-auto">
                <table class="dashboard-table w-full text-sm">
                    <thead class="sticky top-0 bg-white"><tr class="text-left text-xs text-gray-500 bg-gray-50/50">
                        <th class="px-6 py-3 font-medium">Note</th>
                        <th class="px-6 py-3 font-medium">Subject</th>
                        <th class="px-6 py-3 font-medium">Sales</th>
                    </tr></thead>
                    <tbody>
                        @forelse($topNotes as $note)
                        <tr class="border-t border-gray-100 hover:bg-gray-50/50 transition-colors">
                            <td class="px-6 py-3">
                                <div class="flex items-center gap-2">
                                    <img src="{{ $note->cover_image_url }}" alt="" class="w-8 h-8 rounded object-cover">
                                    <span class="text-sm font-medium text-gray-900 truncate max-w-[150px]">{{ $note->title }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-3 text-xs text-gray-600">{{ $note->subject->name }}</td>
                            <td class="px-6 py-3 text-xs font-semibold text-emerald-600">{{ $note->order_items_count }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="3" class="px-6 py-8 text-center text-gray-400 text-xs">No sales data</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Top Subjects by Revenue --}}
        <div class="hover-lift bg-white rounded-xl border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h3 class="text-sm font-semibold text-gray-900">Top Subjects by Revenue</h3>
            </div>
            <div class="overflow-x-auto max-h-80 overflow-y-auto">
                <table class="dashboard-table w-full text-sm">
                    <thead class="sticky top-0 bg-white"><tr class="text-left text-xs text-gray-500 bg-gray-50/50">
                        <th class="px-6 py-3 font-medium">Subject</th>
                        <th class="px-6 py-3 font-medium">Sales</th>
                        <th class="px-6 py-3 font-medium">Revenue</th>
                    </tr></thead>
                    <tbody>
                        @forelse($topSubjects as $subject)
                        <tr class="border-t border-gray-100 hover:bg-gray-50/50 transition-colors">
                            <td class="px-6 py-3 text-sm font-medium text-gray-900">{{ $subject->name }}</td>
                            <td class="px-6 py-3 text-xs text-gray-600">{{ $subject->sales }}</td>
                            <td class="px-6 py-3 text-xs font-semibold text-gray-900">TZS {{ number_format($subject->revenue, 0) }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="3" class="px-6 py-8 text-center text-gray-400 text-xs">No data</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Recent Sales --}}
    <div class="hover-lift bg-white rounded-xl border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-sm font-semibold text-gray-900">Recent Sales</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="dashboard-table w-full text-sm">
                <thead><tr class="text-left text-xs text-gray-500 bg-gray-50/50">
                    <th class="px-6 py-3 font-medium">Order ID</th>
                    <th class="px-6 py-3 font-medium">Customer</th>
                    <th class="px-6 py-3 font-medium">Items</th>
                    <th class="px-6 py-3 font-medium">Amount</th>
                    <th class="px-6 py-3 font-medium">Date</th>
                </tr></thead>
                <tbody>
                    @forelse($recentSales as $sale)
                    <tr class="border-t border-gray-100 hover:bg-gray-50/50 transition-colors">
                        <td class="px-6 py-3 font-mono text-xs text-gray-500">{{ $sale->reference }}</td>
                        <td class="px-6 py-3 text-xs text-gray-700">{{ $sale->user?->name ?? 'Unknown' }}</td>
                        <td class="px-6 py-3 text-xs text-gray-600">{{ $sale->items->count() }} items</td>
                        <td class="px-6 py-3 text-xs font-semibold text-emerald-600">TZS {{ number_format($sale->total_amount, 0) }}</td>
                        <td class="px-6 py-3 text-xs text-gray-500">{{ $sale->created_at->format('d M Y H:i') }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="px-6 py-8 text-center text-gray-400 text-xs">No recent sales</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const chartInstances = {};

        function destroyChart(name) {
            if (chartInstances[name]) {
                chartInstances[name].destroy();
                delete chartInstances[name];
            }
        }

        const commonOptions = {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#064e3b',
                    titleColor: '#fff',
                    bodyColor: '#fff',
                    padding: 10,
                    cornerRadius: 8,
                    displayColors: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: '#f3f4f6', drawBorder: false },
                    ticks: { font: { size: 10, family: 'Nunito' }, color: '#6b7280' },
                    border: { display: false }
                },
                x: {
                    grid: { display: false, drawBorder: false },
                    ticks: { font: { size: 10, family: 'Nunito' }, color: '#6b7280' },
                    border: { display: false }
                }
            }
        };

        const pieOptions = {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#064e3b',
                    titleColor: '#fff',
                    bodyColor: '#fff',
                    padding: 10,
                    cornerRadius: 8,
                    displayColors: true
                }
            }
        };

        // Revenue Trend Chart
        destroyChart('revenueTrend');
        const revenueCtx = document.getElementById('revenueTrendChart').getContext('2d');
        const gradientRevenue = revenueCtx.createLinearGradient(0, 0, 0, 320);
        gradientRevenue.addColorStop(0, 'rgba(16, 185, 129, 0.3)');
        gradientRevenue.addColorStop(1, 'rgba(16, 185, 129, 0.01)');

        chartInstances.revenueTrend = new Chart(revenueCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode($dailyLabels) !!},
                datasets: [{
                    label: 'Revenue',
                    data: {!! json_encode($dailyRevenue) !!},
                    borderColor: '#10b981',
                    backgroundColor: gradientRevenue,
                    borderWidth: 3,
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: '#10b981',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 7
                }]
            },
            options: commonOptions
        });

        // Order Status Doughnut
        destroyChart('orderStatus');
        chartInstances.orderStatus = new Chart(document.getElementById('orderStatusChart'), {
            type: 'doughnut',
            data: {
                labels: ['Paid', 'Pending', 'Failed', 'Cancelled'],
                datasets: [{
                    data: {!! json_encode($orderStatusData) !!},
                    backgroundColor: ['#10b981', '#fbbf24', '#ef4444', '#9ca3af'],
                    borderWidth: 0,
                    hoverOffset: 8
                }]
            },
            options: pieOptions
        });

        // User Growth Chart
        destroyChart('userGrowth');
        const userCtx = document.getElementById('userGrowthChart').getContext('2d');
        const gradientUsers = userCtx.createLinearGradient(0, 0, 0, 256);
        gradientUsers.addColorStop(0, 'rgba(14, 165, 233, 0.5)');
        gradientUsers.addColorStop(1, 'rgba(14, 165, 233, 0.05)');

        chartInstances.userGrowth = new Chart(userCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($monthlyLabels) !!},
                datasets: [{
                    label: 'New Users',
                    data: {!! json_encode($monthlyUsers) !!},
                    backgroundColor: gradientUsers,
                    borderRadius: 6,
                    borderSkipped: false,
                    barThickness: 24
                }]
            },
            options: commonOptions
        });

        // Daily Orders Chart
        destroyChart('dailyOrders');
        chartInstances.dailyOrders = new Chart(document.getElementById('dailyOrdersChart'), {
            type: 'line',
            data: {
                labels: {!! json_encode($dailyLabels) !!},
                datasets: [{
                    label: 'Orders',
                    data: {!! json_encode($dailyOrders) !!},
                    borderColor: '#8b5cf6',
                    backgroundColor: 'rgba(139, 92, 246, 0.1)',
                    borderWidth: 2,
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: '#8b5cf6',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 3,
                    pointHoverRadius: 6
                }]
            },
            options: commonOptions
        });

        // Payment Status Doughnut
        destroyChart('paymentStatus');
        chartInstances.paymentStatus = new Chart(document.getElementById('paymentStatusChart'), {
            type: 'doughnut',
            data: {
                labels: ['Success', 'Pending', 'Failed', 'Refunded'],
                datasets: [{
                    data: {!! json_encode($paymentStatusData) !!},
                    backgroundColor: ['#10b981', '#fbbf24', '#ef4444', '#6b7280'],
                    borderWidth: 0,
                    hoverOffset: 8
                }]
            },
            options: pieOptions
        });

        // Handle resize
        let resizeTimeout;
        window.addEventListener('resize', function() {
            clearTimeout(resizeTimeout);
            resizeTimeout = setTimeout(function() {
                Object.values(chartInstances).forEach(chart => chart.resize());
            }, 250);
        });
    });
</script>
@endsection
