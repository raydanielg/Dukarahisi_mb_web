@extends('layouts.dashboard')

@section('title', 'Customers - ' . config('app.name', 'Elimu Store'))
@section('page_title', 'Manage Customers')

@section('content')
<div class="space-y-6">
    <div class="hover-lift dashboard-card bg-white rounded-xl border border-gray-100 p-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-600 flex items-center justify-center text-white shadow-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-gray-800">Customers</h2>
                    <p class="text-sm text-gray-500 mt-1">View and manage registered customers</p>
                </div>
            </div>
        </div>
    </div>

    <div class="hover-lift dashboard-card bg-white rounded-xl border border-gray-100 p-4">
        <div class="relative">
            <input type="text" id="customerSearch" placeholder="Search by name, email, or phone..." class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none">
            <svg class="w-4 h-4 text-gray-400 absolute left-3.5 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
        </div>
    </div>

    <div class="hover-lift bg-white rounded-xl border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-sm font-semibold text-gray-900 section-header">All Customers</h3>
            <span class="text-xs text-gray-500" id="customersCount">{{ $customers->count() }} customers</span>
        </div>
        <div class="overflow-x-auto">
            <table class="dashboard-table w-full text-sm" id="customersTable">
                <thead><tr class="text-left text-xs text-gray-500 bg-gray-50/50">
                    <th class="px-6 py-3 font-medium">#</th>
                    <th class="px-6 py-3 font-medium">Customer</th>
                    <th class="px-6 py-3 font-medium">Email</th>
                    <th class="px-6 py-3 font-medium">Phone</th>
                    <th class="px-6 py-3 font-medium">Orders</th>
                    <th class="px-6 py-3 font-medium">Joined</th>
                    <th class="px-6 py-3 font-medium text-right">Actions</th>
                </tr></thead>
                <tbody>
                    @forelse($customers as $index => $customer)
                    <tr class="border-t border-gray-100 transition-colors animate-fade" data-id="{{ $customer->id }}" style="animation-delay: {{ $index * 0.05 }}s">
                        <td class="px-6 py-3 text-xs text-gray-500">{{ $index + 1 }}</td>
                        <td class="px-6 py-3">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-700 font-bold text-xs">{{ strtoupper(substr($customer->name, 0, 1)) }}</div>
                                <p class="text-sm font-semibold text-gray-900">{{ $customer->name }}</p>
                            </div>
                        </td>
                        <td class="px-6 py-3 text-xs text-gray-600">{{ $customer->email }}</td>
                        <td class="px-6 py-3 text-xs text-gray-600">{{ $customer->phone_number ?? '-' }}</td>
                        <td class="px-6 py-3"><span class="inline-flex items-center px-2 py-1 rounded-md bg-gray-100 text-xs font-medium text-gray-700">{{ $customer->orders->count() }}</span></td>
                        <td class="px-6 py-3 text-xs text-gray-500">{{ $customer->created_at->format('M d, Y') }}</td>
                        <td class="px-6 py-3 text-right">
                            <button onclick="viewCustomerDetails({{ $customer->id }})" class="p-1.5 text-emerald-600 hover:bg-emerald-50 rounded-lg transition-colors" title="View">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr id="emptyRow"><td colspan="7" class="px-6 py-12 text-center text-gray-400 text-sm">No customers found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Customer Details Drawer --}}
<div id="customerDrawer" class="drawer-overlay" role="dialog" aria-modal="true" onclick="closeCustomerDrawer()">
    <div class="drawer-sidebar" onclick="event.stopPropagation()">
        <div class="drawer-header">
            <button type="button" onclick="closeCustomerDrawer()" class="drawer-close">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
            <h3 class="text-xl font-bold text-white relative z-10">Customer Details</h3>
        </div>
        <div class="drawer-body" id="customerDetailsBody">
            <p class="text-gray-500 text-sm">Select a customer to view details.</p>
        </div>
        <div class="drawer-footer">
            <button type="button" onclick="closeCustomerDrawer()" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-semibold rounded-lg transition-colors">Close</button>
        </div>
    </div>
</div>

