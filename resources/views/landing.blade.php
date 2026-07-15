<!doctype html>
<html lang="sw">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dukarahisi - Subject Notes, Exams, Lesson Plans</title>
    <meta name="description" content="Pata lesson plans, notes, mitihani, schemes na logbooks kwa shule za msingi na sekondari Tanzania.">
    <meta name="keywords" content="subject notes, exams, subjects, topical exams, mock exams, necta exams, regional exams, lesson plans, schemes, logbooks">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#f0f9ff',
                            100: '#e0f2fe',
                            500: '#0ea5e9',
                            600: '#0284c7',
                            700: '#0369a1',
                            800: '#075985',
                            900: '#0c4a6e',
                        }
                    },
                    fontFamily: { sans: ['Inter', 'sans-serif'] }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #F7F7F9; }
        .subject-btn {
            transition: all 0.2s ease;
        }
        .subject-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(14, 165, 233, 0.15);
        }
        .class-card {
            transition: all 0.25s ease;
        }
        .class-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 32px rgba(76, 78, 100, 0.12);
        }
        .gradient-text {
            background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .nav-dropdown:hover .dropdown-menu {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }
        .dropdown-menu {
            opacity: 0;
            visibility: hidden;
            transform: translateY(8px);
            transition: all 0.2s ease;
        }
        .resource-card {
            transition: all 0.25s ease;
        }
        .resource-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 20px 40px rgba(14, 165, 233, 0.15);
        }
        .level-card {
            transition: all 0.2s ease;
        }
        .level-card:hover {
            background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);
            color: white;
        }
        .level-card:hover .level-icon {
            background: rgba(255,255,255,0.2);
        }
    </style>
