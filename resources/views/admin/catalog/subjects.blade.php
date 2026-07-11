@extends('layouts.dashboard')

@section('title', 'Subjects - ' . config('app.name', 'Elimu Store'))
@section('page_title', 'Manage Subjects')

@section('content')
<div class="space-y-6">
    {{-- Header Card --}}
    <div class="hover-lift dashboard-card bg-white rounded-xl border border-gray-100 p-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="text-lg font-bold text-gray-800">Subjects</h2>
                <p class="text-sm text-gray-500 mt-1">Manage subjects for each class</p>
            </div>
            <button onclick="openSubjectDrawer()" class="dashboard-btn inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg shadow-md hover:shadow-lg transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Add New Subject
            </button>
        </div>
    </div>

    {{-- Filters --}}
    <div class="hover-lift dashboard-card bg-white rounded-xl border border-gray-100 p-4">
        <div class="flex flex-col sm:flex-row gap-3">
            <div class="relative flex-1">
                <input type="text" id="subjectSearch" placeholder="Search subjects..." class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none">
                <svg class="w-4 h-4 text-gray-400 absolute left-3.5 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
            <select id="classFilter" class="pl-4 pr-10 py-2.5 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none bg-white min-w-[220px]">
                <option value="">All Classes</option>
                @foreach($classRooms as $classRoom)
                    <option value="{{ $classRoom->id }}">{{ $classRoom->name }} ({{ $classRoom->level->name }})</option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- Table --}}
    <div class="hover-lift bg-white rounded-xl border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-sm font-semibold text-gray-900 section-header">All Subjects</h3>
            <span class="text-xs text-gray-500" id="subjectsCount">{{ $subjects->count() }} subjects</span>
        </div>
        <div class="overflow-x-auto">
            <table class="dashboard-table w-full text-sm" id="subjectsTable">
                <thead><tr class="text-left text-xs text-gray-500 bg-gray-50/50">
                    <th class="px-6 py-3 font-medium">#</th>
                    <th class="px-6 py-3 font-medium">Subject Name</th>
                    <th class="px-6 py-3 font-medium">Class</th>
                    <th class="px-6 py-3 font-medium">Description</th>
                    <th class="px-6 py-3 font-medium">Order</th>
                    <th class="px-6 py-3 font-medium">Status</th>
                    <th class="px-6 py-3 font-medium text-right">Actions</th>
                </tr></thead>
                <tbody>
                    @forelse($subjects as $index => $subject)
                    <tr class="border-t border-gray-100 transition-colors animate-fade" data-id="{{ $subject->id }}" data-class-id="{{ $subject->class_room_id }}" data-name="{{ strtolower($subject->name) }}" style="animation-delay: {{ $index * 0.05 }}s">
                        <td class="px-6 py-3 text-xs text-gray-500">{{ $index + 1 }}</td>
                        <td class="px-6 py-3">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-lg bg-emerald-100 flex items-center justify-center text-emerald-700 font-bold text-xs">
                                    {{ strtoupper(substr($subject->name, 0, 1)) }}
                                </div>
                                <p class="text-sm font-semibold text-gray-900">{{ $subject->name }}</p>
                            </div>
                        </td>
                        <td class="px-6 py-3">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-medium bg-amber-50 text-amber-700 border border-amber-100">{{ $subject->classRoom->name }} ({{ $subject->classRoom->level->name }})</span>
                        </td>
                        <td class="px-6 py-3">
                            <p class="text-xs text-gray-500 max-w-xs truncate">{{ $subject->description ?? 'No description' }}</p>
                        </td>
                        <td class="px-6 py-3">
                            <span class="inline-flex items-center px-2 py-1 rounded-md bg-gray-100 text-xs font-medium text-gray-700">{{ $subject->order }}</span>
                        </td>
                        <td class="px-6 py-3">
                            <button onclick="toggleSubjectStatus({{ $subject->id }})" class="status-badge inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-medium border {{ $subject->is_active ? 'bg-emerald-50 text-emerald-700 border-emerald-100' : 'bg-red-50 text-red-700 border-red-100' }}">
                                {{ $subject->is_active ? 'Active' : 'Inactive' }}
                            </button>
                        </td>
                        <td class="px-6 py-3 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <button onclick="editSubject({{ $subject->id }}, {{ $subject->class_room_id }}, '{{ addslashes($subject->name) }}', '{{ addslashes($subject->description ?? '') }}', {{ $subject->order }}, {{ $subject->is_active ? 1 : 0 }})" class="p-1.5 text-emerald-600 hover:bg-emerald-50 rounded-lg transition-colors" title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.433-4.333A2.001 2.001 0 0119 10a2.001 2.001 0 01-.433 1.333L12.5 17.5l-4 1 1-4 6.067-6.167z"/></svg>
                                </button>
                                <button onclick="deleteSubject({{ $subject->id }})" class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Delete">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr id="emptyRow"><td colspan="7" class="px-6 py-12 text-center text-gray-400 text-sm">No subjects found. Click "Add New Subject" to create one.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Subject Sidebar Drawer --}}
<div id="subjectDrawer" class="drawer-overlay" aria-labelledby="drawer-title" role="dialog" aria-modal="true" onclick="closeSubjectDrawer()">
    <div class="drawer-sidebar" onclick="event.stopPropagation()">
        <div class="drawer-header">
            <button type="button" onclick="closeSubjectDrawer()" class="drawer-close">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
            <h3 class="text-xl font-bold text-white relative z-10" id="drawerTitle">Add New Subject</h3>
            <p class="text-sm text-emerald-100 mt-1 relative z-10">Fill in the details below</p>
        </div>

        <form id="subjectForm" class="drawer-body">
            @csrf
            <input type="hidden" id="subjectId" name="subject_id" value="">

            <div class="drawer-form-group">
                <label for="subjectClass">Class</label>
                <select id="subjectClass" name="class_room_id" required>
                    <option value="">Select a class</option>
                    @foreach($classRooms as $classRoom)
                        <option value="{{ $classRoom->id }}">{{ $classRoom->name }} ({{ $classRoom->level->name }})</option>
                    @endforeach
                </select>
            </div>

            <div class="drawer-form-group">
                <label for="subjectName">Subject Name</label>
                <input type="text" id="subjectName" name="name" required placeholder="e.g. Mathematics">
            </div>

            <div class="drawer-form-group">
                <label for="subjectDescription">Description</label>
                <textarea id="subjectDescription" name="description" rows="4" placeholder="Short description about this subject..."></textarea>
            </div>

            <div class="drawer-form-group">
                <label for="subjectIcon">Icon (optional)</label>
                <input type="text" id="subjectIcon" name="icon" placeholder="e.g. calculator, book-open">
                <p class="text-xs text-gray-400 mt-1">Heroicons name, leave empty for default</p>
            </div>

            <div class="drawer-form-group">
                <label for="subjectOrder">Display Order</label>
                <input type="number" id="subjectOrder" name="order" min="0" value="0" required>
            </div>

            <div class="drawer-form-group">
                <label class="flex items-center gap-3 p-3 rounded-lg border border-gray-200 cursor-pointer hover:bg-gray-50 transition-colors">
                    <input type="checkbox" id="subjectActive" name="is_active" value="1" checked class="w-5 h-5 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                    <div>
                        <span class="text-sm font-semibold text-gray-700 block">Active Subject</span>
                        <span class="text-xs text-gray-500">Make this subject visible to users</span>
                    </div>
                </label>
            </div>
        </form>

        <div class="drawer-footer">
            <button type="button" onclick="closeSubjectDrawer()" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-semibold rounded-lg transition-colors">Cancel</button>
            <button type="submit" form="subjectForm" id="saveBtn" class="dashboard-btn inline-flex items-center gap-2 px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg shadow-lg hover:shadow-xl transition-all">
                <svg id="saveSpinner" class="w-4 h-4 animate-spin hidden" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                <span id="saveText">Save Subject</span>
            </button>
        </div>
    </div>
</div>

