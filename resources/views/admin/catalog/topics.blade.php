@extends('layouts.dashboard')

@section('title', 'Topics - ' . config('app.name', 'Elimu Store'))
@section('page_title', 'Manage Topics')

@section('content')
<div class="space-y-6">
    {{-- Header Card --}}
    <div class="hover-lift dashboard-card bg-white rounded-xl border border-gray-100 p-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="text-lg font-bold text-gray-800">Topics</h2>
                <p class="text-sm text-gray-500 mt-1">Manage topics for each subject by level and class</p>
            </div>
            <button onclick="openTopicDrawer()" class="dashboard-btn inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg shadow-md hover:shadow-lg transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Add New Topic
            </button>
        </div>
    </div>

    {{-- Filters --}}
    <div class="hover-lift dashboard-card bg-white rounded-xl border border-gray-100 p-4">
        <div class="flex flex-col lg:flex-row gap-3">
            <div class="relative flex-1">
                <input type="text" id="topicSearch" placeholder="Search topics..." class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none">
                <svg class="w-4 h-4 text-gray-400 absolute left-3.5 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
            <select id="levelFilter" class="pl-4 pr-10 py-2.5 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none bg-white min-w-[180px]">
                <option value="">All Levels</option>
                @foreach($levels as $level)
                    <option value="{{ $level->id }}">{{ $level->name }}</option>
                @endforeach
            </select>
            <select id="classFilter" class="pl-4 pr-10 py-2.5 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none bg-white min-w-[180px]">
                <option value="">All Classes</option>
                @foreach($classRooms as $classRoom)
                    <option value="{{ $classRoom->id }}" data-level-id="{{ $classRoom->level_id }}">{{ $classRoom->name }} ({{ $classRoom->level->name }})</option>
                @endforeach
            </select>
            <select id="subjectFilter" class="pl-4 pr-10 py-2.5 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none bg-white min-w-[180px]">
                <option value="">All Subjects</option>
                @foreach($subjects as $subject)
                    <option value="{{ $subject->id }}" data-class-id="{{ $subject->class_room_id }}">{{ $subject->name }} ({{ $subject->classRoom->name }})</option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- Table --}}
    <div class="hover-lift bg-white rounded-xl border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-sm font-semibold text-gray-900 section-header">All Topics</h3>
            <span class="text-xs text-gray-500" id="topicsCount">{{ $topics->count() }} topics</span>
        </div>
        <div class="overflow-x-auto">
            <table class="dashboard-table w-full text-sm" id="topicsTable">
                <thead><tr class="text-left text-xs text-gray-500 bg-gray-50/50">
                    <th class="px-6 py-3 font-medium">#</th>
                    <th class="px-6 py-3 font-medium">Topic</th>
                    <th class="px-6 py-3 font-medium">Level</th>
                    <th class="px-6 py-3 font-medium">Class</th>
                    <th class="px-6 py-3 font-medium">Subject</th>
                    <th class="px-6 py-3 font-medium">Description</th>
                    <th class="px-6 py-3 font-medium">Order</th>
                    <th class="px-6 py-3 font-medium">Status</th>
                    <th class="px-6 py-3 font-medium text-right">Actions</th>
                </tr></thead>
                <tbody>
                    @forelse($topics as $index => $topic)
                    <tr class="border-t border-gray-100 transition-colors animate-fade" data-id="{{ $topic->id }}" data-subject-id="{{ $topic->subject_id }}" data-class-id="{{ $topic->subject->classRoom->id }}" data-level-id="{{ $topic->subject->classRoom->level->id }}" data-name="{{ strtolower($topic->name) }}" style="animation-delay: {{ $index * 0.05 }}s">
                        <td class="px-6 py-3 text-xs text-gray-500">{{ $index + 1 }}</td>
                        <td class="px-6 py-3">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-lg bg-emerald-100 flex items-center justify-center text-emerald-700 font-bold text-xs">
                                    {{ strtoupper(substr($topic->name, 0, 1)) }}
                                </div>
                                <p class="text-sm font-semibold text-gray-900">{{ $topic->name }}</p>
                            </div>
                        </td>
                        <td class="px-6 py-3">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-medium bg-emerald-50 text-emerald-700 border border-emerald-100">{{ $topic->subject->classRoom->level->name }}</span>
                        </td>
                        <td class="px-6 py-3">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-medium bg-amber-50 text-amber-700 border border-amber-100">{{ $topic->subject->classRoom->name }}</span>
                        </td>
                        <td class="px-6 py-3">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-medium bg-sky-50 text-sky-700 border border-sky-100">{{ $topic->subject->name }}</span>
                        </td>
                        <td class="px-6 py-3">
                            <p class="text-xs text-gray-500 max-w-xs truncate">{{ $topic->description ?? 'No description' }}</p>
                        </td>
                        <td class="px-6 py-3">
                            <span class="inline-flex items-center px-2 py-1 rounded-md bg-gray-100 text-xs font-medium text-gray-700">{{ $topic->order }}</span>
                        </td>
                        <td class="px-6 py-3">
                            <button onclick="toggleTopicStatus({{ $topic->id }})" class="status-badge inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-medium border {{ $topic->is_active ? 'bg-emerald-50 text-emerald-700 border-emerald-100' : 'bg-red-50 text-red-700 border-red-100' }}">
                                {{ $topic->is_active ? 'Active' : 'Inactive' }}
                            </button>
                        </td>
                        <td class="px-6 py-3 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <button onclick="editTopic({{ $topic->id }}, {{ $topic->subject_id }}, {{ $topic->subject->classRoom->level_id }}, {{ $topic->subject->classRoom->id }}, '{{ addslashes($topic->name) }}', '{{ addslashes($topic->description ?? '') }}', {{ $topic->order }}, {{ $topic->is_active ? 1 : 0 }})" class="p-1.5 text-emerald-600 hover:bg-emerald-50 rounded-lg transition-colors" title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.433-4.333A2.001 2.001 0 0119 10a2.001 2.001 0 01-.433 1.333L12.5 17.5l-4 1 1-4 6.067-6.167z"/></svg>
                                </button>
                                <button onclick="deleteTopic({{ $topic->id }})" class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Delete">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr id="emptyRow"><td colspan="9" class="px-6 py-12 text-center text-gray-400 text-sm">No topics found. Click "Add New Topic" to create one.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Topic Sidebar Drawer --}}
