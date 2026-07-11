<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'Laravel') . ' — ' . __('Authentication'))</title>

    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito:400,500,600,700,800,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate, max-age=0">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="referrer" content="strict-origin-when-cross-origin">

    <style>
        @keyframes simpleFadeIn { from { opacity:0; transform:translateY(12px); } to { opacity:1; transform:translateY(0); } }
        .auth-card-entrance { animation: fadeInUp 0.7s cubic-bezier(0.16,1,0.3,1) both; }
        .auth-header-entrance { animation: fadeInDown 0.6s cubic-bezier(0.16,1,0.3,1) 0.2s both; }
        .auth-field-entrance { animation: fadeIn 0.5s cubic-bezier(0.16,1,0.3,1) both; }
        .auth-btn:hover { animation: pulse 0.8s ease-in-out infinite; }
        .ajax-loader { position:fixed; top:0; left:0; right:0; height:3px; background: linear-gradient(90deg, #024938, #f9ac00, #024938); background-size: 200% 100%; animation: ajaxProgress 1s linear infinite; z-index:9999; display:none; }
        @keyframes ajaxProgress { 0% { background-position: 100% 0; } 100% { background-position: -100% 0; } }
        .page-transition { animation: simpleFadeIn 0.35s ease-out both; }
    </style>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        emerald: { 50:'#e6f5f1',100:'#b3e0d4',200:'#80cbc0',300:'#4db5a8',400:'#1a9f8e',500:'#024938',600:'#023d30',700:'#013028',800:'#01241f',900:'#001816' },
                        gold: { 50:'#fff5e0',100:'#ffe6b3',200:'#ffd680',300:'#ffc64d',400:'#ffb71a',500:'#f9ac00',600:'#d49700',700:'#b07c00',800:'#8c6100',900:'#684600' }
                    }
                }
            }
        }
    </script>
