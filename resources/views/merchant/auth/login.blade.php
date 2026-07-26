<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Merchant Portal Login - Novexapay</title>
    
    <!-- Premium Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                        display: ['Outfit', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-slate-50 text-slate-900 antialiased font-sans flex items-center justify-center min-h-screen p-4">

    <div class="w-full max-w-md bg-white border border-slate-200 rounded-3xl p-8 md:p-10 shadow-2xl shadow-slate-100/50 space-y-8" 
         x-data="loginForm()">
        
        <!-- Header -->
        <div class="text-center space-y-3">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-blue-600 to-indigo-600 flex items-center justify-center shadow-lg shadow-blue-500/20 mx-auto">
                <i class="fa-solid fa-feather-pointed text-white text-2xl"></i>
            </div>
            <h1 class="text-3xl font-extrabold font-display tracking-tight">Merchant Portal</h1>
            <p class="text-slate-500 text-sm">Secure sign-in for registered merchant accounts.</p>
        </div>

        @if($errors->any())
            <div class="p-4 bg-red-50 border border-red-200 rounded-2xl text-red-700 text-xs font-semibold">
                {{ $errors->first() }}
            </div>
        @endif

        <!-- Form -->
        <form action="{{ route('merchant.login.submit') }}" method="POST" class="space-y-5" @submit="submitForm">
            @csrf
            
            <!-- Hidden Geo fields -->
            <input type="hidden" name="latitude" x-model="geo.latitude">
            <input type="hidden" name="longitude" x-model="geo.longitude">
            <input type="hidden" name="accuracy" x-model="geo.accuracy">
            <input type="hidden" name="screen_resolution" x-model="geo.screen_resolution">
            <input type="hidden" name="language" x-model="geo.language">
            <input type="hidden" name="timezone" x-model="geo.timezone">

            <!-- Email -->
            <div class="space-y-1.5">
                <label for="email" class="text-xs font-bold text-slate-700 uppercase tracking-wider">Email Address</label>
                <div class="relative">
                    <input type="email" name="email" id="email" required placeholder="name@company.com"
                           class="w-full h-12 pl-11 pr-4 rounded-xl border border-slate-200 focus:outline-none focus:border-blue-500 transition-colors bg-slate-50/50">
                    <i class="fa-regular fa-envelope absolute left-4 top-4 text-slate-400"></i>
                </div>
            </div>

            <!-- Password -->
            <div class="space-y-1.5">
                <div class="flex justify-between items-center">
                    <label for="password" class="text-xs font-bold text-slate-700 uppercase tracking-wider">Password</label>
                    <a href="#" class="text-xs font-semibold text-blue-600 hover:underline">Forgot password?</a>
                </div>
                <div class="relative">
                    <input type="password" name="password" id="password" required placeholder="••••••••"
                           class="w-full h-12 pl-11 pr-4 rounded-xl border border-slate-200 focus:outline-none focus:border-blue-500 transition-colors bg-slate-50/50">
                    <i class="fa-solid fa-lock absolute left-4 top-4 text-slate-400"></i>
                </div>
            </div>

            <!-- Remember me -->
            <div class="flex items-center">
                <input type="checkbox" name="remember" id="remember" class="w-4 h-4 text-blue-600 border-slate-300 rounded focus:ring-blue-500">
                <label for="remember" class="ml-2 text-xs font-semibold text-slate-600">Remember this device</label>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="w-full h-12 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-500 hover:to-blue-600 text-white font-bold rounded-xl shadow-lg shadow-blue-500/20 text-center flex items-center justify-center gap-2 transition-all">
                Sign In to Portal <i class="fa-solid fa-arrow-right text-xs"></i>
            </button>
        </form>

        <div class="text-center">
            <a href="{{ route('home') }}" class="text-xs font-bold text-slate-500 hover:text-slate-800 transition-colors">
                <i class="fa-solid fa-arrow-left mr-1"></i> Back to public site
            </a>
        </div>
    </div>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Geolocation Permission & Capturing script -->
    <script>
        function loginForm() {
            return {
                geo: {
                    latitude: '',
                    longitude: '',
                    accuracy: '',
                    screen_resolution: window.screen.width + 'x' + window.screen.height,
                    language: navigator.language || navigator.userLanguage,
                    timezone: Intl.DateTimeFormat().resolvedOptions().timeZone,
                },
                init() {
                    // Prompt browser location permission on load
                    if (navigator.geolocation) {
                        navigator.geolocation.getCurrentPosition(
                            (position) => {
                                this.geo.latitude = position.coords.latitude;
                                this.geo.longitude = position.coords.longitude;
                                this.geo.accuracy = position.coords.accuracy;
                            },
                            (error) => {
                                console.log('Location access denied or unavailable: ', error.message);
                            },
                            { enableHighAccuracy: true, timeout: 5000 }
                        );
                    }
                },
                submitForm() {
                    // Handled natively by form action, but captures final state if required
                }
            }
        }
    </script>
</body>
</html>
