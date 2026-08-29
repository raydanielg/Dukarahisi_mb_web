<!doctype html>
<html lang="sw">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Darasa Huru — Pakua App Ya Walimu</title>
    <meta name="description" content="Pakua app ya Darasa Huru - pata lesson plans, notes, mitihani, schemes na logbooks kwenye simu yako.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Sora:wght@600;700;800&family=Caveat:wght@600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#e6f5f1', 100: '#b3e0d4', 200: '#80cbc0', 300: '#4db5a8',
                            400: '#1a9f8e', 500: '#024938', 600: '#023d30', 700: '#013028',
                            800: '#01241f', 900: '#001816',
                        },
                        gold: {
                            50: '#fff5e0', 100: '#ffe6b3', 200: '#ffd680', 300: '#ffc64d',
                            400: '#ffb71a', 500: '#f9ac00', 600: '#d49700', 700: '#b07c00',
                        }
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        display: ['Sora', 'Inter', 'sans-serif'],
                        chalk: ['Caveat', 'cursive'],
                    }
                }
            }
        }
    </script>
    <style>
        :root {
            --brand: #024938;
            --gold: #f9ac00;
        }
        html { scroll-behavior: smooth; }
        body { font-family: 'Inter', sans-serif; }
        ::selection { background: var(--gold); color: #01241f; }

        /* ============ SCROLL PROGRESS ============ */
        #scrollProgress {
            position: fixed; top: 0; left: 0; height: 3px; width: 0%;
            background: linear-gradient(90deg, var(--gold), #ffc64d);
            z-index: 9999; transition: width .1s linear;
            box-shadow: 0 0 12px rgba(249,172,0,.6);
        }

        /* ============ PRELOADER ============ */
        #preloader {
            position: fixed; inset: 0; z-index: 10000;
            background: var(--brand);
            display: flex; align-items: center; justify-content: center;
            flex-direction: column; gap: 18px;
            transition: opacity .6s ease, visibility .6s ease;
        }
        #preloader.hide { opacity: 0; visibility: hidden; }
        .loader-logo { font-family: 'Sora', sans-serif; font-weight: 800; color: #fff; font-size: 1.6rem; letter-spacing: -0.02em; }
        .loader-logo span { color: var(--gold); }
        .loader-bar { width: 160px; height: 4px; background: rgba(255,255,255,.15); border-radius: 99px; overflow: hidden; }
        .loader-bar::after {
            content: ''; display: block; width: 40%; height: 100%;
            background: var(--gold); border-radius: 99px;
            animation: loaderSlide 1s ease-in-out infinite;
        }
        @keyframes loaderSlide {
            0% { transform: translateX(-120%); }
            100% { transform: translateX(320%); }
        }

        /* ============ REVEAL ON SCROLL ============ */
        .reveal { opacity: 0; transform: translateY(36px); transition: opacity .8s cubic-bezier(.22,1,.36,1), transform .8s cubic-bezier(.22,1,.36,1); }
        .reveal.in { opacity: 1; transform: translateY(0); }
        .reveal-left { opacity: 0; transform: translateX(-40px); transition: all .8s cubic-bezier(.22,1,.36,1); }
        .reveal-left.in { opacity: 1; transform: translateX(0); }
        .reveal-right { opacity: 0; transform: translateX(40px); transition: all .8s cubic-bezier(.22,1,.36,1); }
        .reveal-right.in { opacity: 1; transform: translateX(0); }
        .reveal-zoom { opacity: 0; transform: scale(.9); transition: all .8s cubic-bezier(.22,1,.36,1); }
        .reveal-zoom.in { opacity: 1; transform: scale(1); }
        [data-stagger] > * { opacity: 0; transform: translateY(30px); transition: opacity .7s cubic-bezier(.22,1,.36,1), transform .7s cubic-bezier(.22,1,.36,1); }
        [data-stagger].in > * { opacity: 1; transform: translateY(0); }

        /* ============ HERO WORD REVEAL ============ */
        .word-reveal .w {
            display: inline-block; opacity: 0; transform: translateY(30px) rotate(2deg);
            animation: wordUp .7s cubic-bezier(.22,1,.36,1) forwards;
        }
        @keyframes wordUp { to { opacity: 1; transform: translateY(0) rotate(0); } }

        /* ============ CHALK UNDERLINE (SIGNATURE) ============ */
        .chalk-path { stroke-dasharray: 600; stroke-dashoffset: 600; }
        .chalk-draw .chalk-path { animation: chalkDraw 1.1s ease forwards; }
        .chalk-draw .chalk-path.d2 { animation-delay: .5s; }
        @keyframes chalkDraw { to { stroke-dashoffset: 0; } }

        /* ============ TYPING CURSOR ============ */
        .type-cursor { display: inline-block; width: 3px; background: var(--gold); animation: blink .8s step-end infinite; margin-left: 2px; border-radius: 2px; }
        @keyframes blink { 50% { opacity: 0; } }

        /* ============ FLOATS & AMBIENT ============ */
        @keyframes float { 0%,100% { transform: translateY(0); } 50% { transform: translateY(-16px); } }
        .float-anim { animation: float 4.5s ease-in-out infinite; }
        @keyframes floatSlow { 0%,100% { transform: translateY(0) rotate(0); } 50% { transform: translateY(-10px) rotate(2deg); } }
        .float-slow { animation: floatSlow 6.5s ease-in-out infinite; }
        @keyframes orbDrift {
            0%,100% { transform: translate(0,0) scale(1); }
            33% { transform: translate(30px,-25px) scale(1.08); }
            66% { transform: translate(-20px,20px) scale(.95); }
        }
        .orb { animation: orbDrift 14s ease-in-out infinite; }
        .orb2 { animation: orbDrift 18s ease-in-out infinite reverse; }

        /* ============ PHONE MOCKUP ============ */
        .phone-shell {
            box-shadow: 0 50px 100px rgba(2,73,56,.3), 0 0 0 12px #16162a, 0 0 0 14px #2d2d44;
            transform-style: preserve-3d;
            transition: transform .25s ease-out;
            will-change: transform;
        }
        .phone-glow {
            position: absolute; inset: -40px; z-index: -1;
            background: radial-gradient(circle, rgba(249,172,0,.28), transparent 65%);
            filter: blur(30px);
            animation: glowPulse 4s ease-in-out infinite;
        }
        @keyframes glowPulse { 0%,100% { opacity: .6; transform: scale(1); } 50% { opacity: 1; transform: scale(1.06); } }
        .screen { position: absolute; inset: 0; opacity: 0; transform: translateX(24px); transition: opacity .7s ease, transform .7s ease; pointer-events: none; }
        .screen.active { opacity: 1; transform: translateX(0); }
        .screen-dots { display: flex; gap: 6px; }
        .screen-dots span { width: 6px; height: 6px; border-radius: 99px; background: rgba(255,255,255,.3); transition: all .4s; }
        .screen-dots span.on { width: 18px; background: var(--gold); }

        /* ============ MARQUEE ============ */
        .marquee { overflow: hidden; -webkit-mask-image: linear-gradient(90deg, transparent, #000 8%, #000 92%, transparent); mask-image: linear-gradient(90deg, transparent, #000 8%, #000 92%, transparent); }
        .marquee-track { display: flex; gap: 0; width: max-content; animation: marquee 30s linear infinite; }
        .marquee:hover .marquee-track { animation-play-state: paused; }
        @keyframes marquee { to { transform: translateX(-50%); } }

        /* ============ FEATURE CARDS ============ */
        .f-card {
            position: relative; transition: transform .4s cubic-bezier(.22,1,.36,1), box-shadow .4s ease;
            overflow: hidden;
        }
        .f-card::before {
            content: ''; position: absolute; inset: 0; border-radius: inherit;
            background: radial-gradient(500px circle at var(--mx,50%) var(--my,50%), rgba(249,172,0,.14), transparent 45%);
            opacity: 0; transition: opacity .4s;
        }
        .f-card:hover::before { opacity: 1; }
        .f-card:hover { transform: translateY(-10px); box-shadow: 0 30px 60px rgba(2,73,56,.14); }
        .f-icon { transition: transform .45s cubic-bezier(.34,1.56,.64,1); }
        .f-card:hover .f-icon { transform: scale(1.12) rotate(-6deg); }

        /* ============ STEPS CONNECTOR ============ */
        .step-line { stroke-dasharray: 8 8; stroke-dashoffset: 800; transition: stroke-dashoffset 2s ease; }
        .steps-in .step-line { stroke-dashoffset: 0; }
        .step-badge { transition: transform .4s cubic-bezier(.34,1.56,.64,1), box-shadow .4s; }
        .step-item:hover .step-badge { transform: scale(1.1) rotate(-4deg); box-shadow: 0 20px 45px rgba(2,73,56,.3); }

        /* ============ BUTTONS ============ */
        .btn-store { position: relative; overflow: hidden; transition: transform .3s ease, box-shadow .3s ease; }
        .btn-store:hover { transform: translateY(-4px) scale(1.02); }
        .btn-store::after {
            content: ''; position: absolute; top: 0; left: -80%; width: 50%; height: 100%;
            background: linear-gradient(100deg, transparent, rgba(255,255,255,.35), transparent);
            transform: skewX(-20deg); transition: left .6s ease;
        }
        .btn-store:hover::after { left: 130%; }
        .pulse-ring { position: absolute; inset: 0; border-radius: inherit; border: 2px solid rgba(249,172,0,.5); animation: ring 2.2s ease-out infinite; pointer-events: none; }
        .pulse-ring.r2 { animation-delay: 1.1s; }
        @keyframes ring { 0% { transform: scale(1); opacity: 1; } 100% { transform: scale(1.25); opacity: 0; } }

        /* ============ TESTIMONIAL CARDS ============ */
        .t-card { transition: transform .4s ease, box-shadow .4s ease; }
        .t-card:hover { transform: translateY(-8px) rotate(-.5deg); box-shadow: 0 24px 50px rgba(2,73,56,.12); }

        /* ============ NAVBAR ============ */
        #navbar { transition: box-shadow .3s ease, background .3s ease; }
        #navbar.scrolled { box-shadow: 0 6px 30px rgba(2,73,56,.1); }

        /* ============ BACK TO TOP ============ */
        #backTop {
            position: fixed; right: 20px; bottom: 20px; z-index: 90;
            opacity: 0; transform: translateY(20px); pointer-events: none;
            transition: all .4s ease;
        }
        #backTop.show { opacity: 1; transform: translateY(0); pointer-events: auto; }

        /* ============ COUNTER ============ */
        .stat-num { font-variant-numeric: tabular-nums; }

        /* ============ NOTEBOOK LINES (paper texture) ============ */
        .paper-lines {
            background-image: repeating-linear-gradient(0deg, transparent, transparent 31px, rgba(2,73,56,.05) 31px, rgba(2,73,56,.05) 32px);
        }

        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after { animation-duration: .01ms !important; animation-iteration-count: 1 !important; transition-duration: .2s !important; }
            .reveal, .reveal-left, .reveal-right, .reveal-zoom, [data-stagger] > * { opacity: 1 !important; transform: none !important; }
            .word-reveal .w { opacity: 1; transform: none; }
            html { scroll-behavior: auto; }
        }
    </style>
</head>
<body class="text-[#16162a] bg-white overflow-x-hidden antialiased">

    <div id="scrollProgress"></div>

    {{-- Preloader --}}
    <div id="preloader">
        <div class="loader-logo">Darasa <span>Huru</span></div>
        <div class="loader-bar"></div>
    </div>

    {{-- Top Bar --}}
    <div class="bg-brand-600 text-white text-center py-2 text-xs font-medium px-4">
        <span class="inline-flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-gold-400 animate-ping"></span>
            <span class="w-2 h-2 rounded-full bg-gold-400 -ml-4"></span>
            App ya Darasa Huru ipo tayari — pakua sasa, ni bure kwa walimu wote Tanzania 🇹🇿
        </span>
    </div>

    {{-- Navbar --}}
    <nav id="navbar" class="sticky top-0 z-50 bg-white/85 backdrop-blur-xl border-b border-gray-100">
        <div class="max-w-[1200px] mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <a href="#" class="flex items-center gap-2 group">
                    <span class="w-9 h-9 rounded-xl bg-gradient-to-br from-brand-500 to-brand-700 flex items-center justify-center text-white shadow-lg shadow-brand-500/25 group-hover:rotate-[-8deg] transition-transform duration-300">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 3L1 9l11 6l9-4.91V17h2V9M5 13.18v4L12 21l7-3.82v-4L12 17l-7-3.82z"/></svg>
                    </span>
                    <span class="font-display text-lg font-extrabold text-brand-600 tracking-tight">Darasa<span class="text-gold-500">Huru</span></span>
                    <span class="ml-1 px-2 py-0.5 rounded-full bg-gold-400/15 text-gold-600 text-[10px] font-bold">v3.0</span>
                </a>
                <div class="flex items-center gap-1 sm:gap-5">
                    <a href="#features" class="hidden sm:inline-flex text-sm font-medium text-gray-600 hover:text-brand-600 transition relative after:absolute after:left-0 after:-bottom-1 after:h-0.5 after:w-0 after:bg-gold-500 after:transition-all hover:after:w-full">Features</a>
                    <a href="#how-it-works" class="hidden sm:inline-flex text-sm font-medium text-gray-600 hover:text-brand-600 transition relative after:absolute after:left-0 after:-bottom-1 after:h-0.5 after:w-0 after:bg-gold-500 after:transition-all hover:after:w-full">Jinsi Inavyofanya Kazi</a>
                    <a href="#maoni" class="hidden md:inline-flex text-sm font-medium text-gray-600 hover:text-brand-600 transition relative after:absolute after:left-0 after:-bottom-1 after:h-0.5 after:w-0 after:bg-gold-500 after:transition-all hover:after:w-full">Maoni</a>
                    <a href="#download" class="btn-store inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-brand-600 text-white text-sm font-bold hover:bg-brand-700 shadow-lg shadow-brand-500/25">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M5 20h14v-2H5m14-9h-3V4H8v5H5l7 7 7-7z"/></svg>
                        Pakua App
                    </a>
                </div>
            </div>
        </div>
    </nav>

    {{-- ================= HERO ================= --}}
    <section class="relative bg-gradient-to-b from-brand-50/50 via-white to-white py-16 lg:py-24 overflow-hidden paper-lines">
        <div class="orb absolute -top-20 right-0 w-[420px] h-[420px] bg-gold-400/15 rounded-full blur-3xl"></div>
        <div class="orb2 absolute bottom-0 -left-24 w-[480px] h-[480px] bg-brand-400/10 rounded-full blur-3xl"></div>

        <div class="relative max-w-[1200px] mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-2 gap-14 items-center">

                {{-- Left --}}
                <div class="text-center lg:text-left">
                    <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white border border-gold-200 text-gold-600 text-xs font-bold mb-6 shadow-sm reveal">
                        <span class="relative flex h-2.5 w-2.5">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-gold-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-gold-500"></span>
                        </span>
                        App Mpya Kwa Walimu Tanzania
                    </div>

                    <h1 class="font-display text-4xl sm:text-5xl lg:text-[3.6rem] font-extrabold leading-[1.08] tracking-tight mb-4 word-reveal" id="heroTitle">
                        Elimu Yako,
                        <span class="block text-brand-600 relative inline-block">
                            <span id="heroLine2">Mikononi Mwako</span>
                            {{-- Chalk underline (signature) --}}
                            <svg class="chalk-draw absolute -bottom-3 left-0 w-full h-5" viewBox="0 0 300 20" fill="none" preserveAspectRatio="none">
                                <path class="chalk-path" d="M4 12 C 60 4, 150 16, 296 8" stroke="#f9ac00" stroke-width="5" stroke-linecap="round"/>
                                <path class="chalk-path d2" d="M20 17 C 90 12, 190 19, 280 14" stroke="#f9ac00" stroke-width="2.5" stroke-linecap="round" opacity=".55"/>
                            </svg>
                        </span>
                    </h1>

                    <p class="text-base sm:text-lg text-gray-600 mb-2 max-w-lg mx-auto lg:mx-0 reveal">
                        Pakua app ya Darasa Huru upate
                        <span class="font-bold text-brand-600" id="typeTarget">lesson plans</span><span class="type-cursor" style="height:1.1em; vertical-align:-0.15em;"></span>
                    </p>
                    <p class="text-base sm:text-lg text-gray-600 mb-8 max-w-lg mx-auto lg:mx-0 reveal">
                        zote kwenye simu yako. Rahisi, haraka, na kila wakati nawe — hata bila mtandao.
                    </p>

                    <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start mb-10" data-stagger>
                        <a href="#download" class="btn-store inline-flex items-center justify-center gap-3 px-6 py-3.5 rounded-2xl bg-brand-600 text-white font-bold hover:bg-brand-700 shadow-xl shadow-brand-500/25">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M3 20.5V3.5C3 2.91 3.34 2.39 3.84 2.15L13.69 12L3.84 21.85C3.34 21.61 3 21.09 3 20.5M16.81 15.12L6.05 21.34L14.54 12.85L16.81 15.12M20.16 10.81C20.5 11.08 20.75 11.5 20.75 12C20.75 12.5 20.53 12.9 20.18 13.18L17.89 14.5L15.39 12L17.89 9.5L20.16 10.81M6.05 2.66L16.81 8.88L14.54 11.15L6.05 2.66Z"/></svg>
                            <span class="text-left">
                                <span class="block text-[10px] font-medium opacity-80">Pakua kwenye</span>
                                <span class="block text-sm font-extrabold">Google Play</span>
                            </span>
                        </a>
                        <a href="#download" class="btn-store inline-flex items-center justify-center gap-3 px-6 py-3.5 rounded-2xl bg-gray-900 text-white font-bold hover:bg-gray-800 shadow-xl shadow-gray-900/20">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M18.71 19.5C17.88 20.74 17 21.95 15.66 21.97C14.32 22 13.89 21.18 12.37 21.18C10.84 21.18 10.37 21.95 9.1 22C7.79 22.05 6.8 20.68 5.96 19.47C4.25 17 2.94 12.45 4.7 9.39C5.57 7.87 7.13 6.91 8.82 6.88C10.1 6.86 11.32 7.75 12.11 7.75C12.89 7.75 14.37 6.68 15.9 6.84C16.55 6.87 18.39 7.1 19.57 8.82C19.47 8.88 17.39 10.1 17.41 12.63C17.44 15.65 20.06 16.66 20.09 16.67C20.06 16.74 19.67 18.11 18.71 19.5M13 3.5C13.73 2.67 14.94 2.04 15.94 2C16.07 3.17 15.6 4.35 14.9 5.19C14.21 6.04 13.07 6.7 11.95 6.61C11.8 5.46 12.36 4.26 13 3.5Z"/></svg>
                            <span class="text-left">
                                <span class="block text-[10px] font-medium opacity-80">Pakua kwenye</span>
                                <span class="block text-sm font-extrabold">App Store</span>
                            </span>
                        </a>
                    </div>

                    {{-- Animated stats --}}
                    <div class="flex items-center gap-6 justify-center lg:justify-start reveal" id="statsRow">
                        <div>
                            <div class="stat-num font-display text-2xl font-extrabold text-brand-600"><span data-count="10000" data-suffix="+">0</span></div>
                            <div class="text-xs text-gray-500 font-medium">Walimu</div>
                        </div>
                        <div class="w-px h-10 bg-gray-200"></div>
                        <div>
                            <div class="stat-num font-display text-2xl font-extrabold text-brand-600"><span data-count="500" data-suffix="+">0</span></div>
                            <div class="text-xs text-gray-500 font-medium">Materials</div>
                        </div>
                        <div class="w-px h-10 bg-gray-200"></div>
                        <div>
                            <div class="stat-num font-display text-2xl font-extrabold text-brand-600"><span data-count="48" data-decimal="1" data-suffix="★">0</span></div>
                            <div class="text-xs text-gray-500 font-medium">Rating</div>
                        </div>
                    </div>
                </div>

                {{-- Right: Phone --}}
                <div class="relative flex justify-center reveal-zoom" id="phoneWrap">
                    <div class="relative" id="phoneTiltZone">
                        <div class="phone-glow"></div>
                        <div class="phone-shell w-[280px] h-[560px] rounded-[3rem] bg-gradient-to-br from-brand-600 to-brand-800 overflow-hidden relative" id="phoneShell">
                            <div class="absolute top-0 left-1/2 -translate-x-1/2 w-32 h-7 bg-gray-900 rounded-b-2xl z-20"></div>

                            {{-- Screen 1: Materials --}}
                            <div class="screen active pt-10 px-4 pb-4">
                                <div class="flex items-center justify-between mb-4">
                                    <div>
                                        <div class="text-white font-extrabold text-lg font-display">Study Materials</div>
                                        <div class="text-white/60 text-xs">Chagua aina ya material</div>
                                    </div>
                                    <div class="w-10 h-10 rounded-xl bg-white/15 flex items-center justify-center">
                                        <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M19 3h-4.18C14.25 1.44 12.53.64 11 1.2c-.86.3-1.5.96-1.82 1.8H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V5a2 2 0 0 0-2-2m-7 0a1 1 0 0 1 1 1a1 1 0 0 1-1 1a1 1 0 0 1-1-1a1 1 0 0 1 1-1M7 7h10V5h2v14H5V5h2v2m10 4H7V9h10v2m-2 4H7v-2h8v2z"/></svg>
                                    </div>
                                </div>
                                <div class="bg-white/15 rounded-xl px-3 py-2.5 mb-4 flex items-center gap-2">
                                    <svg class="w-4 h-4 text-white/60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                    <div class="text-white/40 text-xs">Tafuta material...</div>
                                </div>
                                <div class="grid grid-cols-2 gap-3">
                                    <div class="bg-white rounded-2xl p-3 float-anim">
                                        <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center mb-2">
                                            <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 24 24"><path d="M19 3h-1V1h-2v2H8V1H6v2H5a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2V5a2 2 0 00-2-2m0 16H5V10h14v9M7 12h5v5H7v-5z"/></svg>
                                        </div>
                                        <div class="text-xs font-bold text-gray-800">Notes</div>
                                        <div class="text-[9px] text-gray-400 mt-0.5">Fungua →</div>
                                    </div>
                                    <div class="bg-white rounded-2xl p-3 float-slow">
                                        <div class="w-10 h-10 rounded-xl bg-indigo-50 flex items-center justify-center mb-2">
                                            <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                                        </div>
                                        <div class="text-xs font-bold text-gray-800">Books</div>
                                        <div class="text-[9px] text-gray-400 mt-0.5">Fungua →</div>
                                    </div>
                                    <div class="bg-white rounded-2xl p-3 float-slow">
                                        <div class="w-10 h-10 rounded-xl bg-violet-50 flex items-center justify-center mb-2">
                                            <svg class="w-5 h-5 text-violet-600" fill="currentColor" viewBox="0 0 24 24"><path d="M6 2a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8l-6-6H6m0 2h7v5h5v11H6V4m2 8v2h8v-2H8m0 4v2h5v-2H8z"/></svg>
                                        </div>
                                        <div class="text-xs font-bold text-gray-800">Logbooks</div>
                                        <div class="text-[9px] text-gray-400 mt-0.5">Fungua →</div>
                                    </div>
                                    <div class="bg-white rounded-2xl p-3 float-anim">
                                        <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center mb-2">
                                            <svg class="w-5 h-5 text-amber-600" fill="currentColor" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6m4 18H6V4h7v5h5v11m-9.1-2L7 14.1l1.4-1.4l1.5 1.5l3.7-3.7l1.4 1.45L10.9 18z"/></svg>
                                        </div>
                                        <div class="text-xs font-bold text-gray-800">Exams</div>
                                        <div class="text-[9px] text-gray-400 mt-0.5">Fungua →</div>
                                    </div>
                                </div>
                                <div class="absolute bottom-4 left-4 right-4 bg-white rounded-2xl flex items-center justify-around py-2.5 shadow-lg">
                                    <div class="flex flex-col items-center gap-0.5">
                                        <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 24 24"><path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8"/></svg>
                                        <div class="text-[8px] text-gray-400 font-medium">Home</div>
                                    </div>
                                    <div class="flex flex-col items-center gap-0.5">
                                        <svg class="w-5 h-5 text-brand-600" fill="currentColor" viewBox="0 0 24 24"><path d="M19 3h-4.18C14.25 1.44 12.53.64 11 1.2c-.86.3-1.5.96-1.82 1.8H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V5a2 2 0 0 0-2-2m-7 0a1 1 0 0 1 1 1a1 1 0 0 1-1 1a1 1 0 0 1-1-1a1 1 0 0 1 1-1M7 7h10V5h2v14H5V5h2v2m10 4H7V9h10v2m-2 4H7v-2h8v2z"/></svg>
                                        <div class="text-[8px] text-brand-600 font-bold">Materials</div>
                                    </div>
                                    <div class="flex flex-col items-center gap-0.5">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        <div class="text-[8px] text-gray-400 font-medium">Orders</div>
                                    </div>
                                    <div class="flex flex-col items-center gap-0.5">
                                        <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 24 24"><path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                        <div class="text-[8px] text-gray-400 font-medium">More</div>
                                    </div>
                                </div>
                            </div>

                            {{-- Screen 2: Download progress --}}
                            <div class="screen pt-10 px-4 pb-4 bg-gradient-to-br from-brand-600 to-brand-800">
                                <div class="text-white font-extrabold text-lg font-display mb-1">Downloads</div>
                                <div class="text-white/60 text-xs mb-5">Materials zako zilizopakuliwa</div>
                                <div class="space-y-3">
                                    <div class="bg-white rounded-2xl p-3.5 flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl bg-rose-50 flex items-center justify-center shrink-0">
                                            <svg class="w-5 h-5 text-rose-500" fill="currentColor" viewBox="0 0 24 24"><path d="M6 2a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8l-6-6H6z"/></svg>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="text-xs font-bold text-gray-800 truncate">Lesson Plan — Hisabati F.2</div>
                                            <div class="h-1.5 bg-gray-100 rounded-full mt-1.5 overflow-hidden"><div class="h-full w-full bg-brand-500 rounded-full"></div></div>
                                        </div>
                                        <svg class="w-5 h-5 text-green-500 shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                                    </div>
                                    <div class="bg-white rounded-2xl p-3.5 flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center shrink-0">
                                            <svg class="w-5 h-5 text-amber-500" fill="currentColor" viewBox="0 0 24 24"><path d="M6 2a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8l-6-6H6z"/></svg>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="text-xs font-bold text-gray-800 truncate">NECTA Mock — Physics F.4</div>
                                            <div class="h-1.5 bg-gray-100 rounded-full mt-1.5 overflow-hidden"><div class="h-full w-[68%] bg-gold-500 rounded-full" style="animation: progGrow 2.5s ease-in-out infinite alternate;"></div></div>
                                        </div>
                                        <div class="text-[10px] font-bold text-gold-600 shrink-0">68%</div>
                                    </div>
                                    <div class="bg-white rounded-2xl p-3.5 flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl bg-indigo-50 flex items-center justify-center shrink-0">
                                            <svg class="w-5 h-5 text-indigo-500" fill="currentColor" viewBox="0 0 24 24"><path d="M6 2a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8l-6-6H6z"/></svg>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="text-xs font-bold text-gray-800 truncate">Scheme of Work — Kiswahili</div>
                                            <div class="h-1.5 bg-gray-100 rounded-full mt-1.5 overflow-hidden"><div class="h-full w-full bg-brand-500 rounded-full"></div></div>
                                        </div>
                                        <svg class="w-5 h-5 text-green-500 shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                                    </div>
                                </div>
                                <div class="mt-5 bg-gold-500/15 border border-gold-400/30 rounded-2xl p-4">
                                    <div class="text-gold-300 text-xs font-bold mb-1">💡 Offline Mode</div>
                                    <div class="text-white/70 text-[11px] leading-relaxed">Materials zilizopakuliwa zinapatikana hata bila mtandao.</div>
                                </div>
                            </div>

                            {{-- Screen indicator dots --}}
                            <div class="absolute bottom-1.5 left-1/2 -translate-x-1/2 z-30 screen-dots" id="screenDots">
                                <span class="on"></span><span></span>
                            </div>
                        </div>

                        {{-- Floating badges --}}
                        <div class="absolute -left-8 top-20 bg-white rounded-2xl shadow-xl p-3 flex items-center gap-2 float-anim">
                            <div class="w-9 h-9 rounded-xl bg-green-100 flex items-center justify-center">
                                <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                            </div>
                            <div>
                                <div class="text-xs font-bold text-gray-800">Imepakuliwa</div>
                                <div class="text-[10px] text-gray-400">Hivi karibuni</div>
                            </div>
                        </div>
                        <div class="absolute -right-6 bottom-32 bg-white rounded-2xl shadow-xl p-3 flex items-center gap-2 float-slow">
                            <div class="w-9 h-9 rounded-xl bg-gold-400/20 flex items-center justify-center">
                                <svg class="w-5 h-5 text-gold-600" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01z"/></svg>
                            </div>
                            <div>
                                <div class="text-xs font-bold text-gray-800">4.8 Rating</div>
                                <div class="text-[10px] text-gray-400">User reviews</div>
                            </div>
                        </div>
                        <div class="absolute -right-10 top-10 bg-white rounded-2xl shadow-xl px-3.5 py-2.5 float-anim hidden sm:block" style="animation-delay:1.2s;">
                            <div class="font-chalk text-xl text-brand-600 leading-none">Karibu Mwalimu! 👋</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ================= SUBJECT MARQUEE ================= --}}
    <section class="bg-brand-700 py-5 border-y-4 border-gold-500/70 relative overflow-hidden">
        <div class="marquee">
            <div class="marquee-track items-center">
                @php
                    $subjects = ['Hisabati','Kiswahili','English','Physics','Chemistry','Biology','Geography','History','Civics','Book Keeping','Commerce','Science','Uraia','Maarifa'];
                @endphp
                @for ($i = 0; $i < 2; $i++)
                    @foreach ($subjects as $s)
                        <span class="flex items-center gap-3 px-5">
                            <span class="font-chalk text-2xl text-white/90 whitespace-nowrap">{{ $s }}</span>
                            <svg class="w-3 h-3 text-gold-400 shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01z"/></svg>
                        </span>
                    @endforeach
                @endfor
            </div>
        </div>
    </section>

    {{-- ================= FEATURES ================= --}}
    <section id="features" class="py-16 lg:py-24 bg-white relative overflow-hidden">
        <div class="orb absolute top-40 -right-32 w-96 h-96 bg-brand-50 rounded-full blur-3xl"></div>
        <div class="relative max-w-[1200px] mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-14 reveal">
                <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-brand-50 text-brand-600 text-xs font-bold mb-4">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01z"/></svg>
                    Features Za App
                </div>
                <h2 class="font-display text-3xl lg:text-4xl font-extrabold tracking-tight mb-4">
                    Kila Kitu Unachohitaji,
                    <span class="relative inline-block text-brand-600">
                        Mahali Pamoja
                        <svg class="chalk-draw-scroll absolute -bottom-2 left-0 w-full h-3" viewBox="0 0 200 12" fill="none" preserveAspectRatio="none">
                            <path class="chalk-path" d="M3 8 C 50 3, 130 10, 197 5" stroke="#f9ac00" stroke-width="4" stroke-linecap="round"/>
                        </svg>
                    </span>
                </h2>
                <p class="text-gray-600">App ya Darasa Huru inakuletea zana zote za kufundisha na kuandaa mada kwenye simu yako.</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6" data-stagger>
                <div class="f-card bg-gradient-to-br from-blue-50 to-white rounded-3xl p-7 border border-blue-100/60">
                    <div class="f-icon w-14 h-14 rounded-2xl bg-gradient-to-br from-blue-500 to-cyan-500 flex items-center justify-center text-white shadow-lg shadow-blue-500/25 mb-5">
                        <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 24 24"><path d="M19 3h-1V1h-2v2H8V1H6v2H5a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2V5a2 2 0 00-2-2m0 16H5V10h14v9M7 12h5v5H7v-5z"/></svg>
                    </div>
                    <h3 class="text-lg font-bold mb-2">Lesson Plans</h3>
                    <p class="text-sm text-gray-600 leading-relaxed">Andaa na upakue lesson plans zilizoandaliwa kwa kila darasa na somo. Zipo tayari kutumia.</p>
                </div>
                <div class="f-card bg-gradient-to-br from-rose-50 to-white rounded-3xl p-7 border border-rose-100/60">
                    <div class="f-icon w-14 h-14 rounded-2xl bg-gradient-to-br from-rose-500 to-pink-500 flex items-center justify-center text-white shadow-lg shadow-rose-500/25 mb-5">
                        <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 24 24"><path d="M19 2l-5 4.5v11l5-4.5V2M6.5 5C4.55 5 2.45 5.4 1 6.5v14.66c0 .25.25.5.5.5.1 0 .15-.07.25-.07 1.35-.65 3.3-1.09 4.75-1.09 1.95 0 4.05.4 5.5 1.5 1.35-.85 3.8-1.5 5.5-1.5 1.65 0 3.35.31 4.75 1.06.1.05.15.03.25.03.25 0 .5-.25.5-.5V6.5c-.6-.45-1.25-.75-2-1V19c-1.1-.35-2.3-.5-3.5-.5-1.7 0-4.15.65-5.5 1.5V6.5C10.55 5.4 8.45 5 6.5 5z"/></svg>
                    </div>
                    <h3 class="text-lg font-bold mb-2">Subject Notes</h3>
                    <p class="text-sm text-gray-600 leading-relaxed">Pata notes za kila somo zilizoandaliwa vizuri kwa ajili ya kufundisha na kujifunzia.</p>
                </div>
                <div class="f-card bg-gradient-to-br from-amber-50 to-white rounded-3xl p-7 border border-amber-100/60">
                    <div class="f-icon w-14 h-14 rounded-2xl bg-gradient-to-br from-amber-500 to-orange-500 flex items-center justify-center text-white shadow-lg shadow-amber-500/25 mb-5">
                        <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6m4 18H6V4h7v5h5v11m-9.1-2L7 14.1l1.4-1.4l1.5 1.5l3.7-3.7l1.4 1.45L10.9 18z"/></svg>
                    </div>
                    <h3 class="text-lg font-bold mb-2">Mitihani</h3>
                    <p class="text-sm text-gray-600 leading-relaxed">Topical exams, mock exams, NECTA na regional exams zote mahali pamoja. Tayari kwa revision.</p>
                </div>
                <div class="f-card bg-gradient-to-br from-emerald-50 to-white rounded-3xl p-7 border border-emerald-100/60">
                    <div class="f-icon w-14 h-14 rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-500 flex items-center justify-center text-white shadow-lg shadow-emerald-500/25 mb-5">
                        <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 24 24"><path d="M6 2a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8l-6-6H6m0 2h7v5h5v11H6V4m2 8v2h8v-2H8m0 4v2h5v-2H8z"/></svg>
                    </div>
                    <h3 class="text-lg font-bold mb-2">Schemes of Work</h3>
                    <p class="text-sm text-gray-600 leading-relaxed">Schemes za kila somo na darasa. Panga mada zako kwa urahisi na upate muongozo kamili.</p>
                </div>
                <div class="f-card bg-gradient-to-br from-violet-50 to-white rounded-3xl p-7 border border-violet-100/60">
                    <div class="f-icon w-14 h-14 rounded-2xl bg-gradient-to-br from-violet-500 to-purple-500 flex items-center justify-center text-white shadow-lg shadow-violet-500/25 mb-5">
                        <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 24 24"><path d="M17 4v6l-2-2l-2 2V4H9v16h10V4h-2M7 7V5h2V4a2 2 0 0 1 2-2h12c1.05 0 2 .95 2 2v16c0 1.05-.95 2-2 2H7c-1.05 0-2-.95-2-2v-1H3v-2h2v-4H3v-2h2V7H3m2-2v2h2V5H5m0 14h2v-2H5v2m0-6h2v-2H5v2z"/></svg>
                    </div>
                    <h3 class="text-lg font-bold mb-2">Logbooks</h3>
                    <p class="text-sm text-gray-600 leading-relaxed">Jaza na udhibiti logbook zako kwa kila somo. Fuatilia maendeleo yako kwa urahisi.</p>
                </div>
                <div class="f-card bg-gradient-to-br from-indigo-50 to-white rounded-3xl p-7 border border-indigo-100/60">
                    <div class="f-icon w-14 h-14 rounded-2xl bg-gradient-to-br from-indigo-500 to-blue-600 flex items-center justify-center text-white shadow-lg shadow-indigo-500/25 mb-5">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    </div>
                    <h3 class="text-lg font-bold mb-2">Vitabu</h3>
                    <p class="text-sm text-gray-600 leading-relaxed">Upatikanaji wa vitabu mbalimbali vya kiada kwa ajili ya kufundisha na kujifunzia.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- ================= HOW IT WORKS ================= --}}
    <section id="how-it-works" class="py-16 lg:py-24 bg-gradient-to-b from-brand-50/40 to-white relative overflow-hidden paper-lines">
        <div class="max-w-[1200px] mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-16 reveal">
                <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-gold-400/15 text-gold-600 text-xs font-bold mb-4">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M11 2v2h7.586L8.293 14.293l1.414 1.414L20 5.414V13h2V2z"/></svg>
                    Jinsi Inavyofanya Kazi
                </div>
                <h2 class="font-display text-3xl lg:text-4xl font-extrabold tracking-tight mb-4">Hatua Tatu Tu</h2>
                <p class="text-gray-600">Anza kutumia app kwa muda mfupi. Rahisi kwa kila mtu.</p>
            </div>

            <div class="relative" id="stepsWrap">
                {{-- Animated dashed connector (desktop) --}}
                <svg class="hidden md:block absolute top-10 left-0 w-full h-24 pointer-events-none" viewBox="0 0 1000 100" fill="none" preserveAspectRatio="none">
                    <path class="step-line" d="M 130 40 C 300 -20, 380 90, 500 40 C 620 -10, 720 95, 870 40" stroke="#f9ac00" stroke-width="3" stroke-linecap="round" opacity=".7"/>
                </svg>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-10" data-stagger>
                    <div class="step-item text-center relative">
                        <div class="relative inline-flex items-center justify-center mb-6">
                            <div class="step-badge w-20 h-20 rounded-3xl bg-gradient-to-br from-brand-500 to-brand-700 flex items-center justify-center text-white shadow-xl shadow-brand-500/25">
                                <svg class="w-9 h-9" fill="currentColor" viewBox="0 0 24 24"><path d="M5 20h14v-2H5m14-9h-3V4H8v5H5l7 7 7-7z"/></svg>
                            </div>
                            <span class="absolute -top-2 -right-2 w-8 h-8 rounded-full bg-gold-500 text-white text-sm font-black flex items-center justify-center shadow-lg">1</span>
                        </div>
                        <h3 class="text-lg font-bold mb-2">Pakua App</h3>
                        <p class="text-sm text-gray-600 max-w-[260px] mx-auto">Pakua app ya Darasa Huru kwenye Google Play Store au App Store bila malipo.</p>
                    </div>
                    <div class="step-item text-center relative">
                        <div class="relative inline-flex items-center justify-center mb-6">
                            <div class="step-badge w-20 h-20 rounded-3xl bg-gradient-to-br from-gold-400 to-gold-600 flex items-center justify-center text-white shadow-xl shadow-gold-500/30">
                                <svg class="w-9 h-9" fill="currentColor" viewBox="0 0 24 24"><path d="M15 14c-2.67 0-8 1.34-8 4v3h16v-3c0-2.66-5.33-4-8-4m0-2a4 4 0 0 0 4-4 4 4 0 0 0-4-4 4 4 0 0 0-4 4 4 4 0 0 0 4 4z"/></svg>
                            </div>
                            <span class="absolute -top-2 -right-2 w-8 h-8 rounded-full bg-brand-600 text-white text-sm font-black flex items-center justify-center shadow-lg">2</span>
                        </div>
                        <h3 class="text-lg font-bold mb-2">Jisajili</h3>
                        <p class="text-sm text-gray-600 max-w-[260px] mx-auto">Tengeneza akaunti yako kwa haraka. Chagua level na darasa lako la kufundisha.</p>
                    </div>
                    <div class="step-item text-center relative">
                        <div class="relative inline-flex items-center justify-center mb-6">
                            <div class="step-badge w-20 h-20 rounded-3xl bg-gradient-to-br from-brand-500 to-brand-700 flex items-center justify-center text-white shadow-xl shadow-brand-500/25">
                                <svg class="w-9 h-9" fill="currentColor" viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                            </div>
                            <span class="absolute -top-2 -right-2 w-8 h-8 rounded-full bg-gold-500 text-white text-sm font-black flex items-center justify-center shadow-lg">3</span>
                        </div>
                        <h3 class="text-lg font-bold mb-2">Anza Kutumia</h3>
                        <p class="text-sm text-gray-600 max-w-[260px] mx-auto">Fikia materials zote, pakua, na tumia kwa kufundisha. Ni rahisi hapo hapo!</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ================= TESTIMONIALS ================= --}}
    <section id="maoni" class="py-16 lg:py-24 bg-white relative overflow-hidden">
        <div class="orb2 absolute -bottom-24 -left-24 w-96 h-96 bg-gold-400/10 rounded-full blur-3xl"></div>
        <div class="relative max-w-[1200px] mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-14 reveal">
                <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-brand-50 text-brand-600 text-xs font-bold mb-4">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01z"/></svg>
                    Maoni Ya Watumiaji
                </div>
                <h2 class="font-display text-3xl lg:text-4xl font-extrabold tracking-tight mb-2">Walimu Wanapenda App</h2>
                <p class="font-chalk text-2xl text-gold-600">"Ni zawadi kwa walimu!"</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6" data-stagger>
                <div class="t-card bg-gradient-to-br from-gray-50 to-white rounded-3xl p-7 border border-gray-100">
                    <div class="flex gap-1 mb-4 text-gold-500">
                        @for ($i = 0; $i < 5; $i++)<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01z"/></svg>@endfor
                    </div>
                    <p class="text-sm text-gray-700 leading-relaxed mb-5">"App hii imefanya kazi yangu kuwa rahisi sana. Sasa ninapata lesson plans na notes kwa urahisi bila kutafuta sana."</p>
                    <div class="flex items-center gap-3">
                        <div class="w-11 h-11 rounded-full bg-gradient-to-br from-brand-500 to-brand-700 flex items-center justify-center text-white font-bold">A</div>
                        <div>
                            <div class="text-sm font-bold">Mwalimu Asha</div>
                            <div class="text-xs text-gray-400">Shule ya Msingi, Dar es Salaam</div>
                        </div>
                    </div>
                </div>
                <div class="t-card bg-gradient-to-br from-gray-50 to-white rounded-3xl p-7 border border-gray-100 md:-translate-y-3">
                    <div class="flex gap-1 mb-4 text-gold-500">
                        @for ($i = 0; $i < 5; $i++)<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01z"/></svg>@endfor
                    </div>
                    <p class="text-sm text-gray-700 leading-relaxed mb-5">"Nimeokoa muda mwingi sana. Schemes na logbooks zipo tayari, nafanya tu kuzibadilisha kidogo kulingana na darasa langu."</p>
                    <div class="flex items-center gap-3">
                        <div class="w-11 h-11 rounded-full bg-gradient-to-br from-gold-400 to-gold-600 flex items-center justify-center text-white font-bold">J</div>
                        <div>
                            <div class="text-sm font-bold">Mwalimu Juma</div>
                            <div class="text-xs text-gray-400">Sekondari, Arusha</div>
                        </div>
                    </div>
                </div>
                <div class="t-card bg-gradient-to-br from-gray-50 to-white rounded-3xl p-7 border border-gray-100">
                    <div class="flex gap-1 mb-4 text-gold-500">
                        @for ($i = 0; $i < 5; $i++)<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01z"/></svg>@endfor
                    </div>
                    <p class="text-sm text-gray-700 leading-relaxed mb-5">"Mitihani mingi ipo tayari. Wanafunzi wangu wanapenda revision kwa kutumia app hii. Ni zawadi kwa walimu!"</p>
                    <div class="flex items-center gap-3">
                        <div class="w-11 h-11 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white font-bold">N</div>
                        <div>
                            <div class="text-sm font-bold">Mwalimu Neema</div>
                            <div class="text-xs text-gray-400">Sekondari, Mwanza</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ================= DOWNLOAD CTA ================= --}}
    <section id="download" class="py-16 lg:py-28 bg-gradient-to-br from-brand-600 via-brand-700 to-brand-800 relative overflow-hidden">
        <div class="orb absolute top-0 right-0 w-[420px] h-[420px] bg-gold-400/10 rounded-full blur-3xl"></div>
        <div class="orb2 absolute bottom-0 left-0 w-[420px] h-[420px] bg-white/5 rounded-full blur-3xl"></div>
        {{-- chalk doodles --}}
        <svg class="absolute top-12 left-8 w-24 h-24 text-white/10 float-slow hidden lg:block" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 100 100"><circle cx="50" cy="50" r="40" stroke-dasharray="8 10"/></svg>
        <svg class="absolute bottom-16 right-10 w-20 h-20 text-gold-400/20 float-anim hidden lg:block" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01z"/></svg>

        <div class="relative max-w-[1000px] mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white/10 text-white text-xs font-bold mb-6 reveal">
                <span class="w-2 h-2 rounded-full bg-gold-400 animate-pulse"></span>
                Pakua App Sasa
            </div>
            <h2 class="font-display text-3xl lg:text-5xl font-extrabold text-white mb-6 leading-tight reveal">
                Anza Safari Yako Ya<br>
                <span class="text-gold-400 relative inline-block">
                    Kufundisha Kwa Rahisi
                    <svg class="chalk-draw-scroll absolute -bottom-3 left-0 w-full h-4" viewBox="0 0 300 16" fill="none" preserveAspectRatio="none">
                        <path class="chalk-path" d="M4 10 C 80 4, 200 14, 296 7" stroke="#fff" stroke-width="4" stroke-linecap="round" opacity=".6"/>
                    </svg>
                </span>
            </h2>
            <p class="text-brand-100 text-base lg:text-lg mb-10 max-w-xl mx-auto reveal">
                Jiunge na walimu zaidi ya <span class="font-extrabold text-gold-400">10,000</span> wanaotumia Darasa Huru kila siku. App ni bure kupakua.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center mb-10" data-stagger>
                <a href="#" class="btn-store relative inline-flex items-center justify-center gap-3 px-8 py-4 rounded-2xl bg-white text-brand-700 font-bold shadow-2xl">
                    <span class="pulse-ring"></span>
                    <span class="pulse-ring r2"></span>
                    <svg class="w-7 h-7 relative" fill="currentColor" viewBox="0 0 24 24"><path d="M3 20.5V3.5C3 2.91 3.34 2.39 3.84 2.15L13.69 12L3.84 21.85C3.34 21.61 3 21.09 3 20.5M16.81 15.12L6.05 21.34L14.54 12.85L16.81 15.12M20.16 10.81C20.5 11.08 20.75 11.5 20.75 12C20.75 12.5 20.53 12.9 20.18 13.18L17.89 14.5L15.39 12L17.89 9.5L20.16 10.81M6.05 2.66L16.81 8.88L14.54 11.15L6.05 2.66Z"/></svg>
                    <span class="text-left relative">
                        <span class="block text-xs font-medium opacity-70">Pata kwenye</span>
                        <span class="block text-base font-extrabold">Google Play</span>
                    </span>
                </a>
                <a href="#" class="btn-store inline-flex items-center justify-center gap-3 px-8 py-4 rounded-2xl bg-gray-900 text-white font-bold shadow-2xl">
                    <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 24 24"><path d="M18.71 19.5C17.88 20.74 17 21.95 15.66 21.97C14.32 22 13.89 21.18 12.37 21.18C10.84 21.18 10.37 21.95 9.1 22C7.79 22.05 6.8 20.68 5.96 19.47C4.25 17 2.94 12.45 4.7 9.39C5.57 7.87 7.13 6.91 8.82 6.88C10.1 6.86 11.32 7.75 12.11 7.75C12.89 7.75 14.37 6.68 15.9 6.84C16.55 6.87 18.39 7.1 19.57 8.82C19.47 8.88 17.39 10.1 17.41 12.63C17.44 15.65 20.06 16.66 20.09 16.67C20.06 16.74 19.67 18.11 18.71 19.5M13 3.5C13.73 2.67 14.94 2.04 15.94 2C16.07 3.17 15.6 4.35 14.9 5.19C14.21 6.04 13.07 6.7 11.95 6.61C11.8 5.46 12.36 4.26 13 3.5Z"/></svg>
                    <span class="text-left">
                        <span class="block text-xs font-medium opacity-70">Pata kwenye</span>
                        <span class="block text-base font-extrabold">App Store</span>
                    </span>
                </a>
            </div>
            <div class="inline-flex items-center gap-4 bg-white/10 rounded-2xl px-6 py-4 backdrop-blur-sm border border-white/10 reveal-zoom hover:bg-white/15 transition">
                <div class="w-20 h-20 bg-white rounded-xl flex items-center justify-center">
                    <svg class="w-16 h-16 text-brand-800" fill="currentColor" viewBox="0 0 24 24"><path d="M3 3h7v7H3V3m2 2v3h3V5H5m9-2h7v7h-7V3m2 2v3h3V5h-3M3 14h7v7H3v-7m2 2v3h3v-3H5m9 2h2v2h-2v-2m2 0h2v2h-2v-2m-2-4h2v2h-2v-2m4 0h2v2h-2v-2m0 4h2v2h-2v-2m2 0h2v2h-2v-2m0-4h2v2h-2v-2m-2 0h-2v-2h2v2m-2 0h-2v-2h2v2m-2 2h2v2h-2v-2m4 2h2v2h-2v-2m0-2h2v2h-2v-2"/></svg>
                </div>
                <div class="text-left">
                    <div class="text-white font-bold text-sm">Skani QR Code</div>
                    <div class="text-white/60 text-xs">Kupakua app kwenye simu yako moja kwa moja</div>
                </div>
            </div>
        </div>
    </section>

    {{-- ================= FOOTER ================= --}}
    <footer class="bg-brand-900 text-white/80 py-12">
        <div class="max-w-[1200px] mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-8">
                <div>
                    <h3 class="font-display text-xl font-extrabold text-white mb-3">Darasa<span class="text-gold-500">Huru</span></h3>
                    <p class="text-sm text-white/60 leading-relaxed mb-4 max-w-xs">
                        Mtandao wa kidijitali kwa ajili ya elimu nchini Tanzania — kuwawezesha walimu kwa zana za kisasa.
                    </p>
                    <div class="flex gap-2">
                        <a href="#" aria-label="Twitter" class="w-9 h-9 rounded-full bg-white/10 flex items-center justify-center hover:bg-gold-500 hover:scale-110 transition-all duration-300">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M22.46 6c-.77.35-1.6.58-2.46.69c.88-.53 1.56-1.37 1.88-2.38c-.83.5-1.75.85-2.72 1.05C18.37 4.5 17.26 4 16 4c-2.35 0-4.27 1.92-4.27 4.29c0 .34.04.67.11.98C8.28 9.09 5.11 7.38 3 4.79c-.37.63-.58 1.37-.58 2.15c0 1.49.75 2.81 1.91 3.56c-.71 0-1.37-.2-1.95-.5v.03c0 2.08 1.48 3.82 3.44 4.21a4.22 4.22 0 0 1-1.93.07a4.28 4.28 0 0 0 4 2.98a8.521 8.521 0 0 1-5.33 1.84c-.34 0-.68-.02-1.02-.06C3.44 20.29 5.7 21 8.12 21C16 21 20.33 14.46 20.33 8.79c0-.19 0-.37-.01-.56c.84-.6 1.56-1.36 2.14-2.23Z"/></svg>
                        </a>
                        <a href="#" aria-label="LinkedIn" class="w-9 h-9 rounded-full bg-white/10 flex items-center justify-center hover:bg-gold-500 hover:scale-110 transition-all duration-300">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M19 3a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h14m-.5 15.5v-5.3a3.26 3.26 0 0 0-3.26-3.26c-.85 0-1.84.52-2.32 1.3v-1.11h-2.79v8.37h2.79v-4.93c0-.77.62-1.4 1.39-1.4a1.4 1.4 0 0 1 1.4 1.4v4.93h2.79M6.88 8.56a1.68 1.68 0 0 0 1.68-1.68c0-.93-.75-1.69-1.68-1.69a1.69 1.69 0 0 0-1.69 1.69c0 .93.76 1.68 1.69 1.68m1.39 9.94v-8.37H5.5v8.37h2.77Z"/></svg>
                        </a>
                        <a href="#" aria-label="Facebook" class="w-9 h-9 rounded-full bg-white/10 flex items-center justify-center hover:bg-gold-500 hover:scale-110 transition-all duration-300">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.04c-5.5 0-10 4.49-10 10.02c0 5 3.66 9.15 8.44 9.9v-7H7.9v-2.9h2.54V9.85c0-2.51 1.49-3.89 3.78-3.89c1.09 0 2.23.19 2.23.19v2.47h-1.26c-1.24 0-1.63.77-1.63 1.56v1.88h2.78l-.45 2.9h-2.33v7a10 10 0 0 0 8.44-9.9c0-5.53-4.5-10.02-10-10.02Z"/></svg>
                        </a>
                    </div>
                </div>
                <div>
                    <h6 class="text-base font-bold text-white mb-4">Links</h6>
                    <div class="flex flex-col gap-2">
                        <a href="#features" class="text-sm text-white/60 hover:text-gold-400 hover:translate-x-1 inline-block transition-all">Features</a>
                        <a href="#how-it-works" class="text-sm text-white/60 hover:text-gold-400 hover:translate-x-1 inline-block transition-all">Jinsi Inavyofanya Kazi</a>
                        <a href="#download" class="text-sm text-white/60 hover:text-gold-400 hover:translate-x-1 inline-block transition-all">Pakua App</a>
                        <a href="{{ route('landing') }}" class="text-sm text-white/60 hover:text-gold-400 hover:translate-x-1 inline-block transition-all">Browse Materials</a>
                    </div>
                </div>
                <div>
                    <h6 class="text-base font-bold text-white mb-4">Wasiliana Nasi</h6>
                    <a href="mailto:info@darasahuru.com" class="flex items-center gap-3 p-3 rounded-2xl bg-white/5 hover:bg-white/10 transition">
                        <div class="w-10 h-10 rounded-full bg-gold-500 flex items-center justify-center text-white shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        </div>
                        <div>
                            <span class="text-xs text-white/50 block">Email</span>
                            <span class="text-sm font-medium text-white">info@darasahuru.com</span>
                        </div>
                    </a>
                </div>
            </div>
            <hr class="border-white/10 my-6">
            <div class="flex flex-col sm:flex-row justify-between items-center gap-4 text-sm text-white/50">
                <p>&copy; {{ date('Y') }} Darasa Huru. Haki zote zimehifadhiwa.</p>
                <p class="font-chalk text-lg text-gold-500/80">Kuwawezesha Walimu Tanzania ✏️</p>
            </div>
        </div>
    </footer>

    {{-- Back to top --}}
    <button id="backTop" aria-label="Rudi juu" class="w-12 h-12 rounded-2xl bg-brand-600 text-white shadow-xl shadow-brand-500/30 flex items-center justify-center hover:bg-brand-700 hover:-translate-y-1 transition-all">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 15l7-7 7 7"/></svg>
    </button>

    <script>
    (function () {
        const reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

        /* ---------- Preloader ---------- */
        const preloader = document.getElementById('preloader');
        window.addEventListener('load', () => {
            setTimeout(() => preloader.classList.add('hide'), reduced ? 0 : 500);
        });
        // Safety fallback
        setTimeout(() => preloader.classList.add('hide'), 3000);

        /* ---------- Scroll progress + navbar + back-to-top ---------- */
        const progress = document.getElementById('scrollProgress');
        const navbar = document.getElementById('navbar');
        const backTop = document.getElementById('backTop');
        function onScroll() {
            const h = document.documentElement;
            const scrolled = h.scrollTop / (h.scrollHeight - h.clientHeight);
            progress.style.width = (scrolled * 100) + '%';
            navbar.classList.toggle('scrolled', h.scrollTop > 10);
            backTop.classList.toggle('show', h.scrollTop > 600);
        }
        window.addEventListener('scroll', onScroll, { passive: true });
        onScroll();
        backTop.addEventListener('click', () => window.scrollTo({ top: 0, behavior: reduced ? 'auto' : 'smooth' }));

        /* ---------- Hero word reveal ---------- */
        const title = document.getElementById('heroTitle');
        if (title && !reduced) {
            const line2 = document.getElementById('heroLine2');
            const splitWords = (el) => {
                const words = el.textContent.trim().split(/\s+/);
                el.innerHTML = words.map(w => `<span class="w">${w}</span>`).join(' ');
            };
            // Split first text node ("Elimu Yako,")
            const firstText = title.childNodes[0];
            if (firstText && firstText.nodeType === 3) {
                const span = document.createElement('span');
                span.textContent = firstText.textContent.trim();
                title.replaceChild(span, firstText);
                splitWords(span);
            }
            splitWords(line2);
            title.querySelectorAll('.w').forEach((w, i) => {
                w.style.animationDelay = (0.1 + i * 0.12) + 's';
            });
        }

        /* ---------- Typing rotator ---------- */
        const typeTarget = document.getElementById('typeTarget');
        if (typeTarget && !reduced) {
            const words = ['lesson plans', 'notes', 'mitihani', 'schemes of work', 'logbooks', 'vitabu'];
            let wi = 0, ci = words[0].length, deleting = true;
            setTimeout(function tick() {
                const word = words[wi];
                if (deleting) {
                    ci--;
                    typeTarget.textContent = word.slice(0, ci);
                    if (ci === 0) { deleting = false; wi = (wi + 1) % words.length; }
                    setTimeout(tick, 45);
                } else {
                    ci++;
                    typeTarget.textContent = words[wi].slice(0, ci);
                    if (ci === words[wi].length) { deleting = true; setTimeout(tick, 2000); return; }
                    setTimeout(tick, 90);
                }
            }, 2500);
        }

        /* ---------- Reveal on scroll ---------- */
        const io = new IntersectionObserver((entries) => {
            entries.forEach(e => {
                if (!e.isIntersecting) return;
                e.target.classList.add('in');
                if (e.target.hasAttribute('data-stagger')) {
                    [...e.target.children].forEach((c, i) => c.style.transitionDelay = (i * 0.12) + 's');
                }
                if (e.target.id === 'stepsWrap') e.target.classList.add('steps-in');
                if (e.target.classList.contains('chalk-draw-scroll')) e.target.classList.add('chalk-draw');
                io.unobserve(e.target);
            });
        }, { threshold: 0.15 });
        document.querySelectorAll('.reveal, .reveal-left, .reveal-right, .reveal-zoom, [data-stagger], #stepsWrap, .chalk-draw-scroll')
            .forEach(el => io.observe(el));

        /* ---------- Animated counters ---------- */
        const statsIO = new IntersectionObserver((entries) => {
            entries.forEach(e => {
                if (!e.isIntersecting) return;
                e.target.querySelectorAll('[data-count]').forEach(el => {
                    const target = parseInt(el.dataset.count, 10);
                    const decimal = el.dataset.decimal ? parseInt(el.dataset.decimal, 10) : 0;
                    const suffix = el.dataset.suffix || '';
                    const dur = 1800, start = performance.now();
                    function step(now) {
                        const p = Math.min((now - start) / dur, 1);
                        const eased = 1 - Math.pow(1 - p, 3);
                        let val = target * eased;
                        if (decimal) {
                            el.textContent = (val / Math.pow(10, decimal)).toFixed(decimal) + suffix;
                        } else {
                            el.textContent = Math.floor(val).toLocaleString('en-US') + suffix;
                        }
                        if (p < 1) requestAnimationFrame(step);
                    }
                    if (reduced) {
                        el.textContent = (decimal ? (target / Math.pow(10, decimal)).toFixed(decimal) : target.toLocaleString('en-US')) + suffix;
                    } else {
                        requestAnimationFrame(step);
                    }
                });
                statsIO.unobserve(e.target);
            });
        }, { threshold: 0.6 });
        const statsRow = document.getElementById('statsRow');
        if (statsRow) statsIO.observe(statsRow);

        /* ---------- Phone: 3D tilt + screen carousel ---------- */
        const zone = document.getElementById('phoneTiltZone');
        const shell = document.getElementById('phoneShell');
        if (zone && shell && !reduced && matchMedia('(pointer:fine)').matches) {
            zone.addEventListener('mousemove', (ev) => {
                const r = zone.getBoundingClientRect();
                const x = (ev.clientX - r.left) / r.width - 0.5;
                const y = (ev.clientY - r.top) / r.height - 0.5;
                shell.style.transform = `perspective(900px) rotateY(${x * 12}deg) rotateX(${-y * 10}deg)`;
            });
            zone.addEventListener('mouseleave', () => {
                shell.style.transform = 'perspective(900px) rotateY(0) rotateX(0)';
            });
        }

        const screens = shell ? shell.querySelectorAll('.screen') : [];
        const dots = document.querySelectorAll('#screenDots span');
        if (screens.length > 1 && !reduced) {
            let si = 0;
            setInterval(() => {
                si = (si + 1) % screens.length;
                screens.forEach((s, i) => s.classList.toggle('active', i === si));
                dots.forEach((d, i) => d.classList.toggle('on', i === si));
            }, 5000);
        }

        /* ---------- Feature card mouse glow ---------- */
        document.querySelectorAll('.f-card').forEach(card => {
            card.addEventListener('mousemove', (ev) => {
                const r = card.getBoundingClientRect();
                card.style.setProperty('--mx', (ev.clientX - r.left) + 'px');
                card.style.setProperty('--my', (ev.clientY - r.top) + 'px');
            });
        });
    })();
    </script>
    <style>
        @keyframes progGrow { from { width: 40%; } to { width: 85%; } }
    </style>
</body>
</html>