@extends('layouts.dashboard')

@section('title', 'Payments - ' . config('app.name', 'Elimu Store'))
@section('page_title', 'Manage Payments')

@section('content')
<div class="space-y-6">
    <div class="hover-lift dashboard-card bg-white rounded-xl border border-gray-100 p-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-600 flex items-center justify-center text-white shadow-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-gray-800">Payments</h2>
                    <p class="text-sm text-gray-500 mt-1">Track and manage payment records</p>
                </div>
            </div>
        </div>
    </div>

    <div class="hover-lift dashboard-card bg-white rounded-xl border border-gray-100 p-4">
        <div class="flex flex-col sm:flex-row gap-3">
            <div class="relative flex-1">
                <input type="text" id="paymentSearch" placeholder="Search by reference or customer..." class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none">
                <svg class="w-4 h-4 text-gray-400 absolute left-3.5 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
            <select id="statusFilter" class="pl-4 pr-10 py-2.5 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none bg-white min-w-[180px]">
                <option value="">All Status</option>
                <option value="pending">Pending</option>
                <option value="completed">Completed</option>
                <option value="failed">Failed</option>
                <option value="refunded">Refunded</option>
            </select>
        </div>
    </div>

    <div class="hover-lift bg-white rounded-xl border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-sm font-semibold text-gray-900 section-header">All Payments</h3>
            <span class="text-xs text-gray-500" id="paymentsCount">{{ $payments->count() }} payments</span>
        </div>
        <div class="overflow-x-auto">
            <table class="dashboard-table w-full text-sm" id="paymentsTable">
                <thead><tr class="text-left text-xs text-gray-500 bg-gray-50/50">
                    <th class="px-6 py-3 font-medium">#</th>
                    <th class="px-6 py-3 font-medium">Reference</th>
                    <th class="px-6 py-3 font-medium">Customer</th>
                    <th class="px-6 py-3 font-medium">Method</th>
                    <th class="px-6 py-3 font-medium">Order Ref</th>
                    <th class="px-6 py-3 font-medium">Status</th>
                    <th class="px-6 py-3 font-medium">Date</th>
                    <th class="px-6 py-3 font-medium text-right">Actions</th>
                </tr></thead>
                <tbody>
                    @forelse($payments as $index => $payment)
                    <tr class="border-t border-gray-100 transition-colors animate-fade" data-id="{{ $payment->id }}" style="animation-delay: {{ $index * 0.05 }}s">
                        <td class="px-6 py-3 text-xs text-gray-500">{{ $index + 1 }}</td>
                        <td class="px-6 py-3"><span class="font-medium text-gray-900">{{ $payment->reference }}</span></td>
                        <td class="px-6 py-3">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-700 font-bold text-xs">{{ strtoupper(substr($payment->order->user->name ?? 'G', 0, 1)) }}</div>
                                <p class="text-sm font-medium text-gray-900">{{ $payment->order->user->name ?? 'Guest' }}</p>
                            </div>
                        </td>
                        <td class="px-6 py-3 text-xs text-gray-600 capitalize">{{ $payment->method }}</td>
                        <td class="px-6 py-3 text-xs text-gray-600">#{{ $payment->order->reference ?? '-' }}</td>
                        <td class="px-6 py-3">
                            <select onchange="updatePaymentStatus({{ $payment->id }}, this.value)" class="status-select text-xs rounded-lg border border-gray-200 px-2 py-1 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none">
                                <option value="pending" {{ $payment->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="completed" {{ $payment->status === 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="failed" {{ $payment->status === 'failed' ? 'selected' : '' }}>Failed</option>
                                <option value="refunded" {{ $payment->status === 'refunded' ? 'selected' : '' }}>Refunded</option>
                            </select>
                        </td>
                        <td class="px-6 py-3 text-xs text-gray-500">{{ $payment->created_at->format('M d, Y') }}</td>
                        <td class="px-6 py-3 text-right">
                            <button onclick="viewPaymentDetails({{ $payment->id }})" class="p-1.5 text-emerald-600 hover:bg-emerald-50 rounded-lg transition-colors" title="View">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr id="emptyRow"><td colspan="8" class="px-6 py-12 text-center text-gray-400 text-sm">No payments found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Payment Details Drawer --}}
<div id="paymentDrawer" class="drawer-overlay" role="dialog" aria-modal="true" onclick="closePaymentDrawer()">
    <div class="drawer-sidebar" onclick="event.stopPropagation()">
        <div class="drawer-header">
            <button type="button" onclick="closePaymentDrawer()" class="drawer-close">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
            <h3 class="text-xl font-bold text-white relative z-10">Payment Details</h3>
        </div>
        <div class="drawer-body" id="paymentDetailsBody">
            <p class="text-gray-500 text-sm">Select a payment to view details.</p>
        </div>
        <div class="drawer-footer">
            <button type="button" onclick="closePaymentDrawer()" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-semibold rounded-lg transition-colors">Close</button>
        </div>
    </div>
</div>

<script>
    const paymentsTable = document.getElementById('paymentsTable').querySelector('tbody');
    const paymentSearch = document.getElementById('paymentSearch');
    const statusFilter = document.getElementById('statusFilter');
    const paymentsCount = document.getElementById('paymentsCount');
    let searchTimeout;

    function showToast(type, message) {
        const Toast = Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 4000, timerProgressBar: true });
        Toast.fire({ icon: type, title: message });
    }

    function updatePaymentsCount() {
        const total = paymentsTable.querySelectorAll('tr[data-id]').length;
        paymentsCount.textContent = total + ' payment' + (total !== 1 ? 's' : '');
    }

    function updatePaymentStatus(id, status) {
        fetch(`{{ url('admin/sales/payments') }}/${id}/status`, {
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
        .catch(error => { showToast('error', 'Failed to update status'); console.error(error); });
    }

    function loadPayments() {
        const term = paymentSearch.value.trim();
        const status = statusFilter.value;
        const url = new URL('{{ route('admin.sales.payments') }}', window.location.origin);
        if (term) url.searchParams.append('search', term);
        if (status) url.searchParams.append('status', status);
        fetch(url, { method: 'GET', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
        .then(response => response.json())
        .then(result => { if (result.success) renderPaymentsTable(result.payments); })
        .catch(error => { showToast('error', 'Error loading payments'); console.error(error); });
    }

    function renderPaymentsTable(payments) {
        paymentsTable.innerHTML = '';
        if (payments.length === 0) {
            paymentsTable.innerHTML = '<tr id="emptyRow"><td colspan="8" class="px-6 py-12 text-center text-gray-400 text-sm">No payments found.</td></tr>';
            updatePaymentsCount();
            return;
        }
        payments.forEach((payment, index) => {
            const user = payment.order && payment.order.user ? payment.order.user : { name: 'Guest' };
            const row = document.createElement('tr');
            row.className = 'border-t border-gray-100 transition-colors animate-fade';
            row.style.animationDelay = `${index * 0.05}s`;
            row.setAttribute('data-id', payment.id);
            row.innerHTML = `
                <td class="px-6 py-3 text-xs text-gray-500">${index + 1}</td>
                <td class="px-6 py-3"><span class="font-medium text-gray-900">${payment.reference}</span></td>
                <td class="px-6 py-3">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-700 font-bold text-xs">${(user.name || 'G').charAt(0).toUpperCase()}</div>
                        <p class="text-sm font-medium text-gray-900">${user.name || 'Guest'}</p>
                    </div>
                </td>
                <td class="px-6 py-3 text-xs text-gray-600 capitalize">${payment.method}</td>
                <td class="px-6 py-3 text-xs text-gray-600">#${payment.order ? payment.order.reference : '-'}</td>
                <td class="px-6 py-3">
                    <select onchange="updatePaymentStatus(${payment.id}, this.value)" class="status-select text-xs rounded-lg border border-gray-200 px-2 py-1 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none">
                        <option value="pending" ${payment.status === 'pending' ? 'selected' : ''}>Pending</option>
                        <option value="completed" ${payment.status === 'completed' ? 'selected' : ''}>Completed</option>
                        <option value="failed" ${payment.status === 'failed' ? 'selected' : ''}>Failed</option>
                        <option value="refunded" ${payment.status === 'refunded' ? 'selected' : ''}>Refunded</option>
                    </select>
                </td>
                <td class="px-6 py-3 text-xs text-gray-500">${new Date(payment.created_at).toLocaleDateString()}</td>
                <td class="px-6 py-3 text-right">
                    <button onclick="viewPaymentDetails(${payment.id})" class="p-1.5 text-emerald-600 hover:bg-emerald-50 rounded-lg transition-colors" title="View">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    </button>
                </td>
            `;
            paymentsTable.appendChild(row);
        });
        updatePaymentsCount();
    }

    function viewPaymentDetails(id) {
        const row = document.querySelector(`tr[data-id="${id}"]`);
        const cells = row.querySelectorAll('td');
        const reference = cells[1].textContent;
        const customer = cells[2].textContent;
        const method = cells[3].textContent;
        const orderRef = cells[4].textContent;
        const status = cells[5].querySelector('select').value;
        const date = cells[6].textContent;
        document.getElementById('paymentDetailsBody').innerHTML = `
            <div class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-gray-50 rounded-lg p-3"><p class="text-xs text-gray-500">Reference</p><p class="text-sm font-semibold text-gray-900">${reference}</p></div>
                    <div class="bg-gray-50 rounded-lg p-3"><p class="text-xs text-gray-500">Date</p><p class="text-sm font-semibold text-gray-900">${date}</p></div>
                    <div class="bg-gray-50 rounded-lg p-3"><p class="text-xs text-gray-500">Customer</p><p class="text-sm font-semibold text-gray-900">${customer}</p></div>
                    <div class="bg-gray-50 rounded-lg p-3"><p class="text-xs text-gray-500">Method</p><p class="text-sm font-semibold text-gray-900 capitalize">${method}</p></div>
                    <div class="bg-gray-50 rounded-lg p-3"><p class="text-xs text-gray-500">Order Ref</p><p class="text-sm font-semibold text-gray-900">${orderRef}</p></div>
                    <div class="bg-gray-50 rounded-lg p-3"><p class="text-xs text-gray-500">Status</p><p class="text-sm font-semibold text-gray-900 capitalize">${status}</p></div>
                </div>
            </div>
        `;
        document.getElementById('paymentDrawer').classList.add('open');
        document.querySelector('#paymentDrawer .drawer-sidebar').classList.add('open');
    }

    function closePaymentDrawer() {
        document.getElementById('paymentDrawer').classList.remove('open');
        document.querySelector('#paymentDrawer .drawer-sidebar').classList.remove('open');
    }

    paymentSearch.addEventListener('input', function() { clearTimeout(searchTimeout); searchTimeout = setTimeout(loadPayments, 400); });
    statusFilter.addEventListener('change', loadPayments);
</script>
@endsection
