@extends('layouts.dashboard')

@section('title', 'Settings - ' . config('app.name', 'Elimu Store'))
@section('page_title', 'System Settings')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-xl border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h1 class="text-lg font-bold text-gray-800">Maintenance Mode</h1>
        </div>
        <form action="{{ route('admin.settings.update') }}" method="POST" class="p-6 space-y-5">
            @csrf

            <div class="flex items-center gap-3">
                <input type="checkbox" name="maintenance_mode" value="1" {{ old('maintenance_mode', $maintenanceMode) ? 'checked' : '' }} id="maintenance_mode" class="w-5 h-5 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                <div>
                    <label for="maintenance_mode" class="text-sm font-semibold text-gray-700">Enable Maintenance Mode</label>
                    <p class="text-xs text-gray-500">When enabled, customers will not be able to access the API or web app.</p>
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Maintenance Message</label>
                <textarea name="maintenance_message" rows="3" class="w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none text-sm">{{ old('maintenance_message', $maintenanceMessage) }}</textarea>
            </div>

            <div class="flex items-center gap-3 pt-4">
                <button type="submit" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg transition-colors">Save Settings</button>
            </div>
        </form>
    </div>
</div>
@endsection
