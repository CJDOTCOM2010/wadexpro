@extends('admin.layout')
@section('title', 'Payment Gateway Hub')
@section('content')
@php
$active = $settings['active_payment_gateway']->value ?? 'paystack';
$env = $settings['payment_environment']->value ?? 'sandbox';
$currency = $settings['payment_currency']->value ?? 'GHS';
$cod = ($settings['cash_on_delivery_enabled']->value ?? 'true') === 'true';
$wallet = ($settings['wallet_payments_enabled']->value ?? 'true') === 'true';
$momo = ($settings['momo_enabled']->value ?? 'true') === 'true';
$gpay = ($settings['googlepay_enabled']->value ?? 'false') === 'true';
$psEnabled = ($settings['paystack_enabled']->value ?? 'true') === 'true';
$fwEnabled = ($settings['flutterwave_enabled']->value ?? 'true') === 'true';
$stEnabled = ($settings['stripe_enabled']->value ?? 'false') === 'true';
@endphp
<div class="p-6 lg:p-10 max-w-5xl mx-auto" x-data="{ tab: 'overview' }">
<div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
<div>
<h2 class="text-3xl font-black text-brand tracking-tighter">Payment Gateway Hub</h2>
<p class="text-brand-muted font-medium mt-1">Configure providers, toggle methods, and manage API credentials.</p>
</div>
<div class="flex items-center gap-3 bg-white p-3 rounded-xl border border-gray-100 shadow-sm">
<span class="text-[10px] font-black text-brand-muted uppercase tracking-widest">Active:</span>
<span class="px-3 py-1 bg-green-500 text-white text-[11px] font-black rounded-lg uppercase">{{ $active }}</span>
<span class="px-2 py-1 {{ $env === 'production' ? 'bg-red-500' : 'bg-amber-500' }} text-white text-[9px] font-black rounded uppercase">{{ $env }}</span>
</div>
</div>
<!-- Tabs -->
<div class="flex gap-2 mb-8 overflow-x-auto pb-2">
@foreach(['overview'=>'Overview','paystack'=>'Paystack','flutterwave'=>'Flutterwave','stripe'=>'Stripe','googlepay'=>'Google Pay','methods'=>'Payment Methods'] as $k=>$v)
<button @click="tab='{{ $k }}'" :class="tab==='{{ $k }}' ? 'bg-brand text-white shadow-lg' : 'bg-white text-brand-muted hover:bg-surface'" class="px-5 py-2.5 rounded-xl font-black text-xs uppercase tracking-widest transition whitespace-nowrap">{{ $v }}</button>
@endforeach
</div>
<form action="{{ route('orchestrator.settings.update') }}" method="POST">
@csrf
<!-- OVERVIEW -->
<div x-show="tab==='overview'" class="space-y-6">
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-8 space-y-8">
<h3 class="text-lg font-black text-brand">Global Configuration</h3>
<div class="space-y-2">
<label class="text-[10px] font-black text-brand-muted uppercase tracking-widest">Primary Gateway</label>
<div class="grid grid-cols-2 md:grid-cols-4 gap-3">
@foreach(['paystack'=>'Paystack','flutterwave'=>'Flutterwave','stripe'=>'Stripe','googlepay'=>'Google Pay'] as $gk=>$gv)
<label class="relative cursor-pointer"><input type="radio" name="settings[active_payment_gateway]" value="{{ $gk }}" class="peer sr-only" {{ $active===$gk?'checked':'' }}>
<div class="p-4 rounded-xl border-2 border-gray-100 peer-checked:border-brand peer-checked:bg-brand/5 hover:border-brand/30 transition text-center">
<span class="font-black text-xs uppercase tracking-widest">{{ $gv }}</span>
</div>
<div class="absolute -top-2 -right-2 w-5 h-5 bg-brand text-white rounded-full items-center justify-center hidden peer-checked:flex"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg></div>
</label>
@endforeach
</div>
<p class="text-[9px] text-gray-400 italic">Switching this immediately changes the provider for all transactions.</p>
</div>
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
<div class="space-y-2">
<label class="text-[10px] font-black text-brand-muted uppercase tracking-widest">Environment Mode</label>
<select name="settings[payment_environment]" class="w-full bg-surface border border-gray-100 rounded-xl px-4 py-3 text-sm font-bold focus:ring-2 focus:ring-accent outline-none">
<option value="sandbox" {{ $env==='sandbox'?'selected':'' }}>Sandbox (Testing)</option>
<option value="production" {{ $env==='production'?'selected':'' }}>Production (Live)</option>
</select>
<p class="text-[9px] text-gray-400 italic">Use Sandbox for testing. Switch to Production only when ready for real transactions.</p>
</div>
<div class="space-y-2">
<label class="text-[10px] font-black text-brand-muted uppercase tracking-widest">Default Currency (ISO 4217)</label>
<input type="text" name="settings[payment_currency]" value="{{ $currency }}" class="w-full bg-surface border border-gray-100 rounded-xl px-4 py-3 text-sm font-bold focus:ring-2 focus:ring-accent outline-none" placeholder="GHS">
<p class="text-[9px] text-gray-400 italic">Currency code used across all payment flows. e.g. GHS, NGN, USD.</p>
</div>
</div>
<div class="space-y-2">
<label class="text-[10px] font-black text-brand-muted uppercase tracking-widest">Webhook Signing Secret (Encrypted)</label>
<input type="password" name="settings[payment_webhook_secret]" value="********" class="w-full bg-surface border border-gray-100 rounded-xl px-4 py-3 text-sm font-bold focus:ring-2 focus:ring-accent outline-none">
<p class="text-[9px] text-gray-400 italic">HMAC secret for verifying incoming payment webhook callbacks.</p>
</div>
</div>
</div>
<!-- PAYSTACK -->
<div x-show="tab==='paystack'" class="space-y-6">
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
<div class="p-6 border-b border-gray-50 bg-[#09A5DB]/5 flex items-center justify-between">
<div class="flex items-center gap-4"><div class="w-10 h-10 rounded-lg bg-[#09A5DB] flex items-center justify-center text-white font-black text-lg">P</div><h3 class="text-lg font-black text-brand">Paystack</h3></div>
<label class="relative inline-flex items-center cursor-pointer"><input type="hidden" name="settings[paystack_enabled]" value="false"><input type="checkbox" name="settings[paystack_enabled]" value="true" class="sr-only peer" {{ $psEnabled?'checked':'' }}><div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#09A5DB]"></div></label>
</div>
<div class="p-8 space-y-5">
<div class="space-y-2"><label class="text-[10px] font-black text-brand-muted uppercase tracking-widest">Public Key</label><input type="text" name="settings[paystack_public_key]" value="{{ $settings['paystack_public_key']->value ?? '' }}" class="w-full bg-surface border border-gray-100 rounded-xl px-4 py-3 text-sm font-bold focus:ring-2 focus:ring-[#09A5DB]/30 outline-none" placeholder="pk_test_..."><p class="text-[9px] text-gray-400 italic">Client-side key for initializing Paystack Inline checkout.</p></div>
<div class="space-y-2"><label class="text-[10px] font-black text-brand-muted uppercase tracking-widest">Secret Key (Encrypted)</label><input type="password" name="settings[paystack_secret_key]" value="********" class="w-full bg-surface border border-gray-100 rounded-xl px-4 py-3 text-sm font-bold focus:ring-2 focus:ring-[#09A5DB]/30 outline-none"><p class="text-[9px] text-gray-400 italic">Server-side key. Encrypted with AES-256-CBC at rest.</p></div>
</div>
</div>
</div>
<!-- FLUTTERWAVE -->
<div x-show="tab==='flutterwave'" class="space-y-6">
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
<div class="p-6 border-b border-gray-50 bg-[#FB9129]/5 flex items-center justify-between">
<div class="flex items-center gap-4"><div class="w-10 h-10 rounded-lg bg-[#FB9129] flex items-center justify-center text-white font-black text-lg">F</div><h3 class="text-lg font-black text-brand">Flutterwave</h3></div>
<label class="relative inline-flex items-center cursor-pointer"><input type="hidden" name="settings[flutterwave_enabled]" value="false"><input type="checkbox" name="settings[flutterwave_enabled]" value="true" class="sr-only peer" {{ $fwEnabled?'checked':'' }}><div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#FB9129]"></div></label>
</div>
<div class="p-8 space-y-5">
<div class="space-y-2"><label class="text-[10px] font-black text-brand-muted uppercase tracking-widest">Public Key</label><input type="text" name="settings[flutterwave_public_key]" value="{{ $settings['flutterwave_public_key']->value ?? '' }}" class="w-full bg-surface border border-gray-100 rounded-xl px-4 py-3 text-sm font-bold focus:ring-2 focus:ring-[#FB9129]/30 outline-none" placeholder="FLWPUBK_TEST-..."><p class="text-[9px] text-gray-400 italic">Client-side key for Flutterwave Standard or Inline.</p></div>
<div class="space-y-2"><label class="text-[10px] font-black text-brand-muted uppercase tracking-widest">Secret Key (Encrypted)</label><input type="password" name="settings[flutterwave_secret_key]" value="********" class="w-full bg-surface border border-gray-100 rounded-xl px-4 py-3 text-sm font-bold focus:ring-2 focus:ring-[#FB9129]/30 outline-none"><p class="text-[9px] text-gray-400 italic">Server-side key. Encrypted with AES-256-CBC at rest.</p></div>
<div class="space-y-2"><label class="text-[10px] font-black text-brand-muted uppercase tracking-widest">Encryption Key (Encrypted)</label><input type="password" name="settings[flutterwave_encryption_key]" value="********" class="w-full bg-surface border border-gray-100 rounded-xl px-4 py-3 text-sm font-bold focus:ring-2 focus:ring-[#FB9129]/30 outline-none"><p class="text-[9px] text-gray-400 italic">Required for direct card charge encryption.</p></div>
</div>
</div>
</div>
<!-- STRIPE -->
<div x-show="tab==='stripe'" class="space-y-6">
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
<div class="p-6 border-b border-gray-50 bg-[#635BFF]/5 flex items-center justify-between">
<div class="flex items-center gap-4"><div class="w-10 h-10 rounded-lg bg-[#635BFF] flex items-center justify-center text-white font-black text-lg">S</div><h3 class="text-lg font-black text-brand">Stripe</h3></div>
<label class="relative inline-flex items-center cursor-pointer"><input type="hidden" name="settings[stripe_enabled]" value="false"><input type="checkbox" name="settings[stripe_enabled]" value="true" class="sr-only peer" {{ $stEnabled?'checked':'' }}><div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#635BFF]"></div></label>
</div>
<div class="p-8 space-y-5">
<div class="space-y-2"><label class="text-[10px] font-black text-brand-muted uppercase tracking-widest">Publishable Key</label><input type="text" name="settings[stripe_public_key]" value="{{ $settings['stripe_public_key']->value ?? '' }}" class="w-full bg-surface border border-gray-100 rounded-xl px-4 py-3 text-sm font-bold focus:ring-2 focus:ring-[#635BFF]/30 outline-none" placeholder="pk_test_..."><p class="text-[9px] text-gray-400 italic">Client-side key for Stripe Elements or Checkout.</p></div>
<div class="space-y-2"><label class="text-[10px] font-black text-brand-muted uppercase tracking-widest">Secret Key (Encrypted)</label><input type="password" name="settings[stripe_secret_key]" value="********" class="w-full bg-surface border border-gray-100 rounded-xl px-4 py-3 text-sm font-bold focus:ring-2 focus:ring-[#635BFF]/30 outline-none"><p class="text-[9px] text-gray-400 italic">Server-side key. Encrypted with AES-256-CBC at rest.</p></div>
</div>
</div>
</div>
<!-- GOOGLE PAY -->
<div x-show="tab==='googlepay'" class="space-y-6">
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
<div class="p-6 border-b border-gray-50 bg-[#4285F4]/5 flex items-center justify-between">
<div class="flex items-center gap-4"><div class="w-10 h-10 rounded-lg bg-[#4285F4] flex items-center justify-center text-white font-black text-lg">G</div><h3 class="text-lg font-black text-brand">Google Pay</h3></div>
<label class="relative inline-flex items-center cursor-pointer"><input type="hidden" name="settings[googlepay_enabled]" value="false"><input type="checkbox" name="settings[googlepay_enabled]" value="true" class="sr-only peer" {{ $gpay?'checked':'' }}><div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#4285F4]"></div></label>
</div>
<div class="p-8 space-y-5">
<div class="space-y-2"><label class="text-[10px] font-black text-brand-muted uppercase tracking-widest">Merchant ID</label><input type="text" name="settings[googlepay_merchant_id]" value="{{ $settings['googlepay_merchant_id']->value ?? '' }}" class="w-full bg-surface border border-gray-100 rounded-xl px-4 py-3 text-sm font-bold focus:ring-2 focus:ring-[#4285F4]/30 outline-none" placeholder="BCR2DN4T..."><p class="text-[9px] text-gray-400 italic">Your Google Pay merchant ID from the Google Pay console.</p></div>
<div class="space-y-2"><label class="text-[10px] font-black text-brand-muted uppercase tracking-widest">Merchant Display Name</label><input type="text" name="settings[googlepay_merchant_name]" value="{{ $settings['googlepay_merchant_name']->value ?? 'WADEXPRO' }}" class="w-full bg-surface border border-gray-100 rounded-xl px-4 py-3 text-sm font-bold focus:ring-2 focus:ring-[#4285F4]/30 outline-none" placeholder="WADEXPRO"><p class="text-[9px] text-gray-400 italic">Name shown to customers on the Google Pay payment sheet.</p></div>
<div class="space-y-2"><label class="text-[10px] font-black text-brand-muted uppercase tracking-widest">Tokenization Gateway</label>
<select name="settings[googlepay_gateway_tokenization]" class="w-full bg-surface border border-gray-100 rounded-xl px-4 py-3 text-sm font-bold focus:ring-2 focus:ring-[#4285F4]/30 outline-none">
<option value="paystack" {{ ($settings['googlepay_gateway_tokenization']->value ?? '')==='paystack'?'selected':'' }}>Paystack</option>
<option value="flutterwave" {{ ($settings['googlepay_gateway_tokenization']->value ?? '')==='flutterwave'?'selected':'' }}>Flutterwave</option>
<option value="stripe" {{ ($settings['googlepay_gateway_tokenization']->value ?? '')==='stripe'?'selected':'' }}>Stripe</option>
</select>
<p class="text-[9px] text-gray-400 italic">Backend gateway that processes the Google Pay token into a charge.</p></div>
</div>
</div>
</div>
<!-- PAYMENT METHODS -->
<div x-show="tab==='methods'" class="space-y-6">
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-8 space-y-6">
<h3 class="text-lg font-black text-brand">Accepted Payment Methods</h3>
<p class="text-xs text-brand-muted">Toggle which payment methods customers can use across WADEXPRO apps.</p>
<div class="grid grid-cols-1 md:grid-cols-2 gap-5">
<!-- Cash -->
<div class="flex items-center justify-between p-5 rounded-xl border border-gray-100 bg-surface/30">
<div class="flex items-center gap-4"><div class="w-10 h-10 rounded-lg bg-green-100 flex items-center justify-center text-green-600"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg></div>
<div><p class="font-black text-sm text-brand">Cash on Delivery</p><p class="text-[9px] text-gray-400 italic">Physical cash at ride/delivery completion.</p></div></div>
<label class="relative inline-flex items-center cursor-pointer"><input type="hidden" name="settings[cash_on_delivery_enabled]" value="false"><input type="checkbox" name="settings[cash_on_delivery_enabled]" value="true" class="sr-only peer" {{ $cod?'checked':'' }}><div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-500"></div></label>
</div>
<!-- Wallet -->
<div class="flex items-center justify-between p-5 rounded-xl border border-gray-100 bg-surface/30">
<div class="flex items-center gap-4"><div class="w-10 h-10 rounded-lg bg-brand/10 flex items-center justify-center text-brand"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg></div>
<div><p class="font-black text-sm text-brand">WADEX Wallet</p><p class="text-[9px] text-gray-400 italic">Pay from pre-loaded wallet balance.</p></div></div>
<label class="relative inline-flex items-center cursor-pointer"><input type="hidden" name="settings[wallet_payments_enabled]" value="false"><input type="checkbox" name="settings[wallet_payments_enabled]" value="true" class="sr-only peer" {{ $wallet?'checked':'' }}><div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-brand"></div></label>
</div>
<!-- Mobile Money -->
<div class="flex items-center justify-between p-5 rounded-xl border border-gray-100 bg-surface/30">
<div class="flex items-center gap-4"><div class="w-10 h-10 rounded-lg bg-yellow-100 flex items-center justify-center text-yellow-600"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg></div>
<div><p class="font-black text-sm text-brand">Mobile Money</p><p class="text-[9px] text-gray-400 italic">MTN MoMo, Vodafone Cash, AirtelTigo.</p></div></div>
<label class="relative inline-flex items-center cursor-pointer"><input type="hidden" name="settings[momo_enabled]" value="false"><input type="checkbox" name="settings[momo_enabled]" value="true" class="sr-only peer" {{ $momo?'checked':'' }}><div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-yellow-500"></div></label>
</div>
<!-- Google Pay -->
<div class="flex items-center justify-between p-5 rounded-xl border border-gray-100 bg-surface/30">
<div class="flex items-center gap-4"><div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center text-blue-600"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg></div>
<div><p class="font-black text-sm text-brand">Google Pay</p><p class="text-[9px] text-gray-400 italic">Contactless payment on Android devices.</p></div></div>
<label class="relative inline-flex items-center cursor-pointer"><input type="hidden" name="settings[googlepay_enabled]" value="false"><input type="checkbox" name="settings[googlepay_enabled]" value="true" class="sr-only peer" {{ $gpay?'checked':'' }}><div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#4285F4]"></div></label>
</div>
</div>
<div class="space-y-2 pt-4 border-t border-gray-100">
<label class="text-[10px] font-black text-brand-muted uppercase tracking-widest">Mobile Money Processing Gateway</label>
<select name="settings[momo_provider]" class="w-full bg-surface border border-gray-100 rounded-xl px-4 py-3 text-sm font-bold focus:ring-2 focus:ring-accent outline-none">
<option value="paystack" {{ ($settings['momo_provider']->value ?? '')==='paystack'?'selected':'' }}>Paystack</option>
<option value="flutterwave" {{ ($settings['momo_provider']->value ?? '')==='flutterwave'?'selected':'' }}>Flutterwave</option>
</select>
<p class="text-[9px] text-gray-400 italic">Which API gateway handles Mobile Money USSD prompts.</p>
</div>
</div>
</div>
<div class="mt-8 flex justify-end">
<button type="submit" class="px-10 py-4 bg-brand text-white font-black rounded-2xl hover:bg-brand-light transition shadow-xl shadow-brand/20 flex items-center gap-3">
<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
Synchronize Gateways
</button>
</div>
</form>
</div>
@endsection
