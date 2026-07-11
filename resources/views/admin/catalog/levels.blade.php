@extends('layouts.dashboard')

@section('title', 'Levels - ' . config('app.name', 'Elimu Store'))
@section('page_title', 'Manage Education Levels')

@section('content')
<div class="space-y-6">
    {{-- Header Card --}}
    <div class="hover-lift dashboard-card bg-white rounded-xl border border-gray-100 p-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="text-lg font-bold text-gray-800">Education Levels</h2>
                <p class="text-sm text-gray-500 mt-1">Manage school levels like Primary, Secondary, Advanced, etc.</p>
            </div>
            <button onclick="openLevelDrawer()" class="dashboard-btn inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg shadow-md hover:shadow-lg transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Add New Level
            </button>
        </div>
    </div>

    {{-- Table --}}
    <div class="hover-lift bg-white rounded-xl border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-sm font-semibold text-gray-900 section-header">All Levels</h3>
            <div class="relative">
                <input type="text" id="levelSearch" placeholder="Search levels..." class="pl-9 pr-4 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none">
                <svg class="w-4 h-4 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="dashboard-table w-full text-sm" id="levelsTable">
                <thead><tr class="text-left text-xs text-gray-500 bg-gray-50/50">
                    <th class="px-6 py-3 font-medium">#</th>
                    <th class="px-6 py-3 font-medium">Level Name</th>
                    <th class="px-6 py-3 font-medium">Description</th>
                    <th class="px-6 py-3 font-medium">Order</th>
                    <th class="px-6 py-3 font-medium">Status</th>
                    <th class="px-6 py-3 font-medium text-right">Actions</th>
                </tr></thead>
                <tbody>
                    @forelse($levels as $index => $level)
                    <tr class="border-t border-gray-100 transition-colors animate-fade" data-id="{{ $level->id }}" data-name="{{ strtolower($level->name) }}" style="animation-delay: {{ $index * 0.05 }}s">
                        <td class="px-6 py-3 text-xs text-gray-500">{{ $index + 1 }}</td>
                        <td class="px-6 py-3">
                            <p class="text-sm font-semibold text-gray-900">{{ $level->name }}</p>
                        </td>
                        <td class="px-6 py-3">
                            <p class="text-xs text-gray-500 max-w-xs truncate">{{ $level->description ?? 'No description' }}</p>
                        </td>
                        <td class="px-6 py-3">
                            <span class="inline-flex items-center px-2 py-1 rounded-md bg-gray-100 text-xs font-medium text-gray-700">{{ $level->order }}</span>
                        </td>
                        <td class="px-6 py-3">
                            <button onclick="toggleLevelStatus({{ $level->id }})" class="status-badge inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-medium border {{ $level->is_active ? 'bg-emerald-50 text-emerald-700 border-emerald-100' : 'bg-red-50 text-red-700 border-red-100' }}">
                                {{ $level->is_active ? 'Active' : 'Inactive' }}
                            </button>
                        </td>
                        <td class="px-6 py-3 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <button onclick="editLevel({{ $level->id }}, '{{ addslashes($level->name) }}', '{{ addslashes($level->description ?? '') }}', {{ $level->order }}, {{ $level->is_active ? 1 : 0 }})" class="p-1.5 text-emerald-600 hover:bg-emerald-50 rounded-lg transition-colors" title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.433-4.333A2.001 2.001 0 0119 10a2.001 2.001 0 01-.433 1.333L12.5 17.5l-4 1 1-4 6.067-6.167z"/></svg>
                                </button>
                                <button onclick="deleteLevel({{ $level->id }})" class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Delete">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr id="emptyRow"><td colspan="6" class="px-6 py-12 text-center text-gray-400 text-sm">No levels found. Click "Add New Level" to create one.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Level Sidebar Drawer --}}
