<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Admin Console</title>
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
<body class="bg-slate-50 text-slate-900 antialiased font-sans min-h-screen flex" x-data="{}">

    <!-- Sidebar -->
    <aside class="w-64 bg-slate-900 text-slate-400 flex flex-col justify-between flex-shrink-0 border-r border-slate-800">
        <div class="p-6 space-y-8">
            <!-- Logo -->
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-gradient-to-tr from-blue-500 to-indigo-600 flex items-center justify-center">
                    <i class="fa-solid fa-feather-pointed text-white text-sm"></i>
                </div>
                <span class="text-lg font-bold font-display tracking-tight text-white">Novexa<span class="text-blue-500">Admin</span></span>
            </a>

            <!-- Navigation Links -->
            <nav class="space-y-1">
                <a href="{{ route('admin.dashboard') }}" 
                   class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all hover:bg-slate-800 hover:text-white {{ request()->routeIs('admin.dashboard') ? 'bg-blue-600 text-white shadow-lg shadow-blue-500/10' : '' }}">
                    <i class="fa-solid fa-chart-pie w-5"></i> Dashboard
                </a>

                <a href="{{ route('admin.merchants') }}" 
                   class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all hover:bg-slate-800 hover:text-white {{ request()->routeIs('admin.merchants*') ? 'bg-blue-600 text-white shadow-lg shadow-blue-500/10' : '' }}">
                    <i class="fa-solid fa-building-columns w-5"></i> Merchants
                </a>

                <a href="{{ route('admin.commissions') }}" 
                   class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all hover:bg-slate-800 hover:text-white {{ request()->routeIs('admin.commissions*') ? 'bg-blue-600 text-white shadow-lg shadow-blue-500/10' : '' }}">
                    <i class="fa-solid fa-sliders w-5"></i> Commission Engine
                </a>

                <a href="{{ route('admin.enquiries') }}" 
                   class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all hover:bg-slate-800 hover:text-white {{ request()->routeIs('admin.enquiries*') ? 'bg-blue-600 text-white shadow-lg shadow-blue-500/10' : '' }}">
                    <i class="fa-solid fa-user-plus w-5"></i> Enquiries Queue
                </a>

                <a href="{{ route('admin.tickets') }}" 
                   class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all hover:bg-slate-800 hover:text-white {{ request()->routeIs('admin.tickets*') ? 'bg-blue-600 text-white shadow-lg shadow-blue-500/10' : '' }}">
                    <i class="fa-regular fa-life-ring w-5"></i> Support Tickets
                </a>

                <div class="h-[1px] bg-slate-800 my-4"></div>

                <a href="{{ route('admin.logs.audit') }}" 
                   class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all hover:bg-slate-800 hover:text-white {{ request()->routeIs('admin.logs.audit') ? 'bg-blue-600 text-white shadow-lg shadow-blue-500/10' : '' }}">
                    <i class="fa-solid fa-shield-halved w-5"></i> Audit Logs
                </a>

                <a href="{{ route('admin.logs.api') }}" 
                   class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all hover:bg-slate-800 hover:text-white {{ request()->routeIs('admin.logs.api') ? 'bg-blue-600 text-white shadow-lg shadow-blue-500/10' : '' }}">
                    <i class="fa-solid fa-receipt w-5"></i> API Gateway Logs
                </a>
            </nav>
        </div>

        <!-- Logged user / Logout -->
        <div class="p-6 border-t border-slate-800 space-y-3">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-slate-800 flex items-center justify-center font-bold text-white uppercase">
                    {{ substr(Auth::guard('admin')->user()->name, 0, 2) }}
                </div>
                <div class="overflow-hidden">
                    <div class="text-sm font-bold text-white truncate">{{ Auth::guard('admin')->user()->name }}</div>
                    <div class="text-xs text-slate-500 truncate">{{ ucfirst(Auth::guard('admin')->user()->role) }}</div>
                </div>
            </div>
            
            <form action="{{ route('admin.logout') }}" method="POST">
                @csrf
                <button type="submit" class="w-full h-10 border border-slate-800 hover:border-slate-700 hover:text-white rounded-xl flex items-center justify-center gap-2 text-xs font-bold transition-all">
                    Exit Console <i class="fa-solid fa-arrow-right-from-bracket"></i>
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content -->
    <div class="flex-grow flex flex-col min-w-0">
        <!-- Top Navigation -->
        <header class="h-20 border-b border-slate-200 bg-white flex items-center justify-between px-8">
            <div class="flex items-center gap-2 text-slate-500 text-sm font-medium">
                <span>Console</span>
                <i class="fa-solid fa-chevron-right text-[10px]"></i>
                <span class="text-slate-800 font-bold">@yield('page_title')</span>
            </div>
            
            <span class="text-xs font-bold text-slate-400 bg-slate-100 px-3 py-1.5 rounded-full uppercase tracking-wider">
                <i class="fa-solid fa-shield-halved mr-1 text-blue-500"></i> SECURED ADMIN ACCESS
            </span>
        </header>

        <!-- Main Panel Body -->
        <main class="flex-grow p-8 overflow-y-auto">
            @if(session('success'))
                <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-2xl text-sm font-bold flex items-center gap-2">
                    <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

</body>
</html>
