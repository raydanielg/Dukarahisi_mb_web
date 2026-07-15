@extends('layouts.dashboard')

@section('title', 'Classes - ' . config('app.name', 'Elimu Store'))
@section('page_title', 'Manage Classes')

@section('content')
@php
    $levelsForSub = [];
    if ($withSubLevels ?? true) {
        foreach ($levels as $l) {
            $levelsForSub[$l->id] = $l->relationLoaded('subLevels') ? $l->subLevels->map(fn($s) => ['id' => $s->id, 'name' => $s->name]) : [];
        }
    }
@endphp
<div class="space-y-6">
    {{-- Header Card --}}
    <div class="hover-lift dashboard-card bg-white rounded-xl border border-gray-100 p-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="text-lg font-bold text-gray-800">Classes</h2>
                <p class="text-sm text-gray-500 mt-1">Manage classes under each education level</p>
            </div>
            <button onclick="openClassDrawer()" class="dashboard-btn inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg shadow-md hover:shadow-lg transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Add New Class
            </button>
        </div>
    </div>

    {{-- Filters --}}
    <div class="hover-lift dashboard-card bg-white rounded-xl border border-gray-100 p-4">
        <div class="flex flex-col sm:flex-row gap-3">
            <div class="relative flex-1">
                <input type="text" id="classSearch" placeholder="Search classes..." class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none">
                <svg class="w-4 h-4 text-gray-400 absolute left-3.5 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
            <select id="levelFilter" class="pl-4 pr-10 py-2.5 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none bg-white min-w-[200px]">
                <option value="">All Levels</option>
                @foreach($levels as $level)
                    <option value="{{ $level->id }}">{{ $level->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- Table --}}
    <div class="hover-lift bg-white rounded-xl border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-sm font-semibold text-gray-900 section-header">All Classes</h3>
            <span class="text-xs text-gray-500" id="classesCount">{{ $classes->count() }} classes</span>
        </div>
        <div class="overflow-x-auto">
            <table class="dashboard-table w-full text-sm" id="classesTable">
                <thead><tr class="text-left text-xs text-gray-500 bg-gray-50/50">
                    <th class="px-6 py-3 font-medium">#</th>
                    <th class="px-6 py-3 font-medium">Class Name</th>
                    <th class="px-6 py-3 font-medium">Level</th>
                    <th class="px-6 py-3 font-medium">Sub-Level</th>
                    <th class="px-6 py-3 font-medium">Description</th>
                    <th class="px-6 py-3 font-medium">Order</th>
                    <th class="px-6 py-3 font-medium">Status</th>
                    <th class="px-6 py-3 font-medium text-right">Actions</th>
                </tr></thead>
                <tbody>
                    @forelse($classes as $index => $classRoom)
                    <tr class="border-t border-gray-100 transition-colors animate-fade" data-id="{{ $classRoom->id }}" data-level-id="{{ $classRoom->level_id }}" data-sub-level-id="{{ $classRoom->sub_level_id ?? '' }}" data-name="{{ strtolower($classRoom->name) }}" style="animation-delay: {{ $index * 0.05 }}s">
                        <td class="px-6 py-3 text-xs text-gray-500">{{ $index + 1 }}</td>
                        <td class="px-6 py-3">
                            <p class="text-sm font-semibold text-gray-900">{{ $classRoom->name }}</p>
                        </td>
                        <td class="px-6 py-3">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-medium bg-emerald-50 text-emerald-700 border border-emerald-100">{{ $classRoom->level->name }}</span>
                        </td>
                        <td class="px-6 py-3">
                            @if(($withSubLevels ?? true) && $classRoom->relationLoaded('subLevel') && $classRoom->subLevel)
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-medium bg-blue-50 text-blue-700 border border-blue-100">{{ $classRoom->subLevel->name }}</span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-medium bg-gray-50 text-gray-500 border border-gray-100">None</span>
                            @endif
                        </td>
                        <td class="px-6 py-3">
                            <p class="text-xs text-gray-500 max-w-xs truncate">{{ $classRoom->description ?? 'No description' }}</p>
                        </td>
                        <td class="px-6 py-3">
                            <span class="inline-flex items-center px-2 py-1 rounded-md bg-gray-100 text-xs font-medium text-gray-700">{{ $classRoom->order }}</span>
                        </td>
                        <td class="px-6 py-3">
                            <button onclick="toggleClassStatus({{ $classRoom->id }})" class="status-badge inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-medium border {{ $classRoom->is_active ? 'bg-emerald-50 text-emerald-700 border-emerald-100' : 'bg-red-50 text-red-700 border-red-100' }}">
                                {{ $classRoom->is_active ? 'Active' : 'Inactive' }}
                            </button>
                        </td>
                        <td class="px-6 py-3 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <button onclick="editClass({{ $classRoom->id }}, {{ $classRoom->level_id }}, '{{ addslashes($classRoom->name) }}', '{{ addslashes($classRoom->description ?? '') }}', '{{ $classRoom->sub_level_id ?? '' }}', {{ $classRoom->order }}, {{ $classRoom->is_active ? 1 : 0 }})" class="p-1.5 text-emerald-600 hover:bg-emerald-50 rounded-lg transition-colors" title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.433-4.333A2.001 2.001 0 0119 10a2.001 2.001 0 01-.433 1.333L12.5 17.5l-4 1 1-4 6.067-6.167z"/></svg>
                                </button>
                                <button onclick="deleteClass({{ $classRoom->id }})" class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Delete">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr id="emptyRow"><td colspan="8" class="px-6 py-12 text-center text-gray-400 text-sm">No classes found. Click "Add New Class" to create one.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Class Sidebar Drawer --}}