</head>
<body class="text-[#4C4E64] bg-[#F7F7F9]">

    <!-- Navbar -->
    <nav class="sticky top-0 z-50 bg-white/90 backdrop-blur-md border-b border-gray-200/60 shadow-sm">
        <div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <a href="/" class="flex items-center gap-2 no-underline">
                    <div class="w-9 h-9 rounded-lg bg-gradient-to-br from-brand-500 to-brand-700 flex items-center justify-center text-white font-bold text-lg">D</div>
                    <span class="text-xl font-bold text-[#4C4E64]">Dukarahisi</span>
                </a>
                <div class="hidden xl:flex items-center gap-1">
                    @php
                    $menu = [
                        ['title' => 'Lesson Plans', 'slug' => 'lesson-plans', 'icon' => 'M19 3h-1V1h-2v2H8V1H6v2H5a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2V5a2 2 0 00-2-2m0 16H5V10h14v9M7 12h5v5H7v-5z', 'color' => 'bg-blue-50 text-blue-600'],
                        ['title' => 'Schemes', 'slug' => 'schemes', 'icon' => 'M6 2a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8l-6-6H6m0 2h7v5h5v11H6V4m2 8v2h8v-2H8m0 4v2h5v-2H8z', 'color' => 'bg-emerald-50 text-emerald-600'],
                        ['title' => 'Logbooks', 'slug' => 'logbooks', 'icon' => 'M17 4v6l-2-2l-2 2V4H9v16h10V4h-2M7 7V5h2V4a2 2 0 0 1 2-2h12c1.05 0 2 .95 2 2v16c0 1.05-.95 2-2 2H7c-1.05 0-2-.95-2-2v-1H3v-2h2v-4H3v-2h2V7H3m2-2v2h2V5H5m0 14h2v-2H5v2m0-6h2v-2H5v2z', 'color' => 'bg-violet-50 text-violet-600'],
                        ['title' => 'Exams', 'slug' => 'exams', 'icon' => 'M19 3h-4.18C14.25 1.44 12.53.64 11 1.2c-.86.3-1.5.96-1.82 1.8H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V5a2 2 0 0 0-2-2m-7 0a1 1 0 0 1 1 1a1 1 0 0 1-1 1a1 1 0 0 1-1-1a1 1 0 0 1 1-1M7 7h10V5h2v14H5V5h2v2m10 4H7V9h10v2m-2 4H7v-2h8v2z', 'color' => 'bg-amber-50 text-amber-600'],
                        ['title' => 'Subject Notes', 'slug' => 'notes', 'icon' => 'M19 2l-5 4.5v11l5-4.5V2M6.5 5C4.55 5 2.45 5.4 1 6.5v14.66c0 .25.25.5.5.5.1 0 .15-.07.25-.07 1.35-.65 3.3-1.09 4.75-1.09 1.95 0 4.05.4 5.5 1.5 1.35-.85 3.8-1.5 5.5-1.5 1.65 0 3.35.31 4.75 1.06.1.05.15.03.25.03.25 0 .5-.25.5-.5V6.5c-.6-.45-1.25-.75-2-1V19c-1.1-.35-2.3-.5-3.5-.5-1.7 0-4.15.65-5.5 1.5V6.5C10.55 5.4 8.45 5 6.5 5z', 'color' => 'bg-rose-50 text-rose-600'],
                        ['title' => 'Books', 'slug' => 'books', 'icon' => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253', 'color' => 'bg-indigo-50 text-indigo-600'],
                    ];
                    @endphp

                    @foreach($menu as $item)
                    <div class="nav-dropdown relative py-4">
                        <button class="flex items-center gap-2 px-4 py-2 text-sm font-bold text-[#4C4E64] hover:text-brand-600 transition rounded-lg hover:bg-gray-50">
                            <div class="w-7 h-7 rounded-lg {{ $item['color'] }} flex items-center justify-center">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="{{ $item['icon'] }}"></path></svg>
                            </div>
                            {{ $item['title'] }}
                            <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <div class="dropdown-menu absolute left-0 top-full w-[720px] bg-white rounded-2xl shadow-2xl border border-gray-100 p-6 z-50">
                            <div class="flex items-center gap-3 mb-5 pb-4 border-b border-gray-100">
                                <div class="w-11 h-11 rounded-xl {{ $item['color'] }} flex items-center justify-center">
                                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="{{ $item['icon'] }}"></path></svg>
                                </div>
                                <div>
                                    <h4 class="font-black text-[#4C4E64]">{{ $item['title'] }}</h4>
                                    <p class="text-xs text-[#4C4E64]/60">Choose a level and class to start</p>
                                </div>
                            </div>
                            <div class="grid grid-cols-3 gap-5">
                                @foreach($levels as $level)
                                @php
                                $levelIsPrimary = stripos($level->name, 'primary') !== false;
                                $levelMediums = $levelIsPrimary ? $level->classRooms->groupBy(fn($c) => $c->medium ?: 'General') : collect();
                                @endphp
                                <div>
                                    <h5 class="text-xs font-black text-brand-600 uppercase tracking-wider mb-3">{{ $level->name }}</h5>
                                    @if($levelIsPrimary && $levelMediums->count() > 1)
                                        @foreach($levelMediums as $mediumName => $mediumClasses)
                                        <div class="mb-3">
                                            <span class="text-[10px] font-bold text-[#4C4E64]/50 uppercase block mb-1.5">{{ ucfirst($mediumName) }} Medium</span>
                                            <div class="flex flex-col gap-1.5">
                                                @foreach($mediumClasses as $classRoom)
                                                <a href="{{ route('materials.type', ['type' => $item['slug'], 'level' => $level->id, 'class' => $classRoom->id]) }}" class="group flex items-center gap-2 p-2 rounded-lg hover:bg-brand-50 transition">
                                                    <div class="w-6 h-6 rounded bg-gray-100 text-[#4C4E64] text-[10px] font-bold flex items-center justify-center group-hover:bg-brand-600 group-hover:text-white transition shrink-0">
                                                        {{ strtoupper(substr($level->name, 0, 1)) }}{{ substr($classRoom->name, -1) }}
                                                    </div>
                                                    <span class="text-xs font-semibold text-[#4C4E64] group-hover:text-brand-600 truncate">{{ $classRoom->name }}</span>
                                                </a>
                                                @endforeach
                                            </div>
                                        </div>
                                        @endforeach
                                    @else
                                    <div class="flex flex-col gap-1.5">
                                        @forelse($level->classRooms as $classRoom)
                                        <a href="{{ route('materials.type', ['type' => $item['slug'], 'level' => $level->id, 'class' => $classRoom->id]) }}" class="group flex items-center gap-2 p-2 rounded-lg hover:bg-brand-50 transition">
                                            <div class="w-6 h-6 rounded bg-gray-100 text-[#4C4E64] text-[10px] font-bold flex items-center justify-center group-hover:bg-brand-600 group-hover:text-white transition shrink-0">
                                                {{ strtoupper(substr($level->name, 0, 1)) }}{{ substr($classRoom->name, -1) }}
                                            </div>
                                            <span class="text-xs font-semibold text-[#4C4E64] group-hover:text-brand-600 truncate">{{ $classRoom->name }}</span>
                                        </a>
                                        @empty
                                        <span class="text-xs text-gray-400">No classes</span>
                                        @endforelse
                                    </div>
                                    @endif
                                </div>
                                @endforeach
                            </div>
                            <div class="mt-5 pt-4 border-t border-gray-100">
                                <a href="{{ route('materials.type', $item['slug']) }}" class="inline-flex items-center text-sm font-bold text-brand-600 hover:text-brand-700 transition">
                                    View all {{ $item['title'] }}
                                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                                </a>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('login') }}" class="hidden sm:inline-flex text-sm font-medium text-[#4C4E64] hover:text-brand-600 transition">Login</a>
                    <a href="{{ route('register') }}" class="inline-flex items-center px-4 py-2 rounded-lg bg-brand-600 text-white text-sm font-semibold hover:bg-brand-700 transition shadow-md shadow-brand-500/20">Register</a>
                    <button class="xl:hidden p-2 rounded-lg hover:bg-gray-100 text-[#4C4E64]" aria-label="Menu">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                    </button>
                </div>
            </div>
        </div>
    </nav>

    @foreach($levels as $levelIndex => $level)
    @php
    $isPrimary = stripos($level->name, 'primary') !== false;
    $mediums = $isPrimary ? $level->classRooms->groupBy(fn($c) => $c->medium ?: 'General') : collect();
    @endphp
    <section id="level-{{ $level->id }}" class="py-10 lg:py-14 {{ $levelIndex % 2 === 0 ? '' : 'bg-white' }}">
        <div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-10">
                <h2 class="text-2xl lg:text-3xl font-black text-[#4C4E64] mb-2">{{ $level->name }}</h2>
                <p class="text-sm text-[#4C4E64]/60">{{ $level->classRooms->count() }} classes · {{ $level->classRooms->sum(fn($c) => $c->subjects->count()) }} subjects</p>
            </div>

            @if($isPrimary && $mediums->count() > 1)
            @foreach($mediums as $mediumName => $mediumClasses)
            <div class="mb-10">
                <div class="flex items-center gap-3 mb-5">
                    <div class="w-10 h-10 rounded-xl {{ $mediumName === 'english' ? 'bg-blue-100 text-blue-600' : 'bg-emerald-100 text-emerald-600' }} flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 0 1 6.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"></path></svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-black text-[#4C4E64]">{{ ucfirst($mediumName) }} Medium</h3>
                        <p class="text-xs text-[#4C4E64]/60">{{ $mediumClasses->count() }} classes</p>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                    @forelse($mediumClasses as $classRoom)
                    <div id="class-{{ $classRoom->id }}" class="class-card bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
                        <div class="p-5 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
                            <h4 class="text-base font-black text-[#4C4E64]">{{ $classRoom->name }}</h4>
                            <span class="text-xs font-bold px-2 py-1 rounded-md bg-brand-100 text-brand-700">{{ $classRoom->subjects->count() }} subjects</span>
                        </div>
                        <div class="p-5">
                            <div class="flex flex-wrap gap-2">
                                @forelse($classRoom->subjects as $subject)
                                <a href="{{ route('materials.subject', ['subject' => $subject->id]) }}" class="subject-btn inline-flex items-center px-3 py-1.5 rounded-lg border border-gray-300 text-[#6D788D] text-xs font-medium hover:border-brand-500 hover:text-brand-600 hover:bg-brand-50">
                                    {{ $subject->name }}
                                    @if($subject->topics->count() > 0)
                                    <span class="ml-1.5 w-4 h-4 rounded-full bg-brand-100 text-brand-600 text-[9px] font-bold flex items-center justify-center">{{ $subject->topics->count() }}</span>
                                    @endif
                                </a>
                                @empty
                                <span class="text-sm text-gray-400">No subjects added yet.</span>
                                @endforelse
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="col-span-full text-center py-10">
                        <p class="text-[#4C4E64]/60">No classes available.</p>
                    </div>
                    @endforelse
                </div>
            </div>
            @endforeach
            @else
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                @forelse($level->classRooms as $classRoom)
                <div id="class-{{ $classRoom->id }}" class="class-card bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
                    <div class="p-5 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
                        <h4 class="text-base font-black text-[#4C4E64]">{{ $classRoom->name }}</h4>
                        <span class="text-xs font-bold px-2 py-1 rounded-md bg-brand-100 text-brand-700">{{ $classRoom->subjects->count() }} subjects</span>
                    </div>
                    <div class="p-5">
                        <div class="flex flex-wrap gap-2">
                            @forelse($classRoom->subjects as $subject)
                            <a href="{{ route('materials.subject', ['subject' => $subject->id]) }}" class="subject-btn inline-flex items-center px-3 py-1.5 rounded-lg border border-gray-300 text-[#6D788D] text-xs font-medium hover:border-brand-500 hover:text-brand-600 hover:bg-brand-50">
                                {{ $subject->name }}
                                @if($subject->topics->count() > 0)
                                <span class="ml-1.5 w-4 h-4 rounded-full bg-brand-100 text-brand-600 text-[9px] font-bold flex items-center justify-center">{{ $subject->topics->count() }}</span>
                                @endif
                            </a>
                            @empty
                            <span class="text-sm text-gray-400">No subjects added yet.</span>
                            @endforelse
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-span-full text-center py-10">
                    <p class="text-[#4C4E64]/60">No classes available for this level.</p>
                </div>
                @endforelse
            </div>
            @endif
        </div>
    </section>
    @endforeach

    <!-- Browse Resources -->
    <section id="resources" class="py-14 lg:py-20 bg-white">
        <div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-12">
                <h2 class="text-2xl lg:text-4xl font-black text-[#4C4E64] mb-4">Browse Resources</h2>
                <p class="text-[#4C4E64]/70">Find everything you need — from daily lesson plans to revision notes and national exams.</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($materialCounts as $slug => $resource)
                <a href="{{ route('materials.type', $slug) }}" class="resource-card group flex items-start gap-5 p-6 bg-[#F7F7F9] rounded-2xl border border-gray-100 hover:border-brand-200 hover:bg-white">
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br {{ $resource['color'] }} flex items-center justify-center text-white shadow-lg shrink-0">
                        <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 24 24"><path d="{{ $resource['icon'] }}"></path></svg>
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center justify-between mb-1">
                            <h3 class="text-lg font-bold text-[#4C4E64] group-hover:text-brand-600 transition">{{ $resource['title'] }}</h3>
                            <span class="px-2 py-1 rounded-full bg-white text-xs font-bold text-[#4C4E64] border border-gray-100">{{ $resource['count'] }}</span>
                        </div>
                        <p class="text-sm text-[#4C4E64]/60 mb-3">Browse {{ $resource['count'] }} {{ strtolower($resource['title']) }} for every class and subject.</p>
                        <span class="inline-flex items-center text-sm font-semibold text-brand-600">
                            Explore
                            <svg class="w-4 h-4 ml-1 transform group-hover:translate-x-1 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        </span>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="py-12 lg:py-16 bg-gradient-to-br from-brand-600 to-brand-800">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-2xl lg:text-4xl font-black text-white mb-4">Ready to improve your teaching?</h2>
            <p class="text-brand-100 mb-8 text-base lg:text-lg">Join thousands of teachers using Dukarahisi to prepare better lessons, exams, and notes.</p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('register') }}" class="inline-flex items-center justify-center px-8 py-3 rounded-xl bg-white text-brand-700 font-bold hover:bg-brand-50 transition shadow-lg">Create Free Account</a>
                <a href="{{ route('login') }}" class="inline-flex items-center justify-center px-8 py-3 rounded-xl bg-brand-700 text-white font-bold border-2 border-brand-400 hover:bg-brand-600 transition">Login</a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-[#FAFAFA] border-t border-gray-200">
        <div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 lg:gap-12">
                <div>
                    <div class="flex items-center gap-2 mb-4">
                        <div class="w-9 h-9 rounded-lg bg-gradient-to-br from-brand-500 to-brand-700 flex items-center justify-center text-white font-bold text-lg">D</div>
                        <span class="text-xl font-black text-[#4C4E64]">Dukarahisi</span>
                    </div>
                    <p class="text-sm text-[#4C4E64]/60 leading-relaxed mb-4 max-w-xs">
                        A digital network for education systems in Tanzania — empowering educators with innovative tools and resources.
                    </p>
                    <p class="text-xs font-semibold text-[#4C4E64]/60 mb-2">Connect With Us</p>
                    <div class="flex gap-2">
                        <a href="#" class="w-9 h-9 rounded-full border border-gray-200 flex items-center justify-center text-[#4C4E64]/60 hover:bg-brand-600 hover:border-brand-600 hover:text-white transition">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M22.46 6c-.77.35-1.6.58-2.46.69c.88-.53 1.56-1.37 1.88-2.38c-.83.5-1.75.85-2.72 1.05C18.37 4.5 17.26 4 16 4c-2.35 0-4.27 1.92-4.27 4.29c0 .34.04.67.11.98C8.28 9.09 5.11 7.38 3 4.79c-.37.63-.58 1.37-.58 2.15c0 1.49.75 2.81 1.91 3.56c-.71 0-1.37-.2-1.95-.5v.03c0 2.08 1.48 3.82 3.44 4.21a4.22 4.22 0 0 1-1.93.07a4.28 4.28 0 0 0 4 2.98a8.521 8.521 0 0 1-5.33 1.84c-.34 0-.68-.02-1.02-.06C3.44 20.29 5.7 21 8.12 21C16 21 20.33 14.46 20.33 8.79c0-.19 0-.37-.01-.56c.84-.6 1.56-1.36 2.14-2.23Z"></path></svg>
                        </a>
                        <a href="#" class="w-9 h-9 rounded-full border border-gray-200 flex items-center justify-center text-[#4C4E64]/60 hover:bg-brand-600 hover:border-brand-600 hover:text-white transition">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M19 3a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h14m-.5 15.5v-5.3a3.26 3.26 0 0 0-3.26-3.26c-.85 0-1.84.52-2.32 1.3v-1.11h-2.79v8.37h2.79v-4.93c0-.77.62-1.4 1.39-1.4a1.4 1.4 0 0 1 1.4 1.4v4.93h2.79M6.88 8.56a1.68 1.68 0 0 0 1.68-1.68c0-.93-.75-1.69-1.68-1.69a1.69 1.69 0 0 0-1.69 1.69c0 .93.76 1.68 1.69 1.68m1.39 9.94v-8.37H5.5v8.37h2.77Z"></path></svg>
                        </a>
                        <a href="#" class="w-9 h-9 rounded-full border border-gray-200 flex items-center justify-center text-[#4C4E64]/60 hover:bg-brand-600 hover:border-brand-600 hover:text-white transition">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.04c-5.5 0-10 4.49-10 10.02c0 5 3.66 9.15 8.44 9.9v-7H7.9v-2.9h2.54V9.85c0-2.51 1.49-3.89 3.78-3.89c1.09 0 2.23.19 2.23.19v2.47h-1.26c-1.24 0-1.63.77-1.63 1.56v1.88h2.78l-.45 2.9h-2.33v7a10 10 0 0 0 8.44-9.9c0-5.53-4.5-10.02-10-10.02Z"></path></svg>
                        </a>
                    </div>
                </div>
                <div>
                    <h6 class="text-base font-bold text-[#4C4E64] mb-4">Quick Links</h6>
                    <div class="flex flex-col gap-2">
                        <a href="#" class="text-sm text-[#4C4E64]/60 hover:text-brand-600 hover:translate-x-1 transition inline-flex items-center gap-2">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M6 2a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6H6m0 2h7v5h5v11H6V4m2 8v2h8v-2H8m0 4v2h5v-2H8z"></path></svg>
                            Schemes
                        </a>
                        <a href="#" class="text-sm text-[#4C4E64]/60 hover:text-brand-600 hover:translate-x-1 transition inline-flex items-center gap-2">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17 4v6l-2-2l-2 2V4H9v16h10V4h-2M7 7V5h2V4a2 2 0 0 1 2-2h12c1.05 0 2 .95 2 2v16c0 1.05-.95 2-2 2H7c-1.05 0-2-.95-2-2v-1H3v-2h2v-4H3v-2h2V7H3m2-2v2h2V5H5m0 14h2v-2H5v2m0-6h2v-2H5v2z"></path></svg>
                            Logbooks
                        </a>
                        <a href="#" class="text-sm text-[#4C4E64]/60 hover:text-brand-600 hover:translate-x-1 transition inline-flex items-center gap-2">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M14 14H7v2h7m5 3H5V8h14m0-5h-1V1h-2v2H8V1H6v2H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V5a2 2 0 0 0-2-2m-2 7H7v2h10v-2z"></path></svg>
                            Lesson Plans
                        </a>
                        <a href="#" class="text-sm text-[#4C4E64]/60 hover:text-brand-600 hover:translate-x-1 transition inline-flex items-center gap-2">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M19 3h-4.18C14.25 1.44 12.53.64 11 1.2c-.86.3-1.5.96-1.82 1.8H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V5a2 2 0 0 0-2-2m-7 0a1 1 0 0 1 1 1a1 1 0 0 1-1 1a1 1 0 0 1-1-1a1 1 0 0 1 1-1M7 7h10V5h2v14H5V5h2v2m10 4H7V9h10v2m-2 4H7v-2h8v2z"></path></svg>
                            Exams
                        </a>
                        <a href="#" class="text-sm text-[#4C4E64]/60 hover:text-brand-600 hover:translate-x-1 transition inline-flex items-center gap-2">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M19 2l-5 4.5v11l5-4.5V2M6.5 5C4.55 5 2.45 5.4 1 6.5v14.66c0 .25.25.5.5.5c.1 0 .15-.07.25-.07c1.35-.65 3.3-1.09 4.75-1.09c1.95 0 4.05.4 5.5 1.5c1.35-.85 3.8-1.5 5.5-1.5c1.65 0 3.35.31 4.75 1.06c.1.05.15.03.25.03c.25 0 .5-.25.5-.5V6.5c-.6-.45-1.25-.75-2-1V19c-1.1-.35-2.3-.5-3.5-.5c-1.7 0-4.15.65-5.5 1.5V6.5C10.55 5.4 8.45 5 6.5 5z"></path></svg>
                            Subject Notes
                        </a>
                    </div>
                </div>
                <div>
                    <h6 class="text-base font-bold text-[#4C4E64] mb-4">Legal</h6>
                    <div class="flex flex-col gap-2">
                        <a href="#" class="text-sm text-[#4C4E64]/60 hover:text-brand-600 hover:translate-x-1 transition inline-flex items-center gap-2">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M21 11c0 5.55-3.84 10.74-9 12c-5.16-1.26-9-6.45-9-12V5l9-4l9 4v6m-9 10c3.75-1 7-5.46 7-9.78V6.3l-7-3.12L5 6.3v4.92C5 15.54 8.25 20 12 21m-2-4l-4-4l1.41-1.41L10 14.17l6.59-6.59L18 9"></path></svg>
                            Privacy Policy
                        </a>
                        <a href="#" class="text-sm text-[#4C4E64]/60 hover:text-brand-600 hover:translate-x-1 transition inline-flex items-center gap-2">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="m23.5 17l-5 5l-3.5-3.5l1.5-1.5l2 2l3.5-3.5l1.5 1.5M6 2a2 2 0 0 0-2 2v16c0 1.11.89 2 2 2h7.81c-.36-.62-.61-1.3-.73-2H6V4h7v5h5v4.08c.33-.05.67-.08 1-.08c.34 0 .67.03 1 .08V8l-6-6M8 12v2h8v-2m-8 4v2h5v-2z"></path></svg>
                            Terms of Service
                        </a>
                    </div>
                </div>
                <div>
                    <h6 class="text-base font-bold text-[#4C4E64] mb-4">Get in Touch</h6>
                    <div class="flex items-center gap-3 p-3 rounded-2xl bg-black/5 hover:bg-black/10 transition">
                        <div class="w-10 h-10 rounded-full bg-brand-600 flex items-center justify-center text-white shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                        </div>
                        <div>
                            <span class="text-xs text-[#4C4E64]/60 block">Email Us</span>
                            <span class="text-sm font-medium text-[#4C4E64]">info@dukarahisi.com</span>
                        </div>
                    </div>
                </div>
            </div>
            <hr class="border-gray-200 my-8">
            <div class="flex flex-col sm:flex-row justify-between items-center gap-4 text-sm text-[#4C4E64]/60">
                <p>© {{ date('Y') }} Dukarahisi. All rights reserved.</p>
                <p>Empowering Education in Tanzania</p>
            </div>
        </div>
    </footer>

    <!-- Scroll to Top -->
    <button onclick="window.scrollTo({top:0,behavior:'smooth'})" class="fixed right-6 bottom-6 w-10 h-10 rounded-full bg-brand-600 text-white shadow-lg flex items-center justify-center hover:bg-brand-700 transition z-40">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path></svg>
    </button>

    <!-- WhatsApp FAB -->
    <a href="https://wa.me/255000000000" target="_blank" class="fixed left-6 bottom-6 w-14 h-14 rounded-full bg-green-500 text-white shadow-lg flex items-center justify-center hover:bg-green-600 transition z-40">
        <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 24 24"><path d="M12.04 2c-5.46 0-9.91 4.45-9.91 9.91c0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38c1.45.79 3.08 1.21 4.74 1.21c5.46 0 9.91-4.45 9.91-9.91c0-2.65-1.03-5.14-2.9-7.01A9.816 9.816 0 0 0 12.04 2m.01 1.67c2.2 0 4.26.86 5.82 2.42a8.225 8.225 0 0 1 2.41 5.83c0 4.54-3.7 8.23-8.24 8.23c-1.48 0-2.93-.39-4.19-1.15l-.3-.17l-3.12.82l.83-3.04l-.2-.32a8.188 8.188 0 0 1-1.26-4.38c.01-4.54 3.7-8.24 8.25-8.24M8.53 7.33c-.16 0-.43.06-.66.31c-.22.25-.87.86-.87 2.07c0 1.22.89 2.39 1 2.56c.14.17 1.76 2.67 4.25 3.73c.59.27 1.05.42 1.41.53c.59.19 1.13.16 1.56.1c.48-.07 1.46-.6 1.67-1.18c.21-.58.21-1.07.15-1.18c-.07-.1-.23-.16-.48-.27c-.25-.14-1.47-.74-1.69-.82c-.23-.08-.37-.12-.56.12c-.16.25-.64.81-.78.97c-.15.17-.29.19-.53.07c-.26-.13-1.06-.39-2-1.23c-.74-.66-1.23-1.47-1.38-1.72c-.12-.24-.01-.39.11-.5c.11-.11.27-.29.37-.44c.13-.14.17-.25.25-.41c.08-.17.04-.31-.02-.43c-.06-.11-.56-1.35-.77-1.84c-.2-.48-.4-.42-.56-.43c-.14 0-.3-.01-.47-.01Z"></path></svg>
    </a>

</body>
</html>