<div id="levelDrawer" class="drawer-overlay" aria-labelledby="drawer-title" role="dialog" aria-modal="true" onclick="closeLevelDrawer()">
    <div class="drawer-sidebar" onclick="event.stopPropagation()">
        <div class="drawer-header">
            <button type="button" onclick="closeLevelDrawer()" class="drawer-close">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
            <h3 class="text-xl font-bold text-white relative z-10" id="drawerTitle">Add New Level</h3>
            <p class="text-sm text-emerald-100 mt-1 relative z-10">Fill in the details below</p>
        </div>

        <form id="levelForm" class="drawer-body">
            @csrf
            <input type="hidden" id="levelId" name="level_id" value="">

            <div class="drawer-form-group">
                <label for="levelName">Level Name</label>
                <input type="text" id="levelName" name="name" required placeholder="e.g. Primary School">
            </div>

            <div class="drawer-form-group">
                <label for="levelDescription">Description</label>
                <textarea id="levelDescription" name="description" rows="4" placeholder="Short description about this level..."></textarea>
            </div>

            <div class="drawer-form-group">
                <label for="levelOrder">Display Order</label>
                <input type="number" id="levelOrder" name="order" min="0" value="0" required>
            </div>

            <div class="drawer-form-group">
                <label class="flex items-center gap-3 p-3 rounded-lg border border-gray-200 cursor-pointer hover:bg-gray-50 transition-colors">
                    <input type="checkbox" id="levelActive" name="is_active" value="1" checked class="w-5 h-5 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                    <div>
                        <span class="text-sm font-semibold text-gray-700 block">Active Level</span>
                        <span class="text-xs text-gray-500">Make this level visible to users</span>
                    </div>
                </label>
            </div>
        </form>

        <div class="drawer-footer">
            <button type="button" onclick="closeLevelDrawer()" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-semibold rounded-lg transition-colors">Cancel</button>
            <button type="submit" form="levelForm" id="saveBtn" class="dashboard-btn inline-flex items-center gap-2 px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg shadow-lg hover:shadow-xl transition-all">
                <svg id="saveSpinner" class="w-4 h-4 animate-spin hidden" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                <span id="saveText">Save Level</span>
            </button>
        </div>
    </div>
</div>

