@extends('layouts.dashboard')

@section('title', 'Orders - ' . config('app.name', 'Elimu Store'))
@section('page_title', 'Manage Orders')

@section('content')
<div class="space-y-6">
    <div class="hover-lift dashboard-card bg-white rounded-xl border border-gray-100 p-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-600 flex items-center justify-center text-white shadow-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-gray-800">Orders</h2>
                    <p class="text-sm text-gray-500 mt-1">Manage customer orders and their status</p>
                </div>
            </div>
        </div>
    </div>

    <div class="hover-lift dashboard-card bg-white rounded-xl border border-gray-100 p-4">
        <div class="flex flex-col sm:flex-row gap-3">
            <div class="relative flex-1">
                <input type="text" id="orderSearch" placeholder="Search by reference or customer..." class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none">
                <svg class="w-4 h-4 text-gray-400 absolute left-3.5 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
            <select id="statusFilter" class="pl-4 pr-10 py-2.5 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none bg-white min-w-[180px]">
                <option value="">All Status</option>
                <option value="pending">Pending</option>
                <option value="completed">Completed</option>
                <option value="cancelled">Cancelled</option>
                <option value="refunded">Refunded</option>
            </select>
        </div>
    </div>

    <div class="hover-lift bg-white rounded-xl border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-sm font-semibold text-gray-900 section-header">All Orders</h3>
            <span class="text-xs text-gray-500" id="ordersCount">{{ $orders->count() }} orders</span>
        </div>
        <div class="overflow-x-auto">
            <table class="dashboard-table w-full text-sm" id="ordersTable">
                <thead><tr class="text-left text-xs text-gray-500 bg-gray-50/50">
                    <th class="px-6 py-3 font-medium">#</th>
                    <th class="px-6 py-3 font-medium">Reference</th>
                    <th class="px-6 py-3 font-medium">Customer</th>
                    <th class="px-6 py-3 font-medium">Items</th>
                    <th class="px-6 py-3 font-medium">Total</th>
                    <th class="px-6 py-3 font-medium">Payment</th>
                    <th class="px-6 py-3 font-medium">Status</th>
                    <th class="px-6 py-3 font-medium">Date</th>
                    <th class="px-6 py-3 font-medium text-right">Actions</th>
                </tr></thead>
                <tbody>
                    @forelse($orders as $index => $order)
                    <tr class="border-t border-gray-100 transition-colors animate-fade" data-id="{{ $order->id }}" style="animation-delay: {{ $index * 0.05 }}s">
                        <td class="px-6 py-3 text-xs text-gray-500">{{ $index + 1 }}</td>
                        <td class="px-6 py-3"><span class="font-medium text-gray-900">#{{ $order->reference }}</span></td>
                        <td class="px-6 py-3">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-700 font-bold text-xs">{{ strtoupper(substr($order->user->name ?? 'G', 0, 1)) }}</div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $order->user->name ?? 'Guest' }}</p>
                                    <p class="text-xs text-gray-500">{{ $order->user->email ?? '' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-3"><span class="text-xs text-gray-700">{{ $order->items->count() }} item{{ $order->items->count() !== 1 ? 's' : '' }}</span></td>
                        <td class="px-6 py-3"><span class="text-sm font-semibold text-gray-900">TSh {{ number_format($order->total_amount, 0) }}</span></td>
                        <td class="px-6 py-3">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-medium border {{ $order->payment && $order->payment->status === 'completed' ? 'bg-emerald-50 text-emerald-700 border-emerald-100' : 'bg-amber-50 text-amber-700 border-amber-100' }}">
                                {{ $order->payment ? ucfirst($order->payment->status) : 'No Payment' }}
                            </span>
                        </td>
                        <td class="px-6 py-3">
                            <select onchange="updateOrderStatus({{ $order->id }}, this.value)" class="status-select text-xs rounded-lg border border-gray-200 px-2 py-1 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none">
                                <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="completed" {{ $order->status === 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                <option value="refunded" {{ $order->status === 'refunded' ? 'selected' : '' }}>Refunded</option>
                            </select>
                        </td>
                        <td class="px-6 py-3 text-xs text-gray-500">{{ $order->created_at->format('M d, Y') }}</td>
                        <td class="px-6 py-3 text-right">
                            <button onclick="viewOrderDetails({{ $order->id }})" class="p-1.5 text-emerald-600 hover:bg-emerald-50 rounded-lg transition-colors" title="View">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr id="emptyRow"><td colspan="9" class="px-6 py-12 text-center text-gray-400 text-sm">No orders found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Order Details Drawer --}}
<div id="orderDrawer" class="drawer-overlay" role="dialog" aria-modal="true" onclick="closeOrderDrawer()">
    <div class="drawer-sidebar" onclick="event.stopPropagation()">
        <div class="drawer-header">
            <button type="button" onclick="closeOrderDrawer()" class="drawer-close">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
            <h3 class="text-xl font-bold text-white relative z-10">Order Details</h3>
        </div>
        <div class="drawer-body" id="orderDetailsBody">
            <p class="text-gray-500 text-sm">Select an order to view details.</p>
        </div>
        <div class="drawer-footer">
            <button type="button" onclick="closeOrderDrawer()" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-semibold rounded-lg transition-colors">Close</button>
        </div>
    </div>
</div>

<script>
    const ordersTable = document.getElementById('ordersTable').querySelector('tbody');
    const orderSearch = document.getElementById('orderSearch');
    const statusFilter = document.getElementById('statusFilter');
    const ordersCount = document.getElementById('ordersCount');
    let searchTimeout;

    function showToast(type, message) {
        const Toast = Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 4000, timerProgressBar: true });
        Toast.fire({ icon: type, title: message });
    }

    function updateRowNumbers() {
        const rows = ordersTable.querySelectorAll('tr[data-id]');
        rows.forEach((row, index) => { row.querySelector('td:first-child').textContent = index + 1; });
    }

    function updateOrdersCount() {
        const total = ordersTable.querySelectorAll('tr[data-id]').length;
        ordersCount.textContent = total + ' order' + (total !== 1 ? 's' : '');
    }

    function updateOrderStatus(id, status) {
        fetch(`{{ url('admin/sales/orders') }}/${id}/status`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ status: status })
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                showToast('success', result.message);
            } else {
                showToast('error', result.message || 'Failed to update status');
            }
        })
        .catch(error => {
            showToast('error', 'Failed to update status');
            console.error(error);
        });
    }

    function loadOrders() {
        const term = orderSearch.value.trim();
        const status = statusFilter.value;
        const url = new URL('{{ route('admin.sales.orders') }}', window.location.origin);
        if (term) url.searchParams.append('search', term);
        if (status) url.searchParams.append('status', status);

        fetch(url, {
            method: 'GET',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) renderOrdersTable(result.orders);
        })
        .catch(error => { showToast('error', 'Error loading orders'); console.error(error); });
    }

    function renderOrdersTable(orders) {
        ordersTable.innerHTML = '';
        if (orders.length === 0) {
            ordersTable.innerHTML = '<tr id="emptyRow"><td colspan="9" class="px-6 py-12 text-center text-gray-400 text-sm">No orders found.</td></tr>';
            updateOrdersCount();
            return;
        }
        orders.forEach((order, index) => {
            const user = order.user || { name: 'Guest', email: '' };
            const paymentStatus = order.payment ? order.payment.status : 'No Payment';
            const paymentClass = order.payment && order.payment.status === 'completed' ? 'bg-emerald-50 text-emerald-700 border-emerald-100' : 'bg-amber-50 text-amber-700 border-amber-100';
            const row = document.createElement('tr');
            row.className = 'border-t border-gray-100 transition-colors animate-fade';
            row.style.animationDelay = `${index * 0.05}s`;
            row.setAttribute('data-id', order.id);
            row.innerHTML = `
                <td class="px-6 py-3 text-xs text-gray-500">${index + 1}</td>
                <td class="px-6 py-3"><span class="font-medium text-gray-900">#${order.reference}</span></td>
                <td class="px-6 py-3">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-700 font-bold text-xs">${(user.name || 'G').charAt(0).toUpperCase()}</div>
                        <div><p class="text-sm font-medium text-gray-900">${user.name || 'Guest'}</p><p class="text-xs text-gray-500">${user.email || ''}</p></div>
                    </div>
                </td>
                <td class="px-6 py-3"><span class="text-xs text-gray-700">${order.items ? order.items.length : 0} item${order.items && order.items.length !== 1 ? 's' : ''}</span></td>
                <td class="px-6 py-3"><span class="text-sm font-semibold text-gray-900">TSh ${parseFloat(order.total_amount).toLocaleString()}</span></td>
                <td class="px-6 py-3"><span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-medium border ${paymentClass}">${paymentStatus.charAt(0).toUpperCase() + paymentStatus.slice(1)}</span></td>
                <td class="px-6 py-3">
                    <select onchange="updateOrderStatus(${order.id}, this.value)" class="status-select text-xs rounded-lg border border-gray-200 px-2 py-1 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none">
                        <option value="pending" ${order.status === 'pending' ? 'selected' : ''}>Pending</option>
                        <option value="completed" ${order.status === 'completed' ? 'selected' : ''}>Completed</option>
                        <option value="cancelled" ${order.status === 'cancelled' ? 'selected' : ''}>Cancelled</option>
                        <option value="refunded" ${order.status === 'refunded' ? 'selected' : ''}>Refunded</option>
                    </select>
                </td>
                <td class="px-6 py-3 text-xs text-gray-500">${new Date(order.created_at).toLocaleDateString()}</td>
                <td class="px-6 py-3 text-right">
                    <button onclick="viewOrderDetails(${order.id})" class="p-1.5 text-emerald-600 hover:bg-emerald-50 rounded-lg transition-colors" title="View">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    </button>
                </td>
            `;
            ordersTable.appendChild(row);
        });
        updateOrdersCount();
    }

    function viewOrderDetails(id) {
        const row = document.querySelector(`tr[data-id="${id}"]`);
        const cells = row.querySelectorAll('td');
        const reference = cells[1].textContent;
        const customer = cells[2].querySelector('p').textContent;
        const email = cells[2].querySelectorAll('p')[1].textContent;
        const items = cells[3].textContent;
        const total = cells[4].textContent;
        const payment = cells[5].textContent;
        const status = cells[6].querySelector('select').value;
        const date = cells[7].textContent;

        document.getElementById('orderDetailsBody').innerHTML = `
            <div class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-gray-50 rounded-lg p-3"><p class="text-xs text-gray-500">Reference</p><p class="text-sm font-semibold text-gray-900">${reference}</p></div>
                    <div class="bg-gray-50 rounded-lg p-3"><p class="text-xs text-gray-500">Date</p><p class="text-sm font-semibold text-gray-900">${date}</p></div>
                    <div class="bg-gray-50 rounded-lg p-3"><p class="text-xs text-gray-500">Customer</p><p class="text-sm font-semibold text-gray-900">${customer}</p><p class="text-xs text-gray-500">${email}</p></div>
                    <div class="bg-gray-50 rounded-lg p-3"><p class="text-xs text-gray-500">Total</p><p class="text-sm font-semibold text-gray-900">${total}</p></div>
                </div>
                <div class="bg-gray-50 rounded-lg p-3"><p class="text-xs text-gray-500">Status</p><p class="text-sm font-semibold text-gray-900 capitalize">${status}</p></div>
                <div class="bg-gray-50 rounded-lg p-3"><p class="text-xs text-gray-500">Payment</p><p class="text-sm font-semibold text-gray-900">${payment}</p></div>
                <div class="bg-gray-50 rounded-lg p-3"><p class="text-xs text-gray-500">Items</p><p class="text-sm font-semibold text-gray-900">${items}</p></div>
            </div>
        `;
        document.getElementById('orderDrawer').classList.add('open');
        document.querySelector('#orderDrawer .drawer-sidebar').classList.add('open');
    }

    function closeOrderDrawer() {
        document.getElementById('orderDrawer').classList.remove('open');
        document.querySelector('#orderDrawer .drawer-sidebar').classList.remove('open');
    }

    orderSearch.addEventListener('input', function() { clearTimeout(searchTimeout); searchTimeout = setTimeout(loadOrders, 400); });
    statusFilter.addEventListener('change', loadOrders);
</script>
@endsection
