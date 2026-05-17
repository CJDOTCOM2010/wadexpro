@extends('admin.layout')
@section('title', 'Branding Configuration')
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

        <!-- Brand Name -->
        <div class="bg-white rounded-xl border border-gray-200">
            <div class="px-5 py-4 border-b border-gray-100 bg-gray-50/50">
                <h3 class="text-base font-semibold text-gray-900">App Names & Identity</h3>
                <p class="text-xs text-gray-500">Names shown on devices</p>
            </div>
            <div class="p-5 space-y-5">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-2">Brand Name</label>
                        <input type="text" name="settings[brand_name]" value="{{ old('settings.brand_name', 'WADEXPRO') }}" class="w-full bg-gray-50 border border-gray-200 rounded-lg py-2.5 px-3 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-2">Brand Short Name</label>
                        <input type="text" name="settings[brand_short_name]" value="{{ old('settings.brand_short_name', 'WADEX') }}" class="w-full bg-gray-50 border border-gray-200 rounded-lg py-2.5 px-3 text-sm">
                    </div>
                </div>
            </div>
        </div>

        <!-- Logos -->
        <div class="bg-white rounded-xl border border-gray-200">
            <div class="px-5 py-4 border-b border-gray-100 bg-gray-50/50">
                <h3 class="text-base font-semibold text-gray-900">App Icons & Splash Screen</h3>
                <p class="text-xs text-gray-500">Logos displayed on device home screen and splash</p>
            </div>
            <div class="p-5">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-3">Splash Screen Logo</label>
                        <div class="flex items-start gap-4">
                            <div class="w-20 h-20 rounded-xl bg-gray-100 border-2 border-dashed border-gray-300 flex items-center justify-center overflow-hidden shrink-0">
                                <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </div>
                            <div class="flex-1">
                                <input type="file" name="brand_logo" accept="image/png,image/jpeg,image/svg+xml,image/webp" class="block w-full text-xs text-gray-500 file:mr-3 file:py-2 file:px-3 file:rounded file:border-0 file:bg-brand file:text-white file:text-xs file:font-medium cursor-pointer">
                                <p class="text-[10px] text-gray-400 mt-2">512×512px, transparent PNG</p>
                            </div>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-3">App Icon</label>
                        <div class="flex items-start gap-4">
                            <div class="w-20 h-20 rounded-xl bg-gray-100 border-2 border-dashed border-gray-300 flex items-center justify-center overflow-hidden shrink-0">
                                <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14"/></svg>
                            </div>
                            <div class="flex-1">
                                <input type="file" name="app_icon" accept="image/png" class="block w-full text-xs text-gray-500 file:mr-3 file:py-2 file:px-3 file:rounded file:border-0 file:bg-brand file:text-white file:text-xs file:font-medium cursor-pointer">
                                <p class="text-[10px] text-gray-400 mt-2">1024×1024px PNG</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
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

@endsection