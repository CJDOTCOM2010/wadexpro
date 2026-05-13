@extends('admin.layout')
@section('title', 'System Prefixes & Identifiers')
@section('content')

<div class="p-8 lg:p-12 max-w-[1200px] mx-auto">
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-12 gap-6">
        <div>
            <div class="flex items-center gap-2 text-[10px] font-black text-accent uppercase tracking-[0.2em] mb-2">
                <a href="{{ route('orchestrator.settings') }}" class="hover:text-brand transition-colors">Settings Hub</a>
                <span class="text-gray-300">/</span>
                <span>Prefixes</span>
            </div>
            <h2 class="text-3xl font-black text-brand tracking-tight">System ID Prefixes</h2>
            <p class="text-sm text-brand-muted font-medium mt-1">Configure global naming conventions for tickets, orders, and users.</p>
        </div>
    </div>

    <form action="{{ route('orchestrator.settings.update') }}" method="POST" class="space-y-8">
        @csrf
        <div class="bg-white rounded-3xl border border-gray-100 shadow-2xl overflow-hidden">
            <div class="p-8 border-b border-gray-50 bg-surface/30">
                <h3 class="text-lg font-black text-brand">Identifier Schemas</h3>
                <p class="text-xs text-brand-muted font-bold">These prefixes will be prepended to all auto-generated system IDs.</p>
            </div>
            
            <div class="p-8 grid grid-cols-1 md:grid-cols-2 gap-10">
                <!-- Ticket Prefix -->
                <div>
                    <label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-3">Support Ticket Prefix</label>
                    <div class="relative group">
                        <input type="text" name="settings[ticket_prefix]" value="{{ \App\Modules\Admin\Models\SystemSetting::get('ticket_prefix', 'TIC-') }}" class="w-full bg-surface border-2 border-transparent focus:border-brand rounded-2xl py-4 px-6 text-sm font-bold outline-none transition-all group-hover:bg-white group-hover:shadow-lg">
                        <div class="absolute right-4 top-1/2 -translate-y-1/2 text-[10px] font-black text-accent uppercase bg-white px-2 py-1 rounded shadow-sm opacity-50">Example: TIC-12345</div>
                    </div>
                </div>

                <!-- Order Prefix -->
                <div>
                    <label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-3">Order/Ride Prefix</label>
                    <div class="relative group">
                        <input type="text" name="settings[order_prefix]" value="{{ \App\Modules\Admin\Models\SystemSetting::get('order_prefix', 'WAD-') }}" class="w-full bg-surface border-2 border-transparent focus:border-brand rounded-2xl py-4 px-6 text-sm font-bold outline-none transition-all group-hover:bg-white group-hover:shadow-lg">
                        <div class="absolute right-4 top-1/2 -translate-y-1/2 text-[10px] font-black text-accent uppercase bg-white px-2 py-1 rounded shadow-sm opacity-50">Example: WAD-8821</div>
                    </div>
                </div>

                <!-- User Prefix -->
                <div>
                    <label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-3">Customer ID Prefix</label>
                    <div class="relative group">
                        <input type="text" name="settings[user_prefix]" value="{{ \App\Modules\Admin\Models\SystemSetting::get('user_prefix', 'USR-') }}" class="w-full bg-surface border-2 border-transparent focus:border-brand rounded-2xl py-4 px-6 text-sm font-bold outline-none transition-all group-hover:bg-white group-hover:shadow-lg">
                        <div class="absolute right-4 top-1/2 -translate-y-1/2 text-[10px] font-black text-accent uppercase bg-white px-2 py-1 rounded shadow-sm opacity-50">Example: USR-9902</div>
                    </div>
                </div>

                <!-- Driver Prefix -->
                <div>
                    <label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-3">Driver ID Prefix</label>
                    <div class="relative group">
                        <input type="text" name="settings[driver_prefix]" value="{{ \App\Modules\Admin\Models\SystemSetting::get('driver_prefix', 'DRV-') }}" class="w-full bg-surface border-2 border-transparent focus:border-brand rounded-2xl py-4 px-6 text-sm font-bold outline-none transition-all group-hover:bg-white group-hover:shadow-lg">
                        <div class="absolute right-4 top-1/2 -translate-y-1/2 text-[10px] font-black text-accent uppercase bg-white px-2 py-1 rounded shadow-sm opacity-50">Example: DRV-7711</div>
                    </div>
                </div>

                <!-- Wallet Prefix -->
                <div>
                    <label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-3">Transaction Prefix</label>
                    <div class="relative group">
                        <input type="text" name="settings[transaction_prefix]" value="{{ \App\Modules\Admin\Models\SystemSetting::get('transaction_prefix', 'TXN-') }}" class="w-full bg-surface border-2 border-transparent focus:border-brand rounded-2xl py-4 px-6 text-sm font-bold outline-none transition-all group-hover:bg-white group-hover:shadow-lg">
                        <div class="absolute right-4 top-1/2 -translate-y-1/2 text-[10px] font-black text-accent uppercase bg-white px-2 py-1 rounded shadow-sm opacity-50">Example: TXN-5541</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex justify-end pt-4">
            <button type="submit" class="bg-brand text-white px-12 py-5 rounded-2xl text-xs font-black shadow-xl hover:shadow-brand/20 hover:-translate-y-1 transition-all uppercase tracking-widest">
                Save Prefix Configuration
            </button>
        </div>
    </form>
</div>

@endsection
