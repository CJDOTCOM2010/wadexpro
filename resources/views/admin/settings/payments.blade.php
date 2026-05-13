@extends('admin.layout')
@section('title', 'Payment Gateway Hub')
@section('content')

<div class="p-6 lg:p-10 max-w-4xl mx-auto" x-data="{ activeTab: 'general' }">
    <div class="mb-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <h2 class="text-3xl font-black text-brand tracking-tighter">Payment Gateway Hub</h2>
            <p class="text-brand-muted font-medium mt-1">Configure API providers and switch between active payment nodes.</p>
        </div>
        <div class="flex items-center gap-3 bg-white p-2 rounded-xl border border-gray-100 shadow-sm">
            <span class="text-[10px] font-black text-brand-muted uppercase tracking-widest pl-2">Current Active:</span>
            <span class="px-3 py-1 bg-brand text-white text-[11px] font-black rounded-lg uppercase tracking-wider">
                {{ strtoupper($settings['active_payment_gateway']->value ?? 'NONE') }}
            </span>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="flex items-center gap-2 mb-8 overflow-x-auto pb-2 scrollbar-hide">
        <button @click="activeTab = 'general'" :class="activeTab === 'general' ? 'bg-brand text-white shadow-lg shadow-brand/20' : 'bg-white text-brand-muted hover:bg-surface'" class="px-6 py-2.5 rounded-xl font-black text-xs uppercase tracking-widest transition-all duration-300 border border-transparent">General</button>
        <button @click="activeTab = 'paystack'" :class="activeTab === 'paystack' ? 'bg-[#09A5DB] text-white shadow-lg shadow-blue-500/20' : 'bg-white text-brand-muted hover:bg-surface'" class="px-6 py-2.5 rounded-xl font-black text-xs uppercase tracking-widest transition-all duration-300 border border-transparent">Paystack</button>
        <button @click="activeTab = 'flutterwave'" :class="activeTab === 'flutterwave' ? 'bg-[#FB9129] text-white shadow-lg shadow-orange-500/20' : 'bg-white text-brand-muted hover:bg-surface'" class="px-6 py-2.5 rounded-xl font-black text-xs uppercase tracking-widest transition-all duration-300 border border-transparent">Flutterwave</button>
        <button @click="activeTab = 'stripe'" :class="activeTab === 'stripe' ? 'bg-[#635BFF] text-white shadow-lg shadow-indigo-500/20' : 'bg-white text-brand-muted hover:bg-surface'" class="px-6 py-2.5 rounded-xl font-black text-xs uppercase tracking-widest transition-all duration-300 border border-transparent">Stripe</button>
    </div>

    <form action="{{ route('orchestrator.settings.update') }}" method="POST">
        @csrf
        
        <!-- General Section -->
        <div x-show="activeTab === 'general'" class="space-y-6 animate-fade-in">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="p-6 border-b border-gray-50 bg-surface/30">
                    <h3 class="text-lg font-black text-brand">System-Wide Logic</h3>
                </div>
                <div class="p-8 space-y-6">
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-brand-muted uppercase tracking-widest">Active System Gateway</label>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            @foreach(['paystack', 'flutterwave', 'stripe'] as $gw)
                            <label class="relative cursor-pointer group">
                                <input type="radio" name="settings[active_payment_gateway]" value="{{ $gw }}" class="peer sr-only" {{ ($settings['active_payment_gateway']->value ?? '') == $gw ? 'checked' : '' }}>
                                <div class="p-4 rounded-xl border-2 border-gray-100 peer-checked:border-brand peer-checked:bg-brand/5 group-hover:border-brand/30 transition-all duration-300 flex flex-col items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-white shadow-sm flex items-center justify-center font-black text-brand uppercase text-xs">
                                        {{ substr($gw, 0, 2) }}
                                    </div>
                                    <span class="font-black text-xs uppercase tracking-widest text-brand-muted peer-checked:text-brand">{{ ucfirst($gw) }}</span>
                                </div>
                                <div class="absolute -top-2 -right-2 w-6 h-6 bg-brand text-white rounded-full flex items-center justify-center opacity-0 peer-checked:opacity-100 transition-opacity">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                </div>
                            </label>
                            @endforeach
                        </div>
                        <p class="text-[10px] text-gray-400 font-medium italic mt-2">Switching this will immediately change the provider used for all incoming mobile and web transactions.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Paystack Section -->
        <div x-show="activeTab === 'paystack'" class="space-y-6 animate-fade-in">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="p-6 border-b border-gray-50 bg-[#09A5DB]/5 flex items-center gap-4">
                    <div class="w-10 h-10 rounded-lg bg-[#09A5DB] flex items-center justify-center text-white font-black text-xl">P</div>
                    <h3 class="text-lg font-black text-brand">Paystack Configuration</h3>
                </div>
                <div class="p-8 space-y-6">
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-brand-muted uppercase tracking-widest">Public Key</label>
                        <input type="text" name="settings[paystack_public_key]" value="{{ $settings['paystack_public_key']->value ?? '' }}" class="w-full bg-surface border border-gray-100 rounded-xl px-5 py-4 text-sm font-bold focus:ring-2 focus:ring-[#09A5DB]/20 outline-none transition-all" placeholder="pk_test_...">
                        <p class="text-[9px] text-gray-400 font-medium italic italic">Used for client-side transaction initialization.</p>
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-brand-muted uppercase tracking-widest">Secret Key (Encrypted)</label>
                        <div class="relative">
                            <input type="password" name="settings[paystack_secret_key]" value="********" class="w-full bg-surface border border-gray-100 rounded-xl px-5 py-4 text-sm font-bold focus:ring-2 focus:ring-[#09A5DB]/20 outline-none transition-all">
                            <div class="absolute right-4 top-1/2 -translate-y-1/2 p-2 bg-white rounded-lg border border-gray-100 shadow-sm pointer-events-none">
                                <svg class="w-4 h-4 text-brand-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            </div>
                        </div>
                        <p class="text-[9px] text-gray-400 font-medium italic italic">WADEX-Guard: This key is encrypted at rest using AES-256-CBC.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Flutterwave Section -->
        <div x-show="activeTab === 'flutterwave'" class="space-y-6 animate-fade-in">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="p-6 border-b border-gray-50 bg-[#FB9129]/5 flex items-center gap-4">
                    <div class="w-10 h-10 rounded-lg bg-[#FB9129] flex items-center justify-center text-white font-black text-xl">F</div>
                    <h3 class="text-lg font-black text-brand">Flutterwave Configuration</h3>
                </div>
                <div class="p-8 space-y-6">
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-brand-muted uppercase tracking-widest">Public Key</label>
                        <input type="text" name="settings[flutterwave_public_key]" value="{{ $settings['flutterwave_public_key']->value ?? '' }}" class="w-full bg-surface border border-gray-100 rounded-xl px-5 py-4 text-sm font-bold focus:ring-2 focus:ring-[#FB9129]/20 outline-none transition-all" placeholder="FLWPUBK_TEST-...">
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-brand-muted uppercase tracking-widest">Secret Key (Encrypted)</label>
                        <div class="relative">
                            <input type="password" name="settings[flutterwave_secret_key]" value="********" class="w-full bg-surface border border-gray-100 rounded-xl px-5 py-4 text-sm font-bold focus:ring-2 focus:ring-[#FB9129]/20 outline-none transition-all">
                            <div class="absolute right-4 top-1/2 -translate-y-1/2 p-2 bg-white rounded-lg border border-gray-100 shadow-sm pointer-events-none">
                                <svg class="w-4 h-4 text-brand-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stripe Section -->
        <div x-show="activeTab === 'stripe'" class="space-y-6 animate-fade-in">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="p-6 border-b border-gray-50 bg-[#635BFF]/5 flex items-center gap-4">
                    <div class="w-10 h-10 rounded-lg bg-[#635BFF] flex items-center justify-center text-white font-black text-xl">S</div>
                    <h3 class="text-lg font-black text-brand">Stripe Configuration</h3>
                </div>
                <div class="p-8 space-y-6">
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-brand-muted uppercase tracking-widest">Publishable Key</label>
                        <input type="text" name="settings[stripe_public_key]" value="{{ $settings['stripe_public_key']->value ?? '' }}" class="w-full bg-surface border border-gray-100 rounded-xl px-5 py-4 text-sm font-bold focus:ring-2 focus:ring-[#635BFF]/20 outline-none transition-all" placeholder="pk_test_...">
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-brand-muted uppercase tracking-widest">Secret Key (Encrypted)</label>
                        <div class="relative">
                            <input type="password" name="settings[stripe_secret_key]" value="********" class="w-full bg-surface border border-gray-100 rounded-xl px-5 py-4 text-sm font-bold focus:ring-2 focus:ring-[#635BFF]/20 outline-none transition-all">
                            <div class="absolute right-4 top-1/2 -translate-y-1/2 p-2 bg-white rounded-lg border border-gray-100 shadow-sm pointer-events-none">
                                <svg class="w-4 h-4 text-brand-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-10 flex justify-end">
            <button type="submit" class="px-10 py-5 bg-brand text-white font-black rounded-2xl hover:bg-brand-light transition-all duration-300 shadow-xl shadow-brand/20 flex items-center gap-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                Synchronize Gateways
            </button>
        </div>
    </form>
</div>

@endsection
