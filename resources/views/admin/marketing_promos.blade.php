@extends('admin.layout')
@section('title', 'Promotions & Coupons')
@section('content')

<div class="mb-8 flex items-center justify-between">
    <div>
        <h2 class="text-2xl font-black text-brand tracking-tight">Promotions & Coupons</h2>
        <p class="text-brand-muted font-medium mt-1">Manage discount codes, referral bonuses, and marketing campaigns.</p>
    </div>
    <div class="flex gap-4">
        <button onclick="document.getElementById('add-modal').classList.remove('hidden')" class="px-6 py-3 bg-brand text-white font-bold rounded-lg hover:bg-brand-light transition flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Create Campaign
        </button>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
    <div class="bg-white p-4 rounded-lg border border-gray-100 shadow-sm flex items-center gap-4">
        <div class="w-12 h-12 rounded-full bg-green-50 text-green-600 flex items-center justify-center shrink-0">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div>
            <p class="text-[10px] font-black text-brand-muted uppercase tracking-widest">Active Promos</p>
            <p class="text-xl font-bold text-brand mt-0.5">{{ number_format($stats['active']) }}</p>
        </div>
    </div>
    <div class="bg-white p-4 rounded-lg border border-gray-100 shadow-sm flex items-center gap-4">
        <div class="w-12 h-12 rounded-full bg-gray-100 text-gray-500 flex items-center justify-center shrink-0">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div>
            <p class="text-[10px] font-black text-brand-muted uppercase tracking-widest">Expired</p>
            <p class="text-xl font-bold text-brand mt-0.5">{{ number_format($stats['expired']) }}</p>
        </div>
    </div>
    <div class="bg-white p-4 rounded-lg border border-gray-100 shadow-sm flex items-center gap-4">
        <div class="w-12 h-12 rounded-full bg-brand/5 text-brand flex items-center justify-center shrink-0">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
        </div>
        <div>
            <p class="text-[10px] font-black text-brand-muted uppercase tracking-widest">Total Redemptions</p>
            <p class="text-xl font-bold text-brand mt-0.5">{{ number_format($stats['total_uses']) }}</p>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
    @forelse($promos as $promo)
        @php
            $isExpired = $promo->expires_at && $promo->expires_at->isPast();
            $isExhausted = $promo->max_uses && $promo->times_used >= $promo->max_uses;
            $isActive = $promo->is_active && !$isExpired && !$isExhausted;
        @endphp
        
        <div class="bg-white rounded-lg border {{ $isActive ? 'border-brand shadow-sm shadow-brand/10' : 'border-gray-100 opacity-75' }} p-6 relative overflow-hidden group flex flex-col">
            @if($isActive)
                <div class="absolute -right-6 -top-6 w-24 h-24 bg-brand/5 rounded-full group-hover:scale-150 transition-transform duration-500"></div>
            @endif
            
            <div class="flex justify-between items-start mb-4 relative z-10">
                <div>
                    @if($isActive)
                        <span class="px-2 py-0.5 rounded text-[9px] font-black uppercase tracking-widest bg-green-100 text-green-700">Active</span>
                    @elseif($isExpired)
                        <span class="px-2 py-0.5 rounded text-[9px] font-black uppercase tracking-widest bg-gray-200 text-gray-600">Expired</span>
                    @elseif($isExhausted)
                        <span class="px-2 py-0.5 rounded text-[9px] font-black uppercase tracking-widest bg-red-100 text-red-600">Depleted</span>
                    @else
                        <span class="px-2 py-0.5 rounded text-[9px] font-black uppercase tracking-widest bg-amber-100 text-amber-700">Paused</span>
                    @endif
                    <h3 class="text-lg font-black text-brand mt-2">{{ $promo->description ?? 'Promo Code' }}</h3>
                </div>
                <div class="bg-surface border border-gray-100 px-3 py-1.5 rounded text-sm font-bold text-brand font-mono tracking-widest {{ !$isActive ? 'line-through text-gray-400' : '' }}">{{ $promo->code }}</div>
            </div>
            
            <div class="space-y-2 mb-6 relative z-10 flex-1">
                <div class="flex justify-between text-sm">
                    <span class="text-brand-muted font-medium">Discount</span>
                    <span class="font-bold text-brand">
                        {{ $promo->type == 'percentage' ? $promo->value.'%' : '₵'.$promo->value }} off
                        @if($promo->max_discount) (Max ₵{{ $promo->max_discount }}) @endif
                    </span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-brand-muted font-medium">Usage</span>
                    <span class="font-bold text-brand">{{ number_format($promo->times_used) }} / {{ $promo->max_uses ? number_format($promo->max_uses) : 'Unlimited' }}</span>
                </div>
                
                @if($promo->max_uses)
                    @php $progress = min(100, ($promo->times_used / $promo->max_uses) * 100); @endphp
                    <div class="w-full bg-surface rounded-full h-1.5 mt-1"><div class="bg-brand h-1.5 rounded-full" style="width: {{ $progress }}%"></div></div>
                @endif
            </div>
            
            <div class="pt-4 border-t border-gray-50 flex justify-between items-center relative z-10">
                <span class="text-xs text-brand-muted">{{ $promo->expires_at ? 'Expires: ' . $promo->expires_at->format('M d, Y') : 'No expiration' }}</span>
                <div class="flex gap-3">
                    <form action="{{ route('orchestrator.marketing.promos.toggle', $promo->id) }}" method="POST">
                        @csrf @method('PATCH')
                        <button type="submit" class="text-xs font-bold {{ $promo->is_active ? 'text-amber-500 hover:text-amber-600' : 'text-green-500 hover:text-green-600' }} transition">{{ $promo->is_active ? 'Pause' : 'Activate' }}</button>
                    </form>
                    <form action="{{ route('orchestrator.marketing.promos.destroy', $promo->id) }}" method="POST" onsubmit="return confirm('Delete this promo code permanently?');">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-xs font-bold text-red-400 hover:text-red-600 transition">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    @empty
        <div class="col-span-full bg-white rounded-lg border border-gray-100 shadow-sm p-12 text-center text-gray-500">
            <p class="font-medium">No promo codes created yet.</p>
        </div>
    @endforelse
