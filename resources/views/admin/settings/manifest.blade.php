@extends('admin.layout')
@section('title', 'Mobile Manifest')
@section('content')

<div class="p-8 lg:p-12 max-w-6xl mx-auto">
    <!-- Header/Breadcrumb -->
    <div class="flex items-center justify-between mb-12">
        <div>
            <div class="flex items-center gap-2 text-[10px] font-black text-accent uppercase tracking-[0.2em] mb-2">
                <a href="{{ route('orchestrator.settings') }}" class="hover:text-brand transition-colors">Settings Hub</a>
                <span class="text-gray-300">/</span>
                <span>Mobile Manifest</span>
            </div>
            <h2 class="text-3xl font-black text-brand tracking-tight">Fleet Integrity & Manifest</h2>
        </div>
        <a href="{{ route('orchestrator.settings') }}" class="bg-surface text-brand-muted hover:bg-gray-100 px-6 py-3 rounded-lg text-xs font-bold transition-all flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Back to Hub
        </a>
    </div>

    @if(session('success'))
    <div class="mb-8 p-4 bg-green-50 border border-green-100 text-green-600 rounded-lg flex items-center gap-3 animate-fade-in">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        <span class="text-xs font-bold">{{ session('success') }}</span>
    </div>
    @endif

    <form action="{{ route('orchestrator.settings.update') }}" method="POST">
        @csrf
        
        <div class="mb-10 flex items-center justify-between bg-white p-6 rounded-lg border border-gray-50 shadow-sm">
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 bg-brand/5 text-brand rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                </div>
                <p class="text-xs font-bold text-brand-muted">Global broadcast of version requirements.</p>
            </div>
            <button type="submit" class="px-8 py-4 bg-accent text-brand font-black text-xs rounded-lg hover:bg-accent-hover transition-all shadow-lg shadow-accent/10">Broadcast Global Update</button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
            <!-- Customer Manifest -->
            <div class="bg-white rounded-lg border border-gray-50 shadow-2xl p-10 relative overflow-hidden group">
                <div class="absolute top-0 right-0 w-32 h-32 bg-brand/5 rounded-full -mr-16 -mt-16 group-hover:scale-110 transition-transform duration-700"></div>
                
                <div class="relative z-10">
                    <div class="flex items-center gap-4 mb-10">
                        <div class="w-14 h-14 bg-surface rounded-lg flex items-center justify-center text-brand">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-black text-brand tracking-tight">Customer Application</h3>
                            <p class="text-[10px] text-brand-muted font-black uppercase tracking-widest mt-1">Manifest Node 01</p>
                        </div>
                    </div>

                    <div class="space-y-8">
                        <div>
                            <label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-3">Minimum Required Build</label>
                            <div class="relative">
                                <input type="text" name="settings[min_customer_app_version]" value="{{ \App\Modules\Admin\Models\SystemSetting::get('min_customer_app_version', '1.0.0') }}" class="w-full bg-surface border-none rounded-lg py-4 px-6 text-[15px] font-black outline-none focus:ring-2 focus:ring-accent/20 transition-all">
                                <span class="absolute right-6 top-1/2 -translate-y-1/2 text-[10px] font-black text-accent uppercase italic">Enforced</span>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <div class="group">
                                <label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-2">Play Store Redirection</label>
                                <input type="text" name="settings[play_store_customer_link]" value="{{ \App\Modules\Admin\Models\SystemSetting::get('play_store_customer_link') }}" placeholder="https://play.google.com/..." class="w-full bg-surface border-none rounded-lg py-3 px-6 text-xs font-bold outline-none border-b-2 border-transparent focus:border-brand transition-all">
                            </div>
                            <div class="group">
                                <label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-2">App Store Redirection</label>
                                <input type="text" name="settings[app_store_customer_link]" value="{{ \App\Modules\Admin\Models\SystemSetting::get('app_store_customer_link') }}" placeholder="https://apps.apple.com/..." class="w-full bg-surface border-none rounded-lg py-3 px-6 text-xs font-bold outline-none border-b-2 border-transparent focus:border-brand transition-all">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Driver Manifest -->
            <div class="bg-white rounded-lg border border-gray-50 shadow-2xl p-10 relative overflow-hidden group">
                <div class="absolute top-0 right-0 w-32 h-32 bg-accent/10 rounded-full -mr-16 -mt-16 group-hover:scale-110 transition-transform duration-700"></div>
                
                <div class="relative z-10">
                    <div class="flex items-center gap-4 mb-10">
                        <div class="w-14 h-14 bg-surface rounded-lg flex items-center justify-center text-brand">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/></svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-black text-brand tracking-tight">Driver & Courier Terminal</h3>
                            <p class="text-[10px] text-brand-muted font-black uppercase tracking-widest mt-1">Manifest Node 02</p>
                        </div>
                    </div>

                    <div class="space-y-8">
                        <div>
                            <label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-3">Minimum Required Build</label>
                            <div class="relative">
                                <input type="text" name="settings[min_driver_app_version]" value="{{ \App\Modules\Admin\Models\SystemSetting::get('min_driver_app_version', '1.0.0') }}" class="w-full bg-surface border-none rounded-lg py-4 px-6 text-[15px] font-black outline-none focus:ring-2 focus:ring-accent/20 transition-all">
                                <span class="absolute right-6 top-1/2 -translate-y-1/2 text-[10px] font-black text-brand uppercase italic">Critical</span>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <div class="group">
                                <label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-2">Android Store Target</label>
                                <input type="text" name="settings[play_store_driver_link]" value="{{ \App\Modules\Admin\Models\SystemSetting::get('play_store_driver_link') }}" placeholder="https://play.google.com/..." class="w-full bg-surface border-none rounded-lg py-3 px-6 text-xs font-bold outline-none border-b-2 border-transparent focus:border-brand transition-all">
                            </div>
                            <div class="group">
                                <label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-2">iOS Store Target</label>
                                <input type="text" name="settings[app_store_driver_link]" value="{{ \App\Modules\Admin\Models\SystemSetting::get('app_store_driver_link') }}" placeholder="https://apps.apple.com/..." class="w-full bg-surface border-none rounded-lg py-3 px-6 text-xs font-bold outline-none border-b-2 border-transparent focus:border-brand transition-all">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@endsection
