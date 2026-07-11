@extends('layouts.dashboard')

@section('title', 'Dashboard - ' . config('app.name', 'Dukarahisi'))

@section('page_title', 'Dashboard')

@section('content')
<div class="space-y-6">
    {{-- Welcome Section --}}
    <div class="bg-gradient-to-r from-emerald-600 to-emerald-700 rounded-2xl p-8 text-white shadow-lg">
        <h1 class="text-3xl font-extrabold mb-2">Karibu, {{ auth()->user()->name }}!</h1>
        <p class="text-emerald-100">Umeingia kwenye mfumo wa Dukarahisi - Notes, Fresh Content, na Mitihani.</p>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-6">
        {{-- Notes Card --}}
        <div class="bg-gradient-to-br from-emerald-600 to-emerald-700 rounded-xl border border-emerald-500 p-4 text-white relative overflow-hidden hover:shadow-lg transition-shadow">
            <div class="absolute top-0 right-0 w-16 h-16 bg-white/10 rounded-full -mr-8 -mt-8"></div>
            <div class="relative z-10">
                <div class="flex items-start justify-between mb-2">
                    <span class="text-[10px] font-medium text-emerald-100">Notes</span>
                    <svg class="w-4 h-4 text-emerald-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <p class="text-xl font-bold tracking-tight text-white">0</p>
                <p class="text-[10px] text-emerald-200 font-medium mt-1">Notes zote</p>
            </div>
        </div>

        {{-- Fresh Content Card --}}
        <div class="bg-gradient-to-br from-amber-400 to-amber-500 rounded-xl border border-amber-300 p-4 text-white relative overflow-hidden hover:shadow-lg transition-shadow">
            <div class="absolute top-0 right-0 w-16 h-16 bg-white/10 rounded-full -mr-8 -mt-8"></div>
            <div class="relative z-10">
                <div class="flex items-start justify-between mb-2">
                    <span class="text-[10px] font-medium text-amber-50">Fresh Content</span>
                    <svg class="w-4 h-4 text-amber-100" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                </div>
                <p class="text-xl font-bold tracking-tight text-white">0</p>
                <p class="text-[10px] text-amber-100 font-medium mt-1">Mada mpya</p>
            </div>
        </div>

        {{-- Exams Card --}}
        <div class="bg-gradient-to-br from-sky-500 to-sky-600 rounded-xl border border-sky-400 p-4 text-white relative overflow-hidden hover:shadow-lg transition-shadow">
            <div class="absolute top-0 right-0 w-16 h-16 bg-white/10 rounded-full -mr-8 -mt-8"></div>
            <div class="relative z-10">
                <div class="flex items-start justify-between mb-2">
                    <span class="text-[10px] font-medium text-sky-100">Mitihani</span>
                    <svg class="w-4 h-4 text-sky-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                </div>
                <p class="text-xl font-bold tracking-tight text-white">0</p>
                <p class="text-[10px] text-sky-200 font-medium mt-1">Mitihani yote</p>
            </div>
        </div>

        {{-- Users Card --}}
        <div class="bg-gradient-to-br from-violet-500 to-violet-600 rounded-xl border border-violet-400 p-4 text-white relative overflow-hidden hover:shadow-lg transition-shadow">
            <div class="absolute top-0 right-0 w-16 h-16 bg-white/10 rounded-full -mr-8 -mt-8"></div>
            <div class="relative z-10">
                <div class="flex items-start justify-between mb-2">
                    <span class="text-[10px] font-medium text-violet-100">Watumiaji</span>
                    <svg class="w-4 h-4 text-violet-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                </div>
                <p class="text-xl font-bold tracking-tight text-white">{{ \App\Models\User::count() }}</p>
                <p class="text-[10px] text-violet-200 font-medium mt-1">Watumiaji wote</p>
            </div>
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h2 class="text-lg font-bold text-gray-800 mb-4">Vitendo vya Haraka</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <a href="#" class="flex items-center gap-3 p-4 rounded-lg border border-gray-200 hover:border-emerald-500 hover:bg-emerald-50 transition-all">
                <div class="w-10 h-10 rounded-lg bg-emerald-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                </div>
                <span class="font-medium text-gray-700">Ongeza Note Mpya</span>
            </a>
            <a href="#" class="flex items-center gap-3 p-4 rounded-lg border border-gray-200 hover:border-gold-500 hover:bg-gold-50 transition-all">
                <div class="w-10 h-10 rounded-lg bg-gold-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-gold-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                </div>
                <span class="font-medium text-gray-700">Ongeza Content Mpya</span>
            </a>
            <a href="#" class="flex items-center gap-3 p-4 rounded-lg border border-gray-200 hover:border-blue-500 hover:bg-blue-50 transition-all">
                <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                </div>
                <span class="font-medium text-gray-700">Ongeza Mtihani Mpya</span>
            </a>
        </div>
    </div>

    {{-- Recent Activity --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h2 class="text-lg font-bold text-gray-800 mb-4">Shughuli za Hivi Karibuni</h2>
        <div class="space-y-4">
            <div class="flex items-center gap-4 p-4 rounded-lg bg-gray-50">
                <div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                </div>
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-800">Hakuna shughuli za hivi karibuni</p>
                    <p class="text-xs text-gray-500">Anza kutumia mfumo</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
