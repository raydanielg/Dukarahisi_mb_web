@extends('layouts.dashboard')

@section('title', 'Notes - ' . config('app.name', 'Elimu Store'))
@section('page_title', 'Manage Notes')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-800">Notes</h1>
        <a href="{{ route('admin.notes.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Add Note
        </a>
    </div>

    <div class="bg-white rounded-xl border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <form method="GET" class="flex gap-3">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search notes..." class="px-4 py-2 border border-gray-200 rounded-lg text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none w-64">
                <select name="status" class="px-4 py-2 border border-gray-200 rounded-lg text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none">
                    <option value="">All Status</option>
                    <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Published</option>
                    <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                </select>
                <button type="submit" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition-colors">Filter</button>
            </form>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead><tr class="text-left text-xs text-gray-500 bg-gray-50/50">
                    <th class="px-6 py-3 font-medium">Cover</th>
                    <th class="px-6 py-3 font-medium">Title</th>
                    <th class="px-6 py-3 font-medium">Subject</th>
                    <th class="px-6 py-3 font-medium">Price</th>
                    <th class="px-6 py-3 font-medium">Status</th>
                    <th class="px-6 py-3 font-medium">Actions</th>
                </tr></thead>
                <tbody>
                    @forelse($notes as $note)
                    <tr class="border-t border-gray-100 hover:bg-gray-50/50 transition-colors">
                        <td class="px-6 py-3">
                            <img src="{{ $note->cover_image_url }}" alt="{{ $note->title }}" class="w-10 h-10 rounded-lg object-cover">
                        </td>
                        <td class="px-6 py-3">
                            <p class="text-sm font-medium text-gray-900">{{ $note->title }}</p>
                            <p class="text-xs text-gray-500">{{ $note->downloads_count }} downloads</p>
                        </td>
                        <td class="px-6 py-3 text-xs text-gray-700">{{ $note->subject->name }} ({{ $note->subject->classRoom->name }})</td>
                        <td class="px-6 py-3 text-xs font-semibold text-gray-900">{{ $note->is_free ? 'Free' : 'TZS ' . number_format($note->price, 0) }}</td>
                        <td class="px-6 py-3">
                            @if($note->status === 'published')
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-emerald-50 text-emerald-700 border border-emerald-100">Published</span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-gray-100 text-gray-600 border border-gray-200">Draft</span>
                            @endif
                        </td>
                        <td class="px-6 py-3">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('admin.notes.edit', $note) }}" class="text-emerald-600 hover:text-emerald-700 text-xs font-medium">Edit</a>
                                <form action="{{ route('admin.notes.destroy', $note) }}" method="POST" data-confirm="Are you sure you want to delete this note?" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-700 text-xs font-medium">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="px-6 py-8 text-center text-gray-400 text-xs">No notes found</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $notes->links() }}
        </div>
    </div>
</div>
@endsection
