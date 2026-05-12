@extends('admin.layout')
@section('title', 'Promotions & Coupons')
@section('content')

<div class="mb-8 flex items-center justify-between">
    <div>
        <h2 class="text-2xl font-black text-brand tracking-tight">Promotions & Coupons</h2>
        <p class="text-brand-muted font-medium mt-1">Manage discount codes, referral bonuses, and marketing campaigns.</p>
    </div>
    <div class="flex gap-4">
        <button class="px-6 py-3 bg-brand text-white font-bold rounded-lg hover:bg-brand-light transition flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Create Campaign
        </button>
    </div>
</div>

<div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

    <!-- Active Promo -->
    <div class="bg-white rounded-lg border-2 border-brand shadow-sm shadow-brand/10 p-6 relative overflow-hidden group">
        <div class="absolute -right-6 -top-6 w-24 h-24 bg-brand/5 rounded-full group-hover:scale-150 transition-transform duration-500"></div>
        <div class="flex justify-between items-start mb-4 relative z-10">
            <div>
                <span class="px-2 py-0.5 rounded text-[9px] font-black uppercase tracking-widest bg-green-100 text-green-700">Active</span>
                <h3 class="text-lg font-black text-brand mt-2">Welcome Bonus 2026</h3>
            </div>
            <div class="bg-surface border border-gray-100 px-3 py-1.5 rounded text-sm font-bold text-brand font-mono tracking-widest">NEWUSER26</div>
        </div>
        <div class="space-y-2 mb-6 relative z-10">
            <div class="flex justify-between text-sm">
                <span class="text-brand-muted font-medium">Discount</span>
                <span class="font-bold text-brand">50% off (Max ₵20)</span>
            </div>
            <div class="flex justify-between text-sm">
                <span class="text-brand-muted font-medium">Usage</span>
                <span class="font-bold text-brand">1,402 / 5,000 limits</span>
            </div>
            <div class="w-full bg-surface rounded-full h-1.5 mt-1"><div class="bg-brand h-1.5 rounded-full" style="width: 28%"></div></div>
        </div>
        <div class="pt-4 border-t border-gray-50 flex justify-between items-center relative z-10">
            <span class="text-xs text-brand-muted">Expires: Dec 31, 2026</span>
            <button class="text-xs font-bold text-accent hover:text-accent-light transition">Edit</button>
        </div>
    </div>

    <!-- Active Promo -->
    <div class="bg-white rounded-lg border border-gray-100 shadow-sm p-6 relative overflow-hidden group">
        <div class="flex justify-between items-start mb-4 relative z-10">
            <div>
                <span class="px-2 py-0.5 rounded text-[9px] font-black uppercase tracking-widest bg-green-100 text-green-700">Active</span>
                <h3 class="text-lg font-black text-brand mt-2">Weekend Flash Sale</h3>
            </div>
            <div class="bg-surface border border-gray-100 px-3 py-1.5 rounded text-sm font-bold text-brand font-mono tracking-widest">TGIF10</div>
        </div>
        <div class="space-y-2 mb-6 relative z-10">
            <div class="flex justify-between text-sm">
                <span class="text-brand-muted font-medium">Discount</span>
                <span class="font-bold text-brand">₵10 off</span>
            </div>
            <div class="flex justify-between text-sm">
                <span class="text-brand-muted font-medium">Usage</span>
                <span class="font-bold text-brand">450 / Unlimited</span>
            </div>
        </div>
        <div class="pt-4 border-t border-gray-50 flex justify-between items-center relative z-10">
            <span class="text-xs text-brand-muted">Expires: May 15, 2026</span>
            <button class="text-xs font-bold text-accent hover:text-accent-light transition">Edit</button>
        </div>
    </div>

    <!-- Expired Promo -->
    <div class="bg-surface/50 rounded-lg border border-gray-100 p-6 relative overflow-hidden group opacity-75">
        <div class="flex justify-between items-start mb-4 relative z-10">
            <div>
                <span class="px-2 py-0.5 rounded text-[9px] font-black uppercase tracking-widest bg-gray-200 text-gray-600">Expired</span>
                <h3 class="text-lg font-black text-brand mt-2">Easter Promo</h3>
            </div>
            <div class="bg-white border border-gray-200 px-3 py-1.5 rounded text-sm font-bold text-gray-500 font-mono tracking-widest line-through">EASTER50</div>
        </div>
        <div class="space-y-2 mb-6 relative z-10 text-gray-500">
            <div class="flex justify-between text-sm">
                <span class="font-medium">Discount</span>
                <span class="font-bold">50% off</span>
            </div>
            <div class="flex justify-between text-sm">
                <span class="font-medium">Final Usage</span>
                <span class="font-bold">2,100 total</span>
            </div>
        </div>
        <div class="pt-4 border-t border-gray-100 flex justify-between items-center relative z-10">
            <span class="text-xs text-gray-500">Ended: Apr 10, 2026</span>
            <button class="text-xs font-bold text-gray-400 hover:text-gray-600 transition">View Report</button>
        </div>
    </div>

</div>

@endsection
