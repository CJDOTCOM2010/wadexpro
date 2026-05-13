@extends('admin.layout')
@section('title', 'Dashboard Branding')
@section('content')

<div class="p-8 lg:p-12 max-w-6xl mx-auto">
    <!-- Header/Breadcrumb -->
    <div class="flex items-center justify-between mb-12">
        <div>
            <div class="flex items-center gap-2 text-[10px] font-black text-accent uppercase tracking-[0.2em] mb-2">
                <a href="{{ route('orchestrator.settings') }}" class="hover:text-brand transition-colors">Settings Hub</a>
                <span class="text-gray-300">/</span>
                <span>Dashboard Branding</span>
            </div>
            <h2 class="text-3xl font-black text-brand tracking-tight">Dashboard Branding</h2>
            <p class="text-sm text-brand-muted font-medium mt-1">Customize the Super Admin interface, logos, and metadata.</p>
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

    <form action="{{ route('orchestrator.settings.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <!-- Section 1: Dashboard Identity -->
        <div class="bg-white rounded-xl border border-gray-50 shadow-2xl overflow-hidden mb-8">
            <div class="p-10">
                <div class="flex items-center justify-between mb-10 pb-6 border-b border-gray-50">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-accent/10 text-accent rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-black text-brand tracking-tight">Dashboard Identity</h3>
                            <p class="text-xs text-brand-muted font-medium">Control the name and labels used across this administrative portal.</p>
                        </div>
                    </div>
                    <button type="submit" class="px-8 py-4 bg-brand text-white font-black text-xs rounded-lg hover:bg-brand-hover shadow-lg hover:shadow-brand/20 transition-all">Update Dashboard</button>
                </div>

                <div class="space-y-10">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="group">
                            <label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-3 group-focus-within:text-brand transition-colors">Application Name</label>
                            <input type="text" name="settings[dashboard_app_name]" value="{{ \App\Modules\Admin\Models\SystemSetting::get('dashboard_app_name', 'WADEXPRO') }}" class="w-full bg-surface border-2 border-transparent focus:border-accent focus:bg-white rounded-lg py-4 px-6 text-[15px] font-bold outline-none transition-all shadow-sm focus:shadow-xl" placeholder="e.g. WADEXPRO">
                            <p class="text-[10px] text-brand-muted mt-2">The main title shown in the sidebar and browser tab.</p>
                        </div>
                        <div class="group">
                            <label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-3 group-focus-within:text-brand transition-colors">Dashboard Tagline</label>
                            <input type="text" name="settings[dashboard_tagline]" value="{{ \App\Modules\Admin\Models\SystemSetting::get('dashboard_tagline', 'Orchestrator') }}" class="w-full bg-surface border-2 border-transparent focus:border-accent focus:bg-white rounded-lg py-4 px-6 text-[15px] font-bold outline-none transition-all shadow-sm focus:shadow-xl" placeholder="e.g. Orchestrator">
                            <p class="text-[10px] text-brand-muted mt-2">Small sub-label shown under the logo.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 2: Dashboard Assets -->
        <div class="bg-white rounded-xl border border-gray-50 shadow-2xl overflow-hidden mb-8">
            <div class="p-10">
                <div class="flex items-center gap-4 mb-10 pb-6 border-b border-gray-50">
                    <div class="w-12 h-12 bg-accent/10 text-accent rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-black text-brand tracking-tight">Dashboard Assets</h3>
                        <p class="text-xs text-brand-muted font-medium">Upload logos and icons specifically for the web dashboard.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
                    <!-- Dashboard Logo -->
                    <div class="space-y-6">
                        <label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-3">Dashboard Logo (Square recommended)</label>
                        <div class="flex items-center gap-6">
                            @php $dashLogo = \App\Modules\Admin\Models\SystemSetting::get('dashboard_logo_url', ''); @endphp
                            <div class="w-24 h-24 rounded-xl bg-brand flex items-center justify-center overflow-hidden shrink-0" id="dash-logo-preview-container">
                                @if($dashLogo)
                                    <img src="{{ $dashLogo }}" class="w-full h-full object-contain p-2">
                                @else
                                    <span class="text-accent font-black text-3xl">W</span>
                                @endif
                            </div>
                            <div class="flex-1">
                                <input type="file" name="dashboard_logo" accept="image/*" class="block w-full text-[10px] text-brand-muted file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:bg-accent file:text-brand file:font-black cursor-pointer" onchange="previewImg(this, 'dash-logo-preview-container')">
                                <p class="text-[9px] text-brand-muted mt-2">Replaces the "W" icon in the top left of the sidebar.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Favicon -->
                    <div class="space-y-6">
                        <label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-3">Browser Favicon</label>
                        <div class="flex items-center gap-6">
                            @php $favicon = \App\Modules\Admin\Models\SystemSetting::get('dashboard_favicon_url', ''); @endphp
                            <div class="w-12 h-12 rounded bg-surface border border-gray-100 flex items-center justify-center overflow-hidden shrink-0" id="favicon-preview-container">
                                @if($favicon)
                                    <img src="{{ $favicon }}" class="w-full h-full object-contain p-1">
                                @else
                                    <svg class="w-6 h-6 text-gray-300" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg>
                                @endif
                            </div>
                            <div class="flex-1">
                                <input type="file" name="dashboard_favicon" accept="image/*" class="block w-full text-[10px] text-brand-muted file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:bg-accent file:text-brand file:font-black cursor-pointer" onchange="previewImg(this, 'favicon-preview-container', true)">
                                <p class="text-[9px] text-brand-muted mt-2">The icon shown in the browser tab (ICO, PNG, or SVG).</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bottom Submit -->
        <div class="flex justify-end">
            <button type="submit" class="px-10 py-4 bg-brand text-white font-black text-xs rounded-lg hover:bg-brand-hover shadow-lg hover:shadow-brand/20 transition-all flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                Save Dashboard Branding
            </button>
        </div>
    </form>
</div>

<script>
    function previewImg(input, containerId, isSmall = false) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const container = document.getElementById(containerId);
                container.innerHTML = '<img src="' + e.target.result + '" class="w-full h-full object-contain ' + (isSmall ? 'p-1' : 'p-2') + '">';
            };
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>

@endsection
