@extends('layouts.merchant')
@section('title', 'Dynamic QR')
@section('page_title', 'Dynamic UPI QR Generator')

@section('content')
<div class="space-y-8" x-data="{ amount: '', desc: '', qrGenerated: false, qrUrl: '', loading: false, success: false }">
    <div class="grid lg:grid-cols-12 gap-8">
        
        <!-- QR Input Form -->
        <div class="lg:col-span-5 bg-white border border-slate-200 rounded-3xl p-6 shadow-sm space-y-6">
            <div>
                <h3 class="font-bold text-slate-900 text-lg">Generate Dynamic QR</h3>
                <p class="text-xs text-slate-500">Create a unique UPI QR code for real-time customer collection.</p>
            </div>

            <div class="space-y-4">
                <div class="space-y-1">
                    <label for="amount" class="text-[10px] font-bold text-slate-500 uppercase">Collection Amount (INR)</label>
                    <input type="number" x-model="amount" id="amount" placeholder="₹0.00" required
                           class="w-full h-11 px-3 rounded-lg border border-slate-200 text-xs focus:outline-none focus:border-blue-500 bg-slate-50/50 font-bold">
                </div>

                <div class="space-y-1">
                    <label for="desc" class="text-[10px] font-bold text-slate-500 uppercase">Remarks / Order ID</label>
                    <input type="text" x-model="desc" id="desc" placeholder="e.g. Order #1052" required
                           class="w-full h-11 px-3 rounded-lg border border-slate-200 text-xs focus:outline-none focus:border-blue-500 bg-slate-50/50">
                </div>

                <button @click="
                    if(!amount) return alert('Enter amount first');
                    loading = true;
                    setTimeout(() => {
                        loading = false;
                        qrGenerated = true;
                        success = false;
                        qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=' + encodeURIComponent('upi://pay?pa=novexapay@yesbank&pn=Novexapay&am=' + amount + '&cu=INR&tn=' + desc);
                    }, 800)
                " class="w-full h-11 bg-blue-600 hover:bg-blue-500 text-white font-bold rounded-lg text-xs shadow-lg shadow-blue-500/10 transition-colors flex items-center justify-center gap-2">
                    <i class="fa-solid fa-qrcode"></i> Generate Collection QR
                </button>
            </div>
        </div>

        <!-- Renders QR Box -->
        <div class="lg:col-span-7 bg-white border border-slate-200 rounded-3xl p-6 shadow-sm flex flex-col items-center justify-center min-h-[400px] text-center space-y-6">
            <template x-if="loading">
                <div class="space-y-3">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto"></div>
                    <span class="text-xs text-slate-400 font-semibold block">Calculating dynamic UPI routes...</span>
                </div>
            </template>

            <template x-if="!qrGenerated && !loading">
                <div class="space-y-2 text-slate-400">
                    <i class="fa-solid fa-circle-info text-4xl block text-slate-300"></i>
                    <h4 class="font-bold text-sm text-slate-700">Awaiting QR Generation</h4>
                    <p class="text-xs max-w-xs mx-auto">Specify the target collection amount and order remarks on the left panel to build a trackable payment QR.</p>
                </div>
            </template>

            <template x-if="qrGenerated && !loading">
                <div class="space-y-6 flex flex-col items-center w-full">
                    <div class="border border-slate-200 rounded-2xl p-4 bg-white shadow-md inline-block">
                        <img :src="qrUrl" alt="UPI QR" class="w-[200px] h-[200px]">
                    </div>
                    
                    <div class="space-y-1">
                        <span class="text-xs font-bold text-slate-400 block uppercase">UPI DEPOSIT REQUEST</span>
                        <h4 class="text-xl font-bold text-slate-800" x-text="'₹' + parseFloat(amount).toFixed(2)"></h4>
                        <p class="text-[10px] text-slate-400 font-mono" x-text="'Order Ref: ' + desc"></p>
                    </div>

                    <div class="h-[1px] bg-slate-100 w-full max-w-sm"></div>

                    <!-- Payment Status Simulators -->
                    <div class="space-y-3 w-full max-w-xs">
                        <div class="flex items-center justify-between text-xs font-bold text-slate-600">
                            <span>Status:</span>
                            <span x-show="!success" class="text-amber-500"><i class="fa-solid fa-spinner animate-spin mr-1"></i> Awaiting Customer Scan</span>
                            <span x-show="success" class="text-green-600"><i class="fa-solid fa-circle-check mr-1"></i> Deposited & Credited</span>
                        </div>

                        <button x-show="!success" @click="success = true"
                                class="w-full h-9 bg-green-600 hover:bg-green-500 text-white font-bold rounded-lg text-xs transition-colors shadow-sm">
                            Simulate Customer Scan & Pay
                        </button>
                    </div>
                </div>
            </template>
        </div>

    </div>
</div>
@endsection