<script>
    const subjectsTable = document.getElementById('subjectsTable').querySelector('tbody');
    const subjectDrawer = document.getElementById('subjectDrawer');
    const subjectSidebar = subjectDrawer.querySelector('.drawer-sidebar');
    const subjectForm = document.getElementById('subjectForm');
    const drawerTitle = document.getElementById('drawerTitle');
    const saveBtn = document.getElementById('saveBtn');
    const saveSpinner = document.getElementById('saveSpinner');
    const saveText = document.getElementById('saveText');
    const subjectSearch = document.getElementById('subjectSearch');
    const classFilter = document.getElementById('classFilter');
    const subjectsCount = document.getElementById('subjectsCount');

    let editingId = null;
    let searchTimeout;

    function openSubjectDrawer() {
        editingId = null;
        drawerTitle.textContent = 'Add New Subject';
        document.getElementById('subjectId').value = '';
        document.getElementById('subjectClass').value = '';
        document.getElementById('subjectName').value = '';
        document.getElementById('subjectDescription').value = '';
        document.getElementById('subjectIcon').value = '';
        document.getElementById('subjectOrder').value = 0;
        document.getElementById('subjectActive').checked = true;
        document.getElementById('saveText').textContent = 'Save Subject';
        subjectDrawer.classList.add('open');
        subjectSidebar.classList.add('open');
        document.body.style.overflow = 'hidden';
    }

    function closeSubjectDrawer() {
        subjectDrawer.classList.remove('open');
        subjectSidebar.classList.remove('open');
        document.body.style.overflow = '';
        editingId = null;
    }

    function editSubject(id, classRoomId, name, description, order, isActive) {
        editingId = id;
        drawerTitle.textContent = 'Edit Subject';
        document.getElementById('subjectId').value = id;
        document.getElementById('subjectClass').value = classRoomId;
        document.getElementById('subjectName').value = name;
        document.getElementById('subjectDescription').value = description;
        document.getElementById('subjectIcon').value = '';
        document.getElementById('subjectOrder').value = order;
        document.getElementById('subjectActive').checked = isActive === 1;
        document.getElementById('saveText').textContent = 'Update Subject';
        subjectDrawer.classList.add('open');
        subjectSidebar.classList.add('open');
        document.body.style.overflow = 'hidden';
    }

    function setLoading(loading) {
        saveBtn.disabled = loading;
        saveSpinner.classList.toggle('hidden', !loading);
        saveText.textContent = loading ? 'Saving...' : (editingId ? 'Update Subject' : 'Save Subject');
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

    function getInitials(name) {
        return name ? name.charAt(0).toUpperCase() : 'S';
    }

    function addSubjectToTable(subject, index) {
        const emptyRow = document.getElementById('emptyRow');
        if (emptyRow) emptyRow.remove();

        const activeClass = subject.is_active ? 'bg-emerald-50 text-emerald-700 border-emerald-100' : 'bg-red-50 text-red-700 border-red-100';
        const activeText = subject.is_active ? 'Active' : 'Inactive';
        const classRoom = subject.class_room ? subject.class_room : (subject.classRoom ? subject.classRoom : null);
        const className = classRoom ? `${classRoom.name} (${classRoom.level ? classRoom.level.name : 'Unknown'})` : 'Unknown';
        const description = subject.description || 'No description';

        const row = document.createElement('tr');
        row.className = 'border-t border-gray-100 transition-colors animate-fade';
        row.setAttribute('data-id', subject.id);
        row.setAttribute('data-class-id', subject.class_room_id);
        row.setAttribute('data-name', subject.name.toLowerCase());
        row.innerHTML = `
            <td class="px-6 py-3 text-xs text-gray-500">${index}</td>
            <td class="px-6 py-3">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-emerald-100 flex items-center justify-center text-emerald-700 font-bold text-xs">${getInitials(subject.name)}</div>
                    <p class="text-sm font-semibold text-gray-900">${subject.name}</p>
                </div>
            </td>
            <td class="px-6 py-3"><span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-medium bg-amber-50 text-amber-700 border border-amber-100">${className}</span></td>
            <td class="px-6 py-3"><p class="text-xs text-gray-500 max-w-xs truncate">${description}</p></td>
            <td class="px-6 py-3"><span class="inline-flex items-center px-2 py-1 rounded-md bg-gray-100 text-xs font-medium text-gray-700">${subject.order}</span></td>
            <td class="px-6 py-3">
                <button onclick="toggleSubjectStatus(${subject.id})" class="status-badge inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-medium border ${activeClass}">${activeText}</button>
            </td>
            <td class="px-6 py-3 text-right">
                <div class="flex items-center justify-end gap-2">
                    <button onclick="editSubject(${subject.id}, ${subject.class_room_id}, '${escapeHtml(subject.name)}', '${escapeHtml(subject.description)}', ${subject.order}, ${subject.is_active ? 1 : 0})" class="p-1.5 text-emerald-600 hover:bg-emerald-50 rounded-lg transition-colors" title="Edit">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.433-4.333A2.001 2.001 0 0119 10a2.001 2.001 0 01-.433 1.333L12.5 17.5l-4 1 1-4 6.067-6.167z"/></svg>
                    </button>
                    <button onclick="deleteSubject(${subject.id})" class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Delete">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </button>
                </div>
            </td>
        `;

        const existingRow = document.querySelector(`tr[data-id="${subject.id}"]`);
        if (existingRow) {
            existingRow.replaceWith(row);
        } else {
            subjectsTable.appendChild(row);
        }
        updateRowNumbers();
        updateSubjectsCount();
    }

    function updateRowNumbers() {
        const rows = subjectsTable.querySelectorAll('tr[data-id]');
        rows.forEach((row, index) => {
            row.querySelector('td:first-child').textContent = index + 1;
        });
        if (rows.length === 0 && !document.getElementById('emptyRow')) {
            subjectsTable.innerHTML = '<tr id="emptyRow"><td colspan="7" class="px-6 py-12 text-center text-gray-400 text-sm">No subjects found. Click "Add New Subject" to create one.</td></tr>';
        }
    }

    function updateSubjectsCount() {
        const total = subjectsTable.querySelectorAll('tr[data-id]').length;
        subjectsCount.textContent = total + ' subject' + (total !== 1 ? 's' : '');
    }

    subjectForm.addEventListener('submit', function(e) {
        e.preventDefault();
        setLoading(true);

        const formData = new FormData(subjectForm);
        const data = {
            class_room_id: parseInt(formData.get('class_room_id')) || 0,
            name: formData.get('name'),
            description: formData.get('description'),
            icon: formData.get('icon'),
            order: parseInt(formData.get('order')) || 0,
            is_active: formData.get('is_active') ? 1 : 0,
            _token: formData.get('_token')
        };

        const url = editingId
            ? `{{ url('admin/catalog/subjects') }}/${editingId}`
            : '{{ route('admin.catalog.subjects.store') }}';
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
                addSubjectToTable(result.subject);
                closeSubjectDrawer();
            } else {
                showToast('error', result.message || 'Something went wrong');
            }
        })
        .catch(error => {
            setLoading(false);
            showToast('error', 'Failed to save subject. Please try again.');
            console.error(error);
        });
    });

    function deleteSubject(id) {
        Swal.fire({
            title: 'Delete Subject?',
            text: 'This action cannot be undone. Notes under this subject may be affected.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`{{ url('admin/catalog/subjects') }}/${id}`, {
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
                                updateSubjectsCount();
                            }, 300);
                        }
                    } else {
                        showToast('error', result.message || 'Failed to delete');
                    }
                })
                .catch(error => {
                    showToast('error', 'Failed to delete subject');
                    console.error(error);
                });
            }
        });
    }

    function toggleSubjectStatus(id) {
        const row = document.querySelector(`tr[data-id="${id}"]`);
        const badge = row.querySelector('.status-badge');
        const isActive = badge.textContent.trim() === 'Active';
        const classRoomId = row.getAttribute('data-class-id');

        fetch(`{{ url('admin/catalog/subjects') }}/${id}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                class_room_id: parseInt(classRoomId),
                name: row.querySelector('td:nth-child(2) p').textContent,
                description: row.querySelector('td:nth-child(4) p').textContent === 'No description' ? '' : row.querySelector('td:nth-child(4) p').textContent,
                order: parseInt(row.querySelector('td:nth-child(5) span').textContent),
                is_active: isActive ? 0 : 1
            })
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                showToast('success', `Subject ${isActive ? 'deactivated' : 'activated'}`);
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

    function loadSubjects() {
        const term = subjectSearch.value.trim();
        const classRoomId = classFilter.value;

        const url = new URL('{{ route('admin.catalog.subjects') }}', window.location.origin);
        if (term) url.searchParams.append('search', term);
        if (classRoomId) url.searchParams.append('class_room_id', classRoomId);

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
                renderSubjectsTable(result.subjects);
            } else {
                showToast('error', 'Failed to load subjects');
            }
        })
        .catch(error => {
            showToast('error', 'Error loading subjects');
            console.error(error);
        });
    }

    function renderSubjectsTable(subjects) {
        subjectsTable.innerHTML = '';

        if (subjects.length === 0) {
            subjectsTable.innerHTML = '<tr id="emptyRow"><td colspan="7" class="px-6 py-12 text-center text-gray-400 text-sm">No subjects found. Click "Add New Subject" to create one.</td></tr>';
            updateSubjectsCount();
            return;
        }

        subjects.forEach((subject, index) => {
            const activeClass = subject.is_active ? 'bg-emerald-50 text-emerald-700 border-emerald-100' : 'bg-red-50 text-red-700 border-red-100';
            const activeText = subject.is_active ? 'Active' : 'Inactive';
            const classRoom = subject.class_room ? subject.class_room : (subject.classRoom ? subject.classRoom : null);
            const className = classRoom ? `${classRoom.name} (${classRoom.level ? classRoom.level.name : 'Unknown'})` : 'Unknown';
            const description = subject.description || 'No description';

            const row = document.createElement('tr');
            row.className = 'border-t border-gray-100 transition-colors animate-fade';
            row.style.animationDelay = `${index * 0.05}s`;
            row.setAttribute('data-id', subject.id);
            row.setAttribute('data-class-id', subject.class_room_id);
            row.setAttribute('data-name', subject.name.toLowerCase());
            row.innerHTML = `
                <td class="px-6 py-3 text-xs text-gray-500">${index + 1}</td>
                <td class="px-6 py-3">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg bg-emerald-100 flex items-center justify-center text-emerald-700 font-bold text-xs">${getInitials(subject.name)}</div>
                        <p class="text-sm font-semibold text-gray-900">${subject.name}</p>
                    </div>
                </td>
                <td class="px-6 py-3"><span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-medium bg-amber-50 text-amber-700 border border-amber-100">${className}</span></td>
                <td class="px-6 py-3"><p class="text-xs text-gray-500 max-w-xs truncate">${description}</p></td>
                <td class="px-6 py-3"><span class="inline-flex items-center px-2 py-1 rounded-md bg-gray-100 text-xs font-medium text-gray-700">${subject.order}</span></td>
                <td class="px-6 py-3">
                    <button onclick="toggleSubjectStatus(${subject.id})" class="status-badge inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-medium border ${activeClass}">${activeText}</button>
                </td>
                <td class="px-6 py-3 text-right">
                    <div class="flex items-center justify-end gap-2">
                        <button onclick="editSubject(${subject.id}, ${subject.class_room_id}, '${escapeHtml(subject.name)}', '${escapeHtml(subject.description)}', ${subject.order}, ${subject.is_active ? 1 : 0})" class="p-1.5 text-emerald-600 hover:bg-emerald-50 rounded-lg transition-colors" title="Edit">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.433-4.333A2.001 2.001 0 0119 10a2.001 2.001 0 01-.433 1.333L12.5 17.5l-4 1 1-4 6.067-6.167z"/></svg>
                        </button>
                        <button onclick="deleteSubject(${subject.id})" class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Delete">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </div>
                </td>
            `;
            subjectsTable.appendChild(row);
        });
        updateSubjectsCount();
    }

    subjectSearch.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(loadSubjects, 400);
    });

    classFilter.addEventListener('change', function() {
        loadSubjects();
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeSubjectDrawer();
    });
</script>
@endsection
