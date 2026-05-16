@extends('admin.layout')
@section('title', 'Promotions & Coupons')
@section('content')

@if(session('error'))
<div class="mb-4 p-3.5 bg-red-50 border border-red-200 rounded-lg flex items-center gap-2.5">
    <svg class="w-4 h-4 text-red-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    <p class="text-sm font-medium text-red-700">{{ session('error') }}</p>
</div>
@endif
@if(session('success'))
<div class="mb-4 p-3.5 bg-green-50 border border-green-200 rounded-lg flex items-center gap-2.5">
    <svg class="w-4 h-4 text-green-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    <p class="text-sm font-medium text-green-700">{{ session('success') }}</p>
</div>
@endif

<div x-data="promoManager()" class="max-w-6xl mx-auto">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-black text-brand tracking-tight">Promotions & Coupons</h2>
            <p class="text-sm text-brand-muted font-medium mt-0.5">Manage discount codes, referral bonuses, and marketing campaigns.</p>
        </div>
        <button @click="showAdd = true" class="px-5 py-2.5 bg-brand text-white rounded-lg text-xs font-bold hover:bg-brand-light transition-colors flex items-center gap-2 shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Create Campaign
        </button>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <div class="bg-white border border-gray-100 rounded-xl p-4 flex items-center gap-3.5">
            <div class="w-10 h-10 bg-green-50 rounded-lg flex items-center justify-center text-green-600 shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <p class="text-lg font-black text-brand">{{ number_format($stats['active'] ?? 0) }}</p>
                <p class="text-[10px] font-bold text-brand-muted uppercase tracking-wider">Active Promos</p>
            </div>
        </div>
        <div class="bg-white border border-gray-100 rounded-xl p-4 flex items-center gap-3.5">
            <div class="w-10 h-10 bg-gray-50 rounded-lg flex items-center justify-center text-gray-500 shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <p class="text-lg font-black text-brand">{{ number_format($stats['expired'] ?? 0) }}</p>
                <p class="text-[10px] font-bold text-brand-muted uppercase tracking-wider">Expired</p>
            </div>
        </div>
        <div class="bg-white border border-gray-100 rounded-xl p-4 flex items-center gap-3.5">
            <div class="w-10 h-10 bg-brand/5 rounded-lg flex items-center justify-center text-brand shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
            </div>
            <div>
                <p class="text-lg font-black text-brand">{{ number_format($stats['total_uses'] ?? 0) }}</p>
                <p class="text-[10px] font-bold text-brand-muted uppercase tracking-wider">Total Redemptions</p>
            </div>
        </div>
    </div>

    {{-- Promo Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($promos as $promo)
        @php
        $isExpired = $promo->expires_at && $promo->expires_at->isPast();
        $isExhausted = $promo->max_uses && $promo->times_used >= $promo->max_uses;
        $isActive = $promo->is_active && !$isExpired && !$isExhausted;
        @endphp
        <div class="bg-white border border-gray-100 rounded-xl overflow-hidden {{ !$isActive ? 'opacity-60' : '' }}">
            <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                <div class="flex items-center gap-2 min-w-0">
                    <span class="px-2 py-0.5 text-[9px] font-bold rounded
                        @if($isActive) bg-green-50 text-green-700
                        @elseif($isExpired) bg-gray-100 text-gray-500
                        @elseif($isExhausted) bg-red-50 text-red-600
                        @else bg-amber-50 text-amber-700 @endif">
                        {{ $isActive ? 'Active' : ($isExpired ? 'Expired' : ($isExhausted ? 'Depleted' : 'Paused')) }}
                    </span>
                    <code class="text-sm font-bold font-mono text-brand tracking-wider truncate">{{ $promo->code }}</code>
                </div>
            </div>
            <div class="px-5 py-4 space-y-2">
                <p class="text-sm font-bold text-brand truncate">{{ $promo->description ?? 'Promo Code' }}</p>
                <div class="flex justify-between text-xs">
                    <span class="text-brand-muted">Discount</span>
                    <span class="font-bold text-brand">{{ $promo->type === 'percentage' ? $promo->value.'%' : '₵'.$promo->value }} off@if($promo->max_discount) (max ₵{{ $promo->max_discount }})@endif</span>
                </div>
                <div class="flex justify-between text-xs">
                    <span class="text-brand-muted">Usage</span>
                    <span class="font-bold text-brand">{{ number_format($promo->times_used) }} / {{ $promo->max_uses ? number_format($promo->max_uses) : '∞' }}</span>
                </div>
                @if($promo->max_uses)
                @php $progress = min(100, ($promo->times_used / $promo->max_uses) * 100); @endphp
                <div class="w-full bg-surface rounded-full h-1.5"><div class="bg-brand h-1.5 rounded-full" style="width: {{ $progress }}%"></div></div>
                @endif
            </div>
            <div class="px-5 py-3 border-t border-gray-100 flex items-center justify-between bg-surface/20">
                <span class="text-[10px] text-brand-muted">{{ $promo->expires_at ? 'Exp: '.$promo->expires_at->format('M d, Y') : 'No expiry' }}</span>
                <div class="flex items-center gap-2">
                    <form action="{{ route('orchestrator.marketing.promos.toggle', $promo->id) }}" method="POST" class="inline">
                        @csrf @method('PATCH')
                        <button type="submit" class="text-[10px] font-bold {{ $promo->is_active ? 'text-amber-500 hover:text-amber-600' : 'text-green-500 hover:text-green-600' }}">{{ $promo->is_active ? 'Pause' : 'Activate' }}</button>
                    </form>
                    <button @click="confirmDelete('{{ $promo->id }}', '{{ addslashes($promo->code) }}')" class="text-[10px] font-bold text-red-400 hover:text-red-600">Delete</button>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full flex flex-col items-center justify-center py-16 text-brand-muted bg-white border border-gray-100 rounded-xl">
            <svg class="w-14 h-14 mb-3 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
            <p class="text-sm font-bold">No promo codes created yet</p>
            <p class="text-xs mt-1">Create your first campaign to get started.</p>
        </div>
        @endforelse
    </div>

    @if($promos->hasPages())
    <div class="mt-4">{{ $promos->links() }}</div>
    @endif

    {{-- Add Modal --}}
    <div x-show="showAdd" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-brand/50 backdrop-blur-sm" @click="showAdd = false"></div>
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg relative z-10 max-h-[90vh] overflow-y-auto" @click.outside="showAdd = false">
            <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between sticky top-0 bg-white">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 bg-accent/10 rounded-lg flex items-center justify-center text-accent">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-brand">Create Promo Code</h3>
                        <p class="text-xs text-brand-muted">Launch a new discount campaign.</p>
                    </div>
                </div>
                <button @click="showAdd = false" class="w-7 h-7 bg-surface rounded-lg flex items-center justify-center text-brand-muted hover:text-brand transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form action="{{ route('orchestrator.marketing.promos.store') }}" method="POST" class="p-6 space-y-4">
                @csrf
                <div>
                    <label class="text-[10px] font-bold text-brand-muted uppercase tracking-wider mb-1.5 block">Promo Code <span class="text-red-500">*</span></label>
                    <input type="text" name="code" required placeholder="e.g. WELCOME26" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-medium font-mono uppercase outline-none focus:ring-2 focus:ring-accent/20 transition-shadow">
                </div>
                <div>
                    <label class="text-[10px] font-bold text-brand-muted uppercase tracking-wider mb-1.5 block">Description</label>
                    <input type="text" name="description" required placeholder="e.g. 20% off first ride" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-medium outline-none focus:ring-2 focus:ring-accent/20 transition-shadow">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-[10px] font-bold text-brand-muted uppercase tracking-wider mb-1.5 block">Discount Type</label>
                        <select name="type" required class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-medium outline-none focus:ring-2 focus:ring-accent/20">
                            <option value="percentage">Percentage (%)</option>
                            <option value="fixed">Fixed Amount (₵)</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-[10px] font-bold text-brand-muted uppercase tracking-wider mb-1.5 block">Value</label>
                        <input type="number" step="0.01" name="value" required placeholder="20" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-medium outline-none focus:ring-2 focus:ring-accent/20 transition-shadow">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-[10px] font-bold text-brand-muted uppercase tracking-wider mb-1.5 block">Max Discount (₵)</label>
                        <input type="number" step="0.01" name="max_discount" placeholder="Optional" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-medium outline-none focus:ring-2 focus:ring-accent/20 transition-shadow">
                    </div>
                    <div>
                        <label class="text-[10px] font-bold text-brand-muted uppercase tracking-wider mb-1.5 block">Max Uses</label>
                        <input type="number" name="max_uses" placeholder="Optional" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-medium outline-none focus:ring-2 focus:ring-accent/20 transition-shadow">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-[10px] font-bold text-brand-muted uppercase tracking-wider mb-1.5 block">Starts At</label>
                        <input type="date" name="starts_at" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-medium outline-none focus:ring-2 focus:ring-accent/20 transition-shadow">
                    </div>
                    <div>
                        <label class="text-[10px] font-bold text-brand-muted uppercase tracking-wider mb-1.5 block">Expires At</label>
                        <input type="date" name="expires_at" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-medium outline-none focus:ring-2 focus:ring-accent/20 transition-shadow">
                    </div>
                </div>
                <div class="flex justify-end gap-2 pt-2 border-t border-gray-100">
                    <button type="button" @click="showAdd = false" class="px-4 py-2 text-xs font-bold text-brand-muted hover:text-brand transition-colors">Cancel</button>
                    <button type="submit" class="px-5 py-2 bg-brand text-white rounded-lg text-xs font-bold hover:bg-brand-light transition-colors">Create Promo</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Multi-step Delete --}}
    <div x-show="deleteStep > 0" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-brand/60 backdrop-blur-sm" @click="closeDelete()"></div>
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-md relative z-10" @click.outside="closeDelete()">
            <template x-if="deleteStep === 1">
                <div class="p-6">
                    <div class="w-14 h-14 bg-red-50 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-7 h-7 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <h3 class="text-lg font-bold text-brand text-center mb-2">Delete Promo?</h3>
                    <p class="text-sm text-brand-muted text-center mb-6">Permanently delete <strong class="text-brand" x-text="deleteLabel"></strong>?</p>
                    <div class="flex gap-2">
                        <button type="button" @click="closeDelete()" class="flex-1 px-4 py-2.5 bg-surface text-brand-muted rounded-lg text-xs font-bold hover:bg-gray-100">Cancel</button>
                        <button type="button" @click="deleteStep = 2" class="flex-1 px-4 py-2.5 bg-red-600 text-white rounded-lg text-xs font-bold hover:bg-red-700">Continue</button>
                    </div>
                </div>
            </template>
            <template x-if="deleteStep === 2">
                <div class="p-6">
                    <div class="w-14 h-14 bg-red-50 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-7 h-7 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <h3 class="text-lg font-bold text-brand text-center mb-2">Type DELETE to confirm</h3>
                    <input type="text" x-model="deleteConfirm" @input="deleteConfirm = deleteConfirm.toUpperCase()" placeholder="Type DELETE" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-bold text-center outline-none focus:ring-2 focus:ring-red-300 transition-shadow mb-6 uppercase tracking-widest">
                    <div class="flex gap-2">
                        <button type="button" @click="deleteStep = 1" class="flex-1 px-4 py-2.5 bg-surface text-brand-muted rounded-lg text-xs font-bold hover:bg-gray-100">Back</button>
                        <button type="button" @click="executeDelete()" :disabled="deleteConfirm !== 'DELETE'" class="flex-1 px-4 py-2.5 rounded-lg text-xs font-bold" :class="deleteConfirm === 'DELETE' ? 'bg-red-600 text-white hover:bg-red-700' : 'bg-gray-100 text-gray-400 cursor-not-allowed'">Confirm</button>
                    </div>
                </div>
            </template>
        </div>
    </div>
</div>

<script>
function promoManager() {
    return {
        showAdd: false,
        deleteStep: 0, deleteId: '', deleteLabel: '', deleteConfirm: '',
        confirmDelete(id, label) { this.deleteId = id; this.deleteLabel = label; this.deleteStep = 1; this.deleteConfirm = ''; },
        closeDelete() { this.deleteStep = 0; this.deleteConfirm = ''; },
        executeDelete() {
            if (this.deleteConfirm !== 'DELETE') return;
            const f = document.createElement('form'); f.method = 'POST'; f.action = '/orchestrator/marketing/promotions/' + this.deleteId;
            const c = document.createElement('input'); c.type = 'hidden'; c.name = '_token'; c.value = '{{ csrf_token() }}'; f.appendChild(c);
            const m = document.createElement('input'); m.type = 'hidden'; m.name = '_method'; m.value = 'DELETE'; f.appendChild(m);
            document.body.appendChild(f); f.submit();
        }
    };
}
</script>
<style>[x-cloak] { display: none !important; }</style>
@endsection