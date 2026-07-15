<!doctype html>
<html lang="sw">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $config['title'] }} - Dukarahisi</title>
    <meta name="description" content="Browse {{ $config['title'] }} for all levels, classes, and subjects on Dukarahisi.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: { 50: '#f0f9ff', 100: '#e0f2fe', 500: '#0ea5e9', 600: '#0284c7', 700: '#0369a1', 800: '#075985', 900: '#0c4a6e' }
                    },
                    fontFamily: { sans: ['Inter', 'sans-serif'] }
                }
            }
        }
    </script>
    <style>body { font-family: 'Inter', sans-serif; background-color: #F7F7F9; }</style>
</head>
<body class="text-[#4C4E64] bg-[#F7F7F9]">

    <nav class="sticky top-0 z-50 bg-white/90 backdrop-blur-md border-b border-gray-200/60 shadow-sm">
        <div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <a href="/" class="flex items-center gap-2 no-underline">
                    <div class="w-9 h-9 rounded-lg bg-gradient-to-br from-brand-500 to-brand-700 flex items-center justify-center text-white font-bold text-lg">D</div>
                    <span class="text-xl font-bold text-[#4C4E64]">Dukarahisi</span>
                </a>
                <a href="{{ route('landing') }}" class="text-sm font-semibold text-brand-600 hover:text-brand-700">Back to Home</a>
            </div>
        </div>
    </nav>

    <section class="py-12 bg-white border-b border-gray-100">
        <div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center gap-4 mb-4">
                <div class="w-14 h-14 rounded-2xl bg-gradient-to-br {{ $config['color'] }} flex items-center justify-center text-white shadow-lg">
                    <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 24 24"><path d="{{ $config['icon'] }}"></path></svg>
                </div>
                <div>
                    <h1 class="text-2xl lg:text-4xl font-black text-[#4C4E64]">{{ $config['title'] }}</h1>
                    <p class="text-[#4C4E64]/60">{{ $totalCount }} {{ strtolower($config['title']) }} available</p>
                </div>
            </div>
        </div>
    </section>

    <section class="py-10">
        <div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
                <aside class="lg:col-span-1">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 sticky top-24">
                        <h3 class="font-bold text-[#4C4E64] mb-4">Filter by Level</h3>
                        <div class="space-y-3">
                            <a href="{{ route('materials.type', $type) }}" class="block text-sm {{ !request('level') ? 'font-bold text-brand-600' : 'text-[#4C4E64]/70 hover:text-brand-600' }}">All Levels</a>
                            @foreach($levels as $level)
                            <div>
                                <a href="{{ route('materials.type', ['type' => $type, 'level' => $level->id]) }}" class="block text-sm font-semibold {{ request('level') == $level->id ? 'text-brand-600' : 'text-[#4C4E64] hover:text-brand-600' }}">{{ $level->name }}</a>
                                <div class="ml-3 mt-1 space-y-1">
                                    @foreach($level->classRooms as $classRoom)
                                    <a href="{{ route('materials.type', ['type' => $type, 'class' => $classRoom->id]) }}" class="block text-xs {{ request('class') == $classRoom->id ? 'text-brand-600 font-bold' : 'text-[#4C4E64]/60 hover:text-brand-600' }}">{{ $classRoom->name }}</a>
                                    @endforeach
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </aside>

                <div class="lg:col-span-3">
                    <form method="GET" class="mb-6">
                        <div class="relative max-w-md">
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search {{ strtolower($config['title']) }}..." class="w-full pl-4 pr-24 py-3 rounded-xl border border-gray-200 focus:border-brand-500 focus:ring-2 focus:ring-brand-500/10 outline-none text-sm bg-white">
                            <button type="submit" class="absolute right-2 top-2 px-4 py-1.5 rounded-lg bg-brand-600 text-white text-sm font-semibold hover:bg-brand-700">Search</button>
                        </div>
                    </form>

                    @if($materials->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                        @foreach($materials as $material)
                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition">
                            <div class="flex items-center justify-between mb-3">
                                <span class="px-2 py-1 rounded-md bg-brand-50 text-brand-600 text-xs font-bold">{{ $material->subject->classRoom->level->name ?? 'General' }}</span>
                                @if($material->is_free ?? false)
                                <span class="px-2 py-1 rounded-md bg-green-100 text-green-600 text-xs font-bold">Free</span>
                                @else
                                <span class="px-2 py-1 rounded-md bg-amber-100 text-amber-600 text-xs font-bold">TSh {{ number_format($material->price ?? 0) }}</span>
                                @endif
                            </div>
                            <h3 class="font-bold text-[#4C4E64] mb-2 line-clamp-2">{{ $material->title }}</h3>
                            <p class="text-sm text-[#4C4E64]/60 mb-4 line-clamp-2">{{ $material->description }}</p>
                            <div class="flex items-center justify-between text-xs text-[#4C4E64]/60 mb-4">
                                <span>{{ $material->subject->name ?? 'General' }}</span>
                                <span>{{ $material->subject->classRoom->name ?? '' }}</span>
                            </div>
                            <a href="{{ route('login') }}" class="block w-full text-center py-2.5 rounded-lg bg-brand-600 text-white font-semibold text-sm hover:bg-brand-700 transition">View Details</a>
                        </div>
                        @endforeach
                    </div>
                    <div class="mt-8">
                        {{ $materials->links() }}
                    </div>
                    @else
                    <div class="text-center py-16 bg-white rounded-xl border border-gray-100">
                        <p class="text-[#4C4E64]/60">No {{ strtolower($config['title']) }} found.</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </section>

</body>
</html>
