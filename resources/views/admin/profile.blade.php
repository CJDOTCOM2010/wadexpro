@extends('admin.layout')
@section('title', 'My Profile & Account Settings')
@section('content')

<!-- Error Alert -->
@if(session('error'))
<div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl flex items-center gap-3">
    <svg class="w-5 h-5 text-red-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
    <p class="text-sm font-medium text-red-700">{{ session('error') }}</p>
</div>
@endif

@php
    $admin = auth('admin')->user();
    $initials = strtoupper(substr($admin->first_name ?? $admin->name ?? 'A', 0, 1) . substr($admin->last_name ?? '', 0, 1)) ?: 'SA';
    $prefs = json_decode($admin->notification_preferences ?? '{}', true) ?? [];
@endphp

<div x-data="{ tab: 'identity' }">
    <!-- Page Header -->
    <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center gap-5">
            <!-- Avatar -->
            <div class="relative">
                <div class="w-20 h-20 rounded-2xl bg-brand text-accent flex items-center justify-center font-black text-3xl shadow-xl overflow-hidden">
                    @if($admin->avatar_url)
                        <img src="{{ $admin->avatar_url }}" class="w-full h-full object-cover">
                    @else
                        {{ $initials }}
                    @endif
                </div>
                <span class="absolute -bottom-1 -right-1 w-5 h-5 bg-green-500 border-2 border-white rounded-full"></span>
            </div>
            <div>
                <h2 class="text-2xl font-black text-brand tracking-tight">{{ $admin->name ?? 'Admin Account' }}</h2>
                <p class="text-brand-muted font-medium text-sm mt-0.5">{{ ucfirst($admin->level ?? $admin->role ?? 'Admin') }} · {{ $admin->email }}</p>
                <span class="inline-flex items-center gap-1.5 mt-1 px-2 py-0.5 bg-green-100 text-green-700 text-[10px] font-black rounded-lg uppercase tracking-wider">
                    <span class="w-1.5 h-1.5 bg-green-500 rounded-full inline-block"></span> Session Active
                </span>
            </div>
        </div>
        <div class="text-right hidden md:block">
            <p class="text-[10px] font-black text-brand-muted uppercase tracking-widest">Account Created</p>
            <p class="text-sm font-bold text-brand">{{ $admin->created_at?->format('d M Y') ?? '—' }}</p>
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
    <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-xl text-green-700 text-sm font-bold flex items-center gap-3">
        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        {{ session('success') }}
    </div>
    @endif
    @if($errors->any())
    <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl text-red-700 text-sm font-bold">
        @foreach($errors->all() as $error)<p>· {{ $error }}</p>@endforeach
    </div>
    @endif

    <!-- Tabs -->
    <div class="flex gap-2 mb-8 overflow-x-auto pb-2 border-b border-gray-100">
        @foreach(['identity'=>'Identity & Info','security'=>'Security','notifications'=>'Notifications','sessions'=>'Sessions & Access'] as $k=>$v)
        <button @click="tab='{{ $k }}'" :class="tab==='{{ $k }}' ? 'bg-brand text-white shadow-lg' : 'bg-white text-brand-muted hover:bg-surface border border-gray-100'" class="px-5 py-2.5 rounded-xl font-black text-xs uppercase tracking-widest transition-all whitespace-nowrap">{{ $v }}</button>
        @endforeach
    </div>

    <!-- IDENTITY TAB -->
    <div x-show="tab==='identity'">
        <form action="{{ route('orchestrator.profile.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Avatar Upload Card -->
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-8 flex flex-col items-center gap-5">
                    <div class="w-32 h-32 rounded-2xl bg-surface flex items-center justify-center font-black text-5xl text-brand-muted overflow-hidden shadow-inner">
                        @if($admin->avatar_url)
                            <img src="{{ $admin->avatar_url }}" class="w-full h-full object-cover">
                        @else
                            {{ $initials }}
                        @endif
                    </div>
                    <div class="text-center">
                        <p class="font-black text-sm text-brand">Profile Avatar</p>
                        <p class="text-[10px] text-gray-400 mt-1 italic">JPG, PNG or WebP · Max 2MB</p>
                    </div>
                    <label class="w-full cursor-pointer">
                        <input type="file" name="avatar" accept="image/*" class="sr-only">
                        <div class="w-full py-3 px-4 border-2 border-dashed border-gray-200 rounded-xl text-center text-xs font-bold text-brand-muted hover:border-brand hover:text-brand transition">
                            Click to Upload New Photo
                        </div>
                    </label>
                </div>

                <!-- Profile Form -->
                <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-100 shadow-sm p-8 space-y-5">
                    <h3 class="text-lg font-black text-brand">Personal Identity</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-brand-muted uppercase tracking-widest">First Name</label>
                            <input type="text" name="first_name" value="{{ old('first_name', $admin->first_name) }}" class="w-full bg-surface border border-gray-100 rounded-xl px-4 py-3 text-sm font-bold focus:ring-2 focus:ring-accent outline-none" placeholder="John">
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-brand-muted uppercase tracking-widest">Last Name</label>
                            <input type="text" name="last_name" value="{{ old('last_name', $admin->last_name) }}" class="w-full bg-surface border border-gray-100 rounded-xl px-4 py-3 text-sm font-bold focus:ring-2 focus:ring-accent outline-none" placeholder="Doe">
                        </div>
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-brand-muted uppercase tracking-widest">Display Name</label>
                        <input type="text" name="name" value="{{ old('name', $admin->name) }}" required class="w-full bg-surface border border-gray-100 rounded-xl px-4 py-3 text-sm font-bold focus:ring-2 focus:ring-accent outline-none" placeholder="Admin Display Name">
                        <p class="text-[9px] text-gray-400 italic">This is shown in the dashboard header and activity logs.</p>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-brand-muted uppercase tracking-widest">Email Address</label>
                            <input type="email" name="email" value="{{ old('email', $admin->email) }}" required class="w-full bg-surface border border-gray-100 rounded-xl px-4 py-3 text-sm font-bold focus:ring-2 focus:ring-accent outline-none" placeholder="admin@wadexpro.com">
                            <p class="text-[9px] text-gray-400 italic">Used for system alerts and login authentication.</p>
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-brand-muted uppercase tracking-widest">Phone Number</label>
                            <input type="text" name="phone" value="{{ old('phone', $admin->phone) }}" class="w-full bg-surface border border-gray-100 rounded-xl px-4 py-3 text-sm font-bold focus:ring-2 focus:ring-accent outline-none" placeholder="+233 xx xxx xxxx">
                            <p class="text-[9px] text-gray-400 italic">Used for 2FA and recovery fallback.</p>
                        </div>
                    </div>
                    <!-- Read-only fields -->
                    <div class="grid grid-cols-2 gap-4 pt-4 border-t border-gray-100">
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-brand-muted uppercase tracking-widest">Account Type</label>
                            <div class="w-full bg-surface border border-gray-100 rounded-xl px-4 py-3 text-sm font-bold text-brand-muted cursor-not-allowed">{{ ucfirst($admin->level ?? $admin->role ?? 'Admin') }}</div>
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-brand-muted uppercase tracking-widest">Account ID</label>
                            <div class="w-full bg-surface border border-gray-100 rounded-xl px-4 py-3 text-xs font-bold text-brand-muted font-mono cursor-not-allowed truncate">{{ $admin->id }}</div>
                        </div>
                    </div>
                    <div class="flex justify-end pt-2">
                        <button type="submit" class="px-8 py-4 bg-brand text-white font-black rounded-xl hover:bg-brand-light transition shadow-lg shadow-brand/20 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                            Save Identity
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- SECURITY TAB -->
    <div x-show="tab==='security'" class="space-y-6">
        <!-- Change Password -->
        <form action="{{ route('orchestrator.profile.password') }}" method="POST">
            @csrf
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-8 space-y-5">
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-10 h-10 rounded-xl bg-red-50 flex items-center justify-center text-red-500">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-black text-brand">Change Password</h3>
                        <p class="text-[11px] text-brand-muted">Update your authentication credentials.</p>
                    </div>
                </div>
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-brand-muted uppercase tracking-widest">Current Password</label>
                    <input type="password" name="current_password" required class="w-full bg-surface border border-gray-100 rounded-xl px-4 py-3 text-sm font-bold focus:ring-2 focus:ring-red-200 outline-none" placeholder="Enter your current password">
                    <p class="text-[9px] text-gray-400 italic">Required to authorize this change.</p>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-brand-muted uppercase tracking-widest">New Password</label>
                        <input type="password" name="password" required class="w-full bg-surface border border-gray-100 rounded-xl px-4 py-3 text-sm font-bold focus:ring-2 focus:ring-red-200 outline-none" placeholder="Minimum 8 characters">
                        <p class="text-[9px] text-gray-400 italic">Must contain uppercase, number, and symbol.</p>
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-brand-muted uppercase tracking-widest">Confirm New Password</label>
                        <input type="password" name="password_confirmation" required class="w-full bg-surface border border-gray-100 rounded-xl px-4 py-3 text-sm font-bold focus:ring-2 focus:ring-red-200 outline-none" placeholder="Repeat new password">
                    </div>
                </div>
                <div class="flex justify-end">
                    <button type="submit" class="px-8 py-4 bg-red-600 text-white font-black rounded-xl hover:bg-red-700 transition shadow-lg flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        Update Password
                    </button>
                </div>
            </div>
        </form>

        <!-- Security Overview -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-8">
            <h3 class="text-lg font-black text-brand mb-5">Security Overview</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="p-4 rounded-xl bg-green-50 border border-green-100">
                    <p class="text-[10px] font-black text-green-700 uppercase tracking-widest">Email Verified</p>
                    <p class="text-sm font-bold text-green-900 mt-1">{{ $admin->email_verified_at ? 'Verified · ' . $admin->email_verified_at->format('d M Y') : 'Not Verified' }}</p>
                </div>
                <div class="p-4 rounded-xl bg-surface border border-gray-100">
                    <p class="text-[10px] font-black text-brand-muted uppercase tracking-widest">Last Login</p>
                    <p class="text-sm font-bold text-brand mt-1">{{ $admin->updated_at?->diffForHumans() ?? '—' }}</p>
                </div>
                <div class="p-4 rounded-xl bg-surface border border-gray-100">
                    <p class="text-[10px] font-black text-brand-muted uppercase tracking-widest">Referral Code</p>
                    <p class="text-sm font-bold font-mono text-brand mt-1">{{ $admin->referral_code ?? '—' }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- NOTIFICATIONS TAB -->
    <div x-show="tab==='notifications'">
        <form action="{{ route('orchestrator.profile.notifications') }}" method="POST">
            @csrf
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-8 space-y-5">
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center text-amber-500">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-black text-brand">Notification Preferences</h3>
                        <p class="text-[11px] text-brand-muted">Control which system events alert you.</p>
                    </div>
                </div>
                @foreach([
                    'notify_new_tickets'     => ['New Support Tickets', 'Alert when a customer opens a new support ticket.'],
                    'notify_sos_alerts'      => ['SOS & Emergency Alerts', 'Urgent notification when a driver or passenger triggers an SOS.'],
                    'notify_new_users'       => ['New User Registrations', 'Alert when a new customer or driver registers.'],
                    'notify_failed_payments' => ['Failed Payments', 'Alert when a customer transaction fails or is reversed.'],
                    'notify_driver_flagged'  => ['Driver Flagged/Suspended', 'Alert when a driver is flagged by the security system.'],
                    'notify_low_drivers'     => ['Low Driver Availability', 'Alert when active driver count drops below threshold.'],
                ] as $key => [$label, $desc])
                <div class="flex items-center justify-between p-4 rounded-xl border border-gray-100 bg-surface/30">
                    <div>
                        <p class="font-black text-sm text-brand">{{ $label }}</p>
                        <p class="text-[10px] text-gray-400 mt-0.5 italic">{{ $desc }}</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer ml-4 flex-shrink-0">
                        <input type="hidden" name="{{ $key }}" value="0">
                        <input type="checkbox" name="{{ $key }}" value="1" class="sr-only peer" {{ ($prefs[$key] ?? false) ? 'checked' : '' }}>
                        <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-brand"></div>
                    </label>
                </div>
                @endforeach
                <div class="flex justify-end pt-4">
                    <button type="submit" class="px-8 py-4 bg-brand text-white font-black rounded-xl hover:bg-brand-light transition shadow-lg shadow-brand/20">Save Preferences</button>
                </div>
            </div>
        </form>
    </div>

    <!-- SESSIONS TAB -->
    <div x-show="tab==='sessions'" class="space-y-6">
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-8 space-y-5">
            <div class="flex items-center gap-3 mb-2">
                <div class="w-10 h-10 rounded-xl bg-brand/5 flex items-center justify-center text-brand">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17H3a2 2 0 01-2-2V5a2 2 0 012-2h14a2 2 0 012 2v10a2 2 0 01-2 2h-2"/></svg>
                </div>
                <div>
                    <h3 class="text-lg font-black text-brand">Active Sessions</h3>
                    <p class="text-[11px] text-brand-muted">Manage where your account is currently signed in.</p>
                </div>
            </div>
            <!-- Current session -->
            <div class="p-4 rounded-xl border-2 border-brand/20 bg-brand/5 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <svg class="w-8 h-8 text-brand" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17H3a2 2 0 01-2-2V5a2 2 0 012-2h14a2 2 0 012 2v10a2 2 0 01-2 2h-2"/></svg>
                    <div>
                        <p class="font-black text-sm text-brand">This Device (Current Session)</p>
                        <p class="text-[10px] text-brand-muted mt-0.5">{{ request()->ip() }} · {{ request()->userAgent() ? substr(request()->userAgent(), 0, 60) . '...' : 'Unknown' }}</p>
                    </div>
                </div>
                <span class="px-3 py-1 bg-green-100 text-green-700 text-[10px] font-black rounded-full uppercase">Active Now</span>
            </div>
        </div>

        <!-- Danger Zone -->
        <div class="bg-white rounded-2xl border border-red-100 shadow-sm p-8 space-y-5">
            <div class="flex items-center gap-3 mb-2">
                <div class="w-10 h-10 rounded-xl bg-red-50 flex items-center justify-center text-red-500">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
                <div>
                    <h3 class="text-lg font-black text-red-600">Danger Zone</h3>
                    <p class="text-[11px] text-brand-muted">These actions are irreversible. Proceed with caution.</p>
                </div>
            </div>
            <div class="flex items-start justify-between gap-6 p-5 rounded-xl border border-red-100 bg-red-50/40">
                <div>
                    <p class="font-black text-sm text-brand">Revoke All Sessions</p>
                    <p class="text-[10px] text-gray-500 mt-1">Signs you out of all devices and invalidates all API tokens. You will need to re-authenticate everywhere.</p>
                </div>
                <form action="{{ route('orchestrator.profile.revoke') }}" method="POST" onsubmit="return confirm('CRITICAL: This will sign you out of all devices. Continue?')">
                    @csrf
                    <button type="submit" class="px-5 py-3 bg-red-600 text-white font-black text-xs rounded-xl hover:bg-red-700 transition whitespace-nowrap shadow-lg">
                        Revoke All
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
