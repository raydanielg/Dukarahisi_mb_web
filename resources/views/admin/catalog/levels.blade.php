@extends('layouts.dashboard')

@section('title', 'Levels - ' . config('app.name', 'Elimu Store'))
@section('page_title', 'Manage Levels')

@section('content')
<div class="space-y-6">
    <div class="max-w-2xl mx-auto bg-white rounded-xl border border-gray-100 p-6">
        <h2 class="text-lg font-bold text-gray-800 mb-4">Add New Level</h2>
        <form action="{{ route('admin.catalog.levels.store') }}" method="POST" class="flex gap-3">
            @csrf
            <input type="text" name="name" placeholder="Level name (e.g. Primary School)" required class="flex-1 px-4 py-2.5 rounded-lg border border-gray-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none text-sm">
            <input type="number" name="order" placeholder="Order" min="0" value="0" required class="w-24 px-4 py-2.5 rounded-lg border border-gray-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none text-sm">
            <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg transition-colors">Add</button>
        </form>
    </div>

    <div class="bg-white rounded-xl border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="text-sm font-semibold text-gray-900">Levels</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead><tr class="text-left text-xs text-gray-500 bg-gray-50/50">
                    <th class="px-6 py-3 font-medium">Name</th>
                    <th class="px-6 py-3 font-medium">Order</th>
                    <th class="px-6 py-3 font-medium">Status</th>
                    <th class="px-6 py-3 font-medium">Actions</th>
                </tr></thead>
                <tbody>
                    @forelse($levels as $level)
                    <tr class="border-t border-gray-100 hover:bg-gray-50/50 transition-colors">
                        <form action="{{ route('admin.catalog.levels.update', $level) }}" method="POST" class="contents">
                            @csrf
                            @method('PUT')
                            <td class="px-6 py-3"><input type="text" name="name" value="{{ $level->name }}" required class="px-3 py-1.5 rounded border border-gray-200 text-sm w-full"></td>
                            <td class="px-6 py-3"><input type="number" name="order" value="{{ $level->order }}" min="0" required class="px-3 py-1.5 rounded border border-gray-200 text-sm w-20"></td>
                            <td class="px-6 py-3">
                                <select name="is_active" class="px-3 py-1.5 rounded border border-gray-200 text-sm">
                                    <option value="1" {{ $level->is_active ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ !$level->is_active ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </td>
                            <td class="px-6 py-3">
                                <div class="flex items-center gap-2">
                                    <button type="submit" class="text-emerald-600 hover:text-emerald-700 text-xs font-medium">Save</button>
                                    <form action="{{ route('admin.catalog.levels.destroy', $level) }}" method="POST" data-confirm="Are you sure?" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-700 text-xs font-medium">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </form>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="px-6 py-8 text-center text-gray-400 text-xs">No levels found</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