<script>
    const levelsTable = document.getElementById('levelsTable').querySelector('tbody');
    const levelDrawer = document.getElementById('levelDrawer');
    const levelSidebar = levelDrawer.querySelector('.drawer-sidebar');
    const levelForm = document.getElementById('levelForm');
    const drawerTitle = document.getElementById('drawerTitle');
    const saveBtn = document.getElementById('saveBtn');
    const saveSpinner = document.getElementById('saveSpinner');
    const saveText = document.getElementById('saveText');

    let editingId = null;

    function openLevelDrawer() {
        editingId = null;
        drawerTitle.textContent = 'Add New Level';
        document.getElementById('levelId').value = '';
        document.getElementById('levelName').value = '';
        document.getElementById('levelDescription').value = '';
        document.getElementById('levelOrder').value = 0;
        document.getElementById('levelActive').checked = true;
        document.getElementById('saveText').textContent = 'Save Level';
        levelDrawer.classList.add('open');
        levelSidebar.classList.add('open');
        document.body.style.overflow = 'hidden';
    }

    function closeLevelDrawer() {
        levelDrawer.classList.remove('open');
        levelSidebar.classList.remove('open');
        document.body.style.overflow = '';
        editingId = null;
    }

    function editLevel(id, name, description, order, isActive) {
        editingId = id;
        drawerTitle.textContent = 'Edit Level';
        document.getElementById('levelId').value = id;
        document.getElementById('levelName').value = name;
        document.getElementById('levelDescription').value = description;
        document.getElementById('levelOrder').value = order;
        document.getElementById('levelActive').checked = isActive === 1;
        document.getElementById('saveText').textContent = 'Update Level';
        levelDrawer.classList.add('open');
        levelSidebar.classList.add('open');
        document.body.style.overflow = 'hidden';
    }

    function setLoading(loading) {
        saveBtn.disabled = loading;
        saveSpinner.classList.toggle('hidden', !loading);
        saveText.textContent = loading ? 'Saving...' : (editingId ? 'Update Level' : 'Save Level');
    }

    function showToast(type, message) {
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 4000,
            timerProgressBar: true
        });
        Toast.fire({ icon: type, title: message });
    }

    function addLevelToTable(level, index) {
        const emptyRow = document.getElementById('emptyRow');
        if (emptyRow) emptyRow.remove();

        const activeClass = level.is_active ? 'bg-emerald-50 text-emerald-700 border-emerald-100' : 'bg-red-50 text-red-700 border-red-100';
        const activeText = level.is_active ? 'Active' : 'Inactive';
        const description = level.description ? level.description.replace(/'/g, "\\'") : '';

        const row = document.createElement('tr');
        row.className = 'border-t border-gray-100 transition-colors';
        row.setAttribute('data-id', level.id);
        row.setAttribute('data-name', level.name.toLowerCase());
        row.innerHTML = `
            <td class="px-6 py-3 text-xs text-gray-500">${index}</td>
            <td class="px-6 py-3"><p class="text-sm font-semibold text-gray-900">${level.name}</p></td>
            <td class="px-6 py-3"><p class="text-xs text-gray-500 max-w-xs truncate">${level.description || 'No description'}</p></td>
            <td class="px-6 py-3"><span class="inline-flex items-center px-2 py-1 rounded-md bg-gray-100 text-xs font-medium text-gray-700">${level.order}</span></td>
            <td class="px-6 py-3">
                <button onclick="toggleLevelStatus(${level.id})" class="status-badge inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-medium border ${activeClass}">${activeText}</button>
            </td>
            <td class="px-6 py-3 text-right">
                <div class="flex items-center justify-end gap-2">
                    <button onclick="editLevel(${level.id}, '${level.name.replace(/'/g, "\\'")}', '${description}', ${level.order}, ${level.is_active ? 1 : 0})" class="p-1.5 text-emerald-600 hover:bg-emerald-50 rounded-lg transition-colors" title="Edit">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.433-4.333A2.001 2.001 0 0119 10a2.001 2.001 0 01-.433 1.333L12.5 17.5l-4 1 1-4 6.067-6.167z"/></svg>
                    </button>
                    <button onclick="deleteLevel(${level.id})" class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Delete">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </button>
                </div>
            </td>
        `;

        const existingRow = document.querySelector(`tr[data-id="${level.id}"]`);
        if (existingRow) {
            existingRow.replaceWith(row);
        } else {
            levelsTable.appendChild(row);
        }
        updateRowNumbers();
    }

    function updateRowNumbers() {
        const rows = levelsTable.querySelectorAll('tr[data-id]');
        rows.forEach((row, index) => {
            row.querySelector('td:first-child').textContent = index + 1;
        });
        if (rows.length === 0 && !document.getElementById('emptyRow')) {
            levelsTable.innerHTML = '<tr id="emptyRow"><td colspan="6" class="px-6 py-12 text-center text-gray-400 text-sm">No levels found. Click "Add New Level" to create one.</td></tr>';
        }
    }

    levelForm.addEventListener('submit', function(e) {
        e.preventDefault();
        setLoading(true);

        const formData = new FormData(levelForm);
        const data = {
            name: formData.get('name'),
            description: formData.get('description'),
            order: parseInt(formData.get('order')) || 0,
            is_active: formData.get('is_active') ? 1 : 0,
            _token: formData.get('_token')
        };

        const url = editingId
            ? `{{ url('admin/catalog/levels') }}/${editingId}`
            : '{{ route('admin.catalog.levels.store') }}';
        const method = editingId ? 'PUT' : 'POST';

        fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': data._token,
                'Accept': 'application/json'
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(result => {
            setLoading(false);
            if (result.success) {
                showToast('success', result.message);
                addLevelToTable(result.level);
                closeLevelDrawer();
            } else {
                showToast('error', result.message || 'Something went wrong');
            }
        })
        .catch(error => {
            setLoading(false);
            showToast('error', 'Failed to save level. Please try again.');
            console.error(error);
        });
    });

    function deleteLevel(id) {
        Swal.fire({
            title: 'Delete Level?',
            text: 'This action cannot be undone. Classes under this level may be affected.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`{{ url('admin/catalog/levels') }}/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(result => {
                    if (result.success) {
                        showToast('success', result.message);
                        const row = document.querySelector(`tr[data-id="${id}"]`);
                        if (row) {
                            row.style.opacity = '0';
                            row.style.transform = 'translateX(20px)';
                            setTimeout(() => {
                                row.remove();
                                updateRowNumbers();
                            }, 300);
                        }
                    } else {
                        showToast('error', result.message || 'Failed to delete');
                    }
                })
                .catch(error => {
                    showToast('error', 'Failed to delete level');
                    console.error(error);
                });
            }
        });
    }

    function toggleLevelStatus(id) {
        const row = document.querySelector(`tr[data-id="${id}"]`);
        const badge = row.querySelector('.status-badge');
        const isActive = badge.textContent.trim() === 'Active';

        fetch(`{{ url('admin/catalog/levels') }}/${id}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                name: row.querySelector('td:nth-child(2) p').textContent,
                description: row.querySelector('td:nth-child(3) p').textContent === 'No description' ? '' : row.querySelector('td:nth-child(3) p').textContent,
                order: parseInt(row.querySelector('td:nth-child(4) span').textContent),
                is_active: isActive ? 0 : 1
            })
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                showToast('success', `Level ${isActive ? 'deactivated' : 'activated'}`);
                const newActive = !isActive;
                badge.className = `status-badge inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-medium border ${newActive ? 'bg-emerald-50 text-emerald-700 border-emerald-100' : 'bg-red-50 text-red-700 border-red-100'}`;
                badge.textContent = newActive ? 'Active' : 'Inactive';
            } else {
                showToast('error', result.message || 'Failed to update status');
            }
        })
        .catch(error => {
            showToast('error', 'Failed to update status');
            console.error(error);
        });
    }

    // AJAX Search with debounce
    let searchTimeout;
    const levelSearch = document.getElementById('levelSearch');
    const searchIcon = levelSearch.parentElement.querySelector('svg');

    levelSearch.addEventListener('input', function(e) {
        const term = e.target.value.trim();

        clearTimeout(searchTimeout);

        // Show loading state
        searchIcon.classList.add('animate-spin');
        searchIcon.innerHTML = '<circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>';

        searchTimeout = setTimeout(function() {
            const url = new URL('{{ route('admin.catalog.levels') }}', window.location.origin);
            if (term) url.searchParams.append('search', term);

            fetch(url, {
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    renderLevelsTable(result.levels);
                } else {
                    showToast('error', 'Failed to search levels');
                }
            })
            .catch(error => {
                showToast('error', 'Search error occurred');
                console.error(error);
            })
            .finally(() => {
                searchIcon.classList.remove('animate-spin');
                searchIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>';
            });
        }, 400);
    });

    function renderLevelsTable(levels) {
        levelsTable.innerHTML = '';

        if (levels.length === 0) {
            levelsTable.innerHTML = '<tr id="emptyRow"><td colspan="6" class="px-6 py-12 text-center text-gray-400 text-sm">No levels found. Click "Add New Level" to create one.</td></tr>';
            return;
        }

        levels.forEach((level, index) => {
            const activeClass = level.is_active ? 'bg-emerald-50 text-emerald-700 border-emerald-100' : 'bg-red-50 text-red-700 border-red-100';
            const activeText = level.is_active ? 'Active' : 'Inactive';
            const description = level.description ? level.description.replace(/'/g, "\\'") : '';
            const displayDescription = level.description || 'No description';

            const row = document.createElement('tr');
            row.className = 'border-t border-gray-100 transition-colors animate-fade';
            row.style.animationDelay = `${index * 0.05}s`;
            row.setAttribute('data-id', level.id);
            row.setAttribute('data-name', level.name.toLowerCase());
            row.innerHTML = `
                <td class="px-6 py-3 text-xs text-gray-500">${index + 1}</td>
                <td class="px-6 py-3"><p class="text-sm font-semibold text-gray-900">${level.name}</p></td>
                <td class="px-6 py-3"><p class="text-xs text-gray-500 max-w-xs truncate">${displayDescription}</p></td>
                <td class="px-6 py-3"><span class="inline-flex items-center px-2 py-1 rounded-md bg-gray-100 text-xs font-medium text-gray-700">${level.order}</span></td>
                <td class="px-6 py-3">
                    <button onclick="toggleLevelStatus(${level.id})" class="status-badge inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-medium border ${activeClass}">${activeText}</button>
                </td>
                <td class="px-6 py-3 text-right">
                    <div class="flex items-center justify-end gap-2">
                        <button onclick="editLevel(${level.id}, '${level.name.replace(/'/g, "\\'")}', '${description}', ${level.order}, ${level.is_active ? 1 : 0})" class="p-1.5 text-emerald-600 hover:bg-emerald-50 rounded-lg transition-colors" title="Edit">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.433-4.333A2.001 2.001 0 0119 10a2.001 2.001 0 01-.433 1.333L12.5 17.5l-4 1 1-4 6.067-6.167z"/></svg>
                        </button>
                        <button onclick="deleteLevel(${level.id})" class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Delete">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </div>
                </td>
            `;
            levelsTable.appendChild(row);
        });
    }

    // Close drawer on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeLevelDrawer();
    });
</script>
@endsection