</head>
<body class="font-['Nunito',sans-serif] antialiased text-slate-800 min-h-screen">

    <div class="fixed inset-0 z-0" style="background-image: url('{{ asset('flat-abstract-background-pattern-vector_822782-866.jpg') }}'); background-size: cover; background-position: center;">
        <div class="absolute inset-0 bg-white/70"></div>
    </div>

    <div id="ajaxLoader" class="ajax-loader"></div>

    <main id="authMain" class="relative z-10 min-h-screen w-full flex flex-col lg:flex-row">
        {{-- Left side: rotating slideshow --}}
        <div class="hidden lg:block lg:w-1/2 relative h-screen overflow-hidden" id="authSlideshow">
            {{-- Slide images --}}
            <div class="absolute inset-0 transition-opacity duration-1000 ease-in-out opacity-100 slide-image" data-index="0">
                <img src="{{ asset('1411.jpg') }}" alt="Kids Dental Care" class="absolute inset-0 w-full h-full object-cover">
            </div>
            <div class="absolute inset-0 transition-opacity duration-1000 ease-in-out opacity-0 slide-image" data-index="1">
                <img src="{{ asset('7678.jpg') }}" alt="Modern Clinic" class="absolute inset-0 w-full h-full object-cover">
            </div>
            <div class="absolute inset-0 transition-opacity duration-1000 ease-in-out opacity-0 slide-image" data-index="2">
                <img src="{{ asset('images.png') }}" alt="Expert Team" class="absolute inset-0 w-full h-full object-cover">
            </div>
            <div class="absolute inset-0 transition-opacity duration-1000 ease-in-out opacity-0 slide-image" data-index="3">
                <img src="{{ asset('watoto.png') }}" alt="Family Care" class="absolute inset-0 w-full h-full object-cover">
            </div>

            <div class="absolute inset-0 bg-gradient-to-br from-emerald-900/70 via-emerald-800/50 to-transparent"></div>
            <div class="absolute inset-0 bg-gradient-to-t from-emerald-900/80 via-transparent to-transparent"></div>

            {{-- Slide text --}}
            <div class="absolute inset-0 p-12 flex flex-col justify-end">
                <div id="slideText" class="absolute bottom-12 left-12 right-12 transition-all duration-500 ease-in-out" style="opacity: 1; transform: translateY(0);">
                    <h2 id="slideTitle" class="text-4xl font-extrabold text-white leading-tight">Gentle Care for Kids</h2>
                    <p id="slideDesc" class="mt-4 text-white/90 text-lg max-w-md">A friendly, stress-free dental experience for your little ones.</p>
                </div>
            </div>

            {{-- Slide indicators --}}
            <div class="absolute bottom-28 right-12 flex gap-2">
                <button class="slide-dot w-2.5 h-2.5 rounded-full bg-white/40 transition-all duration-300" data-index="0"></button>
                <button class="slide-dot w-2.5 h-2.5 rounded-full bg-white/40 transition-all duration-300" data-index="1"></button>
                <button class="slide-dot w-2.5 h-2.5 rounded-full bg-white/40 transition-all duration-300" data-index="2"></button>
                <button class="slide-dot w-2.5 h-2.5 rounded-full bg-white/40 transition-all duration-300" data-index="3"></button>
            </div>
        </div>

        {{-- Right side: auth card --}}
        <div class="w-full lg:w-1/2 min-h-screen flex items-center justify-center p-6 sm:p-12 relative">
            <div class="absolute inset-0 bg-white/10"></div>
            @yield('content')
        </div>
    </main>

    <script>
    // SweetAlert2 side toasts
    (function() {
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 5000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer);
                toast.addEventListener('mouseleave', Swal.resumeTimer);
            }
        });

        @if(session('status'))
            Toast.fire({ icon: 'success', title: '{{ session('status') }}' });
        @endif
        @if(session('error'))
            Toast.fire({ icon: 'error', title: '{{ session('error') }}' });
        @endif
        @if(session('warning'))
            Toast.fire({ icon: 'warning', title: '{{ session('warning') }}' });
        @endif
        @if(session('info'))
            Toast.fire({ icon: 'info', title: '{{ session('info') }}' });
        @endif

        @if($errors->any())
            @foreach($errors->all() as $error)
                Toast.fire({ icon: 'error', title: '{{ $error }}' });
            @endforeach
        @endif
    })();

    // Button loading state on all auth forms
    (function() {
        const authMain = document.getElementById('authMain');
        if (!authMain) return;

        authMain.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', function() {
                const btn = form.querySelector('button[type="submit"]');
                if (!btn) return;

                btn.disabled = true;
                const original = btn.innerHTML;
                btn.setAttribute('data-original', original);
                btn.innerHTML = '<svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-gray-900 inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Processing...';
                btn.classList.add('cursor-not-allowed', 'opacity-90');
            });
        });
    })();

    // Animate.css entrance animations for auth card
    (function() {
        const card = document.querySelector('#authMain .max-w-md');
        if (!card) return;

        // Remove built-in simple fade-in and use stronger entrance
        card.style.animation = 'none';
        card.classList.add('auth-card-entrance');

        // Animate header
        const header = card.querySelector('.bg-gradient-to-br');
        if (header) header.classList.add('auth-header-entrance');

        // Animate form fields with stagger
        const fields = card.querySelectorAll('form > div, form > button');
        fields.forEach((field, i) => {
            field.classList.add('auth-field-entrance');
            field.style.animationDelay = (0.3 + (i * 0.08)) + 's';
        });

        // Add pulse hover class to submit button
        const submitBtn = card.querySelector('button[type="submit"]');
        if (submitBtn) submitBtn.classList.add('auth-btn');
    })();

    // Auth slideshow
    (function() {
        const slideshow = document.getElementById('authSlideshow');
        if (!slideshow) return;

        const images = slideshow.querySelectorAll('.slide-image');
        const dots = slideshow.querySelectorAll('.slide-dot');
        const slideText = document.getElementById('slideText');
        const slideTitle = document.getElementById('slideTitle');
        const slideDesc = document.getElementById('slideDesc');

        const slides = [
            { title: 'Gentle Care for Kids', desc: 'A friendly, stress-free dental experience for your little ones.' },
            { title: 'Modern Facilities', desc: 'State-of-the-art equipment and a clean, comfortable clinic.' },
            { title: 'Expert Dental Team', desc: 'Professional dentists committed to your healthy, confident smile.' },
            { title: 'Care for the Whole Family', desc: 'Comprehensive dental services tailored to every age.' }
        ];

        let current = 0;
        const total = images.length;
        const interval = 5000;
        let isTransitioning = false;

        function updateDots(index) {
            dots.forEach((dot, i) => {
                dot.classList.toggle('bg-white', i === index);
                dot.classList.toggle('w-6', i === index);
                dot.classList.toggle('bg-white/40', i !== index);
                dot.classList.toggle('w-2.5', i !== index);
            });
        }

        function showSlide(index) {
            if (isTransitioning) return;
            isTransitioning = true;

            images.forEach((img, i) => {
                img.classList.toggle('opacity-100', i === index);
                img.classList.toggle('opacity-0', i !== index);
            });

            updateDots(index);

            // Fade text out, update content, then fade in
            slideText.style.opacity = '0';
            slideText.style.transform = 'translateY(12px)';

            setTimeout(() => {
                slideTitle.textContent = slides[index].title;
                slideDesc.textContent = slides[index].desc;
                slideText.style.opacity = '1';
                slideText.style.transform = 'translateY(0)';
                isTransitioning = false;
            }, 500);
        }

        function nextSlide() {
            current = (current + 1) % total;
            showSlide(current);
        }

        dots.forEach((dot, i) => {
            dot.addEventListener('click', () => {
                if (i === current) return;
                current = i;
                showSlide(current);
                resetTimer();
            });
        });

        let timer = setInterval(nextSlide, interval);
        function resetTimer() {
            clearInterval(timer);
            timer = setInterval(nextSlide, interval);
        }

        updateDots(0);
    })();
    </script>
</body>
</html>
