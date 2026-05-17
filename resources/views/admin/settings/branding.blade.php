@extends('admin.layout')
@section('title', 'Branding Configuration')
@php
$settings = $settings ?? collect();
$brandLogoUrl = isset($settings['brand_logo_url']) ? ($settings['brand_logo_url']->value ?? '') : '';
$appIconUrl = isset($settings['app_icon_url']) ? ($settings['app_icon_url']->value ?? '') : '';
@endphp
@section('content')

<style>
.toggle-switch { --toggle-width: 52px; --toggle-height: 28px; }
.toggle-switch input:checked + .toggle-slider { background-color: #1c2c44; }
.toggle-switch .toggle-slider::before { transform: translateX(calc(var(--toggle-width) - var(--toggle-height) - 2px)); }
.toggle-switch input:checked + .toggle-slider::before { transform: translateX(calc(var(--toggle-width) - var(--toggle-height) - 2px)); }
.custom-select { appearance: none; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%236b7280' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right 12px center; padding-right: 40px; }
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
            <p class="text-sm text-gray-500 mt-1">Configure brand identity and assets for mobile and web apps</p>
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

        <!-- App Names & Identity -->
        <div class="bg-white rounded-xl border border-gray-200">
            <div class="px-5 py-4 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
                <div>
                    <h3 class="text-base font-semibold text-gray-900">App Names & Identity</h3>
                    <p class="text-xs text-gray-500">Names shown on devices and app stores</p>
                </div>
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
                        <p class="text-[10px] text-gray-400 mt-1.5">App icon label</p>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-2">Customer App Name</label>
                        <input type="text" name="settings[customer_app_display_name]" value="{{ getSetting('customer_app_display_name', 'WADEXPRO') }}" class="w-full bg-gray-50 border border-gray-200 rounded-lg py-2.5 px-3 text-sm text-gray-700 focus:border-brand focus:bg-white transition-all">
                        <p class="text-[10px] text-gray-400 mt-1.5">App title on device</p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-2">Driver App Name</label>
                        <input type="text" name="settings[driver_app_display_name]" value="{{ getSetting('driver_app_display_name', 'WADEXPRO Driver') }}" class="w-full bg-gray-50 border border-gray-200 rounded-lg py-2.5 px-3 text-sm text-gray-700 focus:border-brand focus:bg-white transition-all">
                        <p class="text-[10px] text-gray-400 mt-1.5">Driver app title on device</p>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-2">Brand Tagline</label>
                    <input type="text" name="settings[brand_tagline]" value="{{ getSetting('brand_tagline', 'Move. Deliver. Thrive.') }}" class="w-full bg-gray-50 border border-gray-200 rounded-lg py-2.5 px-3 text-sm text-gray-700 focus:border-brand focus:bg-white transition-all" placeholder="e.g. Move. Deliver. Thrive.">
                    <p class="text-[10px] text-gray-400 mt-1.5">On splash screen and onboarding</p>
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

        <!-- App Icons & Splash -->
        <div class="bg-white rounded-xl border border-gray-200">
            <div class="px-5 py-4 border-b border-gray-100 bg-gray-50/50">
                <h3 class="text-base font-semibold text-gray-900">App Icons & Splash Screen</h3>
                <p class="text-xs text-gray-500">Logos displayed on device home screen and splash</p>
            </div>
            <div class="p-5">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Brand Logo -->
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-3">Splash Screen Logo</label>
                        <div class="flex items-start gap-4">
                            <div class="w-20 h-20 rounded-xl bg-gray-100 border-2 {{ $brandLogoUrl ? 'border-green-300' : 'border-dashed border-gray-300' }} flex items-center justify-center overflow-hidden shrink-0" id="logo-preview-container">
                                @if($brandLogoUrl)
                                    <img src="{{ $brandLogoUrl }}" class="w-full h-full object-contain p-2">
                                @else
                                    <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                @endif
                            </div>
                            <div class="flex-1">
                                <input type="file" name="brand_logo" accept="image/png,image/jpeg,image/svg+xml,image/webp" class="block w-full text-xs text-gray-500 file:mr-3 file:py-2 file:px-3 file:rounded file:border-0 file:bg-brand file:text-white file:text-xs file:font-medium cursor-pointer" onchange="previewLogo(this)">
                                <p class="text-[10px] text-gray-400 mt-2">512×512px, transparent PNG recommended</p>
                                @if($brandLogoUrl)
                                <span class="text-[10px] text-green-600 mt-1 block flex items-center gap-1">
                                    <span class="w-1.5 h-1.5 bg-green-500 rounded-full"></span>
                                    Logo active
                                </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <!-- App Icon -->
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-3">App Icon (Launcher)</label>
                        <div class="flex items-start gap-4">
                            <div class="w-20 h-20 rounded-xl bg-gray-100 border-2 {{ $appIconUrl ? 'border-green-300' : 'border-dashed border-gray-300' }} flex items-center justify-center overflow-hidden shrink-0" id="app-icon-preview-container">
                                @if($appIconUrl)
                                    <img src="{{ $appIconUrl }}" class="w-full h-full object-contain p-2">
                                @else
                                    <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14"/></svg>
                                @endif
                            </div>
                            <div class="flex-1">
                                <input type="file" name="app_icon" accept="image/png" class="block w-full text-xs text-gray-500 file:mr-3 file:py-2 file:px-3 file:rounded file:border-0 file:bg-brand file:text-white file:text-xs file:font-medium cursor-pointer" onchange="previewAppIcon(this)">
                                <p class="text-[10px] text-gray-400 mt-2">1024×1024px PNG (Play Store requirement)</p>
                                @if($appIconUrl)
                                <span class="text-[10px] text-green-600 mt-1 block flex items-center gap-1">
                                    <span class="w-1.5 h-1.5 bg-green-500 rounded-full"></span>
                                    Icon active
                                </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-6 pt-5 border-t border-gray-100">
                    <label class="block text-xs font-medium text-gray-700 mb-3">Splash Screen Background</label>
                    @php $splashBgColor = isset($settings['splash_background_color']) ? ($settings['splash_background_color']->value ?? '#156400') : '#156400'; @endphp
                    <div class="flex items-center gap-4">
                        <div class="flex items-center gap-3 bg-gray-50 rounded-lg p-2 border border-gray-200">
                            <input type="color" name="settings[splash_background_color]" value="{{ $splashBgColor }}" class="w-10 h-10 rounded cursor-pointer border-0" style="background:transparent;">
                            <input type="text" value="{{ $splashBgColor }}" class="w-20 bg-transparent text-xs font-mono text-gray-600 uppercase outline-none" readonly>
                        </div>
                        <span class="text-[10px] text-gray-400">Background color while logo loads</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- App Store Links -->
        <div class="bg-white rounded-xl border border-gray-200">
            <div class="px-5 py-4 border-b border-gray-100 bg-gray-50/50">
                <h3 class="text-base font-semibold text-gray-900">App Store Links</h3>
                <p class="text-xs text-gray-500">Links to update pages in Play Store and App Store</p>
            </div>
            <div class="p-5">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-4">
                        <h4 class="text-sm font-medium text-gray-900 border-b border-gray-100 pb-2">Customer App</h4>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-2">Play Store Link</label>
                            <input type="url" name="settings[play_store_customer_link]" value="{{ getSetting('play_store_customer_link', '') }}" class="w-full bg-gray-50 border border-gray-200 rounded-lg py-2.5 px-3 text-sm text-gray-700 focus:border-brand focus:bg-white transition-all" placeholder="https://play.google.com/store/apps/...">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-2">App Store Link</label>
                            <input type="url" name="settings[app_store_customer_link]" value="{{ getSetting('app_store_customer_link', '') }}" class="w-full bg-gray-50 border border-gray-200 rounded-lg py-2.5 px-3 text-sm text-gray-700 focus:border-brand focus:bg-white transition-all" placeholder="https://apps.apple.com/app/...">
                        </div>
                    </div>
                    <div class="space-y-4">
                        <h4 class="text-sm font-medium text-gray-900 border-b border-gray-100 pb-2">Driver App</h4>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-2">Play Store Link</label>
                            <input type="url" name="settings[play_store_driver_link]" value="{{ getSetting('play_store_driver_link', '') }}" class="w-full bg-gray-50 border border-gray-200 rounded-lg py-2.5 px-3 text-sm text-gray-700 focus:border-brand focus:bg-white transition-all" placeholder="https://play.google.com/store/apps/...">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-2">App Store Link</label>
                            <input type="url" name="settings[app_store_driver_link]" value="{{ getSetting('app_store_driver_link', '') }}" class="w-full bg-gray-50 border border-gray-200 rounded-lg py-2.5 px-3 text-sm text-gray-700 focus:border-brand focus:bg-white transition-all" placeholder="https://apps.apple.com/app/...">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Brand Colors -->
        <div class="bg-white rounded-xl border border-gray-200">
            <div class="px-5 py-4 border-b border-gray-100 bg-gray-50/50">
                <h3 class="text-base font-semibold text-gray-900">Brand Colors</h3>
                <p class="text-xs text-gray-500">Color palette for mobile app UI</p>
            </div>
            <div class="p-5">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
                    @php
                    $colors = [
                        ['key' => 'brand_primary_color', 'label' => 'Primary Color', 'default' => '#156400', 'desc' => 'Main buttons, headers'],
                        ['key' => 'brand_accent_color', 'label' => 'Accent / Gold', 'default' => '#FFCC00', 'desc' => 'Highlights, CTAs'],
                        ['key' => 'brand_secondary_color', 'label' => 'Secondary', 'default' => '#0D4000', 'desc' => 'Backgrounds'],
                        ['key' => 'brand_dark_color', 'label' => 'Dark Mode', 'default' => '#0A0A0A', 'desc' => 'Dark theme bg'],
                    ];
                    @endphp
                    @foreach($colors as $color)
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-2">{{ $color['label'] }}</label>
                        <div class="flex items-center gap-3 bg-gray-50 rounded-lg p-2 border border-gray-200">
                            <input type="color" name="settings[{{ $color['key'] }}]" value="{{ getSetting($color['key'], $color['default']) }}" class="w-10 h-10 rounded cursor-pointer border-0" style="background:transparent;">
                            <div>
                                <input type="text" value="{{ getSetting($color['key'], $color['default']) }}" class="w-20 bg-transparent text-xs font-mono text-gray-600 uppercase outline-none" readonly>
                                <p class="text-[9px] text-gray-400">{{ $color['desc'] }}</p>
                            </div>
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

        <!-- Version Control -->
        <div class="bg-white rounded-xl border border-gray-200">
            <div class="px-5 py-4 border-b border-gray-100 bg-gray-50/50">
                <h3 class="text-base font-semibold text-gray-900">App Version Control</h3>
                <p class="text-xs text-gray-500">Force update requirements for mobile apps</p>
            </div>
            <div class="p-5">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-2">Customer App Min Version</label>
                        <input type="text" name="settings[min_customer_app_version]" value="{{ getSetting('min_customer_app_version', '1.0.0') }}" class="w-full bg-gray-50 border border-gray-200 rounded-lg py-2.5 px-3 text-sm text-gray-700 focus:border-brand focus:bg-white transition-all" placeholder="1.0.0">
                        <p class="text-[10px] text-gray-400 mt-1.5">Users below this version will be forced to update</p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-2">Driver App Min Version</label>
                        <input type="text" name="settings[min_driver_app_version]" value="{{ getSetting('min_driver_app_version', '1.0.0') }}" class="w-full bg-gray-50 border border-gray-200 rounded-lg py-2.5 px-3 text-sm text-gray-700 focus:border-brand focus:bg-white transition-all" placeholder="1.0.0">
                        <p class="text-[10px] text-gray-400 mt-1.5">Drivers below this version will be forced to update</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Enterprise Name -->
        <div class="bg-white rounded-xl border border-gray-200">
            <div class="px-5 py-4 border-b border-gray-100 bg-gray-50/50">
                <h3 class="text-base font-semibold text-gray-900">Enterprise Identity</h3>
                <p class="text-xs text-gray-500">Corporate name for notifications and reports</p>
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
                <li>• Changes apply to mobile apps on next app launch (within seconds)</li>
                <li>• All brand names, colors, and logos update automatically without app updates</li>
                <li>• App Store links enable in-app update prompts for users</li>
                <li>• Version control forces users to update when you deploy new app versions</li>
            </ul>
        </div>

        <!-- Submit -->
        <div class="flex justify-end pt-2">
            <button type="submit" class="bg-brand text-white px-6 py-2.5 rounded-lg text-sm font-medium shadow-sm hover:bg-brand-light hover:shadow-md transition-all flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                Save & Deploy to Apps
            </button>
        </div>
    </form>
</div>

<script>
function previewLogo(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('logo-preview-container').innerHTML = '<img src="' + e.target.result + '" class="w-full h-full object-contain p-2">';
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function previewAppIcon(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('app-icon-preview-container').innerHTML = '<img src="' + e.target.result + '" class="w-full h-full object-contain p-2">';
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>

@endsection