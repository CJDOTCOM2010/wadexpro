@extends('admin.layout')
@section('title', 'Auth & Identity')
@section('content')

<div class="p-8 lg:p-12 max-w-5xl mx-auto">
    <!-- Header/Breadcrumb -->
    <div class="flex items-center justify-between mb-12">
        <div>
            <div class="flex items-center gap-2 text-[10px] font-black text-accent uppercase tracking-[0.2em] mb-2">
                <a href="{{ route('orchestrator.settings') }}" class="hover:text-brand transition-colors">Settings Hub</a>
                <span class="text-gray-300">/</span>
                <span>Authentication</span>
            </div>
            <h2 class="text-3xl font-black text-brand tracking-tight">Identity & Social Sync</h2>
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
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-black text-brand tracking-tight">Security Bridging</h3>
                        <p class="text-xs text-brand-muted font-medium">Manage OAuth providers and identity handshakes.</p>
                    </div>
                </div>
                <button type="submit" class="px-8 py-4 bg-brand text-white font-black text-xs rounded-lg hover:bg-brand-hover shadow-lg hover:shadow-brand/20 transition-all">Save Credentials</button>
            </div>

            <div class="space-y-12">
                <!-- Google Config -->
                <div x-data="{ googleEnabled: {{ \App\Modules\Admin\Models\SystemSetting::get('google_auth_enabled') ? 'true' : 'false' }} }" class="space-y-8 animate-slide-up">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-red-50 text-red-600 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12.48 10.92v3.28h7.84c-.24 1.84-.92 3.32-2.12 4.52-1.36 1.36-3.48 2.32-6.2 2.32-4.88 0-8.88-3.96-8.88-8.88s4-8.88 8.88-8.88c2.68 0 4.6 1.04 6.04 2.44l2.32-2.32C18.24 1.24 15.48 0 12.48 0 5.59 0 0 5.59 0 12.48S5.59 24.96 12.48 24.96c3.76 0 6.6-1.24 8.8-3.52 2.28-2.28 3.52-5.48 3.52-8.52 0-.64-.04-1.28-.16-1.88h-11.84z"/></svg>
                        </div>
                        <h4 class="text-sm font-black text-brand uppercase tracking-widest">Google Infrastructure</h4>
                    </div>

                    <div class="bg-surface rounded-lg p-8 flex flex-col gap-6 border border-gray-100 group hover:border-red-100 transition-colors">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-[15px] font-bold text-brand">Enable Google Authentication</p>
                                <p class="text-xs text-brand-muted mt-1">Activate seamless OAuth flow across all mobile nodes.</p>
                            </div>
                            <input type="hidden" name="settings[google_auth_enabled]" value="0">
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="settings[google_auth_enabled]" value="1" :checked="googleEnabled" @change="googleEnabled = $event.target.checked" {{ \App\Modules\Admin\Models\SystemSetting::get('google_auth_enabled') ? 'checked' : '' }} class="sr-only peer">
                                <div class="w-14 h-8 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[4px] after:left-[4px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-red-600 shadow-inner"></div>
                            </label>
                        </div>

                        <!-- Platform Toggles -->
                        <div x-show="googleEnabled" x-transition class="pt-6 border-t border-gray-200" x-cloak>
                            <p class="text-[10px] font-black text-brand-muted uppercase tracking-widest mb-4">Active Platforms</p>
                            <div class="flex flex-wrap gap-6">
                                <label class="flex items-center gap-3 cursor-pointer group">
                                    <div class="relative flex items-center">
                                        <input type="hidden" name="settings[google_auth_ios]" value="0">
                                        <input type="checkbox" name="settings[google_auth_ios]" value="1" {{ \App\Modules\Admin\Models\SystemSetting::get('google_auth_ios', 1) ? 'checked' : '' }} class="peer sr-only">
                                        <div class="w-5 h-5 border-2 border-gray-300 rounded bg-white peer-checked:bg-red-600 peer-checked:border-red-600 transition-all flex items-center justify-center">
                                            <svg class="w-3 h-3 text-white opacity-0 peer-checked:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                        </div>
                                    </div>
                                    <span class="text-sm font-bold text-gray-700 group-hover:text-brand transition-colors">iOS App</span>
                                </label>
                                <label class="flex items-center gap-3 cursor-pointer group">
                                    <div class="relative flex items-center">
                                        <input type="hidden" name="settings[google_auth_android]" value="0">
                                        <input type="checkbox" name="settings[google_auth_android]" value="1" {{ \App\Modules\Admin\Models\SystemSetting::get('google_auth_android', 1) ? 'checked' : '' }} class="peer sr-only">
                                        <div class="w-5 h-5 border-2 border-gray-300 rounded bg-white peer-checked:bg-red-600 peer-checked:border-red-600 transition-all flex items-center justify-center">
                                            <svg class="w-3 h-3 text-white opacity-0 peer-checked:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                        </div>
                                    </div>
                                    <span class="text-sm font-bold text-gray-700 group-hover:text-brand transition-colors">Android App</span>
                                </label>
                                <label class="flex items-center gap-3 cursor-pointer group">
                                    <div class="relative flex items-center">
                                        <input type="hidden" name="settings[google_auth_web]" value="0">
                                        <input type="checkbox" name="settings[google_auth_web]" value="1" {{ \App\Modules\Admin\Models\SystemSetting::get('google_auth_web', 1) ? 'checked' : '' }} class="peer sr-only">
                                        <div class="w-5 h-5 border-2 border-gray-300 rounded bg-white peer-checked:bg-red-600 peer-checked:border-red-600 transition-all flex items-center justify-center">
                                            <svg class="w-3 h-3 text-white opacity-0 peer-checked:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                        </div>
                                    </div>
                                    <span class="text-sm font-bold text-gray-700 group-hover:text-brand transition-colors">Web Portal</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div x-show="googleEnabled" x-transition class="grid grid-cols-1 md:grid-cols-2 gap-8" x-cloak>
                        <div class="group">
                            <label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-3">Google Web Client ID</label>
                            <input type="text" name="settings[google_auth_web_client_id]" value="{{ \App\Modules\Admin\Models\SystemSetting::get('google_auth_web_client_id') }}" class="w-full bg-surface border-2 border-transparent focus:border-accent focus:bg-white rounded-lg py-4 px-6 text-[13px] font-bold outline-none transition-all shadow-sm font-mono">
                        </div>
                        <div class="group">
                            <label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-3">Google iOS Client ID</label>
                            <input type="text" name="settings[google_auth_ios_client_id]" value="{{ \App\Modules\Admin\Models\SystemSetting::get('google_auth_ios_client_id') }}" class="w-full bg-surface border-2 border-transparent focus:border-accent focus:bg-white rounded-lg py-4 px-6 text-[13px] font-bold outline-none transition-all shadow-sm font-mono">
                        </div>
                    </div>
                </div>

                <!-- Facebook Config -->
                <div x-data="{ facebookEnabled: {{ \App\Modules\Admin\Models\SystemSetting::get('facebook_auth_enabled') ? 'true' : 'false' }} }" class="space-y-8 pt-10 border-t border-gray-50 animate-slide-up" style="animation-delay: 100ms">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-blue-50 text-blue-600 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                        </div>
                        <h4 class="text-sm font-black text-brand uppercase tracking-widest">Facebook Bridge</h4>
                    </div>

                    <div class="bg-surface rounded-lg p-8 flex flex-col gap-6 border border-gray-100 group hover:border-blue-100 transition-colors">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-[15px] font-bold text-brand">Enable Facebook Authentication</p>
                                <p class="text-xs text-brand-muted mt-1">Activate high-conversion social login nodes.</p>
                            </div>
                            <input type="hidden" name="settings[facebook_auth_enabled]" value="0">
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="settings[facebook_auth_enabled]" value="1" :checked="facebookEnabled" @change="facebookEnabled = $event.target.checked" {{ \App\Modules\Admin\Models\SystemSetting::get('facebook_auth_enabled') ? 'checked' : '' }} class="sr-only peer">
                                <div class="w-14 h-8 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[4px] after:left-[4px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-blue-600 shadow-inner"></div>
                            </label>
                        </div>

                        <!-- Platform Toggles -->
                        <div x-show="facebookEnabled" x-transition class="pt-6 border-t border-gray-200" x-cloak>
                            <p class="text-[10px] font-black text-brand-muted uppercase tracking-widest mb-4">Active Platforms</p>
                            <div class="flex flex-wrap gap-6">
                                <label class="flex items-center gap-3 cursor-pointer group">
                                    <div class="relative flex items-center">
                                        <input type="hidden" name="settings[facebook_auth_ios]" value="0">
                                        <input type="checkbox" name="settings[facebook_auth_ios]" value="1" {{ \App\Modules\Admin\Models\SystemSetting::get('facebook_auth_ios', 1) ? 'checked' : '' }} class="peer sr-only">
                                        <div class="w-5 h-5 border-2 border-gray-300 rounded bg-white peer-checked:bg-blue-600 peer-checked:border-blue-600 transition-all flex items-center justify-center">
                                            <svg class="w-3 h-3 text-white opacity-0 peer-checked:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                        </div>
                                    </div>
                                    <span class="text-sm font-bold text-gray-700 group-hover:text-brand transition-colors">iOS App</span>
                                </label>
                                <label class="flex items-center gap-3 cursor-pointer group">
                                    <div class="relative flex items-center">
                                        <input type="hidden" name="settings[facebook_auth_android]" value="0">
                                        <input type="checkbox" name="settings[facebook_auth_android]" value="1" {{ \App\Modules\Admin\Models\SystemSetting::get('facebook_auth_android', 1) ? 'checked' : '' }} class="peer sr-only">
                                        <div class="w-5 h-5 border-2 border-gray-300 rounded bg-white peer-checked:bg-blue-600 peer-checked:border-blue-600 transition-all flex items-center justify-center">
                                            <svg class="w-3 h-3 text-white opacity-0 peer-checked:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                        </div>
                                    </div>
                                    <span class="text-sm font-bold text-gray-700 group-hover:text-brand transition-colors">Android App</span>
                                </label>
                                <label class="flex items-center gap-3 cursor-pointer group">
                                    <div class="relative flex items-center">
                                        <input type="hidden" name="settings[facebook_auth_web]" value="0">
                                        <input type="checkbox" name="settings[facebook_auth_web]" value="1" {{ \App\Modules\Admin\Models\SystemSetting::get('facebook_auth_web', 1) ? 'checked' : '' }} class="peer sr-only">
                                        <div class="w-5 h-5 border-2 border-gray-300 rounded bg-white peer-checked:bg-blue-600 peer-checked:border-blue-600 transition-all flex items-center justify-center">
                                            <svg class="w-3 h-3 text-white opacity-0 peer-checked:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                        </div>
                                    </div>
                                    <span class="text-sm font-bold text-gray-700 group-hover:text-brand transition-colors">Web Portal</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div x-show="facebookEnabled" x-transition class="grid grid-cols-1 md:grid-cols-2 gap-8" x-cloak>
                        <div class="group">
                            <label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-3">Facebook App ID</label>
                            <input type="text" name="settings[facebook_app_id]" value="{{ \App\Modules\Admin\Models\SystemSetting::get('facebook_app_id') }}" class="w-full bg-surface border-2 border-transparent focus:border-accent focus:bg-white rounded-lg py-4 px-6 text-[13px] font-bold outline-none transition-all shadow-sm font-mono">
                        </div>
                        <div class="group">
                            <label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-3">Facebook Client Token</label>
                            <input type="text" name="settings[facebook_client_token]" value="{{ \App\Modules\Admin\Models\SystemSetting::get('facebook_client_token') }}" class="w-full bg-surface border-2 border-transparent focus:border-accent focus:bg-white rounded-lg py-4 px-6 text-[13px] font-bold outline-none transition-all shadow-sm font-mono">
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection
