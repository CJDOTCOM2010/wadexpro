@extends('admin.layout')
@section('title', 'My Profile & Account Settings')
@section('content')

@php
$admin = auth('admin')->user();
$initials = strtoupper(substr($admin->first_name ?? $admin->name ?? 'A', 0, 1) . substr($admin->last_name ?? '', 0, 1)) ?: 'SA';
$prefs = json_decode($admin->notification_preferences ?? '{}', true) ?? [];
@endphp

@if(session('error'))
<div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl flex items-center gap-3">
    <svg class="w-5 h-5 text-red-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    <p class="text-sm font-medium text-red-700">{{ session('error') }}</p>
</div>
@endif
@if(session('success'))
<div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-xl flex items-center gap-3">
    <svg class="w-5 h-5 text-green-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    <p class="text-sm font-medium text-green-700">{{ session('success') }}</p>
</div>
@endif
@if($errors->any())
<div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl">
    @foreach($errors->all() as $error)<p class="text-sm font-medium text-red-700">· {{ $error }}</p>@endforeach
</div>
@endif

<div x-data="profileManager()" class="max-w-6xl mx-auto">
    <!-- Premium Profile Header -->
    <div class="relative bg-gradient-to-r from-brand via-brand-light to-brand rounded-xl overflow-hidden mb-8">
        <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_top_right,_rgba(248,184,3,0.15),transparent_50%)]"></div>
        <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_bottom_left,_rgba(255,255,255,0.05),transparent_50%)]"></div>
        <div class="relative z-10 px-6 py-8 sm:px-10 sm:py-10">
            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-6">
                <div class="relative group cursor-pointer" @click="$refs.avatarInput.click()">
                    <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-xl bg-white/10 flex items-center justify-center font-black text-3xl sm:text-4xl text-accent overflow-hidden ring-2 ring-white/20 backdrop-blur-sm">
                        <template x-if="avatarPreview">
                            <img :src="avatarPreview" class="w-full h-full object-cover">
                        </template>
                        <template x-if="!avatarPreview">
                            <span>{{ $initials }}</span>
                        </template>
                    </div>
                    <div class="absolute inset-0 rounded-xl bg-brand/50 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                    <span class="absolute -bottom-1 -right-1 w-5 h-5 bg-green-500 border-2 border-white rounded-full"></span>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 mb-1">
                        <h2 class="text-2xl sm:text-3xl font-black text-white tracking-tight truncate">{{ $admin->name ?? 'Admin Account' }}</h2>
                        <span class="px-2 py-0.5 bg-accent/20 text-accent text-[10px] font-bold rounded-lg uppercase tracking-wider whitespace-nowrap">{{ ucfirst($admin->level ?? $admin->role ?? 'Admin') }}</span>
                    </div>
                    <p class="text-white/60 text-sm font-medium">{{ $admin->email }}</p>
                    <div class="flex items-center gap-3 mt-2 flex-wrap">
                        <span class="flex items-center gap-1.5 text-[11px] font-bold text-white/40">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            Joined {{ $admin->created_at?->format('M Y') ?? '—' }}
                        </span>
                        <span class="flex items-center gap-1.5 text-[11px] font-bold text-green-400">
                            <span class="w-1.5 h-1.5 bg-green-400 rounded-full animate-pulse"></span>
                            Session Active
                        </span>
                    </div>
                </div>
                <div class="hidden sm:flex items-center gap-2">
                    <span class="px-3 py-1.5 bg-white/10 rounded-lg text-[10px] font-bold text-white/70">ID: <span class="font-mono">{{ substr($admin->id, 0, 8) }}</span></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-8">
        <div class="bg-white border border-gray-100 rounded-xl p-3.5 flex items-center gap-3">
            <div class="w-9 h-9 bg-green-50 rounded-lg flex items-center justify-center text-green-600 shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
            </div>
            <div>
                <p class="text-lg font-black text-brand">High</p>
                <p class="text-[10px] font-bold text-brand-muted">Security Level</p>
            </div>
        </div>
        <div class="bg-white border border-gray-100 rounded-xl p-3.5 flex items-center gap-3">
            <div class="w-9 h-9 bg-blue-50 rounded-lg flex items-center justify-center text-blue-600 shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
            </div>
            <div>
                <p class="text-lg font-black text-brand">{{ $admin->department ?? 'All' }}</p>
                <p class="text-[10px] font-bold text-brand-muted">Department</p>
            </div>
        </div>
        <div class="bg-white border border-gray-100 rounded-xl p-3.5 flex items-center gap-3">
            <div class="w-9 h-9 bg-amber-50 rounded-lg flex items-center justify-center text-amber-600 shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
            </div>
            <div>
                <p class="text-lg font-black text-brand">6</p>
                <p class="text-[10px] font-bold text-brand-muted">Notifications</p>
            </div>
        </div>
        <div class="bg-white border border-gray-100 rounded-xl p-3.5 flex items-center gap-3">
            <div class="w-9 h-9 bg-purple-50 rounded-lg flex items-center justify-center text-purple-600 shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17H3a2 2 0 01-2-2V5a2 2 0 012-2h14a2 2 0 012 2v10a2 2 0 01-2 2h-2"/></svg>
            </div>
            <div>
                <p class="text-lg font-black text-brand">1</p>
                <p class="text-[10px] font-bold text-brand-muted">Active Session</p>
            </div>
        </div>
    </div>

    <!-- Tabs -->
    <div class="flex gap-1 mb-8 overflow-x-auto pb-1" role="tablist">
        <template x-for="(t, k) in tabs" :key="k">
            <button @click="tab = k" :class="tab === k ? 'bg-brand text-white shadow-md' : 'bg-white text-brand-muted hover:bg-surface border border-gray-100'" class="px-4 py-2.5 rounded-lg text-xs font-bold transition-all whitespace-nowrap flex items-center gap-2" x-html="t.icon + ' ' + t.label"></button>
        </template>
    </div>

    <!-- Tab: Identity -->
    <div x-show="tab === 'identity'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
        <form action="{{ route('orchestrator.profile.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="file" name="avatar" accept="image/*" class="hidden" x-ref="avatarInput" @change="previewAvatar($event)">

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Avatar Card -->
                <div class="bg-white border border-gray-100 rounded-xl p-6">
                    <div class="flex flex-col items-center text-center gap-4">
                        <div class="relative group cursor-pointer" @click="$refs.avatarInput.click()">
                            <div class="w-28 h-28 rounded-xl bg-surface flex items-center justify-center font-black text-5xl text-brand-muted overflow-hidden ring-2 ring-gray-100">
                                <template x-if="avatarPreview">
                                    <img :src="avatarPreview" class="w-full h-full object-cover">
                                </template>
                                <template x-if="!avatarPreview">
                                    <span>{{ $initials }}</span>
                                </template>
                            </div>
                            <div class="absolute inset-0 rounded-xl bg-brand/60 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            </div>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-brand">Profile Photo</p>
                            <p class="text-[10px] text-brand-muted mt-0.5">JPG, PNG or WebP · Max 2MB</p>
                        </div>
                        <button type="button" @click="$refs.avatarInput.click()" class="w-full py-2.5 px-4 border-2 border-dashed border-gray-200 rounded-lg text-xs font-bold text-brand-muted hover:border-brand hover:text-brand transition-colors">
                            Choose Photo
                        </button>
                    </div>
                </div>

                <!-- Profile Form -->
                <div class="lg:col-span-2 bg-white border border-gray-100 rounded-xl p-6 space-y-5">
                    <div class="flex items-center gap-2 mb-1">
                        <div class="w-8 h-8 bg-brand/5 rounded-lg flex items-center justify-center text-brand">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        </div>
                        <h3 class="text-base font-bold text-brand">Personal Information</h3>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="text-[10px] font-bold text-brand-muted uppercase tracking-wider mb-1.5 block">First Name</label>
                            <input type="text" name="first_name" value="{{ old('first_name', $admin->first_name) }}" class="w-full bg-surface border border-gray-100 rounded-lg px-4 py-2.5 text-sm font-bold outline-none focus:ring-2 focus:ring-accent/20 transition-shadow" placeholder="John">
                        </div>
                        <div>
                            <label class="text-[10px] font-bold text-brand-muted uppercase tracking-wider mb-1.5 block">Last Name</label>
                            <input type="text" name="last_name" value="{{ old('last_name', $admin->last_name) }}" class="w-full bg-surface border border-gray-100 rounded-lg px-4 py-2.5 text-sm font-bold outline-none focus:ring-2 focus:ring-accent/20 transition-shadow" placeholder="Doe">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="text-[10px] font-bold text-brand-muted uppercase tracking-wider mb-1.5 block">Display Name</label>
                            <input type="text" name="name" value="{{ old('name', $admin->name) }}" required class="w-full bg-surface border border-gray-100 rounded-lg px-4 py-2.5 text-sm font-bold outline-none focus:ring-2 focus:ring-accent/20 transition-shadow" placeholder="Admin Display Name">
                        </div>
                        <div>
                            <label class="text-[10px] font-bold text-brand-muted uppercase tracking-wider mb-1.5 block">Email Address</label>
                            <input type="email" name="email" value="{{ old('email', $admin->email) }}" required class="w-full bg-surface border border-gray-100 rounded-lg px-4 py-2.5 text-sm font-bold outline-none focus:ring-2 focus:ring-accent/20 transition-shadow" placeholder="admin@wadexpro.com">
                        </div>
                        <div>
                            <label class="text-[10px] font-bold text-brand-muted uppercase tracking-wider mb-1.5 block">Phone Number</label>
                            <input type="text" name="phone" value="{{ old('phone', $admin->phone) }}" class="w-full bg-surface border border-gray-100 rounded-lg px-4 py-2.5 text-sm font-bold outline-none focus:ring-2 focus:ring-accent/20 transition-shadow" placeholder="+233 xx xxx xxxx">
                        </div>
                    </div>

                    <div class="pt-4 border-t border-gray-100">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="text-[10px] font-bold text-brand-muted uppercase tracking-wider mb-1.5 block">Account Type</label>
                                <div class="bg-surface border border-gray-100 rounded-lg px-4 py-2.5 text-sm font-bold text-brand-muted cursor-not-allowed">{{ ucfirst($admin->level ?? $admin->role ?? 'Admin') }}</div>
                            </div>
                            <div>
                                <label class="text-[10px] font-bold text-brand-muted uppercase tracking-wider mb-1.5 block">Department</label>
                                <div class="bg-surface border border-gray-100 rounded-lg px-4 py-2.5 text-sm font-bold text-brand-muted cursor-not-allowed">{{ $admin->department ?? 'Not Assigned' }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end pt-2">
                        <button type="submit" class="px-6 py-2.5 bg-brand text-white rounded-lg text-xs font-bold hover:bg-brand-light transition-colors flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Save Changes
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Tab: Security -->
    <div x-show="tab === 'security'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-6">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Change Password -->
            <div class="lg:col-span-2 bg-white border border-gray-100 rounded-xl p-6">
                <div class="flex items-center gap-3 mb-5">
                    <div class="w-9 h-9 bg-red-50 rounded-lg flex items-center justify-center text-red-500">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-brand">Change Password</h3>
                        <p class="text-[11px] text-brand-muted">Update your authentication credentials.</p>
                    </div>
                </div>
                <form action="{{ route('orchestrator.profile.password') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="text-[10px] font-bold text-brand-muted uppercase tracking-wider mb-1.5 block">Current Password</label>
                        <input type="password" name="current_password" required class="w-full bg-surface border border-gray-100 rounded-lg px-4 py-2.5 text-sm font-bold outline-none focus:ring-2 focus:ring-red-200/50 transition-shadow" placeholder="Enter current password">
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="text-[10px] font-bold text-brand-muted uppercase tracking-wider mb-1.5 block">New Password</label>
                            <input type="password" name="password" required @input="checkStrength($event.target.value)" class="w-full bg-surface border border-gray-100 rounded-lg px-4 py-2.5 text-sm font-bold outline-none focus:ring-2 focus:ring-red-200/50 transition-shadow" placeholder="Min 8 characters">
                        </div>
                        <div>
                            <label class="text-[10px] font-bold text-brand-muted uppercase tracking-wider mb-1.5 block">Confirm New Password</label>
                            <input type="password" name="password_confirmation" required class="w-full bg-surface border border-gray-100 rounded-lg px-4 py-2.5 text-sm font-bold outline-none focus:ring-2 focus:ring-red-200/50 transition-shadow" placeholder="Repeat new password">
                        </div>
                    </div>
                    <!-- Strength Meter -->
                    <div x-show="passwordValue" class="space-y-1.5">
                        <div class="h-1.5 bg-surface rounded-full overflow-hidden">
                            <div class="h-full rounded-full transition-all duration-300" :style="'width: ' + strength.percent + '%'" :class="strength.color"></div>
                        </div>
                        <p class="text-[10px] font-bold" :class="strength.textColor" x-text="strength.label"></p>
                    </div>
                    <div class="flex justify-end">
                        <button type="submit" class="px-6 py-2.5 bg-red-600 text-white rounded-lg text-xs font-bold hover:bg-red-700 transition-colors flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            Update Password
                        </button>
                    </div>
                </form>
            </div>

            <!-- Security Status -->
            <div class="space-y-4">
                <div class="bg-white border border-gray-100 rounded-xl p-6">
                    <h3 class="text-sm font-bold text-brand mb-4">Security Status</h3>
                    <div class="space-y-3">
                        <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                            <div class="flex items-center gap-2.5">
                                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span class="text-xs font-bold text-green-800">Email Verified</span>
                            </div>
                            <span class="text-[10px] font-bold text-green-600">{{ $admin->email_verified_at ? 'Yes' : 'No' }}</span>
                        </div>
                        <div class="flex items-center justify-between p-3 bg-surface rounded-lg">
                            <div class="flex items-center gap-2.5">
                                <svg class="w-4 h-4 text-brand" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                <span class="text-xs font-bold text-brand">Password Age</span>
                            </div>
                            <span class="text-[10px] font-bold text-brand-muted">{{ $admin->updated_at?->diffForHumans() ?? '—' }}</span>
                        </div>
                        <div class="flex items-center justify-between p-3 bg-surface rounded-lg">
                            <div class="flex items-center gap-2.5">
                                <svg class="w-4 h-4 text-brand" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                                <span class="text-xs font-bold text-brand">Last Login IP</span>
                            </div>
                            <span class="text-[10px] font-bold text-brand-muted">{{ request()->ip() }}</span>
                        </div>
                    </div>
                </div>
                <div class="bg-white border border-gray-100 rounded-xl p-6">
                    <h3 class="text-sm font-bold text-brand mb-4">Quick Actions</h3>
                    <div class="space-y-2.5">
                        <a href="{{ route('orchestrator.settings.security') }}" class="flex items-center gap-3 p-3 rounded-lg hover:bg-surface transition-colors">
                            <div class="w-8 h-8 bg-brand/5 rounded-lg flex items-center justify-center text-brand">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/></svg>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-brand">Security Settings</p>
                                <p class="text-[10px] text-brand-muted">Password policy, 2FA, IP whitelist</p>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tab: Notifications -->
    <div x-show="tab === 'notifications'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
        <form action="{{ route('orchestrator.profile.notifications') }}" method="POST">
            @csrf
            <div class="bg-white border border-gray-100 rounded-xl p-6">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-9 h-9 bg-amber-50 rounded-lg flex items-center justify-center text-amber-500">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-brand">Notification Preferences</h3>
                        <p class="text-[11px] text-brand-muted">Control which system events alert you.</p>
                    </div>
                </div>

                <!-- Support -->
                <div class="mb-6">
                    <p class="text-[10px] font-bold text-brand-muted uppercase tracking-wider mb-3">Support & Security</p>
                    <div class="space-y-2">
                        @foreach(['notify_new_tickets' => ['New Support Tickets', 'When a customer opens a new support ticket.'], 'notify_sos_alerts' => ['SOS & Emergency Alerts', 'When a driver or passenger triggers an SOS.']] as $key => [$label, $desc])
                        <div class="flex items-center justify-between p-3.5 bg-surface/50 rounded-lg">
                            <div>
                                <p class="text-xs font-bold text-brand">{{ $label }}</p>
                                <p class="text-[10px] text-brand-muted mt-0.5">{{ $desc }}</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer shrink-0 ml-3">
                                <input type="hidden" name="{{ $key }}" value="0">
                                <input type="checkbox" name="{{ $key }}" value="1" class="sr-only peer" {{ ($prefs[$key] ?? false) ? 'checked' : '' }}>
                                <div class="w-10 h-5 bg-gray-200 rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[0.5px] after:left-[0.5px] after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-brand"></div>
                            </label>
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- Users -->
                <div class="mb-6">
                    <p class="text-[10px] font-bold text-brand-muted uppercase tracking-wider mb-3">Users & Drivers</p>
                    <div class="space-y-2">
                        @foreach(['notify_new_users' => ['New Registrations', 'When a new customer or driver registers.'], 'notify_driver_flagged' => ['Driver Flagged/Suspended', 'When a driver is flagged by security.'], 'notify_low_drivers' => ['Low Driver Availability', 'When active driver count drops below threshold.']] as $key => [$label, $desc])
                        <div class="flex items-center justify-between p-3.5 bg-surface/50 rounded-lg">
                            <div>
                                <p class="text-xs font-bold text-brand">{{ $label }}</p>
                                <p class="text-[10px] text-brand-muted mt-0.5">{{ $desc }}</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer shrink-0 ml-3">
                                <input type="hidden" name="{{ $key }}" value="0">
                                <input type="checkbox" name="{{ $key }}" value="1" class="sr-only peer" {{ ($prefs[$key] ?? false) ? 'checked' : '' }}>
                                <div class="w-10 h-5 bg-gray-200 rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[0.5px] after:left-[0.5px] after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-brand"></div>
                            </label>
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- Payments -->
                <div class="mb-6">
                    <p class="text-[10px] font-bold text-brand-muted uppercase tracking-wider mb-3">Payments & Finance</p>
                    <div class="space-y-2">
                        @foreach(['notify_failed_payments' => ['Failed Payments', 'When a transaction fails or is reversed.']] as $key => [$label, $desc])
                        <div class="flex items-center justify-between p-3.5 bg-surface/50 rounded-lg">
                            <div>
                                <p class="text-xs font-bold text-brand">{{ $label }}</p>
                                <p class="text-[10px] text-brand-muted mt-0.5">{{ $desc }}</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer shrink-0 ml-3">
                                <input type="hidden" name="{{ $key }}" value="0">
                                <input type="checkbox" name="{{ $key }}" value="1" class="sr-only peer" {{ ($prefs[$key] ?? false) ? 'checked' : '' }}>
                                <div class="w-10 h-5 bg-gray-200 rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[0.5px] after:left-[0.5px] after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-brand"></div>
                            </label>
                        </div>
                        @endforeach
                    </div>
                </div>

                <div class="flex justify-end pt-2 border-t border-gray-100">
                    <button type="submit" class="px-6 py-2.5 bg-brand text-white rounded-lg text-xs font-bold hover:bg-brand-light transition-colors flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Save Preferences
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Tab: Sessions -->
    <div x-show="tab === 'sessions'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-6">
        <div class="bg-white border border-gray-100 rounded-xl p-6">
            <div class="flex items-center gap-3 mb-5">
                <div class="w-9 h-9 bg-brand/5 rounded-lg flex items-center justify-center text-brand">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17H3a2 2 0 01-2-2V5a2 2 0 012-2h14a2 2 0 012 2v10a2 2 0 01-2 2h-2"/></svg>
                </div>
                <div>
                    <h3 class="text-base font-bold text-brand">Active Sessions</h3>
                    <p class="text-[11px] text-brand-muted">Manage where your account is signed in.</p>
                </div>
            </div>

            <!-- Current Session -->
            <div class="p-4 rounded-lg border border-brand/20 bg-brand/[0.03] flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-brand/10 rounded-lg flex items-center justify-center text-brand shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17H3a2 2 0 01-2-2V5a2 2 0 012-2h14a2 2 0 012 2v10a2 2 0 01-2 2h-2"/></svg>
                    </div>
                    <div class="min-w-0">
                        <p class="text-sm font-bold text-brand">Current Session</p>
                        <p class="text-[11px] text-brand-muted truncate">{{ request()->ip() }} · {{ substr(request()->userAgent() ?? 'Unknown', 0, 50) }}...</p>
                    </div>
                </div>
                <span class="px-2.5 py-1 bg-green-100 text-green-700 text-[10px] font-bold rounded-lg uppercase tracking-wider whitespace-nowrap shrink-0">Active Now</span>
            </div>

            <div class="p-4 rounded-lg bg-surface/50">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-gray-200 rounded-lg flex items-center justify-center text-brand-muted shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-brand-muted">Other Sessions</p>
                        <p class="text-[11px] text-brand-muted">No other active sessions found.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Danger Zone -->
        <div class="bg-white border border-red-100 rounded-xl p-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-9 h-9 bg-red-50 rounded-lg flex items-center justify-center text-red-500">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
                <div>
                    <h3 class="text-base font-bold text-red-600">Danger Zone</h3>
                    <p class="text-[11px] text-brand-muted">Irreversible actions. Proceed with caution.</p>
                </div>
            </div>
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 p-4 rounded-lg border border-red-100 bg-red-50/40">
                <div>
                    <p class="text-sm font-bold text-brand">Revoke All Sessions</p>
                    <p class="text-[11px] text-brand-muted mt-0.5">Signs you out of all devices. You will need to re-authenticate everywhere.</p>
                </div>
                <form action="{{ route('orchestrator.profile.revoke') }}" method="POST" onsubmit="return confirm('This will sign you out of ALL devices including this one. Continue?')">
                    @csrf
                    <button type="submit" class="px-5 py-2.5 bg-red-600 text-white rounded-lg text-xs font-bold hover:bg-red-700 transition-colors whitespace-nowrap">
                        Revoke All
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function profileManager() {
    return {
        tab: 'identity',
        avatarPreview: null,
        passwordValue: '',
        strength: { percent: 0, color: 'bg-gray-200', textColor: 'text-gray-400', label: '' },
        tabs: {
            identity: { label: 'Identity', icon: '<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>' },
            security: { label: 'Security', icon: '<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>' },
            notifications: { label: 'Notifications', icon: '<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>' },
            sessions: { label: 'Sessions', icon: '<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17H3a2 2 0 01-2-2V5a2 2 0 012-2h14a2 2 0 012 2v10a2 2 0 01-2 2h-2"/></svg>' }
        },
        previewAvatar(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (ev) => this.avatarPreview = ev.target.result;
                reader.readAsDataURL(file);
            }
        },
        checkStrength(val) {
            this.passwordValue = val;
            let score = 0;
            if (val.length >= 8) score += 25;
            if (val.length >= 12) score += 10;
            if (/[a-z]/.test(val) && /[A-Z]/.test(val)) score += 25;
            if (/[0-9]/.test(val)) score += 20;
            if (/[^a-zA-Z0-9]/.test(val)) score += 20;
            this.strength.percent = Math.min(score, 100);
            if (score < 30) { this.strength.color = 'bg-red-500'; this.strength.textColor = 'text-red-600'; this.strength.label = 'Weak'; }
            else if (score < 60) { this.strength.color = 'bg-amber-500'; this.strength.textColor = 'text-amber-600'; this.strength.label = 'Fair'; }
            else if (score < 85) { this.strength.color = 'bg-blue-500'; this.strength.textColor = 'text-blue-600'; this.strength.label = 'Good'; }
            else { this.strength.color = 'bg-green-500'; this.strength.textColor = 'text-green-600'; this.strength.label = 'Strong'; }
        }
    };
}
</script>

<style>
[x-cloak] { display: none !important; }
</style>
@endsection