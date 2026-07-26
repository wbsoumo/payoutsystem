@extends('layouts.admin')
@section('title', 'Commission Engine')
@section('page_title', 'Commission Engine Manager')

@section('content')
<div class="grid lg:grid-cols-12 gap-8">

    <!-- Active Rules List -->
    <div class="lg:col-span-7 space-y-6">
        <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-sm space-y-6">
            <div>
                <h3 class="font-bold text-slate-900 text-lg">Active Commission Rates</h3>
                <p class="text-xs text-slate-500">Every payout checks merchant overrides first, falling back to global rates if none exist.</p>
            </div>

            <div class="space-y-4">
                @forelse($commissions as $rule)
                    <div class="p-5 border border-slate-100 rounded-2xl bg-slate-50/50 space-y-3">
                        <div class="flex justify-between items-start">
                            <div>
                                <span class="text-xs font-bold text-slate-900 block">{{ $rule->name }}</span>
                                <span class="text-[10px] text-slate-400 font-semibold block mt-0.5">
                                    Scope: 
                                    @if($rule->merchant_id)
                                        <span class="text-blue-600 font-bold">Merchant Override ({{ $rule->merchant->company_name }})</span>
                                    @else
                                        <span class="text-slate-500 font-bold">Global Default</span>
                                    @endif
                                </span>
                            </div>
                            <span class="px-2 py-0.5 rounded text-[9px] font-bold uppercase {{ $rule->is_active ? 'bg-green-50 text-green-700 border border-green-200' : 'bg-slate-100 text-slate-500' }}">
                                {{ $rule->is_active ? 'Active' : 'Expired' }}
                            </span>
                        </div>

                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-[10px]">
                            <div>
                                <span class="text-slate-400 font-bold block uppercase">RULE TYPE</span>
                                <span class="font-semibold text-slate-800 uppercase">{{ $rule->type }}</span>
                            </div>
                            <div>
                                <span class="text-slate-400 font-bold block uppercase">GST RATE</span>
                                <span class="font-semibold text-slate-800">{{ $rule->gst_rate }}%</span>
                            </div>
                            <div>
                                <span class="text-slate-400 font-bold block uppercase">EFFECTIVE FROM</span>
                                <span class="font-semibold text-slate-800 font-mono">{{ $rule->effective_date->format('Y-m-d') }}</span>
                            </div>
                            <div>
                                <span class="text-slate-400 font-bold block uppercase">CHARGE DETAILS</span>
                                <span class="font-bold text-slate-900">
                                    @if($rule->type === 'fixed')
                                        ₹{{ number_format($rule->fixed_charge, 2) }}
                                    @elseif($rule->type === 'percentage')
                                        {{ $rule->percentage_charge }}%
                                    @else
                                        Slab Rates (JSON)
                                    @endif
                                </span>
                            </div>
                        </div>

                        @if($rule->type === 'slab' && $rule->slab_rates)
                            <div class="bg-white border border-slate-100 rounded-xl p-3 text-[10px] space-y-1 font-mono">
                                <span class="text-[9px] font-bold text-slate-400 block uppercase mb-1">Calculated Slabs:</span>
                                @foreach($rule->slab_rates as $slab)
                                    <div class="flex justify-between">
                                        <span>Min ₹{{ number_format($slab['min'], 2) }} - Max {{ isset($slab['max']) && $slab['max'] !== '' ? '₹' . number_format($slab['max'], 2) : '∞' }}</span>
                                        <span class="font-bold text-brand-600">{{ $slab['type'] === 'fixed' ? '₹' . $slab['value'] : $slab['value'] . '%' }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="text-center py-6 text-slate-400 font-semibold text-xs">No active commission structures defined. Configure default rates.</div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Create Rules Form -->
    <div class="lg:col-span-5 space-y-6">
        <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-sm space-y-4">
            <h3 class="font-bold text-slate-900 text-lg">Define Commission Rule</h3>
            <p class="text-xs text-slate-500 leading-relaxed">Establish default fees or write override slab rates for high-volume merchants.</p>

            <form action="{{ route('admin.commissions.store') }}" method="POST" class="space-y-4" x-data="{ type: 'percentage' }">
                @csrf
                
                <div class="space-y-1">
                    <label for="name" class="text-[10px] font-bold text-slate-500 uppercase">Rule Name</label>
                    <input type="text" name="name" id="name" placeholder="e.g. Standard Payout default" required
                           class="w-full h-11 px-3 rounded-lg border border-slate-200 text-xs focus:outline-none focus:border-blue-500 bg-slate-50/50">
                </div>

                <div class="space-y-1">
                    <label for="merchant_id" class="text-[10px] font-bold text-slate-500 uppercase">Merchant Override Scope (Optional)</label>
                    <select name="merchant_id" id="merchant_id"
                            class="w-full h-11 px-3 rounded-lg border border-slate-200 text-xs focus:outline-none focus:border-blue-500 bg-slate-50/50">
                        <option value="">Global Default Rule (All Merchants)</option>
                        @foreach($merchants as $m)
                            <option value="{{ $m->id }}">Override for: {{ $m->company_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <label class="text-[10px] font-bold text-slate-500 uppercase">Commission Type</label>
                        <select name="type" required x-model="type"
                                class="w-full h-11 px-3 rounded-lg border border-slate-200 text-xs focus:outline-none focus:border-blue-500 bg-slate-50/50">
                            <option value="fixed">Fixed Flat Charge</option>
                            <option value="percentage">Percentage Rate</option>
                            <option value="slab">Slab-Based Rates</option>
                        </select>
                    </div>

                    <div class="space-y-1">
                        <label for="effective_date" class="text-[10px] font-bold text-slate-500 uppercase">Effective Date</label>
                        <input type="date" name="effective_date" id="effective_date" value="{{ date('Y-m-d') }}" required
                               class="w-full h-11 px-3 rounded-lg border border-slate-200 text-xs focus:outline-none focus:border-blue-500 bg-slate-50/50">
                    </div>
                </div>

                <!-- Type specific inputs -->
                <div x-show="type === 'fixed'" class="space-y-1" x-cloak>
                    <label for="fixed_charge" class="text-[10px] font-bold text-slate-500 uppercase">Flat Charge (INR)</label>
                    <input type="number" name="fixed_charge" id="fixed_charge" step="0.01" value="0.00"
                           class="w-full h-11 px-3 rounded-lg border border-slate-200 text-xs focus:outline-none focus:border-blue-500 bg-slate-50/50 font-bold">
                </div>

                <div x-show="type === 'percentage'" class="space-y-1" x-cloak>
                    <label for="percentage_charge" class="text-[10px] font-bold text-slate-500 uppercase">Percentage Rate (%)</label>
                    <input type="number" name="percentage_charge" id="percentage_charge" step="0.01" max="100" value="0.00"
                           class="w-full h-11 px-3 rounded-lg border border-slate-200 text-xs focus:outline-none focus:border-blue-500 bg-slate-50/50 font-bold">
                </div>

                <div x-show="type === 'slab'" class="space-y-1" x-cloak>
                    <label for="slab_rates" class="text-[10px] font-bold text-slate-500 uppercase">Slab Rates JSON Structure</label>
                    <textarea name="slab_rates" id="slab_rates" rows="5" placeholder='[
  {"min": 0, "max": 1000, "type": "fixed", "value": 5},
  {"min": 1000.01, "max": 50000, "type": "percentage", "value": 1.2},
  {"min": 50000.01, "max": null, "type": "percentage", "value": 0.9}
]'
                              class="w-full p-3 rounded-lg border border-slate-200 text-xs font-mono focus:outline-none focus:border-blue-500 bg-slate-50/50"></textarea>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <label class="text-[10px] font-bold text-slate-500 uppercase">Minimum Fee (INR)</label>
                        <input type="number" name="min_charge" step="0.01" placeholder="Optional"
                               class="w-full h-11 px-3 rounded-lg border border-slate-200 text-xs focus:outline-none focus:border-blue-500 bg-slate-50/50 font-bold">
                    </div>
                    <div class="space-y-1">
                        <label class="text-[10px] font-bold text-slate-500 uppercase">Maximum Fee (INR)</label>
                        <input type="number" name="max_charge" step="0.01" placeholder="Optional"
                               class="w-full h-11 px-3 rounded-lg border border-slate-200 text-xs focus:outline-none focus:border-blue-500 bg-slate-50/50 font-bold">
                    </div>
                </div>

                <div class="space-y-1">
                    <label for="gst_rate" class="text-[10px] font-bold text-slate-500 uppercase">GST Rate on Commission (%)</label>
                    <input type="number" name="gst_rate" id="gst_rate" step="0.01" value="18.00" required
                           class="w-full h-11 px-3 rounded-lg border border-slate-200 text-xs focus:outline-none focus:border-blue-500 bg-slate-50/50 font-bold">
                </div>

                <button type="submit" class="w-full h-11 btn-gradient text-white text-xs font-bold rounded-lg shadow-lg shadow-brand-500/10">
                    Apply Commission Rules
                </button>
            </form>
        </div>
    </div>

</div>
@endsection