<div id="classDrawer" class="drawer-overlay" aria-labelledby="drawer-title" role="dialog" aria-modal="true" onclick="closeClassDrawer()">
    <div class="drawer-sidebar" onclick="event.stopPropagation()">
        <div class="drawer-header">
            <button type="button" onclick="closeClassDrawer()" class="drawer-close">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
            <h3 class="text-xl font-bold text-white relative z-10" id="drawerTitle">Add New Class</h3>
            <p class="text-sm text-emerald-100 mt-1 relative z-10">Fill in the details below</p>
        </div>

        <form id="classForm" class="drawer-body">
            @csrf
            <input type="hidden" id="classId" name="class_id" value="">

            <div class="drawer-form-group">
                <label for="classLevel">Education Level</label>
                <select id="classLevel" name="level_id" required>
                    <option value="">Select a level</option>
                    @foreach($levels as $level)
                        <option value="{{ $level->id }}">{{ $level->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="drawer-form-group">
                <label for="className">Class Name</label>
                <input type="text" id="className" name="name" required placeholder="e.g. Standard 7">
            </div>

            <div class="drawer-form-group" id="subLevelFormGroup" style="display:none;">
                <label for="classSubLevel">Sub-Level <span class="text-gray-400 text-xs">(optional - only for levels with sub-levels)</span></label>
                <select id="classSubLevel" name="sub_level_id">
                    <option value="">No sub-level</option>
                </select>
            </div>

            <div class="drawer-form-group">
                <label for="classDescription">Description</label>
                <textarea id="classDescription" name="description" rows="4" placeholder="Short description about this class..."></textarea>
            </div>

            <div class="drawer-form-group">
                <label for="classOrder">Display Order</label>
                <input type="number" id="classOrder" name="order" min="0" value="0" required>
            </div>

            <div class="drawer-form-group">
                <label class="flex items-center gap-3 p-3 rounded-lg border border-gray-200 cursor-pointer hover:bg-gray-50 transition-colors">
                    <input type="checkbox" id="classActive" name="is_active" value="1" checked class="w-5 h-5 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                    <div>
                        <span class="text-sm font-semibold text-gray-700 block">Active Class</span>
                        <span class="text-xs text-gray-500">Make this class visible to users</span>
                    </div>
                </label>
            </div>
        </form>

        <div class="drawer-footer">
            <button type="button" onclick="closeClassDrawer()" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-semibold rounded-lg transition-colors">Cancel</button>
            <button type="submit" form="classForm" id="saveBtn" class="dashboard-btn inline-flex items-center gap-2 px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg shadow-lg hover:shadow-xl transition-all">
                <svg id="saveSpinner" class="w-4 h-4 animate-spin hidden" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                <span id="saveText">Save Class</span>
            </button>
        </div>
    </div>
</div>

<script>
    const classesTable = document.getElementById('classesTable').querySelector('tbody');
    const classDrawer = document.getElementById('classDrawer');
    const classSidebar = classDrawer.querySelector('.drawer-sidebar');
    const classForm = document.getElementById('classForm');
    const drawerTitle = document.getElementById('drawerTitle');
    const saveBtn = document.getElementById('saveBtn');
    const saveSpinner = document.getElementById('saveSpinner');
    const saveText = document.getElementById('saveText');
    const classSearch = document.getElementById('classSearch');
    const levelFilter = document.getElementById('levelFilter');
    const classesCount = document.getElementById('classesCount');

    let editingId = null;
    let searchTimeout;

    function openClassDrawer() {
        editingId = null;
        drawerTitle.textContent = 'Add New Class';
        document.getElementById('classId').value = '';
        document.getElementById('classLevel').value = '';
        document.getElementById('className').value = '';
        document.getElementById('classSubLevel').innerHTML = '<option value="">No sub-level</option>';
        document.getElementById('subLevelFormGroup').style.display = 'none';
        document.getElementById('classDescription').value = '';
        document.getElementById('classOrder').value = 0;
        document.getElementById('classActive').checked = true;
        document.getElementById('saveText').textContent = 'Save Class';
        classDrawer.classList.add('open');
        classSidebar.classList.add('open');
        document.body.style.overflow = 'hidden';
    }

    function closeClassDrawer() {
        classDrawer.classList.remove('open');
        classSidebar.classList.remove('open');
        document.body.style.overflow = '';
        editingId = null;
    }

    function editClass(id, levelId, name, description, subLevelId, order, isActive) {
        editingId = id;
        drawerTitle.textContent = 'Edit Class';
        document.getElementById('classId').value = id;
        document.getElementById('classLevel').value = levelId;
        document.getElementById('className').value = name;
        populateSubLevels(levelId, subLevelId);
        document.getElementById('classDescription').value = description;
        document.getElementById('classOrder').value = order;
        document.getElementById('classActive').checked = isActive === 1;
        document.getElementById('saveText').textContent = 'Update Class';
        classDrawer.classList.add('open');
        classSidebar.classList.add('open');
        document.body.style.overflow = 'hidden';
    }

    function setLoading(loading) {
        saveBtn.disabled = loading;
        saveSpinner.classList.toggle('hidden', !loading);
        saveText.textContent = loading ? 'Saving...' : (editingId ? 'Update Class' : 'Save Class');
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

    function escapeHtml(text) {
        if (!text) return '';
        return text.replace(/'/g, "\\'").replace(/"/g, '&quot;');
    }

    function addClassToTable(classItem, index) {
        const emptyRow = document.getElementById('emptyRow');
        if (emptyRow) emptyRow.remove();

        const activeClass = classItem.is_active ? 'bg-emerald-50 text-emerald-700 border-emerald-100' : 'bg-red-50 text-red-700 border-red-100';
        const activeText = classItem.is_active ? 'Active' : 'Inactive';
        const levelName = classItem.level ? classItem.level.name : 'Unknown';
        const description = classItem.description || 'No description';
        const subLevelId = classItem.sub_level_id || '';
        const subLevelName = classItem.sub_level ? classItem.sub_level.name : '';
        const subLevelBadge = subLevelName
            ? `<span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-medium bg-blue-50 text-blue-700 border border-blue-100">${subLevelName}</span>`
            : '<span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-medium bg-gray-50 text-gray-500 border border-gray-100">None</span>';

        const row = document.createElement('tr');
        row.className = 'border-t border-gray-100 transition-colors animate-fade';
        row.setAttribute('data-id', classItem.id);
        row.setAttribute('data-level-id', classItem.level_id);
        row.setAttribute('data-sub-level-id', subLevelId);
        row.setAttribute('data-name', classItem.name.toLowerCase());
        row.innerHTML = `
            <td class="px-6 py-3 text-xs text-gray-500">${index}</td>
            <td class="px-6 py-3"><p class="text-sm font-semibold text-gray-900">${classItem.name}</p></td>
            <td class="px-6 py-3"><span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-medium bg-emerald-50 text-emerald-700 border border-emerald-100">${levelName}</span></td>
            <td class="px-6 py-3">${subLevelBadge}</td>
            <td class="px-6 py-3"><p class="text-xs text-gray-500 max-w-xs truncate">${description}</p></td>
            <td class="px-6 py-3"><span class="inline-flex items-center px-2 py-1 rounded-md bg-gray-100 text-xs font-medium text-gray-700">${classItem.order}</span></td>
            <td class="px-6 py-3">
                <button onclick="toggleClassStatus(${classItem.id})" class="status-badge inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-medium border ${activeClass}">${activeText}</button>
            </td>
            <td class="px-6 py-3 text-right">
                <div class="flex items-center justify-end gap-2">
                    <button onclick="editClass(${classItem.id}, ${classItem.level_id}, '${escapeHtml(classItem.name)}', '${escapeHtml(classItem.description)}', '${escapeHtml(subLevelId)}', ${classItem.order}, ${classItem.is_active ? 1 : 0})" class="p-1.5 text-emerald-600 hover:bg-emerald-50 rounded-lg transition-colors" title="Edit">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.433-4.333A2.001 2.001 0 0119 10a2.001 2.001 0 01-.433 1.333L12.5 17.5l-4 1 1-4 6.067-6.167z"/></svg>
                    </button>
                    <button onclick="deleteClass(${classItem.id})" class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Delete">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </button>
                </div>
            </td>
        `;

        const existingRow = document.querySelector(`tr[data-id="${classItem.id}"]`);
        if (existingRow) {
            existingRow.replaceWith(row);
        } else {
            classesTable.appendChild(row);
        }
        updateRowNumbers();
        updateClassesCount();
    }

    function updateRowNumbers() {
        const rows = classesTable.querySelectorAll('tr[data-id]');
        rows.forEach((row, index) => {
            row.querySelector('td:first-child').textContent = index + 1;
        });
        if (rows.length === 0 && !document.getElementById('emptyRow')) {
            classesTable.innerHTML = '<tr id="emptyRow"><td colspan="8" class="px-6 py-12 text-center text-gray-400 text-sm">No classes found. Click "Add New Class" to create one.</td></tr>';
        }
    }

    function updateClassesCount() {
        const total = classesTable.querySelectorAll('tr[data-id]').length;
        classesCount.textContent = total + ' class' + (total !== 1 ? 'es' : '');
    }

    classForm.addEventListener('submit', function(e) {
        e.preventDefault();
        setLoading(true);

        const formData = new FormData(classForm);
        const data = {
            level_id: parseInt(formData.get('level_id')) || 0,
            name: formData.get('name'),
            description: formData.get('description'),
            sub_level_id: formData.get('sub_level_id') || null,
            order: parseInt(formData.get('order')) || 0,
            is_active: formData.get('is_active') ? 1 : 0,
            _token: formData.get('_token')
        };

        const url = editingId
            ? `{{ url('admin/catalog/classes') }}/${editingId}`
            : '{{ route('admin.catalog.classes.store') }}';
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
                addClassToTable(result.class);
                closeClassDrawer();
            } else {
                showToast('error', result.message || 'Something went wrong');
            }
        })
        .catch(error => {
            setLoading(false);
            showToast('error', 'Failed to save class. Please try again.');
            console.error(error);
        });
    });

    function deleteClass(id) {
        Swal.fire({
            title: 'Delete Class?',
            text: 'This action cannot be undone. Subjects under this class may be affected.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`{{ url('admin/catalog/classes') }}/${id}`, {
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
                                updateClassesCount();
                            }, 300);
                        }
                    } else {
                        showToast('error', result.message || 'Failed to delete');
                    }
                })
                .catch(error => {
                    showToast('error', 'Failed to delete class');
                    console.error(error);
                });
            }
        });
    }

    function toggleClassStatus(id) {
        const row = document.querySelector(`tr[data-id="${id}"]`);
        const badge = row.querySelector('.status-badge');
        const isActive = badge.textContent.trim() === 'Active';
        const levelId = row.getAttribute('data-level-id');

        fetch(`{{ url('admin/catalog/classes') }}/${id}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                level_id: parseInt(levelId),
                name: row.querySelector('td:nth-child(2) p').textContent,
                sub_level_id: row.getAttribute('data-sub-level-id') || null,
                description: row.querySelector('td:nth-child(5) p').textContent === 'No description' ? '' : row.querySelector('td:nth-child(5) p').textContent,
                order: parseInt(row.querySelector('td:nth-child(6) span').textContent),
                is_active: isActive ? 0 : 1
            })
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                showToast('success', `Class ${isActive ? 'deactivated' : 'activated'}`);
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

    function loadClasses() {
        const term = classSearch.value.trim();
        const levelId = levelFilter.value;

        const url = new URL('{{ route('admin.catalog.classes') }}', window.location.origin);
        if (term) url.searchParams.append('search', term);
        if (levelId) url.searchParams.append('level_id', levelId);

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
                renderClassesTable(result.classes);
            } else {
                showToast('error', 'Failed to load classes');
            }
        })
        .catch(error => {
            showToast('error', 'Error loading classes');
            console.error(error);
        });
    }

    function renderClassesTable(classes) {
        classesTable.innerHTML = '';

        if (classes.length === 0) {
            classesTable.innerHTML = '<tr id="emptyRow"><td colspan="8" class="px-6 py-12 text-center text-gray-400 text-sm">No classes found. Click "Add New Class" to create one.</td></tr>';
            updateClassesCount();
            return;
        }

        classes.forEach((classItem, index) => {
            const activeClass = classItem.is_active ? 'bg-emerald-50 text-emerald-700 border-emerald-100' : 'bg-red-50 text-red-700 border-red-100';
            const activeText = classItem.is_active ? 'Active' : 'Inactive';
            const levelName = classItem.level ? classItem.level.name : 'Unknown';
            const description = classItem.description || 'No description';
            const subLevelId = classItem.sub_level_id || '';
            const subLevelName = classItem.sub_level ? classItem.sub_level.name : '';
            const subLevelBadge = subLevelName
                ? `<span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-medium bg-blue-50 text-blue-700 border border-blue-100">${subLevelName}</span>`
                : '<span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-medium bg-gray-50 text-gray-500 border border-gray-100">None</span>';

            const row = document.createElement('tr');
            row.className = 'border-t border-gray-100 transition-colors animate-fade';
            row.style.animationDelay = `${index * 0.05}s`;
            row.setAttribute('data-id', classItem.id);
            row.setAttribute('data-level-id', classItem.level_id);
            row.setAttribute('data-sub-level-id', subLevelId);
            row.setAttribute('data-name', classItem.name.toLowerCase());
            row.innerHTML = `
                <td class="px-6 py-3 text-xs text-gray-500">${index + 1}</td>
                <td class="px-6 py-3"><p class="text-sm font-semibold text-gray-900">${classItem.name}</p></td>
                <td class="px-6 py-3"><span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-medium bg-emerald-50 text-emerald-700 border border-emerald-100">${levelName}</span></td>
                <td class="px-6 py-3">${subLevelBadge}</td>
                <td class="px-6 py-3"><p class="text-xs text-gray-500 max-w-xs truncate">${description}</p></td>
                <td class="px-6 py-3"><span class="inline-flex items-center px-2 py-1 rounded-md bg-gray-100 text-xs font-medium text-gray-700">${classItem.order}</span></td>
                <td class="px-6 py-3">
                    <button onclick="toggleClassStatus(${classItem.id})" class="status-badge inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-medium border ${activeClass}">${activeText}</button>
                </td>
                <td class="px-6 py-3 text-right">
                    <div class="flex items-center justify-end gap-2">
                        <button onclick="editClass(${classItem.id}, ${classItem.level_id}, '${escapeHtml(classItem.name)}', '${escapeHtml(classItem.description)}', '${escapeHtml(subLevelId)}', ${classItem.order}, ${classItem.is_active ? 1 : 0})" class="p-1.5 text-emerald-600 hover:bg-emerald-50 rounded-lg transition-colors" title="Edit">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.433-4.333A2.001 2.001 0 0119 10a2.001 2.001 0 01-.433 1.333L12.5 17.5l-4 1 1-4 6.067-6.167z"/></svg>
                        </button>
                        <button onclick="deleteClass(${classItem.id})" class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Delete">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </div>
                </td>
            `;
            classesTable.appendChild(row);
        });
        updateClassesCount();
    }

    const allLevelsForSub = @json($levelsForSub);

    function populateSubLevels(levelId, selectedSubLevelId = null) {
        const select = document.getElementById('classSubLevel');
        const formGroup = document.getElementById('subLevelFormGroup');
        const subs = allLevelsForSub[levelId] || [];
        if (subs.length === 0) {
            select.innerHTML = '<option value="">No sub-level</option>';
            formGroup.style.display = 'none';
            return;
        }
        formGroup.style.display = 'block';
        select.innerHTML = '<option value="">No sub-level</option>';
        subs.forEach(s => {
            const option = document.createElement('option');
            option.value = s.id;
            option.textContent = s.name;
            if (selectedSubLevelId && s.id == selectedSubLevelId) option.selected = true;
            select.appendChild(option);
        });
    }

    document.getElementById('classLevel').addEventListener('change', function() {
        populateSubLevels(this.value);
    });

    classSearch.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(loadClasses, 400);
    });

    levelFilter.addEventListener('change', function() {
        loadClasses();
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeClassDrawer();
    });
</script>
@endsection
