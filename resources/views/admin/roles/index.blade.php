@extends('admin.layout')
@section('title', 'Roles & Permissions')
@section('content')

@php
$totalRoles = $roles->count();
$totalPerms = 0;
foreach ($permissions as $p) $totalPerms += $p->count();
$systemRoles = $roles->where('is_system', true)->count();
@endphp

<div x-data="{ tab: 'roles', showNewRole: false, showNewPerm: false, editRoleId: null }" class="max-w-6xl mx-auto">

    {{-- Header --}}
    <div class="bg-gradient-to-r from-brand via-brand-light to-brand rounded-xl overflow-hidden mb-8 relative">
        <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_top_right,_rgba(248,184,3,0.12),transparent_50%)]"></div>
        <div class="relative z-10 px-6 py-7 sm:px-8 sm:py-8">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <div class="flex items-center gap-2.5 mb-2">
                        <div class="w-8 h-8 bg-accent/20 rounded-lg flex items-center justify-center">
                            <svg class="w-4 h-4 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        </div>
                        <h2 class="text-xl sm:text-2xl font-black text-white tracking-tight">Access Control Matrix</h2>
                    </div>
                    <p class="text-white/60 text-sm font-medium">Create roles, assign granular permissions per module, and control what staff can access.</p>
                </div>
                <div class="flex items-center gap-2">
                    <button @click="showNewPerm = true" class="px-4 py-2.5 bg-white/10 text-white border border-white/20 rounded-lg text-xs font-bold hover:bg-white/20 transition-colors flex items-center gap-2">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Permission
                    </button>
                    <button @click="showNewRole = true" class="px-4 py-2.5 bg-accent text-brand rounded-lg text-xs font-bold hover:bg-accent-hover transition-colors flex items-center gap-2">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        New Role
                    </button>
                </div>
            </div>

            {{-- Stats --}}
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mt-6">
                <div class="bg-white/10 rounded-lg p-3">
                    <p class="text-2xl font-black text-white">{{ $totalRoles }}</p>
                    <p class="text-[10px] font-bold text-white/50 uppercase tracking-wider">Total Roles</p>
                </div>
                <div class="bg-white/10 rounded-lg p-3">
                    <p class="text-2xl font-black text-white">{{ $totalPerms }}</p>
                    <p class="text-[10px] font-bold text-white/50 uppercase tracking-wider">Permissions</p>
                </div>
                <div class="bg-white/10 rounded-lg p-3">
                    <p class="text-2xl font-black text-white">{{ $systemRoles }}</p>
                    <p class="text-[10px] font-bold text-white/50 uppercase tracking-wider">System Roles</p>
                </div>
                <div class="bg-white/10 rounded-lg p-3">
                    <p class="text-2xl font-black text-white">{{ $permissions->count() }}</p>
                    <p class="text-[10px] font-bold text-white/50 uppercase tracking-wider">Modules</p>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
    <div class="mb-6 p-3.5 bg-green-50 border border-green-200 rounded-lg flex items-center gap-2.5">
        <svg class="w-4 h-4 text-green-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <p class="text-sm font-medium text-green-700">{{ session('success') }}</p>
    </div>
    @endif
    @if(session('error'))
    <div class="mb-6 p-3.5 bg-red-50 border border-red-200 rounded-lg flex items-center gap-2.5">
        <svg class="w-4 h-4 text-red-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <p class="text-sm font-medium text-red-700">{{ session('error') }}</p>
    </div>
    @endif

    {{-- Tabs --}}
    <div class="flex gap-1 mb-8 p-1 bg-surface rounded-lg w-fit">
        <button @click="tab='roles'" :class="tab==='roles' ? 'bg-white text-brand shadow-sm' : 'text-brand-muted hover:text-brand'" class="px-5 py-2 rounded-lg text-xs font-bold transition-all flex items-center gap-2">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
            Roles ({{ $totalRoles }})
        </button>
        <button @click="tab='permissions'" :class="tab==='permissions' ? 'bg-white text-brand shadow-sm' : 'text-brand-muted hover:text-brand'" class="px-5 py-2 rounded-lg text-xs font-bold transition-all flex items-center gap-2">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
            All Permissions ({{ $totalPerms }})
        </button>
    </div>

    {{-- ═══ ROLES TAB ═══ --}}
    <div x-show="tab==='roles'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
        <div class="space-y-4">
            @foreach($roles as $role)
            <div class="bg-white border border-gray-100 rounded-xl overflow-hidden" x-data="{ open: false }">
                {{-- Role Header --}}
                <div class="flex items-center justify-between px-5 py-4 cursor-pointer hover:bg-surface/20 transition-colors" @click="open = !open">
                    <div class="flex items-center gap-3.5 min-w-0">
                        <div class="w-10 h-10 rounded-lg flex items-center justify-center shrink-0 {{ $role->is_system ? 'bg-amber-50 text-amber-600' : 'bg-accent/10 text-accent' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        </div>
                        <div class="min-w-0">
                            <div class="flex items-center gap-2">
                                <h3 class="text-sm font-bold text-brand truncate">{{ $role->label ?? $role->name }}</h3>
                                @if($role->is_system)
                                <span class="px-2 py-0.5 bg-amber-50 text-amber-600 text-[9px] font-bold rounded uppercase tracking-wider shrink-0">System</span>
                                @endif
                            </div>
                            <p class="text-[11px] text-brand-muted truncate">{{ $role->description ?? 'No description' }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2.5 shrink-0">
                        <span class="text-[10px] font-bold text-brand-muted bg-surface px-2.5 py-1 rounded-lg">{{ $role->users_count }} staff</span>
                        <span class="text-[10px] font-bold text-accent bg-accent/10 px-2.5 py-1 rounded-lg">{{ $role->permissions->count() }} perms</span>
                        <svg class="w-4 h-4 text-gray-400 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </div>
                </div>

                {{-- Permission Matrix --}}
                <div x-show="open" x-collapse>
                    <form action="{{ route('orchestrator.roles.update', $role->id) }}" method="POST">
                        @csrf @method('PUT')
                        <div class="border-t border-gray-100 p-5 space-y-6">
                            @foreach($permissions as $module => $perms)
                            <div>
                                <div class="flex items-center gap-2.5 mb-3">
                                    <div class="flex-1 flex items-center gap-2.5">
                                        <span class="w-1 h-1 bg-accent rounded-full"></span>
                                        <h4 class="text-[10px] font-bold text-brand uppercase tracking-wider">{{ $module }}</h4>
                                        <span class="text-[9px] text-brand-muted">({{ $perms->count() }})</span>
                                        <div class="flex-1 h-px bg-gray-100"></div>
                                    </div>
                                    <label class="flex items-center gap-1.5 cursor-pointer text-[9px] font-bold text-brand-muted hover:text-brand transition-colors">
                                        <input type="checkbox" class="w-3 h-3 rounded border-gray-300 text-accent focus:ring-accent/30"
                                            onchange="document.querySelectorAll('.perm-{{ Str::slug($module) }}').forEach(cb => cb.checked = this.checked)">
                                        Select All
                                    </label>
                                </div>
                                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-1.5">
                                    @foreach($perms as $perm)
                                    <label class="flex items-center gap-2.5 px-3 py-2 rounded-lg cursor-pointer group hover:bg-accent/5 transition-colors {{ $role->permissions->contains('id', $perm->id) ? 'bg-accent/[0.04]' : '' }}">
                                        <input type="checkbox" name="permissions[]" value="{{ $perm->id }}"
                                            class="perm-{{ Str::slug($module) }} w-3.5 h-3.5 rounded border-gray-300 text-accent focus:ring-accent/30"
                                            {{ $role->permissions->contains('id', $perm->id) ? 'checked' : '' }}>
                                        <div class="min-w-0">
                                            <span class="text-xs font-bold text-brand group-hover:text-accent transition-colors block truncate">{{ $perm->label ?? $perm->name }}</span>
                                            <span class="text-[9px] text-gray-400 font-mono block truncate">{{ $perm->name }}</span>
                                        </div>
                                    </label>
                                    @endforeach
                                </div>
                            </div>
                            @endforeach
                        </div>
                        <div class="px-5 py-3.5 bg-surface/30 border-t border-gray-100 flex items-center justify-between">
                            <div>
                                @unless($role->is_system)
                                <form action="{{ route('orchestrator.roles.destroy', $role->id) }}" method="POST" class="inline" onsubmit="return confirm('Permanently delete this role and revoke it from all assigned staff?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="px-3.5 py-2 text-[10px] font-bold text-red-500 bg-red-50 hover:bg-red-100 rounded-lg transition-colors">Delete Role</button>
                                </form>
                                @endunless
                            </div>
                            <button type="submit" class="px-5 py-2 bg-brand text-white text-xs font-bold rounded-lg hover:bg-brand-light transition-colors">Save Permissions</button>
                        </div>
                    </form>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- ═══ PERMISSIONS TAB ═══ --}}
    <div x-show="tab==='permissions'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
        <div class="space-y-6">
            @foreach($permissions as $module => $perms)
            <div class="bg-white border border-gray-100 rounded-xl overflow-hidden">
                <div class="px-5 py-3.5 border-b border-gray-100 flex items-center justify-between bg-surface/30">
                    <div class="flex items-center gap-2.5">
                        <span class="w-2 h-2 bg-accent rounded-full"></span>
                        <h3 class="text-xs font-bold text-brand uppercase tracking-wider">{{ $module }}</h3>
                        <span class="text-[10px] font-bold text-brand-muted bg-white px-2 py-0.5 rounded-lg border border-gray-100">{{ $perms->count() }} permissions</span>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="bg-surface/20">
                                <th class="text-left px-5 py-3 text-[10px] font-bold text-brand-muted uppercase tracking-wider w-1/3">Permission Key</th>
                                <th class="text-left px-5 py-3 text-[10px] font-bold text-brand-muted uppercase tracking-wider">Description</th>
                                <th class="text-center px-5 py-3 text-[10px] font-bold text-brand-muted uppercase tracking-wider">Roles</th>
                                <th class="text-right px-5 py-3 text-[10px] font-bold text-brand-muted uppercase tracking-wider">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($perms as $perm)
                            <tr class="hover:bg-surface/20 transition-colors">
                                <td class="px-5 py-3">
                                    <code class="text-[11px] font-mono font-bold text-brand bg-surface/50 px-2 py-0.5 rounded">{{ $perm->name }}</code>
                                </td>
                                <td class="px-5 py-3 text-xs text-brand-muted">{{ $perm->label ?? '—' }}</td>
                                <td class="px-5 py-3 text-center">
                                    <span class="inline-flex items-center justify-center min-w-[28px] h-7 px-2 rounded-full bg-accent/10 text-accent text-[11px] font-black">{{ $perm->roles->count() }}</span>
                                </td>
                                <td class="px-5 py-3 text-right">
                                    <form action="{{ route('orchestrator.permissions.destroy', $perm->id) }}" method="POST" class="inline" onsubmit="return confirm('Remove this permission from all roles?');">
                                        @csrf @method('DELETE')
                                        <button class="w-7 h-7 rounded-lg text-gray-300 hover:text-red-500 hover:bg-red-50 transition-all flex items-center justify-center" title="Delete">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- ═══ NEW ROLE MODAL ═══ --}}
    <div x-show="showNewRole" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-brand/50 backdrop-blur-sm" @click="showNewRole = false"></div>
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg relative z-10" @click.outside="showNewRole = false">
            <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 bg-accent/10 rounded-lg flex items-center justify-center text-accent">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-brand">Create New Role</h3>
                        <p class="text-xs text-brand-muted">Define a new access level for staff.</p>
                    </div>
                </div>
                <button @click="showNewRole = false" class="w-7 h-7 bg-surface rounded-lg flex items-center justify-center text-brand-muted hover:text-brand transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form action="{{ route('orchestrator.roles.store') }}" method="POST" class="p-6 space-y-4">
                @csrf
                <div>
                    <label class="text-[10px] font-bold text-brand-muted uppercase tracking-wider mb-1.5 block">Role Identifier <span class="text-red-500">*</span></label>
                    <input type="text" name="name" required placeholder="e.g. fleet_manager" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-medium outline-none focus:ring-2 focus:ring-accent/20 transition-shadow">
                    <p class="text-[10px] text-brand-muted mt-1">Lowercase and underscores only. Cannot be changed later.</p>
                </div>
                <div>
                    <label class="text-[10px] font-bold text-brand-muted uppercase tracking-wider mb-1.5 block">Display Name</label>
                    <input type="text" name="label" placeholder="e.g. Fleet Manager" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-medium outline-none focus:ring-2 focus:ring-accent/20 transition-shadow">
                </div>
                <div>
                    <label class="text-[10px] font-bold text-brand-muted uppercase tracking-wider mb-1.5 block">Description</label>
                    <textarea name="description" rows="2" placeholder="What this role is responsible for..." class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-medium outline-none focus:ring-2 focus:ring-accent/20 transition-shadow resize-none"></textarea>
                </div>
                <div class="flex justify-end gap-2 pt-3 border-t border-gray-100">
                    <button type="button" @click="showNewRole = false" class="px-4 py-2 text-xs font-bold text-brand-muted hover:text-brand transition-colors">Cancel</button>
                    <button type="submit" class="px-5 py-2 bg-brand text-white rounded-lg text-xs font-bold hover:bg-brand-light transition-colors">Create Role</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ═══ NEW PERMISSION MODAL ═══ --}}
    <div x-show="showNewPerm" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-brand/50 backdrop-blur-sm" @click="showNewPerm = false"></div>
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg relative z-10" @click.outside="showNewPerm = false">
            <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 bg-emerald-50 rounded-lg flex items-center justify-center text-emerald-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-brand">Create New Permission</h3>
                        <p class="text-xs text-brand-muted">Add a custom permission node to the access matrix.</p>
                    </div>
                </div>
                <button @click="showNewPerm = false" class="w-7 h-7 bg-surface rounded-lg flex items-center justify-center text-brand-muted hover:text-brand transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form action="{{ route('orchestrator.permissions.store') }}" method="POST" class="p-6 space-y-4">
                @csrf
                <div>
                    <label class="text-[10px] font-bold text-brand-muted uppercase tracking-wider mb-1.5 block">Permission Key <span class="text-red-500">*</span></label>
                    <input type="text" name="name" required placeholder="e.g. reports.generate" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-medium outline-none focus:ring-2 focus:ring-accent/20 transition-shadow">
                    <p class="text-[10px] text-brand-muted mt-1">Format: <code class="font-mono">module.action</code> (e.g. drivers.approve)</p>
                </div>
                <div>
                    <label class="text-[10px] font-bold text-brand-muted uppercase tracking-wider mb-1.5 block">Module Group <span class="text-red-500">*</span></label>
                    <select name="module" required class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-medium outline-none focus:ring-2 focus:ring-accent/20 transition-shadow">
                        <option value="">-- Select Module --</option>
                        @foreach($permissions->keys() as $mod)
                        <option value="{{ $mod }}">{{ $mod }}</option>
                        @endforeach
                        <option value="Custom">Custom Module...</option>
                    </select>
                </div>
                <div>
                    <label class="text-[10px] font-bold text-brand-muted uppercase tracking-wider mb-1.5 block">Display Label</label>
                    <input type="text" name="label" placeholder="e.g. Generate Reports" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-medium outline-none focus:ring-2 focus:ring-accent/20 transition-shadow">
                </div>
                <div class="flex justify-end gap-2 pt-3 border-t border-gray-100">
                    <button type="button" @click="showNewPerm = false" class="px-4 py-2 text-xs font-bold text-brand-muted hover:text-brand transition-colors">Cancel</button>
                    <button type="submit" class="px-5 py-2 bg-brand text-white rounded-lg text-xs font-bold hover:bg-brand-light transition-colors">Create Permission</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection