<!doctype html>
<html lang="sw">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $subject->name }} - Dukarahisi</title>
    <meta name="description" content="Explore topics, notes, books, lesson plans, schemes and logbooks for {{ $subject->name }}.">
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
            <div class="flex items-center gap-2 text-sm text-[#4C4E64]/60 mb-3">
                <a href="/" class="hover:text-brand-600">Home</a>
                <span>/</span>
                <span>{{ $subject->classRoom->level->name }}</span>
                <span>/</span>
                <span>{{ $subject->classRoom->name }}</span>
                <span>/</span>
                <span class="text-brand-600 font-semibold">{{ $subject->name }}</span>
            </div>
            <h1 class="text-2xl lg:text-4xl font-black text-[#4C4E64] mb-2">{{ $subject->name }}</h1>
            <p class="text-[#4C4E64]/60">{{ $subject->classRoom->name }} · {{ $subject->classRoom->level->name }} · {{ $topics->count() }} topics</p>
        </div>
    </section>

    <section class="py-10">
        <div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 sticky top-24">
                        <h3 class="font-bold text-[#4C4E64] mb-4">Topics</h3>
                        @if($topics->count() > 0)
                        <div class="flex flex-col gap-2">
                            @foreach($topics as $topic)
                            <a href="#topic-{{ $topic->id }}" class="text-sm text-[#4C4E64]/70 hover:text-brand-600 hover:bg-brand-50 p-2 rounded-lg transition">{{ $topic->name }}</a>
                            @endforeach
                        </div>
                        @else
                        <p class="text-sm text-gray-400">No topics added yet.</p>
                        @endif
                    </div>
                </div>

                <div class="lg:col-span-2 space-y-8">
                    @if($topics->count() > 0)
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <h2 class="text-xl font-bold text-[#4C4E64] mb-4">Topics</h2>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            @foreach($topics as $topic)
                            <a href="{{ route('materials.topic', $topic->id) }}" class="p-4 rounded-xl border border-gray-100 hover:border-brand-300 hover:bg-brand-50 transition">
                                <h4 class="font-semibold text-[#4C4E64]">{{ $topic->name }}</h4>
                                <p class="text-xs text-[#4C4E64]/60 mt-1">{{ $topic->notes->count() ?? 0 }} notes</p>
                            </a>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    @foreach([
                        ['title' => 'Subject Notes', 'items' => $notes, 'route' => 'materials.type', 'type' => 'notes'],
                        ['title' => 'Books', 'items' => $books, 'route' => 'materials.type', 'type' => 'books'],
                        ['title' => 'Lesson Plans', 'items' => $lessonPlans, 'route' => 'materials.type', 'type' => 'lesson-plans'],
                        ['title' => 'Schemes', 'items' => $schemes, 'route' => 'materials.type', 'type' => 'schemes'],
                        ['title' => 'Logbooks', 'items' => $logbooks, 'route' => 'materials.type', 'type' => 'logbooks'],
                    ] as $section)
                    @if($section['items']->count() > 0)
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="text-xl font-bold text-[#4C4E64]">{{ $section['title'] }}</h2>
                            <a href="{{ route($section['route'], $section['type']) }}" class="text-sm font-semibold text-brand-600 hover:text-brand-700">View all</a>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            @foreach($section['items'] as $item)
                            <div class="p-4 rounded-xl border border-gray-100 hover:border-brand-300 hover:shadow-sm transition">
                                <h4 class="font-semibold text-[#4C4E64] line-clamp-2">{{ $item->title }}</h4>
                                <p class="text-xs text-[#4C4E64]/60 mt-1 line-clamp-2">{{ $item->description }}</p>
                                <a href="{{ route('login') }}" class="inline-flex items-center mt-3 text-sm font-semibold text-brand-600">Read now →</a>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                    @endforeach
                </div>
            </div>
        </div>
    </section>

</body>
</html>
