@extends('admin.layout')
@section('title', 'Roles & Permissions')
@section('content')

@php
$totalRoles = $roles->count();
$totalPerms = 0;
foreach ($permissions as $p) $totalPerms += $p->count();
$systemRoles = $roles->where('is_system', true)->count();
@endphp

<div x-data="accessControl()" class="max-w-6xl mx-auto"

     @delete-role.window="confirmDeleteRole($event.detail)"
     @delete-permission.window="confirmDeletePermission($event.detail)">

    {{-- Header --}}
    <div class="bg-brand rounded-xl overflow-hidden mb-8 relative">
        <div class="px-6 py-7 sm:px-8 sm:py-8">
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

    {{-- ═══ ROLES TAB — Split Panel Layout ═══ --}}
    <div x-show="tab==='roles'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
        <div class="flex gap-6">
            {{-- Left: Role List --}}
            <div class="w-72 shrink-0">
                <div class="bg-white border border-gray-100 rounded-xl overflow-hidden">
                    <div class="px-4 py-3.5 border-b border-gray-100 bg-surface/30">
                        <p class="text-xs font-bold text-brand">Select a Role</p>
                    </div>
                    <div class="overflow-y-auto" style="max-height: 600px;">
                        @foreach($roles as $role)
                        <div @click="selectedRole = '{{ $role->id }}'"
                             :class="selectedRole === '{{ $role->id }}' ? 'bg-accent/10 border-l-2 border-accent' : 'border-l-2 border-transparent hover:bg-surface/30'"
                             class="px-4 py-3.5 cursor-pointer transition-colors border-b border-gray-50 last:border-b-0">
                            <div class="flex items-center justify-between mb-0.5">
                                <h4 class="text-sm font-bold text-brand truncate">{{ $role->label ?? $role->name }}</h4>
                                @if($role->is_system)
                                <span class="px-1.5 py-0.5 bg-amber-50 text-amber-600 text-[8px] font-bold rounded uppercase shrink-0 ml-1">SYS</span>
                                @endif
                            </div>
                            <p class="text-[10px] text-brand-muted truncate">{{ $role->description ?? 'No description' }}</p>
                            <div class="flex items-center gap-2 mt-1.5">
                                <span class="text-[9px] font-bold text-brand-muted bg-surface px-1.5 py-0.5 rounded">{{ $role->users_count }} staff</span>
                                <span class="text-[9px] font-bold text-accent bg-accent/5 px-1.5 py-0.5 rounded">{{ $role->permissions->count() }} perms</span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Right: Permission Matrix --}}
            <div class="flex-1 min-w-0">
                @foreach($roles as $role)
                <div x-show="selectedRole === '{{ $role->id }}'" x-cloak class="bg-white border border-gray-100 rounded-xl overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between bg-surface/30">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg flex items-center justify-center {{ $role->is_system ? 'bg-amber-50 text-amber-600' : 'bg-accent/10 text-accent' }}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            </div>
                            <div>
                                <div class="flex items-center gap-2">
                                    <h3 class="text-sm font-bold text-brand">{{ $role->label ?? $role->name }}</h3>
                                    @if($role->is_system)
                                    <span class="px-2 py-0.5 bg-amber-50 text-amber-600 text-[9px] font-bold rounded uppercase tracking-wider">System</span>
                                    @endif
                                </div>
                                <p class="text-[11px] text-brand-muted">{{ $role->description ?? 'No description' }}</p>
                            </div>
                        </div>
                    </div>

                    <form action="{{ route('orchestrator.roles.update', $role->id) }}" method="POST">
                        @csrf @method('PUT')
                        <div class="overflow-y-auto p-5 space-y-6" style="max-height: 500px;">
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
                                            onchange="document.querySelectorAll('.perm-{{ $role->id }}-{{ Str::slug($module) }}').forEach(cb => cb.checked = this.checked)">
                                        Select All
                                    </label>
                                </div>
                                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-3 xl:grid-cols-4 gap-1.5">
                                    @foreach($perms as $perm)
                                    <label class="flex items-center gap-2.5 px-3 py-2 rounded-lg cursor-pointer group hover:bg-accent/5 transition-colors {{ $role->permissions->contains('id', $perm->id) ? 'bg-accent/[0.04]' : '' }}">
                                        <input type="checkbox" name="permissions[]" value="{{ $perm->id }}"
                                            class="perm-{{ $role->id }}-{{ Str::slug($module) }} w-3.5 h-3.5 rounded border-gray-300 text-accent focus:ring-accent/30"
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
                                <button type="button" @click="confirmDeleteRole({id:'{{ $role->id }}', label:'{{ $role->label ?? $role->name }}', type:'role'})" class="px-3.5 py-2 text-[10px] font-bold text-red-500 bg-red-50 hover:bg-red-100 rounded-lg transition-colors">Delete Role</button>
                                @endunless
                            </div>
                            <button type="submit" class="px-5 py-2 bg-brand text-white text-xs font-bold rounded-lg hover:bg-brand-light transition-colors">Save Permissions</button>
                        </div>
                    </form>
                </div>
                @endforeach

                {{-- Empty state when no role selected --}}
                <div x-show="!selectedRole" class="bg-white border border-gray-100 rounded-xl flex flex-col items-center justify-center py-20 text-brand-muted">
                    <svg class="w-16 h-16 mb-4 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    <p class="text-sm font-bold">Select a Role</p>
                    <p class="text-xs mt-1">Choose a role from the left panel to manage its permissions.</p>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══ PERMISSIONS TAB — Split Panel Layout ═══ --}}
    <div x-show="tab==='permissions'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
        <div class="flex gap-6">
            {{-- Left: Module List --}}
            <div class="w-72 shrink-0">
                <div class="bg-white border border-gray-100 rounded-xl overflow-hidden">
                    <div class="px-4 py-3.5 border-b border-gray-100 bg-surface/30">
                        <div class="flex items-center gap-2 px-2 py-1.5 bg-white border border-gray-100 rounded-lg">
                            <svg class="w-3.5 h-3.5 text-brand-muted shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            <input type="text" x-model="moduleSearch" placeholder="Filter modules..." class="text-[11px] bg-transparent outline-none border-none p-0 w-full text-brand placeholder:text-brand-muted">
                        </div>
                    </div>
                    <div class="overflow-y-auto" style="max-height: 600px;">
                        @foreach($permissions as $module => $perms)
                        <div x-show="moduleSearch === '' || '{{ strtolower($module) }}'.includes(moduleSearch.toLowerCase())"
                             @click="selectedModule = '{{ $module }}'"
                             :class="selectedModule === '{{ $module }}' ? 'bg-accent/10 border-l-2 border-accent' : 'border-l-2 border-transparent hover:bg-surface/30'"
                             class="px-4 py-3 cursor-pointer transition-colors border-b border-gray-50 last:border-b-0">
                            <div class="flex items-center justify-between">
                                <h4 class="text-sm font-bold text-brand truncate">{{ $module }}</h4>
                                <span class="text-[9px] font-bold text-accent bg-accent/5 px-1.5 py-0.5 rounded shrink-0 ml-2">{{ $perms->count() }}</span>
                            </div>
                            <p class="text-[9px] text-brand-muted mt-1 truncate">{{ $perms->first()?->label ?? 'Permission group' }}</p>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Right: Permission Details --}}
            <div class="flex-1 min-w-0">
                @foreach($permissions as $module => $perms)
                <div x-show="selectedModule === '{{ $module }}'" x-cloak class="bg-white border border-gray-100 rounded-xl overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between bg-surface/30">
                        <div class="flex items-center gap-3">
                            <span class="w-2 h-2 bg-accent rounded-full"></span>
                            <h3 class="text-sm font-bold text-brand uppercase tracking-wider">{{ $module }}</h3>
                            <span class="text-[10px] font-bold text-brand-muted bg-white px-2 py-0.5 rounded-lg border border-gray-100">{{ $perms->count() }} permissions</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="flex items-center gap-1.5 px-2 py-1.5 bg-white border border-gray-100 rounded-lg">
                                <svg class="w-3 h-3 text-brand-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                <input type="text" x-model="permSearch" placeholder="Search in module..." class="text-[10px] bg-transparent outline-none border-none p-0 w-28 text-brand placeholder:text-brand-muted">
                            </div>
                            <button @click="showNewPerm = true" class="px-2.5 py-1.5 bg-brand text-white rounded-lg text-[10px] font-bold hover:bg-brand-light transition-colors flex items-center gap-1.5 whitespace-nowrap">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                New
                            </button>
                        </div>
                    </div>
                    <div class="overflow-y-auto" style="max-height: 500px;">
                        <table class="w-full">
                            <thead>
                                <tr class="bg-surface/20 sticky top-0">
                                    <th class="text-left px-5 py-3 text-[10px] font-bold text-brand-muted uppercase tracking-wider w-1/3">Permission Key</th>
                                    <th class="text-left px-5 py-3 text-[10px] font-bold text-brand-muted uppercase tracking-wider">Description</th>
                                    <th class="text-center px-5 py-3 text-[10px] font-bold text-brand-muted uppercase tracking-wider">Roles</th>
                                    <th class="text-right px-5 py-3 text-[10px] font-bold text-brand-muted uppercase tracking-wider">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @foreach($perms as $perm)
                                <tr x-show="permSearch === '' || '{{ strtolower($perm->name) }} {{ strtolower($perm->label ?? '') }}'.includes(permSearch.toLowerCase())" class="hover:bg-surface/20 transition-colors">
                                    <td class="px-5 py-3">
                                        <code class="text-[11px] font-mono font-bold text-brand bg-surface/50 px-2 py-0.5 rounded">{{ $perm->name }}</code>
                                    </td>
                                    <td class="px-5 py-3 text-xs text-brand-muted">{{ $perm->label ?? '—' }}</td>
                                    <td class="px-5 py-3 text-center">
                                        <span class="inline-flex items-center justify-center min-w-[28px] h-7 px-2 rounded-full bg-accent/10 text-accent text-[11px] font-black">{{ $perm->roles->count() }}</span>
                                    </td>
                                    <td class="px-5 py-3 text-right">
                                        <button @click="confirmDeletePermission({id:'{{ $perm->id }}', label:'{{ $perm->label ?? $perm->name }}', type:'permission'})" class="w-7 h-7 rounded-lg text-gray-300 hover:text-red-500 hover:bg-red-50 transition-all flex items-center justify-center" title="Delete">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endforeach

                {{-- Empty state --}}
                <div x-show="!selectedModule" class="bg-white border border-gray-100 rounded-xl flex flex-col items-center justify-center py-20 text-brand-muted">
                    <svg class="w-16 h-16 mb-4 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                    <p class="text-sm font-bold">Select a Module</p>
                    <p class="text-xs mt-1">Choose a module from the left panel to view its permissions.</p>
                </div>
            </div>
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

    {{-- ═══ MULTI-STEP DELETE CONFIRMATION MODAL ═══ --}}
    <div x-show="deleteModal.step > 0" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-brand/60 backdrop-blur-sm" @click="closeDeleteModal()"></div>
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-md relative z-10 overflow-hidden" @click.outside="closeDeleteModal()">

            {{-- Step 1: Initial Warning --}}
            <template x-if="deleteModal.step === 1">
                <div class="p-6">
                    <div class="w-14 h-14 bg-red-50 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-7 h-7 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </div>
                    <h3 class="text-lg font-bold text-brand text-center mb-2">
                        Delete <span x-text="deleteModal.type === 'role' ? 'Role' : 'Permission'"></span>?
                    </h3>
                    <p class="text-sm text-brand-muted text-center mb-6">
                        You are about to delete <strong class="text-brand" x-text="deleteModal.label"></strong>.
                        <template x-if="deleteModal.type === 'role'">
                            <span>This role will be removed from all assigned staff.</span>
                        </template>
                        <template x-if="deleteModal.type === 'permission'">
                            <span>This permission will be removed from all roles using it.</span>
                        </template>
                    </p>

                    <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 mb-6">
                        <div class="flex items-start gap-2.5">
                            <svg class="w-5 h-5 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            <div>
                                <p class="text-xs font-bold text-amber-800">This action <span class="underline">cannot</span> be undone.</p>
                                <p class="text-[11px] text-amber-700 mt-1">Please confirm you understand the consequences before proceeding.</p>
                            </div>
                        </div>
                    </div>

                    <div class="flex gap-2">
                        <button type="button" @click="closeDeleteModal()" class="flex-1 px-4 py-2.5 bg-surface text-brand-muted rounded-lg text-xs font-bold hover:bg-gray-100 transition-colors">Cancel</button>
                        <button type="button" @click="deleteModal.step = 2" class="flex-1 px-4 py-2.5 bg-red-600 text-white rounded-lg text-xs font-bold hover:bg-red-700 transition-colors">I Understand, Continue</button>
                    </div>
                </div>
            </template>

            {{-- Step 2: Type confirmation --}}
            <template x-if="deleteModal.step === 2">
                <div class="p-6">
                    <div class="w-14 h-14 bg-red-50 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-7 h-7 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <h3 class="text-lg font-bold text-brand text-center mb-2">Final Confirmation Required</h3>
                    <p class="text-sm text-brand-muted text-center mb-6">
                        Type <strong class="text-red-600 select-all font-mono bg-red-50 px-2 py-0.5 rounded">DELETE</strong> below to confirm permanent removal of <strong class="text-brand" x-text="deleteModal.label"></strong>.
                    </p>

                    <input type="text" x-model="deleteModal.confirmText" @input="deleteModal.confirmText = deleteModal.confirmText.toUpperCase()" placeholder="Type DELETE to confirm" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-bold text-center outline-none focus:ring-2 focus:ring-red-300 transition-shadow mb-6 uppercase tracking-widest">

                    <div class="flex gap-2">
                        <button type="button" @click="deleteModal.step = 1" class="flex-1 px-4 py-2.5 bg-surface text-brand-muted rounded-lg text-xs font-bold hover:bg-gray-100 transition-colors">Back</button>
                        <button type="button" @click="executeDelete()" :disabled="deleteModal.confirmText !== 'DELETE'" class="flex-1 px-4 py-2.5 rounded-lg text-xs font-bold transition-colors" :class="deleteModal.confirmText === 'DELETE' ? 'bg-red-600 text-white hover:bg-red-700' : 'bg-gray-100 text-gray-400 cursor-not-allowed'">
                            Confirm Delete
                        </button>
                    </div>
                </div>
            </template>
        </div>
    </div>
