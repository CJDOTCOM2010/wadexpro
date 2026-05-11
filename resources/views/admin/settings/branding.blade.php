@extends('admin.layout')
@section('title', 'Mobile App Branding')
@section('content')

<div class="p-8 lg:p-12 max-w-6xl mx-auto">
    <!-- Header/Breadcrumb -->
    <div class="flex items-center justify-between mb-12">
        <div>
            <div class="flex items-center gap-2 text-[10px] font-black text-accent uppercase tracking-[0.2em] mb-2">
                <a href="{{ route('orchestrator.settings') }}" class="hover:text-brand transition-colors">Settings Hub</a>
                <span class="text-gray-300">/</span>
                <span>Mobile App Branding</span>
            </div>
            <h2 class="text-3xl font-black text-brand tracking-tight">Mobile App Branding</h2>
            <p class="text-sm text-brand-muted font-medium mt-1">Control brand names, logos, colors, and taglines across all mobile apps — no code changes required.</p>
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
        
        <!-- Section 1: Brand Identity -->
        <div class="bg-white rounded-xl border border-gray-50 shadow-2xl overflow-hidden mb-8">
            <div class="p-10">
                <div class="flex items-center justify-between mb-10 pb-6 border-b border-gray-50">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-accent/10 text-accent rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/></svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-black text-brand tracking-tight">Brand Identity</h3>
                            <p class="text-xs text-brand-muted font-medium">These values replace every hardcoded brand reference in the Customer and Driver apps.</p>
                        </div>
                    </div>
                    <button type="submit" class="px-8 py-4 bg-brand text-white font-black text-xs rounded-lg hover:bg-brand-hover shadow-lg hover:shadow-brand/20 transition-all">Push Changes</button>
                </div>

                <div class="space-y-10">
                    <!-- Brand Names -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="group">
                            <label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-3 group-focus-within:text-brand transition-colors">Brand Name (Full)</label>
                            <input type="text" name="settings[brand_name]" value="{{ \App\Modules\Admin\Models\SystemSetting::get('brand_name', 'WADEXPRO') }}" class="w-full bg-surface border-2 border-transparent focus:border-accent focus:bg-white rounded-lg py-4 px-6 text-[15px] font-bold outline-none transition-all shadow-sm focus:shadow-xl" placeholder="e.g. WADEXPRO">
                            <p class="text-[10px] text-brand-muted mt-2">Used on splash screens, headers, and app title bar.</p>
                        </div>
                        <div class="group">
                            <label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-3 group-focus-within:text-brand transition-colors">Brand Short Name</label>
                            <input type="text" name="settings[brand_short_name]" value="{{ \App\Modules\Admin\Models\SystemSetting::get('brand_short_name', 'WADEX') }}" class="w-full bg-surface border-2 border-transparent focus:border-accent focus:bg-white rounded-lg py-4 px-6 text-[15px] font-bold outline-none transition-all shadow-sm focus:shadow-xl" placeholder="e.g. WADEX">
                            <p class="text-[10px] text-brand-muted mt-2">Used in compact UI elements and inline text references.</p>
                        </div>
                    </div>

                    <!-- Per-App Display Names -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="group">
                            <label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-3 group-focus-within:text-brand transition-colors">Customer App Display Name</label>
                            <input type="text" name="settings[customer_app_display_name]" value="{{ \App\Modules\Admin\Models\SystemSetting::get('customer_app_display_name', 'WADEXPRO') }}" class="w-full bg-surface border-2 border-transparent focus:border-accent focus:bg-white rounded-lg py-4 px-6 text-[15px] font-bold outline-none transition-all shadow-sm focus:shadow-xl" placeholder="e.g. WADEXPRO">
                            <p class="text-[10px] text-brand-muted mt-2">The app title shown in the Customer mobile app's title bar.</p>
                        </div>
                        <div class="group">
                            <label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-3 group-focus-within:text-brand transition-colors">Driver App Display Name</label>
                            <input type="text" name="settings[driver_app_display_name]" value="{{ \App\Modules\Admin\Models\SystemSetting::get('driver_app_display_name', 'WADEXPRO Driver') }}" class="w-full bg-surface border-2 border-transparent focus:border-accent focus:bg-white rounded-lg py-4 px-6 text-[15px] font-bold outline-none transition-all shadow-sm focus:shadow-xl" placeholder="e.g. WADEXPRO Driver">
                            <p class="text-[10px] text-brand-muted mt-2">The app title shown in the Driver mobile app's title bar.</p>
                        </div>
                    </div>

                    <!-- Tagline -->
                    <div class="group">
                        <label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-3 group-focus-within:text-brand transition-colors">Brand Tagline</label>
                        <input type="text" name="settings[brand_tagline]" value="{{ \App\Modules\Admin\Models\SystemSetting::get('brand_tagline', 'Move. Deliver. Thrive.') }}" class="w-full bg-surface border-2 border-transparent focus:border-accent focus:bg-white rounded-lg py-4 px-6 text-[15px] font-bold outline-none transition-all shadow-sm focus:shadow-xl" placeholder="e.g. Move. Deliver. Thrive.">
                        <p class="text-[10px] text-brand-muted mt-2">Shown on splash screens and onboarding flows across all mobile apps.</p>
                    </div>

                    <!-- Support Info -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="group">
                            <label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-3 group-focus-within:text-brand transition-colors">Support Email</label>
                            <input type="email" name="settings[support_email]" value="{{ \App\Modules\Admin\Models\SystemSetting::get('support_email', 'ops@wadexpro.com') }}" class="w-full bg-surface border-2 border-transparent focus:border-accent focus:bg-white rounded-lg py-4 px-6 text-[15px] font-bold outline-none transition-all shadow-sm focus:shadow-xl" placeholder="e.g. ops@wadexpro.com">
                        </div>
                        <div class="group">
                            <label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-3 group-focus-within:text-brand transition-colors">Support Phone</label>
                            <input type="text" name="settings[brand_support_phone]" value="{{ \App\Modules\Admin\Models\SystemSetting::get('brand_support_phone', '') }}" class="w-full bg-surface border-2 border-transparent focus:border-accent focus:bg-white rounded-lg py-4 px-6 text-[15px] font-bold outline-none transition-all shadow-sm focus:shadow-xl" placeholder="e.g. +233 XX XXX XXXX">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 2: Brand Logo -->
        <div class="bg-white rounded-xl border border-gray-50 shadow-2xl overflow-hidden mb-8">
            <div class="p-10">
                <div class="flex items-center gap-4 mb-10 pb-6 border-b border-gray-50">
                    <div class="w-12 h-12 bg-accent/10 text-accent rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-black text-brand tracking-tight">Brand Logo</h3>
                        <p class="text-xs text-brand-muted font-medium">Upload a logo that will be used on splash screens across all mobile apps.</p>
                    </div>
                </div>

                <div class="flex items-start gap-10">
                    <!-- Logo Preview -->
                    <div class="shrink-0">
                        @php $currentLogo = \App\Modules\Admin\Models\SystemSetting::get('brand_logo_url', ''); @endphp
                        <div class="w-32 h-32 rounded-2xl bg-surface border-2 border-dashed border-gray-200 flex items-center justify-center overflow-hidden" id="logo-preview-container">
                            @if($currentLogo)
                                <img src="{{ $currentLogo }}" alt="Brand Logo" class="w-full h-full object-contain p-2" id="logo-preview">
                            @else
                                <div class="text-center" id="logo-placeholder">
                                    <svg class="w-10 h-10 text-gray-300 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    <p class="text-[9px] text-gray-400 font-bold mt-1">No Logo</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Upload Field -->
                    <div class="flex-1">
                        <label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-3">Upload Logo (PNG, JPG, SVG, WebP — max 2MB)</label>
                        <input type="file" name="brand_logo" accept="image/png,image/jpeg,image/svg+xml,image/webp" 
                               class="block w-full text-sm text-brand-muted file:mr-4 file:py-3 file:px-6 file:rounded-lg file:border-0 file:bg-accent file:text-brand file:text-xs file:font-black hover:file:bg-accent/80 file:transition-colors cursor-pointer"
                               onchange="previewLogo(this)">
                        <p class="text-[10px] text-brand-muted mt-3">Recommended size: 512×512px. Transparent background works best. This logo appears on the splash screen and onboarding flow of both Customer and Driver apps.</p>
                        
                        @if($currentLogo)
                        <div class="mt-4 flex items-center gap-2">
                            <span class="text-[10px] font-bold text-green-600 bg-green-50 px-3 py-1 rounded-full">● Active Logo</span>
                            <span class="text-[10px] text-brand-muted truncate max-w-xs">{{ $currentLogo }}</span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 3: Brand Colors -->
        <div class="bg-white rounded-xl border border-gray-50 shadow-2xl overflow-hidden mb-8">
            <div class="p-10">
                <div class="flex items-center gap-4 mb-10 pb-6 border-b border-gray-50">
                    <div class="w-12 h-12 bg-accent/10 text-accent rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/></svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-black text-brand tracking-tight">Brand Colors</h3>
                        <p class="text-xs text-brand-muted font-medium">Define the color palette used across all mobile app interfaces.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                    @php
                        $colors = [
                            ['key' => 'brand_primary_color', 'label' => 'Primary Color', 'default' => '#156400', 'desc' => 'Main brand color (buttons, headers)'],
                            ['key' => 'brand_accent_color', 'label' => 'Accent / Gold', 'default' => '#FFCC00', 'desc' => 'Highlight and accent elements'],
                            ['key' => 'brand_secondary_color', 'label' => 'Secondary Color', 'default' => '#0D4000', 'desc' => 'Secondary UI backgrounds'],
                            ['key' => 'brand_dark_color', 'label' => 'Dark Background', 'default' => '#0A0A0A', 'desc' => 'Dark mode and splash backgrounds'],
                        ];
                    @endphp

                    @foreach($colors as $color)
                    <div class="group">
                        <label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-3">{{ $color['label'] }}</label>
                        @php $currentColor = \App\Modules\Admin\Models\SystemSetting::get($color['key'], $color['default']); @endphp
                        <div class="flex items-center gap-3 bg-surface rounded-lg p-3 border-2 border-transparent focus-within:border-accent transition-all">
                            <input type="color" name="settings[{{ $color['key'] }}]" value="{{ $currentColor }}" class="w-14 h-14 rounded-lg border-none cursor-pointer p-0" style="background: transparent;">
                            <div>
                                <input type="text" value="{{ $currentColor }}" class="w-24 bg-transparent text-sm font-black text-brand outline-none uppercase" oninput="this.previousElementSibling?.parentElement?.querySelector('input[type=color]').value = this.value" readonly>
                                <p class="text-[9px] text-brand-muted font-medium">{{ $color['desc'] }}</p>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- Live Preview Swatch -->
                <div class="mt-10 p-6 rounded-xl border border-gray-100 bg-surface">
                    <p class="text-[10px] font-black text-brand-muted uppercase tracking-widest mb-4">Live Preview</p>
                    <div class="flex items-center gap-4">
                        @foreach($colors as $color)
                            @php $c = \App\Modules\Admin\Models\SystemSetting::get($color['key'], $color['default']); @endphp
                            <div class="flex flex-col items-center gap-2">
                                <div class="w-16 h-16 rounded-xl shadow-lg border border-gray-100" style="background-color: {{ $c }};"></div>
                                <span class="text-[9px] font-bold text-brand-muted">{{ $color['label'] }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 4: Enterprise Name (legacy) -->
        <div class="bg-white rounded-xl border border-gray-50 shadow-2xl overflow-hidden mb-8">
            <div class="p-10">
                <div class="flex items-center gap-4 mb-10 pb-6 border-b border-gray-50">
                    <div class="w-12 h-12 bg-accent/10 text-accent rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-black text-brand tracking-tight">Enterprise Identity</h3>
                        <p class="text-xs text-brand-muted font-medium">Controls the corporate entity name used in notifications, receipts, and admin reports.</p>
                    </div>
                </div>
                <div class="group max-w-xl">
                    <label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-3">Enterprise Name</label>
                    <input type="text" name="settings[enterprise_name]" value="{{ \App\Modules\Admin\Models\SystemSetting::get('enterprise_name', 'WADEXPRO Logistics Hub') }}" class="w-full bg-surface border-2 border-transparent focus:border-accent focus:bg-white rounded-lg py-4 px-6 text-[15px] font-bold outline-none transition-all shadow-sm focus:shadow-xl">
                </div>
            </div>
        </div>

        <!-- Info Banner -->
        <div class="p-8 bg-brand rounded-xl text-white overflow-hidden relative group mb-8">
            <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-white/10 rounded-full blur-3xl group-hover:scale-150 transition-transform duration-1000"></div>
            <div class="relative z-10">
                <h4 class="text-sm font-black mb-3 flex items-center gap-2">
                    <svg class="w-5 h-5 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    How Dynamic Branding Works
                </h4>
                <ul class="text-xs text-white/70 leading-relaxed font-medium space-y-2">
                    <li>• Changes take effect the next time a mobile app starts (within seconds).</li>
                    <li>• Brand Name, Short Name, and Tagline replace all hardcoded text in both the Customer and Driver apps.</li>
                    <li>• The Brand Logo appears on the splash screen and onboarding flow.</li>
                    <li>• Color changes update the primary UI palette across both apps.</li>
                    <li>• No code changes or app redeployment required.</li>
                </ul>
            </div>
        </div>

        <!-- Bottom Submit -->
        <div class="flex justify-end">
            <button type="submit" class="px-10 py-4 bg-brand text-white font-black text-xs rounded-lg hover:bg-brand-hover shadow-lg hover:shadow-brand/20 transition-all flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                Deploy Branding Changes
            </button>
        </div>
    </form>
</div>

<script>
    function previewLogo(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const container = document.getElementById('logo-preview-container');
                container.innerHTML = '<img src="' + e.target.result + '" alt="Logo Preview" class="w-full h-full object-contain p-2" id="logo-preview">';
            };
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>

@endsection
