@extends('layouts.dashboard')

@section('title', 'Customers - ' . config('app.name', 'Elimu Store'))
@section('page_title', 'Manage Customers')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="hover-lift dashboard-card bg-white rounded-xl border border-gray-100 p-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-600 flex items-center justify-center text-white shadow-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-gray-800">Customers</h2>
                    <p class="text-sm text-gray-500 mt-1">View, reset passwords, delete, and bulk import customers</p>
                </div>
            </div>
            <button onclick="openBulkUploadModal()" class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg transition-colors shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                Import Customers
            </button>
        </div>
    </div>

    {{-- Search --}}
    <div class="hover-lift dashboard-card bg-white rounded-xl border border-gray-100 p-4">
        <div class="relative">
            <input type="text" id="customerSearch" placeholder="Search by name, email, or phone..." class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none">
            <svg class="w-4 h-4 text-gray-400 absolute left-3.5 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
        </div>
    </div>

    {{-- Bulk Actions Toolbar --}}
    <div id="bulkActionsBar" class="hidden hover-lift dashboard-card bg-emerald-50 border border-emerald-100 rounded-xl p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <input type="checkbox" id="selectAllCheckbox" class="w-4 h-4 text-emerald-600 rounded border-gray-300 focus:ring-emerald-500">
            <span class="text-sm font-semibold text-gray-700"><span id="selectedCount">0</span> selected</span>
        </div>
        <div class="flex items-center gap-2">
            <button onclick="bulkResetPassword()" class="inline-flex items-center gap-1.5 px-3 py-2 bg-amber-100 hover:bg-amber-200 text-amber-700 text-xs font-semibold rounded-lg transition-colors">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                Reset Password
            </button>
            <button onclick="bulkDeleteCustomers()" class="inline-flex items-center gap-1.5 px-3 py-2 bg-red-100 hover:bg-red-200 text-red-700 text-xs font-semibold rounded-lg transition-colors">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                Delete
            </button>
        </div>
    </div>

    {{-- Customers Table --}}
    <div class="hover-lift bg-white rounded-xl border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-sm font-semibold text-gray-900 section-header">All Customers</h3>
            <span class="text-xs text-gray-500" id="customersCount">{{ $customers->count() }} customers</span>
        </div>
        <div class="overflow-x-auto">
            <table class="dashboard-table w-full text-sm" id="customersTable">
                <thead><tr class="text-left text-xs text-gray-500 bg-gray-50/50">
                    <th class="px-4 py-3 font-medium w-10">
                        <input type="checkbox" id="selectAllHeader" class="w-4 h-4 text-emerald-600 rounded border-gray-300 focus:ring-emerald-500">
                    </th>
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
                        <td class="px-4 py-3">
                            <input type="checkbox" value="{{ $customer->id }}" class="customer-checkbox w-4 h-4 text-emerald-600 rounded border-gray-300 focus:ring-emerald-500">
                        </td>
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
                            <div class="flex items-center justify-end gap-1">
                                <button onclick="viewCustomerDetails({{ $customer->id }})" class="p-1.5 text-emerald-600 hover:bg-emerald-50 rounded-lg transition-colors" title="View">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </button>
                                <button onclick="resetSinglePassword({{ $customer->id }}, '{{ addslashes($customer->name) }}')" class="p-1.5 text-amber-600 hover:bg-amber-50 rounded-lg transition-colors" title="Reset Password">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                                </button>
                                <button onclick="deleteSingleCustomer({{ $customer->id }}, '{{ addslashes($customer->name) }}')" class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Delete">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
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

{{-- Reset Password Modal --}}
<div id="resetPasswordModal" class="fixed inset-0 z-50 hidden" role="dialog" aria-modal="true">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm transition-opacity" onclick="closeResetPasswordModal()"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden transform transition-all">
            <div class="bg-gradient-to-r from-emerald-500 to-emerald-600 px-6 py-4 flex items-center justify-between">
                <h3 class="text-lg font-bold text-white">Reset Password</h3>
                <button onclick="closeResetPasswordModal()" class="text-white/80 hover:text-white transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form id="resetPasswordForm" class="p-6 space-y-4">
                <input type="hidden" id="resetPasswordIds" name="ids">
                <div class="bg-amber-50 border border-amber-100 rounded-lg p-3 text-sm text-amber-700" id="resetPasswordMessage">
                    Enter a new password for the selected customer(s).
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
                    <input type="password" name="password" required minlength="8" class="w-full px-4 py-2.5 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none" placeholder="Minimum 8 characters">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                    <input type="password" name="password_confirmation" required minlength="8" class="w-full px-4 py-2.5 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none" placeholder="Repeat password">
                </div>
                <div class="flex items-center gap-3 pt-2">
                    <button type="button" onclick="closeResetPasswordModal()" class="flex-1 px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-semibold rounded-lg transition-colors">Cancel</button>
                    <button type="submit" class="flex-1 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg transition-colors">Reset Password</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Bulk Upload Modal --}}
