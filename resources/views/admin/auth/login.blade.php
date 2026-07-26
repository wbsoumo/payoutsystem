<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Admin Login - Novexapay</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
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
<body class="bg-slate-900 text-slate-100 antialiased font-sans flex items-center justify-center min-h-screen p-4">

    <div class="w-full max-w-md bg-slate-800 border border-slate-700 rounded-3xl p-8 md:p-10 shadow-2xl space-y-8">
        
        <!-- Header -->
        <div class="text-center space-y-3">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-brand-500 to-brand-700 flex items-center justify-center shadow-lg shadow-brand-500/20 mx-auto">
                <i class="fa-solid fa-feather-pointed text-white text-2xl"></i>
            </div>
            <h1 class="text-3xl font-extrabold font-display tracking-tight text-white">Admin Console</h1>
            <p class="text-slate-400 text-sm">System management authentication gateway.</p>
        </div>

        @if($errors->any())
            <div class="p-4 bg-red-950/50 border border-red-800/80 rounded-2xl text-red-400 text-xs font-semibold">
                {{ $errors->first() }}
            </div>
        @endif

        <!-- Form -->
        <form action="{{ route('admin.login.submit') }}" method="POST" class="space-y-5">
            @csrf
            
            <!-- Email -->
            <div class="space-y-1.5">
                <label for="email" class="text-xs font-bold text-slate-400 uppercase tracking-wider">Admin Email</label>
                <div class="relative">
                    <input type="email" name="email" id="email" required placeholder="admin@novexapay.com"
                           class="w-full h-12 pl-11 pr-4 rounded-xl border border-slate-700 focus:outline-none focus:border-blue-500 transition-colors bg-slate-850 text-white">
                    <i class="fa-regular fa-envelope absolute left-4 top-4 text-slate-500"></i>
                </div>
            </div>

            <!-- Password -->
            <div class="space-y-1.5">
                <label for="password" class="text-xs font-bold text-slate-400 uppercase tracking-wider">Master Password</label>
                <div class="relative">
                    <input type="password" name="password" id="password" required placeholder="••••••••"
                           class="w-full h-12 pl-11 pr-4 rounded-xl border border-slate-700 focus:outline-none focus:border-blue-500 transition-colors bg-slate-850 text-white">
                    <i class="fa-solid fa-lock absolute left-4 top-4 text-slate-500"></i>
                </div>
            </div>

            <!-- Remember me -->
            <div class="flex items-center">
                <input type="checkbox" name="remember" id="remember" class="w-4 h-4 text-blue-600 border-slate-700 rounded bg-slate-850 focus:ring-blue-500">
                <label for="remember" class="ml-2 text-xs font-semibold text-slate-400">Remember this administrator session</label>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="w-full h-12 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white font-bold rounded-xl shadow-lg shadow-blue-500/20 text-center flex items-center justify-center gap-2 transition-all">
                Authenticate Admin Session <i class="fa-solid fa-shield-halved text-xs"></i>
            </button>
        </form>

        <div class="text-center">
            <a href="{{ route('home') }}" class="text-xs font-bold text-slate-500 hover:text-slate-350 transition-colors">
                <i class="fa-solid fa-arrow-left mr-1"></i> Back to public site
            </a>
        </div>
    </div>

</body>
</html>
