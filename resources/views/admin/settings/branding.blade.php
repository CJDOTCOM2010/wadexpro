@extends('admin.layout')
@section('title', 'General Branding')
@section('content')

<div class="p-8 lg:p-12 max-w-5xl mx-auto">
    <!-- Header/Breadcrumb -->
    <div class="flex items-center justify-between mb-12">
        <div>
            <div class="flex items-center gap-2 text-[10px] font-black text-accent uppercase tracking-[0.2em] mb-2">
                <a href="{{ route('orchestrator.settings') }}" class="hover:text-brand transition-colors">Settings Hub</a>
                <span class="text-gray-300">/</span>
                <span>Branding</span>
            </div>
            <h2 class="text-3xl font-black text-brand tracking-tight">Enterprise Branding</h2>
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

    <div class="bg-white rounded-lg border border-gray-50 shadow-2xl overflow-hidden">
        <form action="{{ route('orchestrator.settings.update') }}" method="POST" class="p-10">
            @csrf
            
            <div class="flex items-center justify-between mb-10 pb-6 border-b border-gray-50">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-accent/10 text-accent rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-black text-brand tracking-tight">Identity Parameters</h3>
                        <p class="text-xs text-brand-muted font-medium">Fine-tune the platform's visual signature.</p>
                    </div>
                </div>
                <button type="submit" class="px-8 py-4 bg-brand text-white font-black text-xs rounded-lg hover:bg-brand-hover shadow-lg hover:shadow-brand/20 transition-all">Push Changes</button>
            </div>

            <div class="space-y-10">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                    <div class="group">
                        <label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-4 group-focus-within:text-brand transition-colors">Enterprise Name</label>
                        <div class="relative">
                            <input type="text" name="settings[enterprise_name]" value="{{ \App\Modules\Admin\Models\SystemSetting::get('enterprise_name', 'WADEXPRO Logistics Hub') }}" class="w-full bg-surface border-2 border-transparent focus:border-accent focus:bg-white rounded-lg py-5 px-8 text-[15px] font-bold outline-none transition-all shadow-sm focus:shadow-xl">
                            <div class="absolute right-6 top-1/2 -translate-y-1/2 text-gray-300">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                            </div>
                        </div>
                    </div>
                    <div class="group">
                        <label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-4 group-focus-within:text-brand transition-colors">Support E-mail Clearance</label>
                        <div class="relative">
                            <input type="email" name="settings[support_email]" value="{{ \App\Modules\Admin\Models\SystemSetting::get('support_email', 'ops@wadexpro.com') }}" class="w-full bg-surface border-2 border-transparent focus:border-accent focus:bg-white rounded-lg py-5 px-8 text-[15px] font-bold outline-none transition-all shadow-sm focus:shadow-xl">
                            <div class="absolute right-6 top-1/2 -translate-y-1/2 text-gray-300">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="p-8 bg-brand rounded-lg text-white overflow-hidden relative group">
                    <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-white/10 rounded-full blur-3xl group-hover:scale-150 transition-transform duration-1000"></div>
                    <div class="relative z-10">
                        <h4 class="text-sm font-black mb-2 italic">Pro Tip: Platform Consistency</h4>
                        <p class="text-xs text-white/70 leading-relaxed font-medium">Changes to the Enterprise Name will reflect globally across all customer notifications, driver receipts, and administrative reports.</p>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection
