@extends('admin.layout')
@section('title', 'Social Authentication Hub')
@section('content')

<div class="p-8 lg:p-12 max-w-[1200px] mx-auto">
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-12 gap-6">
        <div>
            <div class="flex items-center gap-2 text-[10px] font-black text-accent uppercase tracking-[0.2em] mb-2">
                <a href="{{ route('orchestrator.settings') }}" class="hover:text-brand transition-colors">Settings Hub</a>
                <span class="text-gray-300">/</span>
                <span>Social Auth</span>
            </div>
            <h2 class="text-3xl font-black text-brand tracking-tight">External Identity Providers</h2>
            <p class="text-sm text-brand-muted font-medium mt-1">Manage OAuth 2.0 connections for one-tap system access.</p>
        </div>
    </div>

    <form action="{{ route('orchestrator.settings.update') }}" method="POST" class="space-y-10">
        @csrf
        
        <!-- Google Auth Section -->
        <div class="bg-white rounded-3xl border border-gray-100 shadow-2xl overflow-hidden">
            <div class="p-8 border-b border-gray-50 bg-surface/30 flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 bg-white rounded-xl shadow-sm flex items-center justify-center">
                        <svg class="w-6 h-6" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/><path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/><path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l3.66-2.84z" fill="#FBBC05"/><path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/></svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-black text-brand">Google Identity</h3>
                        <p class="text-xs text-brand-muted font-bold">Standard OAuth 2.0 connection for Google accounts.</p>
                    </div>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="settings[google_auth_enabled]" value="1" {{ \App\Modules\Admin\Models\SystemSetting::get('google_auth_enabled') == 'true' ? 'checked' : '' }} class="sr-only peer">
                    <div class="w-14 h-7 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-brand"></div>
                </label>
            </div>
            
            <div class="p-8 grid grid-cols-1 md:grid-cols-2 gap-8">
                <div>
                    <label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-3">Client ID</label>
                    <input type="password" name="settings[google_client_id]" value="{{ \App\Modules\Admin\Models\SystemSetting::get('google_client_id') ? '********' : '' }}" class="w-full bg-surface border-2 border-transparent focus:border-brand rounded-2xl py-4 px-6 text-sm font-bold outline-none transition-all">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-3">Client Secret</label>
                    <input type="password" name="settings[google_client_secret]" value="{{ \App\Modules\Admin\Models\SystemSetting::get('google_client_secret') ? '********' : '' }}" class="w-full bg-surface border-2 border-transparent focus:border-brand rounded-2xl py-4 px-6 text-sm font-bold outline-none transition-all">
                </div>
            </div>
        </div>

        <!-- Facebook Auth Section -->
        <div class="bg-white rounded-3xl border border-gray-100 shadow-xl overflow-hidden">
            <div class="p-8 border-b border-gray-50 bg-surface/30 flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 bg-white rounded-xl shadow-sm flex items-center justify-center">
                        <svg class="w-6 h-6 text-[#1877F2]" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-black text-brand">Facebook Connect</h3>
                        <p class="text-xs text-brand-muted font-bold">Authenticate users via their Facebook profile.</p>
                    </div>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="settings[facebook_auth_enabled]" value="1" {{ \App\Modules\Admin\Models\SystemSetting::get('facebook_auth_enabled') == 'true' ? 'checked' : '' }} class="sr-only peer">
                    <div class="w-14 h-7 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-brand"></div>
                </label>
            </div>
            
            <div class="p-8 grid grid-cols-1 md:grid-cols-2 gap-8">
                <div>
                    <label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-3">App ID</label>
                    <input type="password" name="settings[facebook_client_id]" value="{{ \App\Modules\Admin\Models\SystemSetting::get('facebook_client_id') ? '********' : '' }}" class="w-full bg-surface border-2 border-transparent focus:border-brand rounded-2xl py-4 px-6 text-sm font-bold outline-none transition-all">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-3">App Secret</label>
                    <input type="password" name="settings[facebook_client_secret]" value="{{ \App\Modules\Admin\Models\SystemSetting::get('facebook_client_secret') ? '********' : '' }}" class="w-full bg-surface border-2 border-transparent focus:border-brand rounded-2xl py-4 px-6 text-sm font-bold outline-none transition-all">
                </div>
            </div>
        </div>

        <!-- Apple Auth Section -->
        <div class="bg-white rounded-3xl border border-gray-100 shadow-xl overflow-hidden">
            <div class="p-8 border-b border-gray-50 bg-surface/30 flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 bg-white rounded-xl shadow-sm flex items-center justify-center">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M17.05 20.28c-.96.95-2.23 1.54-3.71 1.54-1.49 0-2.76-.59-3.71-1.54-.95-.95-1.54-2.22-1.54-3.71 0-1.49.59-2.76 1.54-3.71.95-.95 2.22-1.54 3.71-1.54 1.48 0 2.75.59 3.71 1.54.95.95 1.54 2.22 1.54 3.71 0 1.49-.59 2.76-1.54 3.71zM13.34 1.66c-.46 1.15-1.37 2.05-2.51 2.52.26-.64.4-1.33.4-2.06s-.14-1.42-.4-2.06c1.14.47 2.05 1.37 2.51 2.52l.001.079z"/></svg>
                        <i class="fab fa-apple text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-black text-brand">Apple ID</h3>
                        <p class="text-xs text-brand-muted font-bold">Secure authentication for iOS and macOS ecosystem users.</p>
                    </div>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="settings[apple_auth_enabled]" value="1" {{ \App\Modules\Admin\Models\SystemSetting::get('apple_auth_enabled') == 'true' ? 'checked' : '' }} class="sr-only peer">
                    <div class="w-14 h-7 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-brand"></div>
                </label>
            </div>
            
            <div class="p-8 grid grid-cols-1 md:grid-cols-2 gap-8">
                <div>
                    <label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-3">Service ID (Client ID)</label>
                    <input type="password" name="settings[apple_client_id]" value="{{ \App\Modules\Admin\Models\SystemSetting::get('apple_client_id') ? '********' : '' }}" class="w-full bg-surface border-2 border-transparent focus:border-brand rounded-2xl py-4 px-6 text-sm font-bold outline-none transition-all">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-3">Key ID / Team ID</label>
                    <input type="password" name="settings[apple_client_secret]" value="{{ \App\Modules\Admin\Models\SystemSetting::get('apple_client_secret') ? '********' : '' }}" class="w-full bg-surface border-2 border-transparent focus:border-brand rounded-2xl py-4 px-6 text-sm font-bold outline-none transition-all">
                </div>
            </div>
        </div>

        <div class="flex justify-end pt-4">
            <button type="submit" class="bg-brand text-white px-12 py-5 rounded-2xl text-xs font-black shadow-xl hover:shadow-brand/20 hover:-translate-y-1 transition-all uppercase tracking-widest">
                Deploy Identity Nodes
            </button>
        </div>
    </form>
</div>

@endsection
