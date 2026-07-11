@extends('layouts.dashboard')

@section('title', 'Settings - ' . config('app.name', 'Elimu Store'))
@section('page_title', 'System Settings')

@section('content')
<div class="space-y-6">
    {{-- Header Card --}}
    <div class="hover-lift dashboard-card bg-white rounded-xl border border-gray-100 p-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-600 flex items-center justify-center text-white shadow-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-gray-800">System Settings</h2>
                    <p class="text-sm text-gray-500 mt-1">Manage application settings and preferences</p>
                </div>
            </div>
            <button type="submit" form="settingsForm" id="saveBtn" class="dashboard-btn inline-flex items-center gap-2 px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg shadow-lg hover:shadow-xl transition-all">
                <svg id="saveSpinner" class="w-4 h-4 animate-spin hidden" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                <span id="saveText">Save Settings</span>
            </button>
        </div>
    </div>

    <form id="settingsForm" action="{{ route('admin.settings.update') }}" method="POST" class="space-y-6">
        @csrf

        {{-- General Settings --}}
        <div class="hover-lift bg-white rounded-xl border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <h3 class="text-sm font-semibold text-gray-900 section-header">General Information</h3>
            </div>
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="drawer-form-group !mb-0">
                    <label for="app_name">App / Website Name</label>
                    <input type="text" id="app_name" name="app_name" value="{{ old('app_name', $settings['app_name']) }}" required>
                </div>
                <div class="drawer-form-group !mb-0">
                    <label for="app_currency">Currency</label>
                    <input type="text" id="app_currency" name="app_currency" value="{{ old('app_currency', $settings['app_currency']) }}" required>
                </div>
                <div class="drawer-form-group !mb-0">
                    <label for="contact_email">Contact Email</label>
                    <input type="email" id="contact_email" name="contact_email" value="{{ old('contact_email', $settings['contact_email']) }}">
                </div>
                <div class="drawer-form-group !mb-0">
                    <label for="contact_phone">Contact Phone</label>
                    <input type="text" id="contact_phone" name="contact_phone" value="{{ old('contact_phone', $settings['contact_phone']) }}">
                </div>
                <div class="drawer-form-group !mb-0">
                    <label for="support_whatsapp">Support WhatsApp</label>
                    <input type="text" id="support_whatsapp" name="support_whatsapp" value="{{ old('support_whatsapp', $settings['support_whatsapp']) }}">
                </div>
                <div class="drawer-form-group !mb-0">
                    <label for="footer_text">Footer Text</label>
                    <input type="text" id="footer_text" name="footer_text" value="{{ old('footer_text', $settings['footer_text']) }}">
                </div>
                <div class="drawer-form-group !mb-0 md:col-span-2">
                    <label for="seo_description">SEO / Meta Description</label>
                    <textarea id="seo_description" name="seo_description" rows="3">{{ old('seo_description', $settings['seo_description']) }}</textarea>
                </div>
            </div>
        </div>

        {{-- Maintenance Mode --}}
        <div class="hover-lift bg-white rounded-xl border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-2">
                <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <h3 class="text-sm font-semibold text-gray-900 section-header">Maintenance Mode</h3>
            </div>
            <div class="p-6 space-y-5">
                <label class="flex items-start gap-3 p-4 rounded-lg border border-gray-200 cursor-pointer hover:bg-gray-50 transition-colors">
                    <input type="checkbox" name="maintenance_mode" value="1" {{ old('maintenance_mode', $settings['maintenance_mode']) ? 'checked' : '' }} id="maintenance_mode" class="w-5 h-5 mt-0.5 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                    <div>
                        <span class="text-sm font-semibold text-gray-700 block">Enable Maintenance Mode</span>
                        <span class="text-xs text-gray-500">When enabled, customers will not be able to access the API or web app.</span>
                    </div>
                </label>
                <div class="drawer-form-group !mb-0">
                    <label for="maintenance_message">Maintenance Message</label>
                    <textarea id="maintenance_message" name="maintenance_message" rows="3">{{ old('maintenance_message', $settings['maintenance_message']) }}</textarea>
                </div>
            </div>
        </div>

        {{-- Clear Cache --}}
        <div class="hover-lift bg-white rounded-xl border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-2">
                <svg class="w-4 h-4 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                <h3 class="text-sm font-semibold text-gray-900 section-header">Cache & Performance</h3>
            </div>
            <div class="p-6 flex flex-col sm:flex-row items-center justify-between gap-4">
                <div>
                    <p class="text-sm font-semibold text-gray-700">Clear Application Cache</p>
                    <p class="text-xs text-gray-500 mt-1">Clear config, route, view, and data cache. Use this after making changes.</p>
                </div>
                <button type="button" onclick="clearCache()" id="clearCacheBtn" class="inline-flex items-center gap-2 px-5 py-2.5 bg-sky-600 hover:bg-sky-700 text-white text-sm font-semibold rounded-lg shadow-lg hover:shadow-xl transition-all">
                    <svg id="cacheSpinner" class="w-4 h-4 animate-spin hidden" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    <span id="cacheText">Clear Cache</span>
                </button>
            </div>
        </div>
    </form>
