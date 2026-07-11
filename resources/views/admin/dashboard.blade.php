@extends('layouts.dashboard')

@section('title', 'Admin Dashboard - ' . config('app.name', 'Elimu Store'))
@section('page_title', 'Dashboard Overview')

@section('content')
@php
$publishedPercent = $totalNotes > 0 ? round(($publishedNotes / $totalNotes) * 100) : 0;
$paidPercent = $totalOrders > 0 ? round((\App\Models\Order::where('status', 'paid')->count() / $totalOrders) * 100) : 0;
$freeNotesPercent = $totalNotes > 0 ? round((\App\Models\Note::where('is_free', true)->count() / $totalNotes) * 100) : 0;
$verifiedUsersPercent = $totalUsers > 0 ? round((\App\Models\User::where('role', 'customer')->whereNotNull('phone_verified_at')->count() / $totalUsers) * 100) : 0;
@endphp

<div class="space-y-6">
    {{-- KPI Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
        <div class="bg-gradient-to-br from-emerald-600 to-emerald-700 rounded-xl border border-emerald-500 p-4 text-white relative overflow-hidden hover:shadow-lg transition-shadow">
            <div class="absolute top-0 right-0 w-16 h-16 bg-white/10 rounded-full -mr-8 -mt-8"></div>
            <div class="relative z-10">
                <div class="flex items-start justify-between mb-2">
                    <span class="text-[10px] font-medium text-emerald-100">Notes Zote</span>
                    <svg class="w-4 h-4 text-emerald-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <p class="text-xl font-bold tracking-tight text-white">{{ number_format($totalNotes) }}</p>
                <p class="text-[10px] text-emerald-200 font-medium mt-1">{{ $publishedNotes }} zimechapishwa</p>
            </div>
        </div>

        <div class="bg-gradient-to-br from-amber-400 to-amber-500 rounded-xl border border-amber-300 p-4 text-white relative overflow-hidden hover:shadow-lg transition-shadow">
            <div class="absolute top-0 right-0 w-16 h-16 bg-white/10 rounded-full -mr-8 -mt-8"></div>
            <div class="relative z-10">
                <div class="flex items-start justify-between mb-2">
                    <span class="text-[10px] font-medium text-amber-50">Mauzo</span>
                    <svg class="w-4 h-4 text-amber-100" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <p class="text-xl font-bold tracking-tight text-white">TZS {{ number_format($totalRevenue, 0) }}</p>
                <p class="text-[10px] text-amber-100 font-medium mt-1">{{ $totalOrders }} orders</p>
            </div>
        </div>

        <div class="bg-gradient-to-br from-sky-500 to-sky-600 rounded-xl border border-sky-400 p-4 text-white relative overflow-hidden hover:shadow-lg transition-shadow">
            <div class="absolute top-0 right-0 w-16 h-16 bg-white/10 rounded-full -mr-8 -mt-8"></div>
            <div class="relative z-10">
                <div class="flex items-start justify-between mb-2">
                    <span class="text-[10px] font-medium text-sky-100">Watumiaji</span>
                    <svg class="w-4 h-4 text-sky-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                </div>
                <p class="text-xl font-bold tracking-tight text-white">{{ number_format($totalUsers) }}</p>
                <p class="text-[10px] text-sky-200 font-medium mt-1">Wateja wote</p>
            </div>
        </div>

        <div class="bg-gradient-to-br from-violet-500 to-violet-600 rounded-xl border border-violet-400 p-4 text-white relative overflow-hidden hover:shadow-lg transition-shadow">
            <div class="absolute top-0 right-0 w-16 h-16 bg-white/10 rounded-full -mr-8 -mt-8"></div>
            <div class="relative z-10">
                <div class="flex items-start justify-between mb-2">
                    <span class="text-[10px] font-medium text-violet-100">Orders Pending</span>
                    <svg class="w-4 h-4 text-violet-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                </div>
                <p class="text-xl font-bold tracking-tight text-white">{{ number_format($pendingOrders) }}</p>
                <p class="text-[10px] text-violet-200 font-medium mt-1">Inasubiri malipo</p>
            </div>
        </div>
    </div>

    {{-- Circular Progress Charts --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl border border-gray-100 p-5 flex flex-col items-center">
            <h3 class="text-xs font-semibold text-gray-700 mb-3">Published Notes</h3>
            <div class="relative w-24 h-24">
                <svg class="w-24 h-24 transform -rotate-90">
                    <circle cx="48" cy="48" r="42" stroke="#e5e7eb" stroke-width="8" fill="none"/>
                    <circle cx="48" cy="48" r="42" stroke="#10b981" stroke-width="8" fill="none" stroke-dasharray="264" stroke-dashoffset="{{ 264 - (264 * $publishedPercent / 100) }}" stroke-linecap="round"/>
                </svg>
                <div class="absolute inset-0 flex items-center justify-center">
                    <span class="text-lg font-bold text-gray-800">{{ $publishedPercent }}%</span>
                </div>
            </div>
            <p class="text-xs text-gray-500 mt-2">{{ $publishedNotes }}/{{ $totalNotes }} notes</p>
        </div>

        <div class="bg-white rounded-xl border border-gray-100 p-5 flex flex-col items-center">
            <h3 class="text-xs font-semibold text-gray-700 mb-3">Paid Orders</h3>
            <div class="relative w-24 h-24">
                <svg class="w-24 h-24 transform -rotate-90">
                    <circle cx="48" cy="48" r="42" stroke="#e5e7eb" stroke-width="8" fill="none"/>
                    <circle cx="48" cy="48" r="42" stroke="#f59e0b" stroke-width="8" fill="none" stroke-dasharray="264" stroke-dashoffset="{{ 264 - (264 * $paidPercent / 100) }}" stroke-linecap="round"/>
                </svg>
                <div class="absolute inset-0 flex items-center justify-center">
                    <span class="text-lg font-bold text-gray-800">{{ $paidPercent }}%</span>
                </div>
            </div>
            <p class="text-xs text-gray-500 mt-2">{{ \App\Models\Order::where('status', 'paid')->count() }}/{{ $totalOrders }} orders</p>
        </div>

        <div class="bg-white rounded-xl border border-gray-100 p-5 flex flex-col items-center">
            <h3 class="text-xs font-semibold text-gray-700 mb-3">Free Notes</h3>
            <div class="relative w-24 h-24">
                <svg class="w-24 h-24 transform -rotate-90">
                    <circle cx="48" cy="48" r="42" stroke="#e5e7eb" stroke-width="8" fill="none"/>
                    <circle cx="48" cy="48" r="42" stroke="#0ea5e9" stroke-width="8" fill="none" stroke-dasharray="264" stroke-dashoffset="{{ 264 - (264 * $freeNotesPercent / 100) }}" stroke-linecap="round"/>
                </svg>
                <div class="absolute inset-0 flex items-center justify-center">
                    <span class="text-lg font-bold text-gray-800">{{ $freeNotesPercent }}%</span>
                </div>
            </div>
            <p class="text-xs text-gray-500 mt-2">{{ \App\Models\Note::where('is_free', true)->count() }}/{{ $totalNotes }} notes</p>
        </div>

        <div class="bg-white rounded-xl border border-gray-100 p-5 flex flex-col items-center">
            <h3 class="text-xs font-semibold text-gray-700 mb-3">Verified Users</h3>
            <div class="relative w-24 h-24">
                <svg class="w-24 h-24 transform -rotate-90">
                    <circle cx="48" cy="48" r="42" stroke="#e5e7eb" stroke-width="8" fill="none"/>
                    <circle cx="48" cy="48" r="42" stroke="#8b5cf6" stroke-width="8" fill="none" stroke-dasharray="264" stroke-dashoffset="{{ 264 - (264 * $verifiedUsersPercent / 100) }}" stroke-linecap="round"/>
                </svg>
                <div class="absolute inset-0 flex items-center justify-center">
                    <span class="text-lg font-bold text-gray-800">{{ $verifiedUsersPercent }}%</span>
                </div>
            </div>
            <p class="text-xs text-gray-500 mt-2">{{ \App\Models\User::where('role', 'customer')->whereNotNull('phone_verified_at')->count() }}/{{ $totalUsers }} users</p>
        </div>
    </div>

    {{-- Charts --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Revenue Chart --}}
        <div class="bg-white rounded-xl border border-gray-100 p-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-semibold text-gray-900">Revenue Overview (Last 14 Days)</h3>
                <span class="text-xs text-gray-500">Payments</span>
            </div>
            <div class="relative h-72 w-full">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>

        {{-- User Activity Chart --}}
        <div class="bg-white rounded-xl border border-gray-100 p-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-semibold text-gray-900">User Activity (Last 14 Days)</h3>
                <span class="text-xs text-gray-500">Logins & Signups</span>
            </div>
            <div class="relative h-72 w-full">
                <canvas id="activityChart"></canvas>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Recent Orders --}}
        <div class="bg-white rounded-xl border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="text-sm font-semibold text-gray-900">Orders za Hivi Karibuni</h3>
                <a href="#" class="text-xs font-medium text-emerald-600 hover:text-emerald-700">View All</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead><tr class="text-left text-xs text-gray-500 bg-gray-50/50">
                        <th class="px-6 py-3 font-medium">Order ID</th>
                        <th class="px-6 py-3 font-medium">Customer</th>
                        <th class="px-6 py-3 font-medium">Amount</th>
                        <th class="px-6 py-3 font-medium">Status</th>
                    </tr></thead>
                    <tbody>
                        @forelse($recentOrders as $order)
                        <tr class="border-t border-gray-100 hover:bg-gray-50/50 transition-colors">
                            <td class="px-6 py-3 font-mono text-xs text-gray-500">{{ $order->reference }}</td>
                            <td class="px-6 py-3 text-xs text-gray-700">{{ $order->user?->name ?? 'Unknown' }}</td>
                            <td class="px-6 py-3 text-xs font-semibold text-gray-900">TZS {{ number_format($order->total_amount, 0) }}</td>
                            <td class="px-6 py-3">
                                @if($order->status === 'paid')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-emerald-50 text-emerald-700 border border-emerald-100">Paid</span>
                                @elseif($order->status === 'pending')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-amber-50 text-amber-700 border border-amber-100">Pending</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-red-50 text-red-700 border border-red-100">{{ ucfirst($order->status) }}</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="px-6 py-8 text-center text-gray-400 text-xs">No orders yet</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Top Notes --}}
        <div class="bg-white rounded-xl border border-gray-100 p-6">
            <h3 class="text-sm font-semibold text-gray-900 mb-4">Notes Zinazouzwa Zaidi</h3>
            <div class="space-y-3">
                @forelse($topNotes as $note)
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-emerald-100 flex items-center justify-center text-emerald-700 font-bold text-xs">
                        {{ strtoupper(substr($note->title, 0, 1)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 truncate">{{ $note->title }}</p>
                        <p class="text-xs text-gray-400">{{ $note->order_items_count }} purchases</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-bold text-gray-900">TZS {{ number_format($note->price, 0) }}</p>
                    </div>
                </div>
                @empty
                <p class="text-sm text-gray-400 text-center py-4">No notes yet</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const chartInstances = {};

        function initCharts() {
            // Destroy existing charts if any
            if (chartInstances.revenue) chartInstances.revenue.destroy();
            if (chartInstances.activity) chartInstances.activity.destroy();

            const dailyLabels = {!! json_encode($dailyLabels) !!};
            const dailySales = {!! json_encode($dailySales) !!};
            const dailyUsers = {!! json_encode($dailyUsers) !!};

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
                },
                interaction: {
                    intersect: false,
                    mode: 'index'
                }
            };

            const revenueCtx = document.getElementById('revenueChart').getContext('2d');
            const gradientRevenue = revenueCtx.createLinearGradient(0, 0, 0, 288);
            gradientRevenue.addColorStop(0, 'rgba(16, 185, 129, 0.25)');
            gradientRevenue.addColorStop(1, 'rgba(16, 185, 129, 0.01)');

            chartInstances.revenue = new Chart(revenueCtx, {
                type: 'line',
                data: {
                    labels: dailyLabels,
                    datasets: [{
                        label: 'Revenue (TZS)',
                        data: dailySales,
                        borderColor: '#10b981',
                        backgroundColor: gradientRevenue,
                        borderWidth: 3,
                        tension: 0.4,
                        fill: true,
                        pointBackgroundColor: '#10b981',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6
                    }]
                },
                options: commonOptions
            });

            const activityCtx = document.getElementById('activityChart').getContext('2d');
            const gradientActivity = activityCtx.createLinearGradient(0, 0, 0, 288);
            gradientActivity.addColorStop(0, 'rgba(14, 165, 233, 0.8)');
            gradientActivity.addColorStop(1, 'rgba(14, 165, 233, 0.2)');

            chartInstances.activity = new Chart(activityCtx, {
                type: 'bar',
                data: {
                    labels: dailyLabels,
                    datasets: [{
                        label: 'New Users',
                        data: dailyUsers,
                        backgroundColor: gradientActivity,
                        borderRadius: 6,
                        borderSkipped: false,
                        barThickness: 16,
                        maxBarThickness: 24
                    }]
                },
                options: commonOptions
            });
        }

        // Initialize after a short delay to ensure container is rendered
        setTimeout(initCharts, 100);

        // Handle window resize gracefully
        let resizeTimeout;
        window.addEventListener('resize', function() {
            clearTimeout(resizeTimeout);
            resizeTimeout = setTimeout(function() {
                if (chartInstances.revenue) chartInstances.revenue.resize();
                if (chartInstances.activity) chartInstances.activity.resize();
            }, 250);
        });
    });
</script>
@endsection