</div>

<script>
function accessControl() {
    return {
        tab: 'roles',
        selectedRole: '{{ $roles->first()?->id ?? '' }}',
        selectedModule: '{{ $permissions->keys()->first() ?? '' }}',
        moduleSearch: '',
        permSearch: '',
        showNewRole: false,
        showNewPerm: false,
        deleteModal: {
            step: 0,
            type: '',
            id: '',
            label: '',
            confirmText: '',
            actionUrl: '',
        },
        confirmDeleteRole(data) {
            this.deleteModal = {
                step: 1,
                type: 'role',
                id: data.id,
                label: data.label,
                confirmText: '',
                actionUrl: '{{ route('orchestrator.roles.destroy', '__ID__') }}'.replace('__ID__', data.id),
            };
        },
        confirmDeletePermission(data) {
            this.deleteModal = {
                step: 1,
                type: 'permission',
                id: data.id,
                label: data.label,
                confirmText: '',
                actionUrl: '{{ route('orchestrator.permissions.destroy', '__ID__') }}'.replace('__ID__', data.id),
            };
        },
        closeDeleteModal() {
            this.deleteModal.step = 0;
            this.deleteModal.confirmText = '';
        },
        executeDelete() {
            if (this.deleteModal.confirmText !== 'DELETE') return;
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = this.deleteModal.actionUrl;
            const csrf = document.createElement('input');
            csrf.type = 'hidden';
            csrf.name = '_token';
            csrf.value = '{{ csrf_token() }}';
            form.appendChild(csrf);
            const method = document.createElement('input');
            method.type = 'hidden';
            method.name = '_method';
            method.value = 'DELETE';
            form.appendChild(method);
            document.body.appendChild(form);
            form.submit();
        }
    };
}
</script>
<style>[x-cloak] { display: none !important; }</style>
@endsection