</div>

<script>
    const settingsForm = document.getElementById('settingsForm');
    const saveBtn = document.getElementById('saveBtn');
    const saveSpinner = document.getElementById('saveSpinner');
    const saveText = document.getElementById('saveText');
    const clearCacheBtn = document.getElementById('clearCacheBtn');
    const cacheSpinner = document.getElementById('cacheSpinner');
    const cacheText = document.getElementById('cacheText');

    function showToast(type, message) {
        const Toast = Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 4000, timerProgressBar: true });
        Toast.fire({ icon: type, title: message });
    }

    function setSaving(saving) {
        saveBtn.disabled = saving;
        saveSpinner.classList.toggle('hidden', !saving);
        saveText.textContent = saving ? 'Saving...' : 'Save Settings';
    }

    function setClearing(clearing) {
        clearCacheBtn.disabled = clearing;
        cacheSpinner.classList.toggle('hidden', !clearing);
        cacheText.textContent = clearing ? 'Clearing...' : 'Clear Cache';
    }

    settingsForm.addEventListener('submit', function(e) {
        e.preventDefault();
        setSaving(true);
        const formData = new FormData(settingsForm);
        const data = {
            app_name: formData.get('app_name'),
            app_currency: formData.get('app_currency'),
            contact_email: formData.get('contact_email'),
            contact_phone: formData.get('contact_phone'),
            support_whatsapp: formData.get('support_whatsapp'),
            footer_text: formData.get('footer_text'),
            seo_description: formData.get('seo_description'),
            maintenance_mode: formData.get('maintenance_mode') ? 1 : 0,
            maintenance_message: formData.get('maintenance_message'),
            _token: formData.get('_token')
        };

        fetch('{{ route('admin.settings.update') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': data._token,
                'Accept': 'application/json'
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(result => {
            setSaving(false);
            if (result.success) {
                showToast('success', result.message);
            } else {
                showToast('error', result.message || 'Failed to save settings');
            }
        })
        .catch(error => {
            setSaving(false);
            showToast('error', 'Failed to save settings. Please try again.');
            console.error(error);
        });
    });

    function clearCache() {
        Swal.fire({
            title: 'Clear Cache?',
            text: 'This will clear application, config, route, and view cache.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#0284c7',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, clear it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                setClearing(true);
                fetch('{{ route('admin.settings.clear-cache') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(result => {
                    setClearing(false);
                    if (result.success) {
                        showToast('success', result.message);
                    } else {
                        showToast('error', result.message || 'Failed to clear cache');
                    }
                })
                .catch(error => {
                    setClearing(false);
                    showToast('error', 'Failed to clear cache. Please try again.');
                    console.error(error);
                });
            }
        });
    }
</script>
@endsection
