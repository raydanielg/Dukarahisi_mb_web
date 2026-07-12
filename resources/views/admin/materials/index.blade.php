@extends('layouts.dashboard')

@section('title', $config['title'] . ' - ' . config('app.name', 'Elimu Store'))
@section('page_title', 'Manage ' . $config['title'])

@section('content')
<div class="space-y-6">
    {{-- Header Card --}}
    <div class="hover-lift dashboard-card bg-white rounded-xl border border-gray-100 p-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-600 flex items-center justify-center text-white shadow-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $config['icon'] }}"/></svg>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-gray-800">{{ $config['title'] }}</h2>
                    <p class="text-sm text-gray-500 mt-1">Manage {{ strtolower($config['title']) }} by subject, class, and level</p>
                </div>
            </div>
            <button onclick="openMaterialDrawer()" class="dashboard-btn inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg shadow-md hover:shadow-lg transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Add New {{ $config['singular'] }}
            </button>
        </div>
    </div>

    {{-- Filters --}}
    <div class="hover-lift dashboard-card bg-white rounded-xl border border-gray-100 p-4">
        <div class="flex flex-col xl:flex-row gap-3">
            <div class="relative flex-1">
                <input type="text" id="materialSearch" placeholder="Search {{ strtolower($config['title']) }}..." class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none">
                <svg class="w-4 h-4 text-gray-400 absolute left-3.5 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
            <select id="levelFilter" class="pl-4 pr-10 py-2.5 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none bg-white min-w-[160px]">
                <option value="">All Levels</option>
                @foreach($levels as $level)
                    <option value="{{ $level->id }}">{{ $level->name }}</option>
                @endforeach
            </select>
            <select id="classFilter" class="pl-4 pr-10 py-2.5 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none bg-white min-w-[160px]">
                <option value="">All Classes</option>
                @foreach($classRooms as $classRoom)
                    <option value="{{ $classRoom->id }}" data-level-id="{{ $classRoom->level_id }}">{{ $classRoom->name }} ({{ $classRoom->level->name }})</option>
                @endforeach
            </select>
            <select id="subjectFilter" class="pl-4 pr-10 py-2.5 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none bg-white min-w-[160px]">
                <option value="">All Subjects</option>
                @foreach($subjects as $subject)
                    <option value="{{ $subject->id }}" data-class-id="{{ $subject->class_room_id }}">{{ $subject->name }} ({{ $subject->classRoom->name }})</option>
                @endforeach
            </select>
            <select id="topicFilter" class="pl-4 pr-10 py-2.5 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none bg-white min-w-[160px]">
                <option value="">All Topics</option>
                @foreach($topics as $topic)
                    <option value="{{ $topic->id }}" data-subject-id="{{ $topic->subject_id }}">{{ $topic->name }} ({{ $topic->subject->name }})</option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- Table --}}
    <div class="hover-lift bg-white rounded-xl border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-sm font-semibold text-gray-900 section-header">All {{ $config['title'] }}</h3>
            <span class="text-xs text-gray-500" id="itemsCount">{{ $items->count() }} {{ strtolower($config['title']) }}</span>
        </div>
        <div class="overflow-x-auto">
            <table class="dashboard-table w-full text-sm" id="materialsTable">
                <thead><tr class="text-left text-xs text-gray-500 bg-gray-50/50">
                    <th class="px-6 py-3 font-medium">#</th>
                    <th class="px-6 py-3 font-medium">Title</th>
                    <th class="px-6 py-3 font-medium">Level</th>
                    <th class="px-6 py-3 font-medium">Class</th>
                    <th class="px-6 py-3 font-medium">Subject</th>
                    <th class="px-6 py-3 font-medium">Topic</th>
                    <th class="px-6 py-3 font-medium">Price</th>
                    <th class="px-6 py-3 font-medium">Order</th>
                    <th class="px-6 py-3 font-medium">Status</th>
                    <th class="px-6 py-3 font-medium text-right">Actions</th>
                </tr></thead>
                <tbody>
                    @forelse($items as $index => $item)
                    <tr class="border-t border-gray-100 transition-colors animate-fade" data-id="{{ $item->id }}" data-subject-id="{{ $item->subject_id }}" data-class-id="{{ $item->subject->classRoom->id }}" data-level-id="{{ $item->subject->classRoom->level->id }}" data-name="{{ strtolower($item->title) }}" style="animation-delay: {{ $index * 0.05 }}s">
                        <td class="px-6 py-3 text-xs text-gray-500">{{ $index + 1 }}</td>
                        <td class="px-6 py-3">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-lg bg-emerald-100 flex items-center justify-center text-emerald-700 font-bold text-xs">
                                    {{ strtoupper(substr($item->title, 0, 1)) }}
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-gray-900">{{ $item->title }}</p>
                                    @if($item->file_path)
                                        <a href="{{ asset(str_replace('public/', 'storage/', $item->file_path)) }}" target="_blank" class="text-[10px] text-red-600 hover:text-red-700 font-medium inline-flex items-center gap-1 mt-0.5">
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8l-6-6z"/><path d="M14 2v6h6M16 13H8M16 17H8M10 9H8" stroke="white" stroke-width="2" stroke-linecap="round"/></svg>
                                            PDF
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-3">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-medium bg-emerald-50 text-emerald-700 border border-emerald-100">{{ $item->subject->classRoom->level->name }}</span>
                        </td>
                        <td class="px-6 py-3">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-medium bg-amber-50 text-amber-700 border border-amber-100">{{ $item->subject->classRoom->name }}</span>
                        </td>
                        <td class="px-6 py-3">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-medium bg-sky-50 text-sky-700 border border-sky-100">{{ $item->subject->name }}</span>
                        </td>
                        <td class="px-6 py-3">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-medium bg-purple-50 text-purple-700 border border-purple-100">{{ $item->topic->name ?? 'No topic' }}</span>
                        </td>
                        <td class="px-6 py-3">
                            @if($item->is_free || ($item->price ?? 0) <= 0)
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-medium bg-emerald-50 text-emerald-700 border border-emerald-100">Free</span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-medium bg-amber-50 text-amber-700 border border-amber-100">TZS {{ number_format($item->price, 2) }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-3">
                            <span class="inline-flex items-center px-2 py-1 rounded-md bg-gray-100 text-xs font-medium text-gray-700">{{ $item->order ?? 0 }}</span>
                        </td>
                        <td class="px-6 py-3">
                            <button onclick="toggleMaterialStatus({{ $item->id }})" class="status-badge inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-medium border {{ $item->is_active ? 'bg-emerald-50 text-emerald-700 border-emerald-100' : 'bg-red-50 text-red-700 border-red-100' }}">
                                {{ $item->is_active ? 'Active' : 'Inactive' }}
                            </button>
                        </td>
                        <td class="px-6 py-3 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <button onclick="editMaterial({{ $item->id }}, {{ $item->subject_id }}, {{ $item->subject->classRoom->level_id }}, {{ $item->subject->classRoom->id }}, {{ $item->topic_id ?? 'null' }}, '{{ addslashes($item->title) }}', {{ $item->price ?? 0 }}, {{ $item->order ?? 0 }}, {{ $item->is_active ? 1 : 0 }}, {{ $item->is_free ? 1 : 0 }}, '{{ addslashes($item->file_path ?? '') }}')" class="p-1.5 text-emerald-600 hover:bg-emerald-50 rounded-lg transition-colors" title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.433-4.333A2.001 2.001 0 0119 10a2.001 2.001 0 01-.433 1.333L12.5 17.5l-4 1 1-4 6.067-6.167z"/></svg>
                                </button>
                                <button onclick="deleteMaterial({{ $item->id }})" class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Delete">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr id="emptyRow"><td colspan="10" class="px-6 py-12 text-center text-gray-400 text-sm">No {{ strtolower($config['title']) }} found. Click "Add New {{ $config['singular'] }}" to create one.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Material Sidebar Drawer --}}
<div id="materialDrawer" class="drawer-overlay" aria-labelledby="drawer-title" role="dialog" aria-modal="true" onclick="closeMaterialDrawer()">
    <div class="drawer-sidebar" onclick="event.stopPropagation()">
        <div class="drawer-header">
            <button type="button" onclick="closeMaterialDrawer()" class="drawer-close">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
            <h3 class="text-xl font-bold text-white relative z-10" id="drawerTitle">Add New {{ $config['singular'] }}</h3>
            <p class="text-sm text-emerald-100 mt-1 relative z-10">Select level, class, and subject</p>
        </div>

        <form id="materialForm" class="drawer-body">
            @csrf
            <input type="hidden" id="materialId" name="material_id" value="">

            <div class="drawer-form-group">
                <label for="materialLevel">Education Level</label>
                <select id="materialLevel" name="level_id" required>
                    <option value="">Select a level</option>
                    @foreach($levels as $level)
                        <option value="{{ $level->id }}">{{ $level->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="drawer-form-group">
                <label for="materialClass">Class</label>
                <select id="materialClass" name="class_room_id" required disabled>
                    <option value="">Select a level first</option>
                </select>
            </div>

            <div class="drawer-form-group">
                <label for="materialSubject">Subject</label>
                <select id="materialSubject" name="subject_id" required disabled>
                    <option value="">Select a class first</option>
                </select>
            </div>

            <div class="drawer-form-group">
                <label for="materialTopic">Topic</label>
                <select id="materialTopic" name="topic_id" disabled>
                    <option value="">Select a subject first</option>
                </select>
            </div>

            <div class="drawer-form-group">
                <label for="materialTitle">Title</label>
                <input type="text" id="materialTitle" name="title" required placeholder="e.g. Introduction to Algebra">
            </div>

            <div class="drawer-form-group">
                <label for="materialPrice">Price (TZS)</label>
                <input type="number" id="materialPrice" name="price" min="0" step="0.01" value="0" placeholder="0.00">
                <p class="text-xs text-gray-500 mt-1">Enter 0 or check "Free" to make this document free</p>
            </div>

            <div class="drawer-form-group">
                <label class="flex items-center gap-3 p-3 rounded-lg border border-gray-200 cursor-pointer hover:bg-gray-50 transition-colors">
                    <input type="checkbox" id="materialFree" name="is_free" value="1" checked class="w-5 h-5 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                    <div>
                        <span class="text-sm font-semibold text-gray-700 block">Free Document</span>
                        <span class="text-xs text-gray-500">Users can access this without payment</span>
                    </div>
                </label>
            </div>

            <div class="drawer-form-group">
                <label for="materialPdfFile">PDF File (optional)</label>
                <div class="relative border-2 border-dashed border-emerald-200 rounded-lg p-5 hover:border-emerald-500 hover:bg-emerald-50/30 transition-all bg-emerald-50/20 group" id="pdfDropZone">
                    <input type="file" id="materialPdfFile" name="pdf_file" accept="application/pdf" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                    <div class="text-center pointer-events-none">
                        <div class="w-12 h-12 mx-auto rounded-full bg-emerald-100 flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                            <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                        </div>
                        <p class="text-sm font-semibold text-gray-700" id="pdfFileName">Click or drop PDF here</p>
                        <p class="text-xs text-gray-500 mt-1.5">Maximum file size: <span class="font-medium text-emerald-600">50MB</span> • Only PDF files accepted</p>
                    </div>
                </div>
                <div id="currentPdfContainer" class="hidden mt-3 p-3 rounded-lg border border-emerald-100 bg-emerald-50/50">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2 min-w-0">
                            <div class="w-8 h-8 rounded-lg bg-red-100 flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4 text-red-500" fill="currentColor" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8l-6-6z"/><path d="M14 2v6h6M16 13H8M16 17H8M10 9H8" stroke="white" stroke-width="2" stroke-linecap="round"/></svg>
                            </div>
                            <div class="min-w-0">
                                <span class="text-sm text-gray-700 truncate block" id="currentPdfName">Current PDF</span>
                                <span class="text-[10px] text-gray-500" id="currentPdfSize"></span>
                            </div>
                        </div>
                        <a id="currentPdfLink" href="#" target="_blank" class="text-xs font-semibold text-emerald-600 hover:text-emerald-700 flex-shrink-0 ml-2">View</a>
                    </div>
                    <button type="button" onclick="removePdfFile()" class="mt-2 text-xs text-red-600 hover:text-red-700 font-medium flex items-center gap-1">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        Remove and upload new PDF
                    </button>
                </div>
                <input type="hidden" id="pdfRemoved" name="pdf_removed" value="0">
            </div>

            <div class="drawer-form-group">
                <label for="materialOrder">Display Order</label>
                <input type="number" id="materialOrder" name="order" min="0" value="0" required>
            </div>

            <div class="drawer-form-group">
                <label class="flex items-center gap-3 p-3 rounded-lg border border-gray-200 cursor-pointer hover:bg-gray-50 transition-colors">
                    <input type="checkbox" id="materialActive" name="is_active" value="1" checked class="w-5 h-5 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                    <div>
                        <span class="text-sm font-semibold text-gray-700 block">Active {{ $config['singular'] }}</span>
                        <span class="text-xs text-gray-500">Make this visible to users</span>
                    </div>
                </label>
            </div>
        </form>

        <div class="drawer-footer">
            <button type="button" onclick="closeMaterialDrawer()" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-semibold rounded-lg transition-colors">Cancel</button>
            <button type="submit" form="materialForm" id="saveBtn" class="dashboard-btn inline-flex items-center gap-2 px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg shadow-lg hover:shadow-xl transition-all">
                <svg id="saveSpinner" class="w-4 h-4 animate-spin hidden" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                <span id="saveText">Save {{ $config['singular'] }}</span>
            </button>
        </div>
    </div>
</div>

<script>
    const materialsTable = document.getElementById('materialsTable').querySelector('tbody');
    const materialDrawer = document.getElementById('materialDrawer');
    const materialSidebar = materialDrawer.querySelector('.drawer-sidebar');
    const materialForm = document.getElementById('materialForm');
    const drawerTitle = document.getElementById('drawerTitle');
    const saveBtn = document.getElementById('saveBtn');
    const saveSpinner = document.getElementById('saveSpinner');
    const saveText = document.getElementById('saveText');
    const materialSearch = document.getElementById('materialSearch');
    const levelFilter = document.getElementById('levelFilter');
    const classFilter = document.getElementById('classFilter');
    const subjectFilter = document.getElementById('subjectFilter');
    const topicFilter = document.getElementById('topicFilter');
    const itemsCount = document.getElementById('itemsCount');
    const materialLevel = document.getElementById('materialLevel');
    const materialClass = document.getElementById('materialClass');
    const materialSubject = document.getElementById('materialSubject');
    const materialTopic = document.getElementById('materialTopic');
    const materialPdfFile = document.getElementById('materialPdfFile');
    const pdfFileName = document.getElementById('pdfFileName');
    const currentPdfContainer = document.getElementById('currentPdfContainer');
    const currentPdfName = document.getElementById('currentPdfName');
    const currentPdfLink = document.getElementById('currentPdfLink');
    const pdfRemoved = document.getElementById('pdfRemoved');

    const allLevels = @json($levels);
    const allClassRooms = @json($classRooms);
    const allSubjects = @json($subjects);
    const allTopics = @json($topics);
    const materialType = '{{ $type }}';
    const singularName = '{{ $config['singular'] }}';

    let editingId = null;
    let searchTimeout;

    function openMaterialDrawer() {
        editingId = null;
        drawerTitle.textContent = 'Add New ' + singularName;
        document.getElementById('materialId').value = '';
        materialLevel.value = '';
        materialClass.innerHTML = '<option value="">Select a level first</option>';
        materialClass.disabled = true;
        materialSubject.innerHTML = '<option value="">Select a class first</option>';
        materialSubject.disabled = true;
        materialTopic.innerHTML = '<option value="">Select a subject first</option>';
        materialTopic.disabled = true;
        document.getElementById('materialTitle').value = '';
        document.getElementById('materialPrice').value = '0';
        document.getElementById('materialFree').checked = true;
        materialPdfFile.value = '';
        pdfFileName.textContent = 'Click or drop PDF here';
        pdfFileName.classList.remove('text-emerald-700', 'font-semibold');
        currentPdfContainer.classList.add('hidden');
        currentPdfLink.href = '#';
        pdfRemoved.value = '0';
        document.getElementById('materialOrder').value = 0;
        document.getElementById('materialActive').checked = true;
        document.getElementById('saveText').textContent = 'Save ' + singularName;
        materialDrawer.classList.add('open');
        materialSidebar.classList.add('open');
        document.body.style.overflow = 'hidden';
    }

    function closeMaterialDrawer() {
        materialDrawer.classList.remove('open');
        materialSidebar.classList.remove('open');
        document.body.style.overflow = '';
        editingId = null;
    }

    function editMaterial(id, subjectId, levelId, classRoomId, topicId, title, price, order, isActive, isFree, filePath) {
        editingId = id;
        drawerTitle.textContent = 'Edit ' + singularName;
        document.getElementById('materialId').value = id;
        materialLevel.value = levelId;
        populateMaterialClasses(levelId, classRoomId);
        populateMaterialSubjects(classRoomId, subjectId);
        populateMaterialTopics(subjectId, topicId);
        document.getElementById('materialTitle').value = title;
        document.getElementById('materialPrice').value = price || 0;
        document.getElementById('materialFree').checked = isFree === 1;
        document.getElementById('materialOrder').value = order;
        document.getElementById('materialActive').checked = isActive === 1;
        materialPdfFile.value = '';
        pdfFileName.textContent = 'Click or drop PDF here';
        pdfRemoved.value = '0';
        if (filePath) {
            currentPdfContainer.classList.remove('hidden');
            currentPdfName.textContent = filePath.split('/').pop();
            currentPdfLink.href = '{{ url('/materials') }}/' + materialType + '/' + editingId + '/preview';
        } else {
            currentPdfContainer.classList.add('hidden');
            currentPdfLink.href = '#';
        }
        document.getElementById('saveText').textContent = 'Update ' + singularName;
        materialDrawer.classList.add('open');
        materialSidebar.classList.add('open');
        document.body.style.overflow = 'hidden';
    }

    function formatFileSize(bytes) {
        if (!bytes || bytes === 0) return '';
        const units = ['B', 'KB', 'MB', 'GB'];
        let size = bytes;
        let unitIndex = 0;
        while (size >= 1024 && unitIndex < units.length - 1) {
            size /= 1024;
            unitIndex++;
        }
        return size.toFixed(1) + ' ' + units[unitIndex];
    }

    function removePdfFile() {
        materialPdfFile.value = '';
        pdfFileName.textContent = 'Click or drop PDF here';
        pdfFileName.classList.remove('text-emerald-700', 'font-semibold');
        currentPdfContainer.classList.add('hidden');
        pdfRemoved.value = '1';
    }

    materialPdfFile.addEventListener('change', function() {
        if (this.files && this.files[0]) {
            const file = this.files[0];
            pdfFileName.textContent = file.name;
            pdfFileName.classList.add('text-emerald-700', 'font-semibold');
            pdfRemoved.value = '0';
            currentPdfContainer.classList.add('hidden');

            // Validate size client-side (50MB)
            if (file.size > 50 * 1024 * 1024) {
                Swal.fire({
                    icon: 'error',
                    title: 'File too large',
                    text: 'The PDF file must be smaller than 50MB.',
                    confirmButtonColor: '#10B981'
                });
                removePdfFile();
            }
        } else {
            pdfFileName.textContent = 'Click or drop PDF here';
            pdfFileName.classList.remove('text-emerald-700', 'font-semibold');
        }
    });

    function populateMaterialClasses(levelId, selectedClassId = null) {
        materialClass.innerHTML = '<option value="">Select a class</option>';
        const filteredClasses = allClassRooms.filter(c => c.level_id == levelId);
        filteredClasses.forEach(c => {
            const option = document.createElement('option');
            option.value = c.id;
            option.textContent = c.name;
            if (selectedClassId && c.id == selectedClassId) option.selected = true;
            materialClass.appendChild(option);
        });
        materialClass.disabled = filteredClasses.length === 0;
    }

    function populateMaterialSubjects(classRoomId, selectedSubjectId = null) {
        materialSubject.innerHTML = '<option value="">Select a subject</option>';
        const filteredSubjects = allSubjects.filter(s => s.class_room_id == classRoomId);
        filteredSubjects.forEach(s => {
            const option = document.createElement('option');
            option.value = s.id;
            option.textContent = s.name;
            if (selectedSubjectId && s.id == selectedSubjectId) option.selected = true;
            materialSubject.appendChild(option);
        });
        materialSubject.disabled = filteredSubjects.length === 0;
    }

    function populateMaterialTopics(subjectId, selectedTopicId = null) {
        materialTopic.innerHTML = '<option value="">Select a topic (optional)</option>';
        const filteredTopics = allTopics.filter(t => t.subject_id == subjectId);
        filteredTopics.forEach(t => {
            const option = document.createElement('option');
            option.value = t.id;
            option.textContent = t.name;
            if (selectedTopicId && t.id == selectedTopicId) option.selected = true;
            materialTopic.appendChild(option);
        });
        materialTopic.disabled = filteredTopics.length === 0;
    }

    materialLevel.addEventListener('change', function() {
        populateMaterialClasses(this.value);
        materialSubject.innerHTML = '<option value="">Select a class first</option>';
        materialSubject.disabled = true;
        materialTopic.innerHTML = '<option value="">Select a subject first</option>';
        materialTopic.disabled = true;
    });

    materialClass.addEventListener('change', function() {
        populateMaterialSubjects(this.value);
        materialTopic.innerHTML = '<option value="">Select a subject first</option>';
        materialTopic.disabled = true;
    });

    materialSubject.addEventListener('change', function() {
        populateMaterialTopics(this.value);
    });

    function setLoading(loading) {
        saveBtn.disabled = loading;
        saveSpinner.classList.toggle('hidden', !loading);
        saveText.textContent = loading ? 'Saving...' : (editingId ? 'Update ' + singularName : 'Save ' + singularName);
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
        return name ? name.charAt(0).toUpperCase() : 'M';
    }

    function addMaterialToTable(item, index = null) {
        const emptyRow = document.getElementById('emptyRow');
        if (emptyRow) emptyRow.remove();
        if (index === null) {
            index = materialsTable.querySelectorAll('tr[data-id]').length + 1;
        }

        const activeClass = item.is_active ? 'bg-emerald-50 text-emerald-700 border-emerald-100' : 'bg-red-50 text-red-700 border-red-100';
        const activeText = item.is_active ? 'Active' : 'Inactive';
        const subject = item.subject ? item.subject : null;
        const classRoom = subject && subject.class_room ? subject.class_room : (subject && subject.classRoom ? subject.classRoom : null);
        const level = classRoom && classRoom.level ? classRoom.level : null;
        const className = classRoom ? classRoom.name : 'Unknown';
        const levelName = level ? level.name : 'Unknown';
        const subjectName = subject ? subject.name : 'Unknown';
        const classRoomId = classRoom ? classRoom.id : '';
        const levelId = level ? level.id : '';
        const topicName = item.topic ? item.topic.name : 'No topic';
        const topicId = item.topic ? item.topic.id : 'null';
        const order = item.order ?? 0;
        const price = parseFloat(item.price) || 0;
        const isFree = item.is_free || price <= 0;
        const filePath = item.file_path ? escapeHtml(item.file_path) : '';
        const priceBadge = isFree
            ? `<span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-medium bg-emerald-50 text-emerald-700 border border-emerald-100">Free</span>`
            : `<span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-medium bg-amber-50 text-amber-700 border border-amber-100">TZS ${price.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</span>`;

        const row = document.createElement('tr');
        row.className = 'border-t border-gray-100 transition-colors animate-fade';
        row.setAttribute('data-id', item.id);
        row.setAttribute('data-subject-id', item.subject_id);
        row.setAttribute('data-class-id', classRoomId);
        row.setAttribute('data-level-id', levelId);
        row.setAttribute('data-name', item.title.toLowerCase());
        row.setAttribute('data-price', price);
        row.setAttribute('data-is-free', isFree ? '1' : '0');
        row.innerHTML = `
            <td class="px-6 py-3 text-xs text-gray-500">${index}</td>
            <td class="px-6 py-3">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-emerald-100 flex items-center justify-center text-emerald-700 font-bold text-xs">${getInitials(item.title)}</div>
                    <div>
                        <p class="text-sm font-semibold text-gray-900">${item.title}</p>
                        ${item.file_path ? `<a href="{{ url('/materials') }}/${materialType}/${item.id}/preview" target="_blank" class="text-[10px] text-red-600 hover:text-red-700 font-medium inline-flex items-center gap-1 mt-0.5"><svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8l-6-6z"/><path d="M14 2v6h6M16 13H8M16 17H8M10 9H8" stroke="white" stroke-width="2" stroke-linecap="round"/></svg>PDF</a>` : ''}
                    </div>
                </div>
            </td>
            <td class="px-6 py-3"><span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-medium bg-emerald-50 text-emerald-700 border border-emerald-100">${levelName}</span></td>
            <td class="px-6 py-3"><span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-medium bg-amber-50 text-amber-700 border border-amber-100">${className}</span></td>
            <td class="px-6 py-3"><span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-medium bg-sky-50 text-sky-700 border border-sky-100">${subjectName}</span></td>
            <td class="px-6 py-3"><span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-medium bg-purple-50 text-purple-700 border border-purple-100">${topicName}</span></td>
            <td class="px-6 py-3">${priceBadge}</td>
            <td class="px-6 py-3"><span class="inline-flex items-center px-2 py-1 rounded-md bg-gray-100 text-xs font-medium text-gray-700">${order}</span></td>
            <td class="px-6 py-3">
                <button onclick="toggleMaterialStatus(${item.id})" class="status-badge inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-medium border ${activeClass}">${activeText}</button>
            </td>
            <td class="px-6 py-3 text-right">
                <div class="flex items-center justify-end gap-2">
                    <button onclick="editMaterial(${item.id}, ${item.subject_id}, ${levelId}, ${classRoomId}, ${topicId}, '${escapeHtml(item.title)}', ${price}, ${order}, ${item.is_active ? 1 : 0}, ${isFree ? 1 : 0}, '${filePath}')" class="p-1.5 text-emerald-600 hover:bg-emerald-50 rounded-lg transition-colors" title="Edit">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.433-4.333A2.001 2.001 0 0119 10a2.001 2.001 0 01-.433 1.333L12.5 17.5l-4 1 1-4 6.067-6.167z"/></svg>
                    </button>
                    <button onclick="deleteMaterial(${item.id})" class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Delete">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </button>
                </div>
            </td>
        `;

        const existingRow = document.querySelector(`tr[data-id="${item.id}"]`);
        if (existingRow) {
            existingRow.replaceWith(row);
        } else {
            materialsTable.appendChild(row);
        }
        updateRowNumbers();
        updateItemsCount();
    }

    function updateRowNumbers() {
        const rows = materialsTable.querySelectorAll('tr[data-id]');
        rows.forEach((row, index) => {
            row.querySelector('td:first-child').textContent = index + 1;
        });
        if (rows.length === 0 && !document.getElementById('emptyRow')) {
            materialsTable.innerHTML = '<tr id="emptyRow"><td colspan="10" class="px-6 py-12 text-center text-gray-400 text-sm">No ' + singularName.toLowerCase() + 's found. Click "Add New ' + singularName + '" to create one.</td></tr>';
        }
    }

    function updateItemsCount() {
        const total = materialsTable.querySelectorAll('tr[data-id]').length;
        itemsCount.textContent = total + ' ' + singularName.toLowerCase() + (total !== 1 ? 's' : '');
    }

    materialForm.addEventListener('submit', function(e) {
        e.preventDefault();
        setLoading(true);

        const formData = new FormData(materialForm);
        formData.set('order', parseInt(formData.get('order')) || 0);
        formData.set('price', parseFloat(formData.get('price')) || 0);
        formData.set('is_free', formData.get('is_free') ? 1 : 0);
        formData.set('is_active', formData.get('is_active') ? 1 : 0);
        formData.delete('_token');

        const url = editingId
            ? `{{ url('materials') }}/${materialType}/${editingId}`
            : `{{ url('materials') }}/${materialType}`;
        const method = editingId ? 'POST' : 'POST';

        if (editingId) {
            formData.append('_method', 'PUT');
        }

        fetch(url, {
            method: method,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(async response => {
            const data = await response.json().catch(() => ({}));
            if (!response.ok) {
                if (response.status === 422 && data.errors) {
                    const firstErrors = Object.values(data.errors).flat();
                    const message = firstErrors.length ? firstErrors[0] : (data.message || 'Validation failed');
                    throw new Error(message);
                }
                throw new Error(data.message || 'Something went wrong');
            }
            return data;
        })
        .then(result => {
            setLoading(false);
            if (result.success) {
                showToast('success', result.message);
                addMaterialToTable(result.item);
                closeMaterialDrawer();
            } else {
                showToast('error', result.message || 'Something went wrong');
            }
        })
        .catch(error => {
            setLoading(false);
            showToast('error', error.message || 'Failed to save ' + singularName.toLowerCase() + '. Please try again.');
            console.error(error);
        });
    });

    function deleteMaterial(id) {
        Swal.fire({
            title: 'Delete ' + singularName + '?',
            text: 'This action cannot be undone.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`{{ url('materials') }}/${materialType}/${id}`, {
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
                                updateItemsCount();
                            }, 300);
                        }
                    } else {
                        showToast('error', result.message || 'Failed to delete');
                    }
                })
                .catch(error => {
                    showToast('error', 'Failed to delete ' + singularName.toLowerCase());
                    console.error(error);
                });
            }
        });
    }

    function toggleMaterialStatus(id) {
        const row = document.querySelector(`tr[data-id="${id}"]`);
        const badge = row.querySelector('.status-badge');
        const isActive = badge.textContent.trim() === 'Active';
        const subjectId = row.getAttribute('data-subject-id');
        const currentPrice = parseFloat(row.getAttribute('data-price')) || 0;
        const currentIsFree = row.getAttribute('data-is-free') === '1';

        fetch(`{{ url('materials') }}/${materialType}/${id}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                subject_id: parseInt(subjectId),
                title: row.querySelector('td:nth-child(2) p').textContent,
                price: currentPrice,
                is_free: currentIsFree ? 1 : 0,
                order: parseInt(row.querySelector('td:nth-child(8) span').textContent),
                is_active: isActive ? 0 : 1
            })
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                showToast('success', singularName + ' ' + (isActive ? 'deactivated' : 'activated'));
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

    function loadMaterials() {
        const term = materialSearch.value.trim();
        const levelId = levelFilter.value;
        const classRoomId = classFilter.value;
        const subjectId = subjectFilter.value;
        const topicId = topicFilter.value;

        const url = new URL('{{ url('materials') }}/' + materialType, window.location.origin);
        if (term) url.searchParams.append('search', term);
        if (levelId) url.searchParams.append('level_id', levelId);
        if (classRoomId) url.searchParams.append('class_room_id', classRoomId);
        if (subjectId) url.searchParams.append('subject_id', subjectId);
        if (topicId) url.searchParams.append('topic_id', topicId);

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
                renderMaterialsTable(result.items);
            } else {
                showToast('error', 'Failed to load ' + singularName.toLowerCase() + 's');
            }
        })
        .catch(error => {
            showToast('error', 'Error loading ' + singularName.toLowerCase() + 's');
            console.error(error);
        });
    }

    function renderMaterialsTable(items) {
        materialsTable.innerHTML = '';

        if (items.length === 0) {
            materialsTable.innerHTML = '<tr id="emptyRow"><td colspan="9" class="px-6 py-12 text-center text-gray-400 text-sm">No ' + singularName.toLowerCase() + 's found. Click "Add New ' + singularName + '" to create one.</td></tr>';
            updateItemsCount();
            return;
        }

        items.forEach((item, index) => {
            const activeClass = item.is_active ? 'bg-emerald-50 text-emerald-700 border-emerald-100' : 'bg-red-50 text-red-700 border-red-100';
            const activeText = item.is_active ? 'Active' : 'Inactive';
            const subject = item.subject ? item.subject : null;
            const classRoom = subject && subject.class_room ? subject.class_room : (subject && subject.classRoom ? subject.classRoom : null);
            const level = classRoom && classRoom.level ? classRoom.level : null;
            const className = classRoom ? classRoom.name : 'Unknown';
            const levelName = level ? level.name : 'Unknown';
            const subjectName = subject ? subject.name : 'Unknown';
            const classRoomId = classRoom ? classRoom.id : '';
            const levelId = level ? level.id : '';
            const topicName = item.topic ? item.topic.name : 'No topic';
            const topicId = item.topic ? item.topic.id : 'null';
            const order = item.order ?? 0;
            const filePath = item.file_path ? escapeHtml(item.file_path) : '';

            const row = document.createElement('tr');
            row.className = 'border-t border-gray-100 transition-colors animate-fade';
            row.style.animationDelay = `${index * 0.05}s`;
            row.setAttribute('data-id', item.id);
            row.setAttribute('data-subject-id', item.subject_id);
            row.setAttribute('data-class-id', classRoomId);
            row.setAttribute('data-level-id', levelId);
            row.setAttribute('data-name', item.title.toLowerCase());
            row.innerHTML = `
                <td class="px-6 py-3 text-xs text-gray-500">${index + 1}</td>
                <td class="px-6 py-3">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg bg-emerald-100 flex items-center justify-center text-emerald-700 font-bold text-xs">${getInitials(item.title)}</div>
                        <div>
                            <p class="text-sm font-semibold text-gray-900">${item.title}</p>
                            ${item.file_path ? `<a href="{{ url('/materials') }}/${materialType}/${item.id}/preview" target="_blank" class="text-[10px] text-red-600 hover:text-red-700 font-medium inline-flex items-center gap-1 mt-0.5"><svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8l-6-6z"/><path d="M14 2v6h6M16 13H8M16 17H8M10 9H8" stroke="white" stroke-width="2" stroke-linecap="round"/></svg>PDF</a>` : ''}
                        </div>
                    </div>
                </td>
                <td class="px-6 py-3"><span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-medium bg-emerald-50 text-emerald-700 border border-emerald-100">${levelName}</span></td>
                <td class="px-6 py-3"><span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-medium bg-amber-50 text-amber-700 border border-amber-100">${className}</span></td>
                <td class="px-6 py-3"><span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-medium bg-sky-50 text-sky-700 border border-sky-100">${subjectName}</span></td>
                <td class="px-6 py-3"><span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-medium bg-purple-50 text-purple-700 border border-purple-100">${topicName}</span></td>
                <td class="px-6 py-3"><span class="inline-flex items-center px-2 py-1 rounded-md bg-gray-100 text-xs font-medium text-gray-700">${order}</span></td>
                <td class="px-6 py-3">
                    <button onclick="toggleMaterialStatus(${item.id})" class="status-badge inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-medium border ${activeClass}">${activeText}</button>
                </td>
                <td class="px-6 py-3 text-right">
                    <div class="flex items-center justify-end gap-2">
                        <button onclick="editMaterial(${item.id}, ${item.subject_id}, ${levelId}, ${classRoomId}, ${topicId}, '${escapeHtml(item.title)}', ${order}, ${item.is_active ? 1 : 0}, '${filePath}')" class="p-1.5 text-emerald-600 hover:bg-emerald-50 rounded-lg transition-colors" title="Edit">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.433-4.333A2.001 2.001 0 0119 10a2.001 2.001 0 01-.433 1.333L12.5 17.5l-4 1 1-4 6.067-6.167z"/></svg>
                        </button>
                        <button onclick="deleteMaterial(${item.id})" class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Delete">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </div>
                </td>
            `;
            materialsTable.appendChild(row);
        });
        updateItemsCount();
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
        populateFilterTopics();
    }

    function populateFilterTopics() {
        const subjectId = subjectFilter.value;
        const selectedTopic = topicFilter.value;
        topicFilter.innerHTML = '<option value="">All Topics</option>';
        allTopics.filter(t => !subjectId || t.subject_id == subjectId).forEach(t => {
            const option = document.createElement('option');
            option.value = t.id;
            option.textContent = t.name + ' (' + (t.subject ? t.subject.name : '') + ')';
            option.setAttribute('data-subject-id', t.subject_id);
            if (t.id == selectedTopic) option.selected = true;
            topicFilter.appendChild(option);
        });
    }

    materialSearch.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(loadMaterials, 400);
    });

    levelFilter.addEventListener('change', function() {
        populateFilterClasses();
        loadMaterials();
    });

    classFilter.addEventListener('change', function() {
        populateFilterSubjects();
        loadMaterials();
    });

    subjectFilter.addEventListener('change', function() {
        populateFilterTopics();
        loadMaterials();
    });

    topicFilter.addEventListener('change', function() {
        loadMaterials();
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeMaterialDrawer();
    });
</script>
@endsection