<script>
    const customersTable = document.getElementById('customersTable').querySelector('tbody');
    const customerSearch = document.getElementById('customerSearch');
    const customersCount = document.getElementById('customersCount');
    let searchTimeout;

    function showToast(type, message) {
        const Toast = Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 4000, timerProgressBar: true });
        Toast.fire({ icon: type, title: message });
    }

    function updateCustomersCount() {
        const total = customersTable.querySelectorAll('tr[data-id]').length;
        customersCount.textContent = total + ' customer' + (total !== 1 ? 's' : '');
    }

    function loadCustomers() {
        const term = customerSearch.value.trim();
        const url = new URL('{{ route('admin.sales.customers') }}', window.location.origin);
        if (term) url.searchParams.append('search', term);
        fetch(url, { method: 'GET', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
        .then(response => response.json())
        .then(result => { if (result.success) renderCustomersTable(result.customers); })
        .catch(error => { showToast('error', 'Error loading customers'); console.error(error); });
    }

    function renderCustomersTable(customers) {
        customersTable.innerHTML = '';
        if (customers.length === 0) {
            customersTable.innerHTML = '<tr id="emptyRow"><td colspan="7" class="px-6 py-12 text-center text-gray-400 text-sm">No customers found.</td></tr>';
            updateCustomersCount();
            return;
        }
        customers.forEach((customer, index) => {
            const row = document.createElement('tr');
            row.className = 'border-t border-gray-100 transition-colors animate-fade';
            row.style.animationDelay = `${index * 0.05}s`;
            row.setAttribute('data-id', customer.id);
            row.innerHTML = `
                <td class="px-6 py-3 text-xs text-gray-500">${index + 1}</td>
                <td class="px-6 py-3">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-700 font-bold text-xs">${customer.name.charAt(0).toUpperCase()}</div>
                        <p class="text-sm font-semibold text-gray-900">${customer.name}</p>
                    </div>
                </td>
                <td class="px-6 py-3 text-xs text-gray-600">${customer.email}</td>
                <td class="px-6 py-3 text-xs text-gray-600">${customer.phone_number || '-'}</td>
                <td class="px-6 py-3"><span class="inline-flex items-center px-2 py-1 rounded-md bg-gray-100 text-xs font-medium text-gray-700">${customer.orders ? customer.orders.length : 0}</span></td>
                <td class="px-6 py-3 text-xs text-gray-500">${new Date(customer.created_at).toLocaleDateString()}</td>
                <td class="px-6 py-3 text-right">
                    <button onclick="viewCustomerDetails(${customer.id})" class="p-1.5 text-emerald-600 hover:bg-emerald-50 rounded-lg transition-colors" title="View">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    </button>
                </td>
            `;
            customersTable.appendChild(row);
        });
        updateCustomersCount();
    }

    function viewCustomerDetails(id) {
        const row = document.querySelector(`tr[data-id="${id}"]`);
        const cells = row.querySelectorAll('td');
        const name = cells[1].textContent.trim();
        const email = cells[2].textContent;
        const phone = cells[3].textContent;
        const orders = cells[4].textContent;
        const joined = cells[5].textContent;
        document.getElementById('customerDetailsBody').innerHTML = `
            <div class="space-y-4">
                <div class="flex items-center gap-3">
                    <div class="w-14 h-14 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-700 font-bold text-lg">${name.charAt(0).toUpperCase()}</div>
                    <div><p class="text-lg font-bold text-gray-900">${name}</p><p class="text-sm text-gray-500">Customer</p></div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-gray-50 rounded-lg p-3"><p class="text-xs text-gray-500">Email</p><p class="text-sm font-semibold text-gray-900">${email}</p></div>
                    <div class="bg-gray-50 rounded-lg p-3"><p class="text-xs text-gray-500">Phone</p><p class="text-sm font-semibold text-gray-900">${phone}</p></div>
                    <div class="bg-gray-50 rounded-lg p-3"><p class="text-xs text-gray-500">Orders</p><p class="text-sm font-semibold text-gray-900">${orders}</p></div>
                    <div class="bg-gray-50 rounded-lg p-3"><p class="text-xs text-gray-500">Joined</p><p class="text-sm font-semibold text-gray-900">${joined}</p></div>
                </div>
            </div>
        `;
        document.getElementById('customerDrawer').classList.add('open');
        document.querySelector('#customerDrawer .drawer-sidebar').classList.add('open');
    }

    function closeCustomerDrawer() {
        document.getElementById('customerDrawer').classList.remove('open');
        document.querySelector('#customerDrawer .drawer-sidebar').classList.remove('open');
    }

    customerSearch.addEventListener('input', function() { clearTimeout(searchTimeout); searchTimeout = setTimeout(loadCustomers, 400); });
</script>
@endsection