</div>

<div class="mt-4">
    {{ $promos->links() }}
</div>

<!-- Add Modal -->
<div id="add-modal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-xl max-w-lg w-full p-6 max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-lg font-black text-brand">Create Promo Code</h3>
            <button onclick="document.getElementById('add-modal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
        </div>
        <form action="{{ route('orchestrator.marketing.promos.store') }}" method="POST">
            @csrf
            <div class="space-y-4 mb-6">
                <div>
                    <label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-1">Promo Code (e.g. WELCOME26)</label>
                    <input type="text" name="code" required class="w-full bg-surface border border-gray-200 rounded p-2 text-sm focus:ring-2 focus:ring-brand/20 outline-none uppercase font-mono">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-1">Description (Internal / Display)</label>
                    <input type="text" name="description" required class="w-full bg-surface border border-gray-200 rounded p-2 text-sm focus:ring-2 focus:ring-brand/20 outline-none">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-1">Discount Type</label>
                        <select name="type" required class="w-full bg-surface border border-gray-200 rounded p-2 text-sm focus:ring-2 focus:ring-brand/20 outline-none cursor-pointer">
                            <option value="percentage">Percentage (%)</option>
                            <option value="fixed">Fixed Amount (₵)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-1">Value</label>
                        <input type="number" step="0.01" name="value" required class="w-full bg-surface border border-gray-200 rounded p-2 text-sm focus:ring-2 focus:ring-brand/20 outline-none">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-1">Max Discount (₵) (Optional)</label>
                        <input type="number" step="0.01" name="max_discount" class="w-full bg-surface border border-gray-200 rounded p-2 text-sm focus:ring-2 focus:ring-brand/20 outline-none">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-1">Max Global Uses (Optional)</label>
                        <input type="number" name="max_uses" class="w-full bg-surface border border-gray-200 rounded p-2 text-sm focus:ring-2 focus:ring-brand/20 outline-none">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-1">Starts At (Optional)</label>
                        <input type="date" name="starts_at" class="w-full bg-surface border border-gray-200 rounded p-2 text-sm focus:ring-2 focus:ring-brand/20 outline-none">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-1">Expires At (Optional)</label>
                        <input type="date" name="expires_at" class="w-full bg-surface border border-gray-200 rounded p-2 text-sm focus:ring-2 focus:ring-brand/20 outline-none">
                    </div>
                </div>
            </div>
            <div class="flex justify-end pt-4 border-t border-gray-100 gap-3">
                <button type="button" onclick="document.getElementById('add-modal').classList.add('hidden')" class="px-4 py-2 text-brand font-bold text-sm">Cancel</button>
                <button type="submit" class="px-6 py-2.5 bg-brand text-white font-bold rounded shadow-sm hover:bg-brand-light transition">Save Promo</button>
            </div>
        </form>
    </div>
</div>

@endsection
