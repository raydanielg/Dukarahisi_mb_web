@extends('layouts.dashboard')

@section('title', 'My Profile - ' . config('app.name', 'Elimu Store'))
@section('page_title', 'My Profile')

@section('content')
<div class="space-y-6">
    {{-- Header / Profile Card --}}
    <div class="hover-lift dashboard-card bg-white rounded-xl border border-gray-100 overflow-hidden">
        <div class="h-32 bg-gradient-to-r from-emerald-500 to-emerald-600"></div>
        <div class="px-6 pb-6">
            <div class="flex flex-col sm:flex-row items-start sm:items-end gap-4 -mt-12">
                <div class="relative group">
                    <div class="w-24 h-24 rounded-2xl bg-white p-1 shadow-lg">
                        <div class="w-full h-full rounded-xl bg-gradient-to-br from-emerald-100 to-emerald-200 flex items-center justify-center text-emerald-700 text-3xl font-bold overflow-hidden">
                            @if($user->avatar)
                                <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
                            @else
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            @endif
                        </div>
                    </div>
                    <button onclick="document.getElementById('avatarInput').click()" class="absolute -bottom-2 -right-2 p-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg shadow-md transition-colors" title="Change Avatar">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </button>
                    <input type="file" id="avatarInput" name="avatar" accept="image/*" class="hidden" onchange="uploadAvatar(this)">
                </div>
                <div class="flex-1 pb-1">
                    <h2 class="text-xl font-bold text-gray-800" id="profileName">{{ $user->name }}</h2>
                    <p class="text-sm text-gray-500">{{ ucfirst($user->role) }} Administrator</p>
                </div>
                <div class="flex gap-2 pb-1">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-100">
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Active
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Edit Profile --}}
        <div class="lg:col-span-2 space-y-6">
            <div class="hover-lift bg-white rounded-xl border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    <h3 class="text-sm font-semibold text-gray-900 section-header">Personal Information</h3>
                </div>
                <form id="profileForm" class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                    @csrf
                    <div class="drawer-form-group !mb-0">
                        <label for="name">Full Name</label>
                        <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                    </div>
                    <div class="drawer-form-group !mb-0">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                    </div>
                    <div class="drawer-form-group !mb-0">
                        <label for="phone_number">Phone Number</label>
                        <input type="text" id="phone_number" name="phone_number" value="{{ old('phone_number', $user->phone_number) }}">
                    </div>
                    <div class="drawer-form-group !mb-0">
                        <label for="role">Role</label>
                        <input type="text" id="role" value="{{ ucfirst($user->role) }}" disabled class="bg-gray-50 cursor-not-allowed">
                    </div>
                    <div class="md:col-span-2 flex items-center justify-end pt-2">
                        <button type="submit" id="saveProfileBtn" class="dashboard-btn inline-flex items-center gap-2 px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg shadow-lg hover:shadow-xl transition-all">
                            <svg id="saveProfileSpinner" class="w-4 h-4 animate-spin hidden" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            <span id="saveProfileText">Save Changes</span>
                        </button>
                    </div>
                </form>
            </div>

            {{-- Change Password --}}
            <div class="hover-lift bg-white rounded-xl border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-2">
                    <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    <h3 class="text-sm font-semibold text-gray-900 section-header">Change Password</h3>
                </div>
                <form id="passwordForm" class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                    @csrf
                    <div class="drawer-form-group !mb-0">
                        <label for="current_password">Current Password</label>
                        <input type="password" id="current_password" name="current_password" required>
                    </div>
                    <div class="drawer-form-group !mb-0">
                        <label for="password">New Password</label>
                        <input type="password" id="password" name="password" required>
                    </div>
                    <div class="drawer-form-group !mb-0">
                        <label for="password_confirmation">Confirm New Password</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" required>
                    </div>
                    <div class="md:col-span-2 flex items-center justify-end pt-2">
                        <button type="submit" id="savePasswordBtn" class="inline-flex items-center gap-2 px-6 py-2.5 bg-amber-600 hover:bg-amber-700 text-white text-sm font-semibold rounded-lg shadow-lg hover:shadow-xl transition-all">
                            <svg id="savePasswordSpinner" class="w-4 h-4 animate-spin hidden" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            <span id="savePasswordText">Update Password</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Account Info --}}
        <div class="space-y-6">
            <div class="hover-lift bg-white rounded-xl border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-2">
                    <svg class="w-4 h-4 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <h3 class="text-sm font-semibold text-gray-900 section-header">Account Info</h3>
                </div>
                <div class="p-6 space-y-4">
                    <div class="flex justify-between items-center py-2 border-b border-gray-50">
                        <span class="text-xs text-gray-500">Member Since</span>
                        <span class="text-sm font-medium text-gray-900">{{ $user->created_at->format('M d, Y') }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-gray-50">
                        <span class="text-xs text-gray-500">Email Verified</span>
                        <span class="text-sm font-medium text-gray-900">{{ $user->email_verified_at ? 'Yes' : 'No' }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-gray-50">
                        <span class="text-xs text-gray-500">Phone Verified</span>
                        <span class="text-sm font-medium text-gray-900">{{ $user->phone_verified_at ? 'Yes' : 'No' }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2">
                        <span class="text-xs text-gray-500">Last Updated</span>
                        <span class="text-sm font-medium text-gray-900">{{ $user->updated_at->format('M d, Y') }}</span>
                    </div>
                </div>
            </div>

            <div class="hover-lift bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-xl p-6 text-white">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 rounded-lg bg-white/20 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <h3 class="font-semibold">Account Status</h3>
                </div>
                <p class="text-sm text-emerald-100">Your account is active and you have full admin access to manage the platform.</p>
            </div>
        </div>
    </div>
</div>

<script>
    const profileForm = document.getElementById('profileForm');
    const passwordForm = document.getElementById('passwordForm');
    const avatarInput = document.getElementById('avatarInput');
    const profileName = document.getElementById('profileName');

    function showToast(type, message) {
        const Toast = Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 4000, timerProgressBar: true });
        Toast.fire({ icon: type, title: message });
    }

    function setButtonLoading(btn, spinner, text, loading, label) {
        btn.disabled = loading;
        spinner.classList.toggle('hidden', !loading);
        text.textContent = loading ? 'Saving...' : label;
    }

    profileForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const btn = document.getElementById('saveProfileBtn');
        const spinner = document.getElementById('saveProfileSpinner');
        const text = document.getElementById('saveProfileText');
        setButtonLoading(btn, spinner, text, true, 'Save Changes');

        const formData = new FormData(profileForm);
        const data = {
            name: formData.get('name'),
            email: formData.get('email'),
            phone_number: formData.get('phone_number'),
            _token: formData.get('_token')
        };

        fetch('{{ route('admin.profile.update') }}', {
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
            setButtonLoading(btn, spinner, text, false, 'Save Changes');
            if (result.success) {
                showToast('success', result.message);
                profileName.textContent = result.user.name;
            } else {
                showToast('error', result.message || 'Failed to update profile');
            }
        })
        .catch(error => {
            setButtonLoading(btn, spinner, text, false, 'Save Changes');
            showToast('error', 'Failed to update profile. Please try again.');
            console.error(error);
        });
    });

    passwordForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const btn = document.getElementById('savePasswordBtn');
        const spinner = document.getElementById('savePasswordSpinner');
        const text = document.getElementById('savePasswordText');
        setButtonLoading(btn, spinner, text, true, 'Update Password');

        const formData = new FormData(passwordForm);
        const data = {
            current_password: formData.get('current_password'),
            password: formData.get('password'),
            password_confirmation: formData.get('password_confirmation'),
            _token: formData.get('_token')
        };

        fetch('{{ route('admin.profile.password') }}', {
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
            setButtonLoading(btn, spinner, text, false, 'Update Password');
            if (result.success) {
                showToast('success', result.message);
                passwordForm.reset();
            } else {
                showToast('error', result.message || 'Failed to update password');
            }
        })
        .catch(error => {
            setButtonLoading(btn, spinner, text, false, 'Update Password');
            showToast('error', 'Failed to update password. Please try again.');
            console.error(error);
        });
    });

    function uploadAvatar(input) {
        if (!input.files || !input.files[0]) return;

        const formData = new FormData();
        formData.append('avatar', input.files[0]);
        formData.append('_token', '{{ csrf_token() }}');

        Swal.fire({
            title: 'Uploading...',
            allowOutsideClick: false,
            didOpen: () => { Swal.showLoading(); }
        });

        fetch('{{ route('admin.profile.avatar') }}', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
            body: formData
        })
        .then(response => response.json())
        .then(result => {
            Swal.close();
            if (result.success) {
                showToast('success', result.message);
                location.reload();
            } else {
                showToast('error', result.message || 'Failed to upload avatar');
            }
        })
        .catch(error => {
            Swal.close();
            showToast('error', 'Failed to upload avatar. Please try again.');
            console.error(error);
        });
    }
</script>
@endsection
