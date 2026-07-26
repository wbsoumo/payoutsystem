<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') - Novexapay | Enterprise FinTech Portal</title>
    
    <!-- Premium Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS & Custom Config -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'Outfit', 'sans-serif'],
                        display: ['Outfit', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50: '#f0f4ff',
                            100: '#e1e9ff',
                            200: '#c7d7ff',
                            300: '#9fbaff',
                            400: '#7093ff',
                            500: '#4361ee', // Primary Premium Blue
                            600: '#2b3ff2',
                            700: '#1e2bd3',
                            800: '#1b24ab',
                            900: '#1c2488',
                            950: '#101452',
                        },
                    }
                }
            }
        }
    </script>
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- FontAwesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        .glass-nav {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(67, 97, 238, 0.1);
        }
        .text-gradient {
            background: linear-gradient(135deg, #4361ee 0%, #101452 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .btn-gradient {
            background: linear-gradient(135deg, #4361ee 0%, #2b3ff2 100%);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .btn-gradient:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px -10px rgba(67, 97, 238, 0.5);
        }
        .shadow-soft {
            box-shadow: 0 20px 40px -15px rgba(0, 0, 0, 0.05);
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-900 antialiased font-sans flex flex-col min-h-screen">

    <!-- Header / Navigation -->
    <header class="fixed top-0 left-0 right-0 z-50 glass-nav" x-data="{ open: false }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <!-- Logo -->
                <div class="flex-shrink-0 flex items-center">
                    <a href="{{ route('home') }}" class="flex items-center gap-2">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-brand-500 to-brand-700 flex items-center justify-center shadow-lg shadow-brand-500/20">
                            <i class="fa-solid fa-feather-pointed text-white text-xl"></i>
                        </div>
                        <span class="text-2xl font-bold font-display tracking-tight text-brand-950">Novexa<span class="text-brand-500">pay</span></span>
                    </a>
                </div>

                <!-- Desktop Menu -->
                <nav class="hidden md:flex items-center space-x-8">
                    <a href="{{ route('features') }}" class="text-sm font-semibold text-slate-600 hover:text-brand-500 transition-colors">Features</a>
                    <a href="{{ route('pricing') }}" class="text-sm font-semibold text-slate-600 hover:text-brand-500 transition-colors">Pricing</a>
                    <a href="{{ route('docs') }}" class="text-sm font-semibold text-slate-600 hover:text-brand-500 transition-colors">API Docs</a>
                    <a href="{{ route('developers') }}" class="text-sm font-semibold text-slate-600 hover:text-brand-500 transition-colors">Developers</a>
                    <a href="{{ route('about') }}" class="text-sm font-semibold text-slate-600 hover:text-brand-500 transition-colors">About</a>
                    <a href="{{ route('security') }}" class="text-sm font-semibold text-slate-600 hover:text-brand-500 transition-colors">Security</a>
                </nav>

                <!-- Action Button -->
                <div class="hidden md:flex items-center gap-4">
                    <a href="{{ route('merchant.login') }}" class="text-sm font-semibold text-slate-700 hover:text-brand-500 transition-colors">Log In</a>
                    <a href="{{ route('contact') }}" class="btn-gradient text-sm font-semibold text-white px-6 h-11 rounded-xl flex items-center justify-center shadow-lg shadow-brand-500/10">Request Access</a>
                </div>

                <!-- Mobile Menu Button -->
                <div class="flex items-center md:hidden">
                    <button @click="open = !open" class="text-slate-600 hover:text-slate-900 focus:outline-none">
                        <i class="fa-solid" :class="open ? 'fa-xmark text-2xl' : 'fa-bars text-2xl'"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div x-show="open" 
             x-transition:enter="transition ease-out duration-100"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-75"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="md:hidden border-t border-slate-100 bg-white/95 backdrop-blur-md px-4 pt-2 pb-6 space-y-3">
            <a href="{{ route('features') }}" class="block px-3 py-2 rounded-lg text-base font-medium text-slate-700 hover:bg-slate-50 hover:text-brand-500">Features</a>
            <a href="{{ route('pricing') }}" class="block px-3 py-2 rounded-lg text-base font-medium text-slate-700 hover:bg-slate-50 hover:text-brand-500">Pricing</a>
            <a href="{{ route('docs') }}" class="block px-3 py-2 rounded-lg text-base font-medium text-slate-700 hover:bg-slate-50 hover:text-brand-500">API Docs</a>
            <a href="{{ route('developers') }}" class="block px-3 py-2 rounded-lg text-base font-medium text-slate-700 hover:bg-slate-50 hover:text-brand-500">Developers</a>
            <a href="{{ route('about') }}" class="block px-3 py-2 rounded-lg text-base font-medium text-slate-700 hover:bg-slate-50 hover:text-brand-500">About</a>
            <a href="{{ route('security') }}" class="block px-3 py-2 rounded-lg text-base font-medium text-slate-700 hover:bg-slate-50 hover:text-brand-500">Security</a>
            <div class="h-[1px] bg-slate-100 my-4"></div>
            <a href="{{ route('merchant.login') }}" class="block text-center px-3 py-2 rounded-lg text-base font-medium text-slate-700 hover:bg-slate-50">Log In</a>
            <a href="{{ route('contact') }}" class="block text-center btn-gradient text-white py-3 rounded-xl font-medium shadow-md shadow-brand-500/10">Request Access</a>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-grow pt-20">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-slate-900 text-slate-400 py-16 border-t border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 md:grid-cols-5 gap-8 mb-12">
                <!-- Branding column -->
                <div class="col-span-2 space-y-4">
                    <a href="{{ route('home') }}" class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg bg-gradient-to-tr from-brand-500 to-brand-700 flex items-center justify-center">
                            <i class="fa-solid fa-feather-pointed text-white text-sm"></i>
                        </div>
                        <span class="text-xl font-bold font-display tracking-tight text-white">Novexa<span class="text-brand-500">pay</span></span>
                    </a>
                    <p class="text-sm text-slate-400 max-w-sm">Enterprise-grade merchant wallets, real-time ledgers, custom commission slab overrides, and high-performance developer APIs built on secure, scalable architecture.</p>
                    <div class="flex gap-4">
                        <a href="#" class="w-8 h-8 rounded-lg bg-slate-800 flex items-center justify-center text-slate-400 hover:bg-brand-500 hover:text-white transition-colors"><i class="fa-brands fa-twitter"></i></a>
                        <a href="#" class="w-8 h-8 rounded-lg bg-slate-800 flex items-center justify-center text-slate-400 hover:bg-brand-500 hover:text-white transition-colors"><i class="fa-brands fa-linkedin-in"></i></a>
                        <a href="#" class="w-8 h-8 rounded-lg bg-slate-800 flex items-center justify-center text-slate-400 hover:bg-brand-500 hover:text-white transition-colors"><i class="fa-brands fa-github"></i></a>
                    </div>
                </div>

                <!-- Nav columns -->
                <div>
                    <h3 class="text-sm font-bold text-white tracking-wider uppercase mb-4">Product</h3>
                    <ul class="space-y-2 text-sm">
                        <li><a href="{{ route('features') }}" class="hover:text-white transition-colors">Features</a></li>
                        <li><a href="{{ route('pricing') }}" class="hover:text-white transition-colors">Pricing</a></li>
                        <li><a href="{{ route('status') }}" class="hover:text-white transition-colors">Status Page</a></li>
                    </ul>
                </div>

                <div>
                    <h3 class="text-sm font-bold text-white tracking-wider uppercase mb-4">Developers</h3>
                    <ul class="space-y-2 text-sm">
                        <li><a href="{{ route('docs') }}" class="hover:text-white transition-colors">API Reference</a></li>
                        <li><a href="{{ route('developers') }}" class="hover:text-white transition-colors">Libraries</a></li>
                        <li><a href="{{ route('security') }}" class="hover:text-white transition-colors">Security Audit</a></li>
                    </ul>
                </div>

                <div>
                    <h3 class="text-sm font-bold text-white tracking-wider uppercase mb-4">Company</h3>
                    <ul class="space-y-2 text-sm">
                        <li><a href="{{ route('about') }}" class="hover:text-white transition-colors">About Us</a></li>
                        <li><a href="{{ route('compliance') }}" class="hover:text-white transition-colors">Compliance</a></li>
                        <li><a href="{{ route('privacy') }}" class="hover:text-white transition-colors">Privacy Policy</a></li>
                        <li><a href="{{ route('terms') }}" class="hover:text-white transition-colors">Terms of Service</a></li>
                    </ul>
                </div>
            </div>

            <div class="h-[1px] bg-slate-800 my-8"></div>

            <div class="flex flex-col md:flex-row justify-between items-center gap-4 text-xs">
                <span>&copy; {{ date('Y') }} Novexapay (brand of Novexa Tech Private Limited). All rights reserved.</span>
                <span class="flex gap-4">
                    <a href="{{ route('privacy') }}" class="hover:text-white">Privacy</a>
                    <a href="{{ route('terms') }}" class="hover:text-white">Terms</a>
                    <a href="{{ route('compliance') }}" class="hover:text-white">Regulatory</a>
                </span>
            </div>
        </div>
    </footer>

</body>
</html>
