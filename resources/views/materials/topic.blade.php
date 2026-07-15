<!doctype html>
<html lang="sw">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $topic->name }} - Dukarahisi</title>
    <meta name="description" content="Study notes and materials for {{ $topic->name }}.">
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
                <a href="{{ route('materials.subject', $topic->subject->id) }}" class="hover:text-brand-600">{{ $topic->subject->name }}</a>
                <span>/</span>
                <span class="text-brand-600 font-semibold">{{ $topic->name }}</span>
            </div>
            <h1 class="text-2xl lg:text-4xl font-black text-[#4C4E64] mb-2">{{ $topic->name }}</h1>
            <p class="text-[#4C4E64]/60">{{ $topic->subject->name }} · {{ $topic->subject->classRoom->name }} · {{ $topic->subject->classRoom->level->name }}</p>
        </div>
    </section>

    <section class="py-10">
        <div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8">
            @if($notes->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                @foreach($notes as $note)
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                    <div class="flex items-center justify-between mb-3">
                        <span class="px-2 py-1 rounded-md bg-brand-50 text-brand-600 text-xs font-bold">Note</span>
                        @if($note->is_free)
                        <span class="px-2 py-1 rounded-md bg-green-100 text-green-600 text-xs font-bold">Free</span>
                        @else
                        <span class="px-2 py-1 rounded-md bg-amber-100 text-amber-600 text-xs font-bold">TSh {{ number_format($note->price) }}</span>
                        @endif
                    </div>
                    <h3 class="font-bold text-[#4C4E64] mb-2">{{ $note->title }}</h3>
                    <p class="text-sm text-[#4C4E64]/60 mb-4 line-clamp-2">{{ $note->description }}</p>
                    <a href="{{ route('login') }}" class="block w-full text-center py-2.5 rounded-lg bg-brand-600 text-white font-semibold text-sm hover:bg-brand-700 transition">View Details</a>
                </div>
                @endforeach
            </div>
            @else
            <div class="text-center py-16 bg-white rounded-xl border border-gray-100">
                <p class="text-[#4C4E64]/60">No notes available for this topic yet.</p>
            </div>
            @endif
        </div>
    </section>

</body>
</html>
