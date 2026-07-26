<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Merchant Portal</title>
    
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
    <style>
        .glass-card {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(226, 232, 240, 0.8);
        }
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }
        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-900 antialiased font-sans min-h-screen flex">

    <!-- Sidebar -->
    <aside class="w-64 bg-slate-900 text-slate-400 flex flex-col justify-between flex-shrink-0 border-r border-slate-800 h-screen sticky top-0">
        <div class="p-6 space-y-6 flex-grow overflow-y-auto no-scrollbar">
            <!-- Logo -->
            <a href="{{ route('merchant.dashboard') }}" class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-gradient-to-tr from-blue-500 to-indigo-600 flex items-center justify-center">
                    <i class="fa-solid fa-feather-pointed text-white text-sm"></i>
                </div>
                <span class="text-lg font-bold font-display tracking-tight text-white">Novexa<span class="text-blue-500">pay</span></span>
            </a>

            <!-- Navigation Links -->
            <div class="space-y-6">
                <!-- MAIN -->
                <div class="space-y-2">
                    <span class="text-[9px] font-bold text-slate-500 tracking-wider uppercase px-4">Main Navigation</span>
                    <nav class="space-y-1">
                        <a href="{{ route('merchant.dashboard') }}" 
                           class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-xs font-semibold transition-all hover:bg-slate-800 hover:text-white {{ request()->routeIs('merchant.dashboard') ? 'bg-blue-600 text-white shadow-lg shadow-blue-500/10' : '' }}">
                            <i class="fa-solid fa-chart-pie w-5"></i> Dashboard
                        </a>
                        <a href="{{ route('merchant.payouts') }}" 
                           class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-xs font-semibold transition-all hover:bg-slate-800 hover:text-white {{ request()->routeIs('merchant.payouts*') ? 'bg-blue-600 text-white shadow-lg shadow-blue-500/10' : '' }}">
                            <i class="fa-solid fa-money-bill-transfer w-5"></i> Payouts
                        </a>
                        <a href="{{ route('merchant.ledger') }}" 
                           class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-xs font-semibold transition-all hover:bg-slate-800 hover:text-white {{ request()->routeIs('merchant.ledger') ? 'bg-blue-600 text-white shadow-lg shadow-blue-500/10' : '' }}">
                            <i class="fa-solid fa-receipt w-5"></i> Wallet Ledger
                        </a>
                        <a href="{{ route('merchant.settlements') }}" 
                           class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-xs font-semibold transition-all hover:bg-slate-800 hover:text-white {{ request()->routeIs('merchant.settlements*') ? 'bg-blue-600 text-white shadow-lg shadow-blue-500/10' : '' }}">
                            <i class="fa-solid fa-building-columns w-5"></i> Settlements
                        </a>
                    </nav>
                </div>

                <!-- PAYMENT SERVICES -->
                <div class="space-y-2">
                    <span class="text-[9px] font-bold text-slate-500 tracking-wider uppercase px-4">Payment Services</span>
                    <nav class="space-y-1">
                        <a href="{{ route('merchant.collections') }}" 
                           class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-xs font-semibold transition-all hover:bg-slate-800 hover:text-white {{ request()->routeIs('merchant.collections*') ? 'bg-blue-600 text-white shadow-lg shadow-blue-500/10' : '' }}">
                            <i class="fa-solid fa-hand-holding-dollar w-5"></i> Collection Account
                        </a>
                        <a href="{{ route('merchant.cc-to-bank') }}" 
                           class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-xs font-semibold transition-all hover:bg-slate-800 hover:text-white {{ request()->routeIs('merchant.cc-to-bank*') ? 'bg-blue-600 text-white shadow-lg shadow-blue-500/10' : '' }}">
                            <i class="fa-regular fa-credit-card w-5"></i> Credit Card to Bank
                        </a>
                        <a href="{{ route('merchant.virtual-accounts') }}" 
                           class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-xs font-semibold transition-all hover:bg-slate-800 hover:text-white {{ request()->routeIs('merchant.virtual-accounts*') ? 'bg-blue-600 text-white shadow-lg shadow-blue-500/10' : '' }}">
                            <i class="fa-solid fa-network-wired w-5"></i> Virtual Accounts
                        </a>
                        <a href="{{ route('merchant.dynamic-qr') }}" 
                           class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-xs font-semibold transition-all hover:bg-slate-800 hover:text-white {{ request()->routeIs('merchant.dynamic-qr*') ? 'bg-blue-600 text-white shadow-lg shadow-blue-500/10' : '' }}">
                            <i class="fa-solid fa-qrcode w-5"></i> Dynamic QR
                        </a>
                        <a href="{{ route('merchant.payment-links') }}" 
                           class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-xs font-semibold transition-all hover:bg-slate-800 hover:text-white {{ request()->routeIs('merchant.payment-links*') ? 'bg-blue-600 text-white shadow-lg shadow-blue-500/10' : '' }}">
                            <i class="fa-solid fa-link w-5"></i> Payment Links
                        </a>
                    </nav>
                </div>

                <!-- DEVELOPER -->
                <div class="space-y-2">
                    <span class="text-[9px] font-bold text-slate-500 tracking-wider uppercase px-4">Developer Tools</span>
                    <nav class="space-y-1">
                        <a href="{{ route('merchant.api-docs') }}" 
                           class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-xs font-semibold transition-all hover:bg-slate-800 hover:text-white {{ request()->routeIs('merchant.api-docs*') ? 'bg-blue-600 text-white shadow-lg shadow-blue-500/10' : '' }}">
                            <i class="fa-solid fa-code w-5"></i> API & Sandbox Docs
                        </a>
                        <a href="{{ route('merchant.webhooks') }}" 
                           class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-xs font-semibold transition-all hover:bg-slate-800 hover:text-white {{ request()->routeIs('merchant.webhooks*') ? 'bg-blue-600 text-white shadow-lg shadow-blue-500/10' : '' }}">
                            <i class="fa-solid fa-bell w-5"></i> Webhook Endpoint
                        </a>
                    </nav>
                </div>

                <!-- ACCOUNT -->
                <div class="space-y-2">
                    <span class="text-[9px] font-bold text-slate-500 tracking-wider uppercase px-4">My Account</span>
                    <nav class="space-y-1">
                        <a href="{{ route('merchant.profile') }}" 
                           class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-xs font-semibold transition-all hover:bg-slate-800 hover:text-white {{ request()->routeIs('merchant.profile') ? 'bg-blue-600 text-white shadow-lg shadow-blue-500/10' : '' }}">
                            <i class="fa-regular fa-user w-5"></i> Representative Profile
                        </a>
                        <a href="{{ route('merchant.kyc') }}" 
                           class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-xs font-semibold transition-all hover:bg-slate-800 hover:text-white {{ request()->routeIs('merchant.kyc*') ? 'bg-blue-600 text-white shadow-lg shadow-blue-500/10' : '' }}">
                            <i class="fa-solid fa-shield-halved w-5"></i> KYC Verification
                        </a>
                        <a href="{{ route('merchant.disputes') }}" 
                           class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-xs font-semibold transition-all hover:bg-slate-800 hover:text-white {{ request()->routeIs('merchant.disputes*') ? 'bg-blue-600 text-white shadow-lg shadow-blue-500/10' : '' }}">
                            <i class="fa-solid fa-triangle-exclamation w-5"></i> Dispute Panel
                        </a>
                        <a href="{{ route('merchant.tickets') }}" 
                           class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-xs font-semibold transition-all hover:bg-slate-800 hover:text-white {{ request()->routeIs('merchant.tickets*') ? 'bg-blue-600 text-white shadow-lg shadow-blue-500/10' : '' }}">
                            <i class="fa-regular fa-circle-question w-5"></i> Help & Support
                        </a>
                        <a href="{{ route('merchant.settings') }}" 
                           class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-xs font-semibold transition-all hover:bg-slate-800 hover:text-white {{ request()->routeIs('merchant.settings*') ? 'bg-blue-600 text-white shadow-lg shadow-blue-500/10' : '' }}">
                            <i class="fa-solid fa-sliders w-5"></i> General Settings
                        </a>
                    </nav>
                </div>
            </div>
        </div>

        <!-- Logged user / Logout -->
        <div class="p-6 border-t border-slate-800 space-y-3">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-slate-800 flex items-center justify-center font-bold text-white uppercase">
                    {{ substr(Auth::guard('merchant')->user()->name, 0, 2) }}
                </div>
                <div class="overflow-hidden">
                    <div class="text-sm font-bold text-white truncate">{{ Auth::guard('merchant')->user()->name }}</div>
                    <div class="text-xs text-slate-500 truncate">{{ Auth::guard('merchant')->user()->merchant->company_name }}</div>
                </div>
            </div>
            
            <form action="{{ route('merchant.logout') }}" method="POST">
                @csrf
                <button type="submit" class="w-full h-10 border border-slate-800 hover:border-slate-700 hover:text-white rounded-xl flex items-center justify-center gap-2 text-xs font-bold transition-all">
                    Sign Out <i class="fa-solid fa-arrow-right-from-bracket"></i>
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Working Panel -->
    <div class="flex-grow flex flex-col min-w-0">
        <!-- Top Navigation / Header -->
        <header class="h-20 border-b border-slate-200 bg-white flex items-center justify-between px-8">
            <div class="flex items-center gap-2 text-slate-500 text-sm font-medium">
                <span>Portal</span>
                <i class="fa-solid fa-chevron-right text-[10px]"></i>
                <span class="text-slate-800 font-bold">@yield('page_title')</span>
            </div>

            <!-- Wallet Stats Badge -->
            <div class="flex items-center gap-4">
                <div class="px-4 py-2 border border-slate-200 rounded-2xl bg-slate-50 flex items-center gap-3">
                    <span class="w-2.5 h-2.5 rounded-full bg-blue-600 shadow-sm animate-pulse"></span>
                    <div>
                        <span class="text-[10px] text-slate-400 font-bold block leading-none">WALLET BALANCE</span>
                        <span class="text-sm font-extrabold text-slate-900 font-display">₹{{ number_format(Auth::guard('merchant')->user()->merchant->wallet->balance, 2) }}</span>
                    </div>
                </div>

                <!-- KYC Status indicator -->
                @php
                    $kyc = Auth::guard('merchant')->user()->merchant->kyc_status;
                @endphp
                @if($kyc === 'approved')
                    <span class="px-3 py-1 bg-green-50 border border-green-200 rounded-xl text-green-700 text-xs font-bold uppercase"><i class="fa-solid fa-circle-check mr-1"></i> KYC Active</span>
                @elseif($kyc === 'submitted')
                    <span class="px-3 py-1 bg-yellow-50 border border-yellow-200 rounded-xl text-yellow-700 text-xs font-bold uppercase"><i class="fa-solid fa-clock mr-1"></i> KYC Pending Approval</span>
                @else
                    <span class="px-3 py-1 bg-red-50 border border-red-200 rounded-xl text-red-700 text-xs font-bold uppercase"><i class="fa-solid fa-circle-exclamation mr-1"></i> Submit KYC Documents</span>
                @endif
            </div>
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

</body>
</html>
