@extends('admin.layout')
@section('title', 'User Management')
@section('content')

@php
$typeLabels = ['admin' => ['Admin', 'bg-brand text-accent'], 'driver' => ['Driver', 'bg-blue-50 text-blue-700'], 'customer' => ['Customer', 'bg-emerald-50 text-emerald-700']];
@endphp

<div x-data="userManager()" class="max-w-6xl mx-auto">

    @if(session('error'))
    <div class="mb-4 p-3.5 bg-red-50 border border-red-200 rounded-lg flex items-center gap-2.5">
        <svg class="w-4 h-4 text-red-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <p class="text-sm font-medium text-red-700">{{ session('error') }}</p>
    </div>
    @endif
    @if(session('success'))
    <div class="mb-4 p-3.5 bg-green-50 border border-green-200 rounded-lg flex items-center gap-2.5">
        <svg class="w-4 h-4 text-green-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <p class="text-sm font-medium text-green-700">{{ session('success') }}</p>
    </div>
    @endif

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-black text-brand tracking-tight">User Management</h2>
            <p class="text-sm text-brand-muted font-medium mt-0.5">Manage customers, drivers, and admin accounts.</p>
        </div>
        <button @click="openCreate()" class="px-5 py-2.5 bg-brand text-white rounded-lg text-xs font-bold hover:bg-brand-light transition-colors flex items-center gap-2 shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Add User
        </button>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <div class="bg-white border border-gray-100 rounded-xl p-4 flex items-center gap-3.5">
            <div class="w-10 h-10 bg-accent/10 rounded-lg flex items-center justify-center text-accent shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
            </div>
            <div>
                <p class="text-lg font-black text-brand">{{ number_format($stats['total_nodes'] ?? 0) }}</p>
                <p class="text-[10px] font-bold text-brand-muted uppercase tracking-wider">Total Users</p>
            </div>
        </div>
        <div class="bg-white border border-gray-100 rounded-xl p-4 flex items-center gap-3.5">
            <div class="w-10 h-10 bg-green-50 rounded-lg flex items-center justify-center text-green-600 shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
            </div>
            <div>
                <p class="text-lg font-black text-brand">{{ number_format($stats['active_sessions'] ?? 0) }}</p>
                <p class="text-[10px] font-bold text-brand-muted uppercase tracking-wider">Active (24h)</p>
            </div>
        </div>
        <div class="bg-white border border-gray-100 rounded-xl p-4 flex items-center gap-3.5">
            <div class="w-10 h-10 bg-red-50 rounded-lg flex items-center justify-center text-red-600 shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
            </div>
            <div>
                <p class="text-lg font-black text-brand">{{ number_format($stats['revoked_access'] ?? 0) }}</p>
                <p class="text-[10px] font-bold text-brand-muted uppercase tracking-wider">Suspended</p>
            </div>
        </div>
    </div>

    {{-- Table --}}
    <div class="bg-white border border-gray-100 rounded-xl overflow-hidden">
        {{-- Toolbar --}}
        <div class="p-4 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-surface/20">
            <form action="{{ route('orchestrator.users') }}" method="GET" class="flex items-center gap-3 flex-1">
                <select name="type" onchange="this.form.submit()" class="bg-white border border-gray-200 rounded-lg px-3 py-2 text-xs font-bold outline-none focus:ring-2 focus:ring-accent/20">
                    <option value="">All Types</option>
                    <option value="admin" {{ request('type') == 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="driver" {{ request('type') == 'driver' ? 'selected' : '' }}>Driver</option>
                    <option value="customer" {{ request('type') == 'customer' ? 'selected' : '' }}>Customer</option>
                </select>
                <div class="relative flex-1 max-w-xs">
                    <svg class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search name, email, phone..." class="w-full bg-white border border-gray-200 rounded-lg pl-9 pr-3 py-2 text-sm outline-none focus:ring-2 focus:ring-accent/20 transition-shadow">
                </div>
                @if(request('search') || request('type'))
                <a href="{{ route('orchestrator.users') }}" class="text-xs font-bold text-brand-muted hover:text-brand transition-colors shrink-0">Clear</a>
                @endif
            </form>
            <p class="text-[11px] font-bold text-brand-muted whitespace-nowrap">{{ $users->firstItem() ?? 0 }}–{{ $users->lastItem() ?? 0 }} of {{ $users->total() }}</p>
        </div>

        {{-- List --}}
        <div class="divide-y divide-gray-50">
            @forelse($users as $user)
            @php
            $initial = strtoupper(substr($user->name, 0, 1));
            $typeInfo = $typeLabels[$user->user_type] ?? ['User', 'bg-gray-50 text-gray-600'];
            @endphp
            <div class="px-4 py-3.5 flex items-center gap-3.5 hover:bg-surface/20 transition-colors group">
                <div class="w-9 h-9 rounded-lg flex items-center justify-center font-bold text-xs uppercase shrink-0 {{ $user->user_type === 'admin' ? 'bg-brand text-accent' : 'bg-surface text-brand-muted' }}">
                    {{ $initial }}{{ strtoupper(substr($user->name, -1, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2">
                        <span class="text-sm font-bold text-brand truncate">{{ $user->name }}</span>
                        <span class="px-1.5 py-0.5 rounded text-[9px] font-bold uppercase tracking-wider shrink-0 {{ $typeInfo[1] }}">{{ $typeInfo[0] }}</span>
                    </div>
                    <div class="flex items-center gap-2 text-[11px] text-brand-muted">
                        <span>{{ $user->email }}</span>
                        <span class="text-gray-300">|</span>
                        <span>{{ $user->phone }}</span>
                    </div>
                </div>
                <div class="flex items-center gap-2.5 shrink-0">
                    @if($user->is_active)
                    <span class="flex items-center gap-1.5 text-[10px] font-bold text-green-600">
                        <span class="w-1.5 h-1.5 bg-green-500 rounded-full"></span> Active
                    </span>
                    @else
                    <span class="flex items-center gap-1.5 text-[10px] font-bold text-red-500">
                        <span class="w-1.5 h-1.5 bg-red-500 rounded-full"></span> Suspended
                    </span>
                    @endif
                    <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                        <form action="{{ route('orchestrator.users.toggle', $user->id) }}" method="POST" class="inline">
                            @csrf @method('PATCH')
                            <button type="submit" class="w-7 h-7 rounded-lg flex items-center justify-center {{ $user->is_active ? 'text-amber-500 hover:bg-amber-50' : 'text-green-500 hover:bg-green-50' }} transition-colors" title="{{ $user->is_active ? 'Suspend' : 'Activate' }}">
                                @if($user->is_active)
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                @else
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                @endif
                            </button>
                        </form>
                        <button @click="openEdit('{{ $user->id }}', '{{ addslashes($user->name) }}', '{{ $user->email }}', '{{ $user->phone }}')" class="w-7 h-7 rounded-lg flex items-center justify-center text-brand-muted hover:text-brand hover:bg-surface transition-colors" title="Edit">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </button>
                        <button @click="confirmDelete('{{ $user->id }}', '{{ addslashes($user->name) }}')" class="w-7 h-7 rounded-lg flex items-center justify-center text-gray-300 hover:text-red-500 hover:bg-red-50 transition-colors" title="Delete">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </div>
                </div>
            </div>
            @empty
            <div class="flex flex-col items-center justify-center py-16 text-brand-muted">
                <svg class="w-14 h-14 mb-3 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                <p class="text-sm font-bold">No users found</p>
                <p class="text-xs mt-1">Try adjusting your search or filters.</p>
            </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        @if($users->hasPages())
        <div class="px-4 py-3 border-t border-gray-100 bg-surface/20">
            {{ $users->appends(request()->except('page'))->links() }}
        </div>
        @endif
    </div>

    {{-- Create Modal --}}
    <div x-show="showCreate" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-brand/50 backdrop-blur-sm" @click="showCreate = false"></div>
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg relative z-10" @click.outside="showCreate = false">
            <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 bg-accent/10 rounded-lg flex items-center justify-center text-accent">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-brand">Add User</h3>
                        <p class="text-xs text-brand-muted">Create a new user account.</p>
                    </div>
                </div>
                <button @click="showCreate = false" class="w-7 h-7 bg-surface rounded-lg flex items-center justify-center text-brand-muted hover:text-brand transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form action="{{ route('orchestrator.users.store') }}" method="POST" class="p-6 space-y-4">
                @csrf
                <div>
                    <label class="text-[10px] font-bold text-brand-muted uppercase tracking-wider mb-1.5 block">Full Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name" required placeholder="e.g. John Doe" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-medium outline-none focus:ring-2 focus:ring-accent/20 transition-shadow">
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="text-[10px] font-bold text-brand-muted uppercase tracking-wider mb-1.5 block">Email <span class="text-red-500">*</span></label>
                        <input type="email" name="email" required placeholder="john@example.com" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-medium outline-none focus:ring-2 focus:ring-accent/20 transition-shadow">
                    </div>
                    <div>
                        <label class="text-[10px] font-bold text-brand-muted uppercase tracking-wider mb-1.5 block">Phone <span class="text-red-500">*</span></label>
                        <input type="text" name="phone" required placeholder="+233 xx xxx xxxx" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-medium outline-none focus:ring-2 focus:ring-accent/20 transition-shadow">
                    </div>
                </div>
                <div>
                    <label class="text-[10px] font-bold text-brand-muted uppercase tracking-wider mb-1.5 block">Password <span class="text-red-500">*</span></label>
                    <input type="password" name="password" required placeholder="Min 8 characters" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-medium outline-none focus:ring-2 focus:ring-accent/20 transition-shadow">
                </div>
                <div>
                    <label class="text-[10px] font-bold text-brand-muted uppercase tracking-wider mb-1.5 block">User Type <span class="text-red-500">*</span></label>
                    <select name="user_type" required class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-medium outline-none focus:ring-2 focus:ring-accent/20 transition-shadow">
                        <option value="customer">Customer</option>
                        <option value="driver">Driver</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                <div class="flex justify-end gap-2 pt-2 border-t border-gray-100">
                    <button type="button" @click="showCreate = false" class="px-4 py-2 text-xs font-bold text-brand-muted hover:text-brand transition-colors">Cancel</button>
                    <button type="submit" class="px-5 py-2 bg-brand text-white rounded-lg text-xs font-bold hover:bg-brand-light transition-colors">Create User</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Edit Modal --}}
    <div x-show="showEdit" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-brand/50 backdrop-blur-sm" @click="showEdit = false"></div>
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg relative z-10" @click.outside="showEdit = false">
            <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 bg-blue-50 rounded-lg flex items-center justify-center text-blue-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-brand">Edit User</h3>
                        <p class="text-xs text-brand-muted">Update user profile information.</p>
                    </div>
                </div>
                <button @click="showEdit = false" class="w-7 h-7 bg-surface rounded-lg flex items-center justify-center text-brand-muted hover:text-brand transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form :action="`/orchestrator/users/${editId}`" method="POST" class="p-6 space-y-4">
                @csrf @method('PUT')
                <div>
                    <label class="text-[10px] font-bold text-brand-muted uppercase tracking-wider mb-1.5 block">Full Name</label>
                    <input type="text" name="name" x-model="editName" required class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-medium outline-none focus:ring-2 focus:ring-accent/20 transition-shadow">
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="text-[10px] font-bold text-brand-muted uppercase tracking-wider mb-1.5 block">Email</label>
                        <input type="email" name="email" x-model="editEmail" required class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-medium outline-none focus:ring-2 focus:ring-accent/20 transition-shadow">
                    </div>
                    <div>
                        <label class="text-[10px] font-bold text-brand-muted uppercase tracking-wider mb-1.5 block">Phone</label>
                        <input type="text" name="phone" x-model="editPhone" required class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-medium outline-none focus:ring-2 focus:ring-accent/20 transition-shadow">
                    </div>
                </div>
                <div class="flex justify-end gap-2 pt-2 border-t border-gray-100">
                    <button type="button" @click="showEdit = false" class="px-4 py-2 text-xs font-bold text-brand-muted hover:text-brand transition-colors">Cancel</button>
                    <button type="submit" class="px-5 py-2 bg-brand text-white rounded-lg text-xs font-bold hover:bg-brand-light transition-colors">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Multi-step Delete Confirmation --}}
    <div x-show="deleteStep > 0" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-brand/60 backdrop-blur-sm" @click="closeDelete()"></div>
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-md relative z-10 overflow-hidden" @click.outside="closeDelete()">
            <template x-if="deleteStep === 1">
                <div class="p-6">
                    <div class="w-14 h-14 bg-red-50 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-7 h-7 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </div>
                    <h3 class="text-lg font-bold text-brand text-center mb-2">Delete User?</h3>
                    <p class="text-sm text-brand-muted text-center mb-6">You are about to permanently delete <strong class="text-brand" x-text="deleteLabel"></strong>. This action cannot be undone.</p>
                    <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 mb-6">
                        <div class="flex items-start gap-2.5">
                            <svg class="w-5 h-5 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            <p class="text-xs font-bold text-amber-800">All associated data will be permanently removed. Please confirm you understand.</p>
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <button type="button" @click="closeDelete()" class="flex-1 px-4 py-2.5 bg-surface text-brand-muted rounded-lg text-xs font-bold hover:bg-gray-100">Cancel</button>
                        <button type="button" @click="deleteStep = 2" class="flex-1 px-4 py-2.5 bg-red-600 text-white rounded-lg text-xs font-bold hover:bg-red-700">Continue</button>
                    </div>
                </div>
            </template>
            <template x-if="deleteStep === 2">
                <div class="p-6">
                    <div class="w-14 h-14 bg-red-50 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-7 h-7 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <h3 class="text-lg font-bold text-brand text-center mb-2">Final Confirmation</h3>
                    <p class="text-sm text-brand-muted text-center mb-6">Type <strong class="text-red-600 font-mono bg-red-50 px-2 py-0.5 rounded">DELETE</strong> to confirm permanent removal.</p>
                    <input type="text" x-model="deleteConfirm" @input="deleteConfirm = deleteConfirm.toUpperCase()" placeholder="Type DELETE to confirm" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-bold text-center outline-none focus:ring-2 focus:ring-red-300 transition-shadow mb-6 uppercase tracking-widest">
                    <div class="flex gap-2">
                        <button type="button" @click="deleteStep = 1" class="flex-1 px-4 py-2.5 bg-surface text-brand-muted rounded-lg text-xs font-bold hover:bg-gray-100">Back</button>
                        <button type="button" @click="executeDelete()" :disabled="deleteConfirm !== 'DELETE'" class="flex-1 px-4 py-2.5 rounded-lg text-xs font-bold transition-colors" :class="deleteConfirm === 'DELETE' ? 'bg-red-600 text-white hover:bg-red-700' : 'bg-gray-100 text-gray-400 cursor-not-allowed'">Confirm Delete</button>
                    </div>
                </div>
            </template>
        </div>
    </div>
</div>

<script>
function userManager() {
    return {
        showCreate: false,
        showEdit: false,
        editId: '',
        editName: '',
        editEmail: '',
        editPhone: '',
        deleteStep: 0,
        deleteId: '',
        deleteLabel: '',
        deleteConfirm: '',
        openCreate() { this.showCreate = true; },
        openEdit(id, name, email, phone) {
            this.editId = id;
            this.editName = name;
            this.editEmail = email;
            this.editPhone = phone;
            this.showEdit = true;
        },
        confirmDelete(id, label) {
            this.deleteId = id;
            this.deleteLabel = label;
            this.deleteStep = 1;
            this.deleteConfirm = '';
        },
        closeDelete() {
            this.deleteStep = 0;
            this.deleteConfirm = '';
        },
        executeDelete() {
            if (this.deleteConfirm !== 'DELETE') return;
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '/orchestrator/users/' + this.deleteId;
            const csrf = document.createElement('input');
            csrf.type = 'hidden'; csrf.name = '_token'; csrf.value = '{{ csrf_token() }}';
            form.appendChild(csrf);
            const method = document.createElement('input');
            method.type = 'hidden'; method.name = '_method'; method.value = 'DELETE';
            form.appendChild(method);
            document.body.appendChild(form);
            form.submit();
        }
    };
}
</script>
<style>[x-cloak] { display: none !important; }</style>
@endsection