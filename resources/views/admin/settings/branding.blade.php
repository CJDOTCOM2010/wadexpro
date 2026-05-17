@extends('admin.layout')
@section('title', 'Branding Configuration')
@php
function getSetting($key, $default = null) {
    global $settings;
    return $settings[$key]->value ?? $default;
}
@endphp
@section('content')

<style>
.toggle-switch { --toggle-width: 52px; --toggle-height: 28px; }
.toggle-switch input:checked + .toggle-slider { background-color: #1c2c44; }
.toggle-switch .toggle-slider::before { transform: translateX(calc(var(--toggle-width) - var(--toggle-height) - 2px)); }
.toggle-switch input:checked + .toggle-slider::before { transform: translateX(calc(var(--toggle-width) - var(--toggle-height) - 2px)); }
.custom-select { appearance: none; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%236b7280' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right 12px center; padding-right: 40px; }
.custom-select:focus { box-shadow: 0 0 0 3px rgba(28, 44, 68, 0.1); }
</style>

<div class="p-6 lg:p-8 max-w-6xl mx-auto">
    <!-- Header -->
    <div class="flex items-center justify-between mb-8">
        <div>
            <div class="flex items-center gap-2 text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-2">
                <a href="{{ route('orchestrator.settings') }}" class="hover:text-brand">Settings Hub</a>
                <span>/</span>
                <span class="text-brand">Branding</span>
            </div>
            <h2 class="text-2xl font-bold text-gray-900">Branding Configuration</h2>
            <p class="text-sm text-gray-500 mt-1">Configure brand identity, logos, and colors for mobile and web apps</p>
        </div>
    </div>

    @if(session('success'))
    <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-600 rounded-lg flex items-center gap-3">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        <span class="text-sm font-medium">{{ session('success') }}</span>
    </div>
    @endif

    <form action="{{ route('orchestrator.settings.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf

        <!-- Brand Identity -->
        <div class="bg-white rounded-xl border border-gray-200">
            <div class="px-5 py-4 border-b border-gray-100 bg-gray-50/50">
                <h3 class="text-base font-semibold text-gray-900">Brand Identity</h3>
                <p class="text-xs text-gray-500">Core brand names used across all platforms</p>
            </div>
            <div class="p-5 space-y-5">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-2">Brand Name (Full)</label>
                        <input type="text" name="settings[brand_name]" value="{{ getSetting('brand_name', 'WADEXPRO') }}" class="w-full bg-gray-50 border border-gray-200 rounded-lg py-2.5 px-3 text-sm text-gray-700 focus:border-brand focus:bg-white transition-all" placeholder="e.g. WADEXPRO">
                        <p class="text-[10px] text-gray-400 mt-1.5">Splash screens and headers</p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-2">Brand Short Name</label>
                        <input type="text" name="settings[brand_short_name]" value="{{ getSetting('brand_short_name', 'WADEX') }}" class="w-full bg-gray-50 border border-gray-200 rounded-lg py-2.5 px-3 text-sm text-gray-700 focus:border-brand focus:bg-white transition-all" placeholder="e.g. WADEX">
                        <p class="text-[10px] text-gray-400 mt-1.5">Compact UI elements</p>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-2">Customer App Name</label>
                        <input type="text" name="settings[customer_app_display_name]" value="{{ getSetting('customer_app_display_name', 'WADEXPRO') }}" class="w-full bg-gray-50 border border-gray-200 rounded-lg py-2.5 px-3 text-sm text-gray-700 focus:border-brand focus:bg-white transition-all">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-2">Driver App Name</label>
                        <input type="text" name="settings[driver_app_display_name]" value="{{ getSetting('driver_app_display_name', 'WADEXPRO Driver') }}" class="w-full bg-gray-50 border border-gray-200 rounded-lg py-2.5 px-3 text-sm text-gray-700 focus:border-brand focus:bg-white transition-all">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-2">Brand Tagline</label>
                    <input type="text" name="settings[brand_tagline]" value="{{ getSetting('brand_tagline', 'Move. Deliver. Thrive.') }}" class="w-full bg-gray-50 border border-gray-200 rounded-lg py-2.5 px-3 text-sm text-gray-700 focus:border-brand focus:bg-white transition-all" placeholder="e.g. Move. Deliver. Thrive.">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-2">Support Email</label>
                        <input type="email" name="settings[support_email]" value="{{ getSetting('support_email', 'ops@wadexpro.com') }}" class="w-full bg-gray-50 border border-gray-200 rounded-lg py-2.5 px-3 text-sm text-gray-700 focus:border-brand focus:bg-white transition-all">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-2">Support Phone</label>
                        <input type="text" name="settings[brand_support_phone]" value="{{ getSetting('brand_support_phone', '') }}" class="w-full bg-gray-50 border border-gray-200 rounded-lg py-2.5 px-3 text-sm text-gray-700 focus:border-brand focus:bg-white transition-all" placeholder="+233 XX XXX XXXX">
                    </div>
                </div>
            </div>
        </div>

        <!-- Brand Logo -->
        <div class="bg-white rounded-xl border border-gray-200">
            <div class="px-5 py-4 border-b border-gray-100 bg-gray-50/50">
                <h3 class="text-base font-semibold text-gray-900">Brand Logo</h3>
                <p class="text-xs text-gray-500">Mobile app splash screen logo</p>
            </div>
            <div class="p-5">
                <div class="flex items-start gap-6">
                    <div class="shrink-0">
                        @php $currentLogo = getSetting('brand_logo_url', ''); @endphp
                        <div class="w-24 h-24 rounded-xl bg-gray-100 border-2 border-dashed border-gray-300 flex items-center justify-center overflow-hidden" id="logo-preview-container">
                            @if($currentLogo)
                                <img src="{{ $currentLogo }}" alt="Brand Logo" class="w-full h-full object-contain p-2" id="logo-preview">
                            @else
                                <div class="text-center" id="logo-placeholder">
                                    <svg class="w-8 h-8 text-gray-300 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    <p class="text-[9px] text-gray-400 mt-1">No Logo</p>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="flex-1">
                        <label class="block text-xs font-medium text-gray-700 mb-2">Upload Logo (PNG, JPG, SVG, WebP)</label>
                        <input type="file" name="brand_logo" accept="image/png,image/jpeg,image/svg+xml,image/webp" class="block w-full text-xs text-gray-500 file:mr-3 file:py-2 file:px-3 file:rounded file:border-0 file:bg-brand file:text-white file:text-xs file:font-medium hover:file:bg-brand-light cursor-pointer" onchange="previewLogo(this)">
                        <p class="text-[10px] text-gray-400 mt-2">Recommended: 512×512px, transparent background. Used on splash screens.</p>
                        @if($currentLogo)
                        <div class="mt-3 flex items-center gap-2">
                            <span class="text-[10px] font-medium text-green-600 bg-green-50 px-2 py-1 rounded">Active</span>
                            <span class="text-[10px] text-gray-400 truncate max-w-xs">{{ $currentLogo }}</span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Brand Colors -->
        <div class="bg-white rounded-xl border border-gray-200">
            <div class="px-5 py-4 border-b border-gray-100 bg-gray-50/50">
                <h3 class="text-base font-semibold text-gray-900">Brand Colors</h3>
                <p class="text-xs text-gray-500">Color palette for mobile apps</p>
            </div>
            <div class="p-5">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
                    @php
                    $colors = [
                        ['key' => 'brand_primary_color', 'label' => 'Primary Color', 'default' => '#156400'],
                        ['key' => 'brand_accent_color', 'label' => 'Accent / Gold', 'default' => '#FFCC00'],
                        ['key' => 'brand_secondary_color', 'label' => 'Secondary Color', 'default' => '#0D4000'],
                        ['key' => 'brand_dark_color', 'label' => 'Dark Background', 'default' => '#0A0A0A'],
                    ];
                    @endphp
                    @foreach($colors as $color)
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-2">{{ $color['label'] }}</label>
                        <div class="flex items-center gap-3 bg-gray-50 rounded-lg p-2 border border-gray-200">
                            <input type="color" name="settings[{{ $color['key'] }}]" value="{{ getSetting($color['key'], $color['default']) }}" class="w-10 h-10 rounded cursor-pointer border-0" style="background:transparent;">
                            <input type="text" value="{{ getSetting($color['key'], $color['default']) }}" class="w-20 bg-transparent text-xs font-mono text-gray-600 uppercase outline-none" readonly>
                        </div>
                    </div>
                    @endforeach
                </div>
                <!-- Color Preview -->
                <div class="mt-6 p-4 rounded-lg bg-gray-50 border border-gray-200">
                    <p class="text-[10px] font-medium text-gray-500 mb-3">Live Preview</p>
                    <div class="flex items-center gap-3">
                        @foreach($colors as $color)
                        <div class="flex flex-col items-center gap-1.5">
                            <div class="w-12 h-12 rounded-lg border border-gray-200 shadow-sm" style="background-color: {{ getSetting($color['key'], $color['default']) }};"></div>
                            <span class="text-[9px] text-gray-400">{{ explode(' ', $color['label'])[0] }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Enterprise Identity -->
        <div class="bg-white rounded-xl border border-gray-200">
            <div class="px-5 py-4 border-b border-gray-100 bg-gray-50/50">
                <h3 class="text-base font-semibold text-gray-900">Enterprise Identity</h3>
                <p class="text-xs text-gray-500">Corporate entity for notifications and reports</p>
            </div>
            <div class="p-5">
                <div class="max-w-md">
                    <label class="block text-xs font-medium text-gray-700 mb-2">Enterprise Name</label>
                    <input type="text" name="settings[enterprise_name]" value="{{ getSetting('enterprise_name', 'WADEXPRO Logistics Hub') }}" class="w-full bg-gray-50 border border-gray-200 rounded-lg py-2.5 px-3 text-sm text-gray-700 focus:border-brand focus:bg-white transition-all">
                </div>
            </div>
        </div>

        <!-- Info Box -->
        <div class="p-5 bg-brand rounded-xl text-white">
            <h4 class="text-sm font-semibold mb-2 flex items-center gap-2">
                <svg class="w-4 h-4 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                How it works
            </h4>
            <ul class="text-xs text-white/70 space-y-1">
                <li>• Changes apply to mobile apps on next startup (within seconds)</li>
                <li>• Brand Name replaces all hardcoded text in Customer and Driver apps</li>
                <li>• Colors update the primary UI palette across both apps</li>
                <li>• No code changes or app redeployment required</li>
            </ul>
        </div>

        <!-- Submit -->
        <div class="flex justify-end pt-2">
            <button type="submit" class="bg-brand text-white px-6 py-2.5 rounded-lg text-sm font-medium shadow-sm hover:bg-brand-light hover:shadow-md transition-all flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                Save Configuration
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