@extends('admin.layout')
@section('title', 'FAQ Manager')
@section('content')

<div class="mb-8 flex items-center justify-between">
    <div>
        <h2 class="text-2xl font-black text-brand tracking-tight">FAQ Manager</h2>
        <p class="text-brand-muted font-medium mt-1">Manage Help Center questions for customers and drivers.</p>
    </div>
    <div class="flex gap-4">
        <button class="px-6 py-3 bg-brand text-white font-bold rounded-lg hover:bg-brand-light transition flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Add FAQ
        </button>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    
    <!-- Customer FAQs -->
    <div>
        <h3 class="text-[10px] font-black text-brand-muted uppercase tracking-widest mb-4 pl-2 border-l-2 border-brand">Customer Help Center</h3>
        <div class="space-y-4">
            
            <!-- Item -->
            <div class="bg-white rounded border border-gray-100 p-5 shadow-sm group">
                <div class="flex justify-between items-start mb-2">
                    <h4 class="text-sm font-bold text-brand">How do I reset my password?</h4>
                    <button class="text-gray-400 hover:text-accent transition opacity-0 group-hover:opacity-100"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg></button>
                </div>
                <p class="text-xs text-brand-muted leading-relaxed">Go to the login screen and tap "Forgot Password". Enter your registered phone number to receive an OTP to reset it.</p>
                <div class="mt-3 pt-3 border-t border-gray-50 text-[10px] font-bold text-gray-400 uppercase tracking-widest flex items-center justify-between">
                    <span>Category: Account</span>
                    <span>Order: 1</span>
                </div>
            </div>

            <!-- Item -->
            <div class="bg-white rounded border border-gray-100 p-5 shadow-sm group">
                <div class="flex justify-between items-start mb-2">
                    <h4 class="text-sm font-bold text-brand">Can I cancel a ride after booking?</h4>
                    <button class="text-gray-400 hover:text-accent transition opacity-0 group-hover:opacity-100"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg></button>
                </div>
                <p class="text-xs text-brand-muted leading-relaxed">Yes, you can cancel before the driver arrives. Note that a cancellation fee may apply if the driver has already waited at the pickup location for over 5 minutes.</p>
                <div class="mt-3 pt-3 border-t border-gray-50 text-[10px] font-bold text-gray-400 uppercase tracking-widest flex items-center justify-between">
                    <span>Category: Rides</span>
                    <span>Order: 2</span>
                </div>
            </div>

        </div>
    </div>

    <!-- Driver FAQs -->
    <div>
        <h3 class="text-[10px] font-black text-brand-muted uppercase tracking-widest mb-4 pl-2 border-l-2 border-accent">Driver Help Center</h3>
        <div class="space-y-4">
            
            <!-- Item -->
            <div class="bg-white rounded border border-gray-100 p-5 shadow-sm group">
                <div class="flex justify-between items-start mb-2">
                    <h4 class="text-sm font-bold text-brand">When do I get my payouts?</h4>
                    <button class="text-gray-400 hover:text-accent transition opacity-0 group-hover:opacity-100"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg></button>
                </div>
                <p class="text-xs text-brand-muted leading-relaxed">Wallet balances can be withdrawn to Mobile Money instantly at any time, subject to a minimum withdrawal amount of ₵50.</p>
                <div class="mt-3 pt-3 border-t border-gray-50 text-[10px] font-bold text-gray-400 uppercase tracking-widest flex items-center justify-between">
                    <span>Category: Payments</span>
                    <span>Order: 1</span>
                </div>
            </div>

        </div>
    </div>

</div>

@endsection