<div id="bulkUploadModal" class="fixed inset-0 z-50 hidden" role="dialog" aria-modal="true">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm transition-opacity" onclick="closeBulkUploadModal()"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden transform transition-all">
            <div class="bg-gradient-to-r from-emerald-500 to-emerald-600 px-6 py-4 flex items-center justify-between">
                <h3 class="text-lg font-bold text-white">Import Customers</h3>
                <button onclick="closeBulkUploadModal()" class="text-white/80 hover:text-white transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form id="bulkUploadForm" class="p-6 space-y-4">
                <div class="bg-blue-50 border border-blue-100 rounded-lg p-4 text-sm text-blue-700 space-y-2">
                    <p class="font-semibold">CSV Format Requirements:</p>
                    <ul class="list-disc list-inside text-xs space-y-1">
                        <li>Columns must be: <code class="bg-blue-100 px-1 rounded">name</code>, <code class="bg-blue-100 px-1 rounded">email</code>, <code class="bg-blue-100 px-1 rounded">phone_number</code>, <code class="bg-blue-100 px-1 rounded">password</code></li>
                        <li>First row must be the header</li>
                        <li>Email and phone must be unique</li>
                        <li>Password must be at least 8 characters</li>
                        <li>Max file size: 2MB</li>
                    </ul>
                    <a href="data:text/csv;charset=utf-8,name,email,phone_number,password\nJohn Doe,john@example.com,0712345678,password123" download="customers_template.csv" class="inline-flex items-center gap-1 text-xs font-semibold text-emerald-600 hover:text-emerald-700 mt-2">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        Download template
                    </a>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">CSV File</label>
                    <div class="relative border-2 border-dashed border-emerald-200 rounded-lg p-4 hover:border-emerald-500 hover:bg-emerald-50/30 transition-all bg-emerald-50/20">
                        <input type="file" id="csvFileInput" name="csv_file" accept=".csv,text/csv" required class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                        <div class="text-center pointer-events-none">
                            <svg class="w-8 h-8 mx-auto text-emerald-500 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6M5 7h14M5 7a2 2 0 00-2 2v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2M5 7V5a2 2 0 012-2h10a2 2 0 012 2v2"/></svg>
                            <p class="text-sm text-gray-600 font-medium" id="csvFileName">Click or drop CSV here</p>
                        </div>
                    </div>
                </div>
                <div id="uploadResult" class="hidden rounded-lg p-3 text-sm"></div>
                <div class="flex items-center gap-3 pt-2">
                    <button type="button" onclick="closeBulkUploadModal()" class="flex-1 px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-semibold rounded-lg transition-colors">Cancel</button>
                    <button type="submit" id="uploadBtn" class="flex-1 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg transition-colors flex items-center justify-center gap-2">
                        <span id="uploadBtnText">Import Customers</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    const customersTable = document.getElementById('customersTable').querySelector('tbody');
    const customerSearch = document.getElementById('customerSearch');
    const customersCount = document.getElementById('customersCount');
    const bulkActionsBar = document.getElementById('bulkActionsBar');
    const selectAllCheckbox = document.getElementById('selectAllCheckbox');
    const selectAllHeader = document.getElementById('selectAllHeader');
    const selectedCount = document.getElementById('selectedCount');
    let searchTimeout;
    let resetPasswordMode = 'single'; // 'single' or 'bulk'
    let singleResetId = null;

    function showToast(type, message) {
        const Toast = Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 4000, timerProgressBar: true });
        Toast.fire({ icon: type, title: message });
    }

    function updateCustomersCount() {
        const total = customersTable.querySelectorAll('tr[data-id]').length;
        customersCount.textContent = total + ' customer' + (total !== 1 ? 's' : '');
    }

    function getSelectedIds() {
        return Array.from(customersTable.querySelectorAll('.customer-checkbox:checked')).map(cb => parseInt(cb.value));
    }

    function updateSelectionUI() {
        const ids = getSelectedIds();
        const total = customersTable.querySelectorAll('tr[data-id]').length;
        selectedCount.textContent = ids.length;
        bulkActionsBar.classList.toggle('hidden', ids.length === 0);
        selectAllCheckbox.checked = total > 0 && ids.length === total;
        selectAllHeader.checked = total > 0 && ids.length === total;
    }

    function toggleSelectAll(checked) {
        customersTable.querySelectorAll('.customer-checkbox').forEach(cb => cb.checked = checked);
        selectAllCheckbox.checked = checked;
        selectAllHeader.checked = checked;
        updateSelectionUI();
    }

    selectAllCheckbox.addEventListener('change', function() { toggleSelectAll(this.checked); });
    selectAllHeader.addEventListener('change', function() { toggleSelectAll(this.checked); });

    customersTable.addEventListener('change', function(e) {
        if (e.target.classList.contains('customer-checkbox')) {
            updateSelectionUI();
        }
    });

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
            updateSelectionUI();
            updateCustomersCount();
            return;
        }
        customers.forEach((customer, index) => {
            const row = document.createElement('tr');
            row.className = 'border-t border-gray-100 transition-colors animate-fade';
            row.style.animationDelay = `${index * 0.05}s`;
            row.setAttribute('data-id', customer.id);
            row.innerHTML = `
                <td class="px-4 py-3"><input type="checkbox" value="${customer.id}" class="customer-checkbox w-4 h-4 text-emerald-600 rounded border-gray-300 focus:ring-emerald-500"></td>
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
                    <div class="flex items-center justify-end gap-1">
                        <button onclick="viewCustomerDetails(${customer.id})" class="p-1.5 text-emerald-600 hover:bg-emerald-50 rounded-lg transition-colors" title="View">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        </button>
                        <button onclick="resetSinglePassword(${customer.id}, '${escapeHtml(customer.name)}')" class="p-1.5 text-amber-600 hover:bg-amber-50 rounded-lg transition-colors" title="Reset Password">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                        </button>
                        <button onclick="deleteSingleCustomer(${customer.id}, '${escapeHtml(customer.name)}')" class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Delete">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </div>
                </td>
            `;
            customersTable.appendChild(row);
        });
        updateSelectionUI();
        updateCustomersCount();
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML.replace(/'/g, "\\'");
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

    function resetSinglePassword(id, name) {
        resetPasswordMode = 'single';
        singleResetId = id;
        document.getElementById('resetPasswordIds').value = id;
        document.getElementById('resetPasswordMessage').textContent = `Enter a new password for ${name}.`;
        document.getElementById('resetPasswordModal').classList.remove('hidden');
    }

    function bulkResetPassword() {
        const ids = getSelectedIds();
        if (ids.length === 0) return;
        resetPasswordMode = 'bulk';
        singleResetId = null;
        document.getElementById('resetPasswordIds').value = JSON.stringify(ids);
        document.getElementById('resetPasswordMessage').textContent = `Enter a new password for ${ids.length} selected customer(s).`;
        document.getElementById('resetPasswordModal').classList.remove('hidden');
    }

    function closeResetPasswordModal() {
        document.getElementById('resetPasswordModal').classList.add('hidden');
        document.getElementById('resetPasswordForm').reset();
        resetPasswordMode = 'single';
        singleResetId = null;
    }

    document.getElementById('resetPasswordForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const password = formData.get('password');
        const confirmation = formData.get('password_confirmation');
        const submitBtn = this.querySelector('button[type="submit"]');

        if (password !== confirmation) {
            showToast('error', 'Passwords do not match');
            return;
        }
        if (password.length < 8) {
            showToast('error', 'Password must be at least 8 characters');
            return;
        }

        let ids = [];
        const idsValue = document.getElementById('resetPasswordIds').value;
        try {
            ids = JSON.parse(idsValue);
        } catch (e) {
            ids = [parseInt(idsValue)];
        }

        const originalBtnText = submitBtn.textContent;
        submitBtn.disabled = true;
        submitBtn.textContent = 'Resetting...';

        const url = ids.length === 1
            ? `{{ route('admin.sales.customers') }}/${ids[0]}/reset-password`
            : `{{ route('admin.sales.customers.bulk-reset-password') }}`;

        fetch(url, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ ids, password, password_confirmation: confirmation })
        })
        .then(async response => {
            const data = await response.json().catch(() => ({}));
            if (!response.ok) throw new Error(data.message || 'Reset failed');
            return data;
        })
        .then(result => {
            showToast('success', result.message || 'Password reset successfully');
            closeResetPasswordModal();
        })
        .catch(error => {
            showToast('error', error.message || 'Failed to reset password');
        })
        .finally(() => {
            submitBtn.disabled = false;
            submitBtn.textContent = originalBtnText;
        });
    });

    function deleteSingleCustomer(id, name) {
        Swal.fire({
            title: 'Delete Customer?',
            text: `Are you sure you want to delete ${name}?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, delete!',
            showLoaderOnConfirm: true,
            preConfirm: () => {
                return fetch(`{{ route('admin.sales.customers') }}/${id}`, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
                })
                .then(async response => {
                    const data = await response.json().catch(() => ({}));
                    if (!response.ok) throw new Error(data.message || 'Delete failed');
                    return data;
                })
                .catch(error => { Swal.showValidationMessage(error.message); });
            }
        }).then((result) => {
            if (result.isConfirmed && result.value?.success) {
                showToast('success', result.value.message);
                const row = document.querySelector(`tr[data-id="${id}"]`);
                if (row) { row.remove(); updateSelectionUI(); updateCustomersCount(); checkEmptyTable(); }
            }
        });
    }

    function bulkDeleteCustomers() {
        const ids = getSelectedIds();
        if (ids.length === 0) return;
        Swal.fire({
            title: 'Delete Selected Customers?',
            text: `This will permanently delete ${ids.length} customer(s).`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, delete all!',
            showLoaderOnConfirm: true,
            preConfirm: () => {
                return fetch(`{{ route('admin.sales.customers') }}/bulk`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ ids })
                })
                .then(async response => {
                    const data = await response.json().catch(() => ({}));
                    if (!response.ok) throw new Error(data.message || 'Delete failed');
                    return data;
                })
                .catch(error => { Swal.showValidationMessage(error.message); });
            }
        }).then((result) => {
            if (result.isConfirmed && result.value?.success) {
                showToast('success', result.value.message);
                ids.forEach(id => {
                    const row = document.querySelector(`tr[data-id="${id}"]`);
                    if (row) row.remove();
                });
                updateSelectionUI();
                updateCustomersCount();
                checkEmptyTable();
            }
        });
    }

    function checkEmptyTable() {
        if (customersTable.querySelectorAll('tr[data-id]').length === 0 && !document.getElementById('emptyRow')) {
            customersTable.innerHTML = '<tr id="emptyRow"><td colspan="7" class="px-6 py-12 text-center text-gray-400 text-sm">No customers found.</td></tr>';
        }
    }

    function openBulkUploadModal() {
        document.getElementById('bulkUploadModal').classList.remove('hidden');
        document.getElementById('uploadResult').classList.add('hidden');
        document.getElementById('bulkUploadForm').reset();
        document.getElementById('csvFileName').textContent = 'Click or drop CSV here';
    }

    function closeBulkUploadModal() {
        document.getElementById('bulkUploadModal').classList.add('hidden');
        document.getElementById('uploadResult').classList.add('hidden');
        document.getElementById('bulkUploadForm').reset();
        document.getElementById('csvFileName').textContent = 'Click or drop CSV here';
    }

    document.getElementById('csvFileInput').addEventListener('change', function() {
        document.getElementById('csvFileName').textContent = this.files && this.files[0] ? this.files[0].name : 'Click or drop CSV here';
    });

    document.getElementById('bulkUploadForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const btn = document.getElementById('uploadBtn');
        const btnText = document.getElementById('uploadBtnText');
        const resultBox = document.getElementById('uploadResult');
        btn.disabled = true;
        btnText.textContent = 'Importing...';

        const formData = new FormData(this);
        fetch(`{{ route('admin.sales.customers.bulk-upload') }}`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
            body: formData
        })
        .then(async response => {
            const data = await response.json().catch(() => ({}));
            if (!response.ok) {
                throw new Error(data.message || 'Import failed');
            }
            return data;
        })
        .then(result => {
            resultBox.classList.remove('hidden', 'bg-red-50', 'text-red-700', 'bg-emerald-50', 'text-emerald-700');
            if (result.errors && result.errors.length) {
                resultBox.classList.add('bg-amber-50', 'text-amber-700');
                resultBox.innerHTML = `<p class="font-semibold">${result.message}</p><ul class="list-disc list-inside mt-1 text-xs">${result.errors.map(e => `<li>${e}</li>`).join('')}</ul>`;
                showToast('warning', result.message);
            } else {
                resultBox.classList.add('bg-emerald-50', 'text-emerald-700');
                resultBox.innerHTML = `<p class="font-semibold">${result.message}</p>`;
                showToast('success', result.message);
                loadCustomers();
                setTimeout(closeBulkUploadModal, 1500);
            }
        })
        .catch(error => {
            resultBox.classList.remove('hidden', 'bg-emerald-50', 'text-emerald-700', 'bg-amber-50', 'text-amber-700');
            resultBox.classList.add('bg-red-50', 'text-red-700');
            resultBox.textContent = error.message || 'Import failed. Please try again.';
            showToast('error', error.message || 'Import failed');
        })
        .finally(() => {
            btn.disabled = false;
            btnText.textContent = 'Import Customers';
        });
    });

    customerSearch.addEventListener('input', function() { clearTimeout(searchTimeout); searchTimeout = setTimeout(loadCustomers, 400); });
</script>
@endsection