<div id="topicDrawer" class="drawer-overlay" aria-labelledby="drawer-title" role="dialog" aria-modal="true" onclick="closeTopicDrawer()">
    <div class="drawer-sidebar" onclick="event.stopPropagation()">
        <div class="drawer-header">
            <button type="button" onclick="closeTopicDrawer()" class="drawer-close">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
            <h3 class="text-xl font-bold text-white relative z-10" id="drawerTitle">Add New Topic</h3>
            <p class="text-sm text-emerald-100 mt-1 relative z-10">Select level, class, and subject</p>
        </div>

        <form id="topicForm" class="drawer-body">
            @csrf
            <input type="hidden" id="topicId" name="topic_id" value="">

            <div class="drawer-form-group">
                <label for="topicLevel">Education Level</label>
                <select id="topicLevel" name="level_id" required>
                    <option value="">Select a level</option>
                    @foreach($levels as $level)
                        <option value="{{ $level->id }}">{{ $level->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="drawer-form-group">
                <label for="topicClass">Class</label>
                <select id="topicClass" name="class_room_id" required disabled>
                    <option value="">Select a level first</option>
                </select>
            </div>

            <div class="drawer-form-group">
                <label for="topicSubject">Subject</label>
                <select id="topicSubject" name="subject_id" required disabled>
                    <option value="">Select a class first</option>
                </select>
            </div>

            <div class="drawer-form-group">
                <label for="topicName">Topic Name</label>
                <input type="text" id="topicName" name="name" required placeholder="e.g. Algebra Basics">
            </div>

            <div class="drawer-form-group">
                <label for="topicDescription">Description</label>
                <textarea id="topicDescription" name="description" rows="4" placeholder="Short description about this topic..."></textarea>
            </div>

            <div class="drawer-form-group">
                <label for="topicOrder">Display Order</label>
                <input type="number" id="topicOrder" name="order" min="0" value="0" required>
            </div>

            <div class="drawer-form-group">
                <label class="flex items-center gap-3 p-3 rounded-lg border border-gray-200 cursor-pointer hover:bg-gray-50 transition-colors">
                    <input type="checkbox" id="topicActive" name="is_active" value="1" checked class="w-5 h-5 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                    <div>
                        <span class="text-sm font-semibold text-gray-700 block">Active Topic</span>
                        <span class="text-xs text-gray-500">Make this topic visible to users</span>
                    </div>
                </label>
            </div>
        </form>

        <div class="drawer-footer">
            <button type="button" onclick="closeTopicDrawer()" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-semibold rounded-lg transition-colors">Cancel</button>
            <button type="submit" form="topicForm" id="saveBtn" class="dashboard-btn inline-flex items-center gap-2 px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg shadow-lg hover:shadow-xl transition-all">
                <svg id="saveSpinner" class="w-4 h-4 animate-spin hidden" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                <span id="saveText">Save Topic</span>
            </button>
        </div>
    </div>
</div>

<script>
    const topicsTable = document.getElementById('topicsTable').querySelector('tbody');
    const topicDrawer = document.getElementById('topicDrawer');
    const topicSidebar = topicDrawer.querySelector('.drawer-sidebar');
    const topicForm = document.getElementById('topicForm');
    const drawerTitle = document.getElementById('drawerTitle');
    const saveBtn = document.getElementById('saveBtn');
    const saveSpinner = document.getElementById('saveSpinner');
    const saveText = document.getElementById('saveText');
    const topicSearch = document.getElementById('topicSearch');
    const levelFilter = document.getElementById('levelFilter');
    const classFilter = document.getElementById('classFilter');
    const subjectFilter = document.getElementById('subjectFilter');
    const topicsCount = document.getElementById('topicsCount');
    const topicLevel = document.getElementById('topicLevel');
    const topicClass = document.getElementById('topicClass');
    const topicSubject = document.getElementById('topicSubject');

    const allLevels = @json($levels);
    const allClassRooms = @json($classRooms);
    const allSubjects = @json($subjects);

    let editingId = null;
    let searchTimeout;

    function openTopicDrawer() {
        editingId = null;
        drawerTitle.textContent = 'Add New Topic';
        document.getElementById('topicId').value = '';
        topicLevel.value = '';
        topicClass.innerHTML = '<option value="">Select a level first</option>';
        topicClass.disabled = true;
        topicSubject.innerHTML = '<option value="">Select a class first</option>';
        topicSubject.disabled = true;
        document.getElementById('topicName').value = '';
        document.getElementById('topicDescription').value = '';
        document.getElementById('topicOrder').value = 0;
        document.getElementById('topicActive').checked = true;
        document.getElementById('saveText').textContent = 'Save Topic';
        topicDrawer.classList.add('open');
        topicSidebar.classList.add('open');
        document.body.style.overflow = 'hidden';
    }

    function closeTopicDrawer() {
        topicDrawer.classList.remove('open');
        topicSidebar.classList.remove('open');
        document.body.style.overflow = '';
        editingId = null;
    }

    function editTopic(id, subjectId, levelId, classRoomId, name, description, order, isActive) {
        editingId = id;
        drawerTitle.textContent = 'Edit Topic';
        document.getElementById('topicId').value = id;
        topicLevel.value = levelId;
        populateTopicClasses(levelId, classRoomId);
        populateTopicSubjects(classRoomId, subjectId);
        document.getElementById('topicName').value = name;
        document.getElementById('topicDescription').value = description;
        document.getElementById('topicOrder').value = order;
        document.getElementById('topicActive').checked = isActive === 1;
        document.getElementById('saveText').textContent = 'Update Topic';
        topicDrawer.classList.add('open');
        topicSidebar.classList.add('open');
        document.body.style.overflow = 'hidden';
    }

    function populateTopicClasses(levelId, selectedClassId = null) {
        topicClass.innerHTML = '<option value="">Select a class</option>';
        const filteredClasses = allClassRooms.filter(c => c.level_id == levelId);
        filteredClasses.forEach(c => {
            const option = document.createElement('option');
            option.value = c.id;
            option.textContent = c.name;
            if (selectedClassId && c.id == selectedClassId) option.selected = true;
            topicClass.appendChild(option);
        });
        topicClass.disabled = filteredClasses.length === 0;
    }

    function populateTopicSubjects(classRoomId, selectedSubjectId = null) {
        topicSubject.innerHTML = '<option value="">Select a subject</option>';
        const filteredSubjects = allSubjects.filter(s => s.class_room_id == classRoomId);
        filteredSubjects.forEach(s => {
            const option = document.createElement('option');
            option.value = s.id;
            option.textContent = s.name;
            if (selectedSubjectId && s.id == selectedSubjectId) option.selected = true;
            topicSubject.appendChild(option);
        });
        topicSubject.disabled = filteredSubjects.length === 0;
    }

    topicLevel.addEventListener('change', function() {
        populateTopicClasses(this.value);
        topicSubject.innerHTML = '<option value="">Select a class first</option>';
        topicSubject.disabled = true;
    });

    topicClass.addEventListener('change', function() {
        populateTopicSubjects(this.value);
    });

    function setLoading(loading) {
        saveBtn.disabled = loading;
        saveSpinner.classList.toggle('hidden', !loading);
        saveText.textContent = loading ? 'Saving...' : (editingId ? 'Update Topic' : 'Save Topic');
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
        return name ? name.charAt(0).toUpperCase() : 'T';
    }

    function addTopicToTable(topic, index) {
        const emptyRow = document.getElementById('emptyRow');
        if (emptyRow) emptyRow.remove();

        const activeClass = topic.is_active ? 'bg-emerald-50 text-emerald-700 border-emerald-100' : 'bg-red-50 text-red-700 border-red-100';
        const activeText = topic.is_active ? 'Active' : 'Inactive';
        const subject = topic.subject ? topic.subject : (topic.subject ? topic.subject : null);
        const classRoom = subject && subject.class_room ? subject.class_room : (subject && subject.classRoom ? subject.classRoom : null);
        const level = classRoom && classRoom.level ? classRoom.level : null;
        const className = classRoom ? classRoom.name : 'Unknown';
        const levelName = level ? level.name : 'Unknown';
        const subjectName = subject ? subject.name : 'Unknown';
        const classRoomId = classRoom ? classRoom.id : '';
        const levelId = level ? level.id : '';
        const description = topic.description || 'No description';

        const row = document.createElement('tr');
        row.className = 'border-t border-gray-100 transition-colors animate-fade';
        row.setAttribute('data-id', topic.id);
        row.setAttribute('data-subject-id', topic.subject_id);
        row.setAttribute('data-class-id', classRoomId);
        row.setAttribute('data-level-id', levelId);
        row.setAttribute('data-name', topic.name.toLowerCase());
        row.innerHTML = `
            <td class="px-6 py-3 text-xs text-gray-500">${index}</td>
            <td class="px-6 py-3">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-emerald-100 flex items-center justify-center text-emerald-700 font-bold text-xs">${getInitials(topic.name)}</div>
                    <p class="text-sm font-semibold text-gray-900">${topic.name}</p>
                </div>
            </td>
            <td class="px-6 py-3"><span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-medium bg-emerald-50 text-emerald-700 border border-emerald-100">${levelName}</span></td>
            <td class="px-6 py-3"><span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-medium bg-amber-50 text-amber-700 border border-amber-100">${className}</span></td>
            <td class="px-6 py-3"><span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-medium bg-sky-50 text-sky-700 border border-sky-100">${subjectName}</span></td>
            <td class="px-6 py-3"><p class="text-xs text-gray-500 max-w-xs truncate">${description}</p></td>
            <td class="px-6 py-3"><span class="inline-flex items-center px-2 py-1 rounded-md bg-gray-100 text-xs font-medium text-gray-700">${topic.order}</span></td>
            <td class="px-6 py-3">
                <button onclick="toggleTopicStatus(${topic.id})" class="status-badge inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-medium border ${activeClass}">${activeText}</button>
            </td>
            <td class="px-6 py-3 text-right">
                <div class="flex items-center justify-end gap-2">
                    <button onclick="editTopic(${topic.id}, ${topic.subject_id}, ${levelId}, ${classRoomId}, '${escapeHtml(topic.name)}', '${escapeHtml(topic.description)}', ${topic.order}, ${topic.is_active ? 1 : 0})" class="p-1.5 text-emerald-600 hover:bg-emerald-50 rounded-lg transition-colors" title="Edit">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.433-4.333A2.001 2.001 0 0119 10a2.001 2.001 0 01-.433 1.333L12.5 17.5l-4 1 1-4 6.067-6.167z"/></svg>
                    </button>
                    <button onclick="deleteTopic(${topic.id})" class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Delete">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </button>
                </div>
            </td>
        `;

        const existingRow = document.querySelector(`tr[data-id="${topic.id}"]`);
        if (existingRow) {
            existingRow.replaceWith(row);
        } else {
            topicsTable.appendChild(row);
        }
        updateRowNumbers();
        updateTopicsCount();
    }

    function updateRowNumbers() {
        const rows = topicsTable.querySelectorAll('tr[data-id]');
        rows.forEach((row, index) => {
            row.querySelector('td:first-child').textContent = index + 1;
        });
        if (rows.length === 0 && !document.getElementById('emptyRow')) {
            topicsTable.innerHTML = '<tr id="emptyRow"><td colspan="9" class="px-6 py-12 text-center text-gray-400 text-sm">No topics found. Click "Add New Topic" to create one.</td></tr>';
        }
    }

    function updateTopicsCount() {
        const total = topicsTable.querySelectorAll('tr[data-id]').length;
        topicsCount.textContent = total + ' topic' + (total !== 1 ? 's' : '');
    }

    topicForm.addEventListener('submit', function(e) {
        e.preventDefault();
        setLoading(true);

        const formData = new FormData(topicForm);
        const data = {
            subject_id: parseInt(formData.get('subject_id')) || 0,
            name: formData.get('name'),
            description: formData.get('description'),
            order: parseInt(formData.get('order')) || 0,
            is_active: formData.get('is_active') ? 1 : 0,
            _token: formData.get('_token')
        };

        const url = editingId
            ? `{{ url('admin/catalog/topics') }}/${editingId}`
            : '{{ route('admin.catalog.topics.store') }}';
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
                addTopicToTable(result.topic);
                closeTopicDrawer();
            } else {
                showToast('error', result.message || 'Something went wrong');
            }
        })
        .catch(error => {
            setLoading(false);
            showToast('error', 'Failed to save topic. Please try again.');
            console.error(error);
        });
    });

    function deleteTopic(id) {
        Swal.fire({
            title: 'Delete Topic?',
            text: 'This action cannot be undone. Notes under this topic may be affected.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`{{ url('admin/catalog/topics') }}/${id}`, {
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
                                updateTopicsCount();
                            }, 300);
                        }
                    } else {
                        showToast('error', result.message || 'Failed to delete');
                    }
                })
                .catch(error => {
                    showToast('error', 'Failed to delete topic');
                    console.error(error);
                });
            }
        });
    }

    function toggleTopicStatus(id) {
        const row = document.querySelector(`tr[data-id="${id}"]`);
        const badge = row.querySelector('.status-badge');
        const isActive = badge.textContent.trim() === 'Active';
        const subjectId = row.getAttribute('data-subject-id');

        fetch(`{{ url('admin/catalog/topics') }}/${id}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                subject_id: parseInt(subjectId),
                name: row.querySelector('td:nth-child(2) p').textContent,
                description: row.querySelector('td:nth-child(6) p').textContent === 'No description' ? '' : row.querySelector('td:nth-child(6) p').textContent,
                order: parseInt(row.querySelector('td:nth-child(7) span').textContent),
                is_active: isActive ? 0 : 1
            })
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                showToast('success', `Topic ${isActive ? 'deactivated' : 'activated'}`);
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

    function loadTopics() {
        const term = topicSearch.value.trim();
        const levelId = levelFilter.value;
        const classRoomId = classFilter.value;
        const subjectId = subjectFilter.value;

        const url = new URL('{{ route('admin.catalog.topics') }}', window.location.origin);
        if (term) url.searchParams.append('search', term);
        if (levelId) url.searchParams.append('level_id', levelId);
        if (classRoomId) url.searchParams.append('class_room_id', classRoomId);
        if (subjectId) url.searchParams.append('subject_id', subjectId);

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
                renderTopicsTable(result.topics);
            } else {
                showToast('error', 'Failed to load topics');
            }
        })
        .catch(error => {
            showToast('error', 'Error loading topics');
            console.error(error);
        });
    }

    function renderTopicsTable(topics) {
        topicsTable.innerHTML = '';

        if (topics.length === 0) {
            topicsTable.innerHTML = '<tr id="emptyRow"><td colspan="9" class="px-6 py-12 text-center text-gray-400 text-sm">No topics found. Click "Add New Topic" to create one.</td></tr>';
            updateTopicsCount();
            return;
        }

        topics.forEach((topic, index) => {
            const activeClass = topic.is_active ? 'bg-emerald-50 text-emerald-700 border-emerald-100' : 'bg-red-50 text-red-700 border-red-100';
            const activeText = topic.is_active ? 'Active' : 'Inactive';
            const subject = topic.subject ? topic.subject : null;
            const classRoom = subject && subject.class_room ? subject.class_room : (subject && subject.classRoom ? subject.classRoom : null);
            const level = classRoom && classRoom.level ? classRoom.level : null;
            const className = classRoom ? classRoom.name : 'Unknown';
            const levelName = level ? level.name : 'Unknown';
            const subjectName = subject ? subject.name : 'Unknown';
            const classRoomId = classRoom ? classRoom.id : '';
            const levelId = level ? level.id : '';
            const description = topic.description || 'No description';

            const row = document.createElement('tr');
            row.className = 'border-t border-gray-100 transition-colors animate-fade';
            row.style.animationDelay = `${index * 0.05}s`;
            row.setAttribute('data-id', topic.id);
            row.setAttribute('data-subject-id', topic.subject_id);
            row.setAttribute('data-class-id', classRoomId);
            row.setAttribute('data-level-id', levelId);
            row.setAttribute('data-name', topic.name.toLowerCase());
            row.innerHTML = `
                <td class="px-6 py-3 text-xs text-gray-500">${index + 1}</td>
                <td class="px-6 py-3">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg bg-emerald-100 flex items-center justify-center text-emerald-700 font-bold text-xs">${getInitials(topic.name)}</div>
                        <p class="text-sm font-semibold text-gray-900">${topic.name}</p>
                    </div>
                </td>
                <td class="px-6 py-3"><span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-medium bg-emerald-50 text-emerald-700 border border-emerald-100">${levelName}</span></td>
                <td class="px-6 py-3"><span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-medium bg-amber-50 text-amber-700 border border-amber-100">${className}</span></td>
                <td class="px-6 py-3"><span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-medium bg-sky-50 text-sky-700 border border-sky-100">${subjectName}</span></td>
                <td class="px-6 py-3"><p class="text-xs text-gray-500 max-w-xs truncate">${description}</p></td>
                <td class="px-6 py-3"><span class="inline-flex items-center px-2 py-1 rounded-md bg-gray-100 text-xs font-medium text-gray-700">${topic.order}</span></td>
                <td class="px-6 py-3">
                    <button onclick="toggleTopicStatus(${topic.id})" class="status-badge inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-medium border ${activeClass}">${activeText}</button>
                </td>
                <td class="px-6 py-3 text-right">
                    <div class="flex items-center justify-end gap-2">
                        <button onclick="editTopic(${topic.id}, ${topic.subject_id}, ${levelId}, ${classRoomId}, '${escapeHtml(topic.name)}', '${escapeHtml(topic.description)}', ${topic.order}, ${topic.is_active ? 1 : 0})" class="p-1.5 text-emerald-600 hover:bg-emerald-50 rounded-lg transition-colors" title="Edit">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.433-4.333A2.001 2.001 0 0119 10a2.001 2.001 0 01-.433 1.333L12.5 17.5l-4 1 1-4 6.067-6.167z"/></svg>
                        </button>
                        <button onclick="deleteTopic(${topic.id})" class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Delete">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </div>
                </td>
            `;
            topicsTable.appendChild(row);
        });
        updateTopicsCount();
    }

    function populateFilterClasses() {
        const levelId = levelFilter.value;
        const selectedClass = classFilter.value;
        classFilter.innerHTML = '<option value="">All Classes</option>';
        allClassRooms.filter(c => !levelId || c.level_id == levelId).forEach(c => {
            const option = document.createElement('option');
            option.value = c.id;
            option.textContent = c.name + ' (' + (c.level ? c.level.name : '') + ')';
            option.setAttribute('data-level-id', c.level_id);
            if (c.id == selectedClass) option.selected = true;
            classFilter.appendChild(option);
        });
        populateFilterSubjects();
    }

    function populateFilterSubjects() {
        const classRoomId = classFilter.value;
        const selectedSubject = subjectFilter.value;
        subjectFilter.innerHTML = '<option value="">All Subjects</option>';
        allSubjects.filter(s => !classRoomId || s.class_room_id == classRoomId).forEach(s => {
            const option = document.createElement('option');
            option.value = s.id;
            option.textContent = s.name + ' (' + (s.class_room ? s.class_room.name : '') + ')';
            option.setAttribute('data-class-id', s.class_room_id);
            if (s.id == selectedSubject) option.selected = true;
            subjectFilter.appendChild(option);
        });
    }

    topicSearch.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(loadTopics, 400);
    });

    levelFilter.addEventListener('change', function() {
        populateFilterClasses();
        loadTopics();
    });

    classFilter.addEventListener('change', function() {
        populateFilterSubjects();
        loadTopics();
    });

    subjectFilter.addEventListener('change', function() {
        loadTopics();
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeTopicDrawer();
    });
</script>
@endsection
