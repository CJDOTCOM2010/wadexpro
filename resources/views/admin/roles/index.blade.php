@extends('admin.layout')
@section('title', 'Roles & Permissions')
@section('content')
<div x-data="{ tab: 'roles', showNewRole: false, showNewPerm: false, editRoleId: null }">

    {{-- Header --}}
    <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-black text-brand tracking-tight">Access Control Matrix</h2>
            <p class="text-brand-muted font-medium mt-1 text-sm">Create roles, assign granular permissions per module, and control what staff can access.</p>
        </div>
        <div class="flex gap-2">
            <button @click="showNewPerm = true" class="px-5 py-2.5 bg-white border border-gray-200 text-brand font-bold rounded-lg hover:shadow-md transition text-sm flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Permission
            </button>
            <button @click="showNewRole = true" class="px-5 py-2.5 bg-brand text-white font-bold rounded-lg hover:bg-brand-light transition text-sm flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                New Role
            </button>
        </div>
    </div>

    @if(session('success'))<div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-lg font-bold text-sm flex items-center gap-2"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>{{ session('success') }}</div>@endif
    @if(session('error'))<div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg font-bold text-sm flex items-center gap-2"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>{{ session('error') }}</div>@endif

    {{-- Tabs --}}
    <div class="flex gap-2 mb-8 border-b border-gray-100 pb-4">
        <button @click="tab='roles'" :class="tab==='roles' ? 'bg-brand text-white shadow-lg' : 'bg-white text-brand border border-gray-100 hover:bg-gray-50'"
            class="px-6 py-2.5 rounded-lg font-black text-xs uppercase tracking-wider transition">🔐 Roles ({{ $roles->count() }})</button>
        <button @click="tab='permissions'" :class="tab==='permissions' ? 'bg-brand text-white shadow-lg' : 'bg-white text-brand border border-gray-100 hover:bg-gray-50'"
            class="px-6 py-2.5 rounded-lg font-black text-xs uppercase tracking-wider transition">🛡️ All Permissions</button>
    </div>

    {{-- ═══════════════════════════════════════════════════════════ --}}
    {{-- ROLES TAB — Each role shows a full permission matrix      --}}
    {{-- ═══════════════════════════════════════════════════════════ --}}
    <div x-show="tab==='roles'" x-cloak>
        @foreach($roles as $role)
        <div class="mb-8 bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden" x-data="{ open: editRoleId === '{{ $role->id }}' }">
            {{-- Role Header --}}
            <div class="flex items-center justify-between px-6 py-5 cursor-pointer hover:bg-surface/20 transition" @click="open = !open">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-lg flex items-center justify-center {{ $role->is_system ? 'bg-amber-50 text-amber-600' : 'bg-accent/10 text-accent' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    </div>
                    <div>
                        <h3 class="font-black text-brand text-base">{{ $role->label ?? $role->name }}</h3>
                        <p class="text-xs text-brand-muted mt-0.5">{{ $role->description ?? 'No description' }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <span class="text-xs font-bold text-brand-muted bg-surface px-3 py-1 rounded-full">{{ $role->users_count }} staff</span>
                    <span class="text-xs font-bold text-accent bg-accent/10 px-3 py-1 rounded-full">{{ $role->permissions->count() }} permissions</span>
                    @if($role->is_system)
                    <span class="px-2.5 py-1 bg-amber-50 text-amber-600 text-[9px] font-black rounded uppercase tracking-widest">System</span>
                    @endif
                    <svg class="w-5 h-5 text-gray-400 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </div>
            </div>

            {{-- Permission Matrix (expandable) --}}
            <div x-show="open" x-cloak x-transition class="border-t border-gray-100">
                <form action="{{ route('orchestrator.roles.update', $role->id) }}" method="POST">
                    @csrf @method('PUT')
                    <div class="p-6">
                        @foreach($permissions as $module => $perms)
                        <div class="mb-6 last:mb-0">
                            {{-- Module Header --}}
                            <div class="flex items-center gap-3 mb-3">
                                <div class="w-1.5 h-1.5 bg-accent rounded-full"></div>
                                <h4 class="text-[11px] font-black text-brand uppercase tracking-[0.15em]">{{ $module }}</h4>
                                <div class="flex-1 h-px bg-gray-100"></div>
                                {{-- Select All for module --}}
                                <label class="flex items-center gap-1.5 cursor-pointer">
                                    <input type="checkbox" class="w-3 h-3 rounded text-accent focus:ring-accent/30"
                                        onchange="document.querySelectorAll('.perm-{{ Str::slug($module) }}').forEach(cb => cb.checked = this.checked)">
                                    <span class="text-[9px] font-black text-brand-muted uppercase tracking-widest">All</span>
                                </label>
                            </div>
                            {{-- Permission Checkboxes --}}
                            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-x-4 gap-y-2 pl-5">
                                @foreach($perms as $perm)
                                <label class="flex items-center gap-2 py-1.5 px-2 rounded-md cursor-pointer group hover:bg-accent/5 transition">
                                    <input type="checkbox" name="permissions[]" value="{{ $perm->id }}"
                                        class="perm-{{ Str::slug($module) }} w-4 h-4 rounded text-accent focus:ring-accent/30 border-gray-300"
                                        {{ $role->permissions->contains('id', $perm->id) ? 'checked' : '' }}>
                                    <div>
                                        <span class="text-xs font-bold text-brand group-hover:text-accent transition block">{{ $perm->label ?? $perm->name }}</span>
                                        <span class="text-[9px] text-gray-400 font-mono">{{ $perm->name }}</span>
                                    </div>
                                </label>
                                @endforeach
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <div class="px-6 py-4 bg-surface/30 border-t border-gray-50 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            @unless($role->is_system)
                            <form action="{{ route('orchestrator.roles.destroy', $role->id) }}" method="POST" class="inline" onsubmit="return confirm('Permanently delete this role and revoke it from all assigned staff?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="px-4 py-2 text-xs font-bold text-red-500 bg-red-50 hover:bg-red-100 rounded-lg transition">Delete Role</button>
                            </form>
                            @endunless
                        </div>
                        <button type="submit" class="px-8 py-2.5 bg-brand text-white text-xs font-black uppercase rounded-lg hover:bg-brand-light transition tracking-widest shadow-md">
                            Save Permissions
                        </button>
                    </div>
                </form>
            </div>
        </div>
        @endforeach
    </div>

    {{-- ═══════════════════════════════════════════════════════════ --}}
    {{-- PERMISSIONS TAB — Grouped by module                       --}}
    {{-- ═══════════════════════════════════════════════════════════ --}}
    <div x-show="tab==='permissions'" x-cloak>
        @foreach($permissions as $module => $perms)
        <div class="mb-6">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-2 h-2 bg-accent rounded-full"></div>
                <h3 class="text-sm font-black text-brand uppercase tracking-widest">{{ $module }}</h3>
                <span class="text-[9px] font-bold text-brand-muted bg-surface px-2 py-0.5 rounded-full">{{ $perms->count() }} permissions</span>
            </div>
            <div class="bg-white rounded-lg border border-gray-100 shadow-sm overflow-hidden">
                <table class="w-full text-left">
                    <thead><tr class="bg-surface/30 border-b border-gray-100">
                        <th class="px-5 py-3 text-[10px] font-black text-brand-muted uppercase tracking-widest w-1/4">Permission Key</th>
                        <th class="px-5 py-3 text-[10px] font-black text-brand-muted uppercase tracking-widest">Description</th>
                        <th class="px-5 py-3 text-[10px] font-black text-brand-muted uppercase tracking-widest text-center">Roles Using</th>
                        <th class="px-5 py-3 text-[10px] font-black text-brand-muted uppercase tracking-widest text-right">Action</th>
                    </tr></thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($perms as $perm)
                        <tr class="hover:bg-surface/20 transition group">
                            <td class="px-5 py-3"><code class="text-xs font-mono font-bold text-brand bg-surface/50 px-2 py-0.5 rounded">{{ $perm->name }}</code></td>
                            <td class="px-5 py-3 text-xs text-brand-muted font-medium">{{ $perm->label ?? '-' }}</td>
                            <td class="px-5 py-3 text-center">
                                <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-accent/10 text-accent text-xs font-black">{{ $perm->roles->count() }}</span>
                            </td>
                            <td class="px-5 py-3 text-right">
                                <form action="{{ route('orchestrator.permissions.destroy', $perm->id) }}" method="POST" class="inline" onsubmit="return confirm('Remove this permission from all roles?');">
                                    @csrf @method('DELETE')
                                    <button class="text-gray-300 hover:text-red-500 transition" title="Delete"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>
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

    {{-- ═══ NEW ROLE MODAL ═══ --}}
    <div x-show="showNewRole" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm" @click.self="showNewRole = false">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg mx-4 p-6" @click.stop>
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 rounded-lg bg-accent/10 text-accent flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                </div>
                <div>
                    <h3 class="text-lg font-black text-brand">Create New Role</h3>
                    <p class="text-xs text-brand-muted">Define a new access level for staff members.</p>
                </div>
            </div>
            <form action="{{ route('orchestrator.roles.store') }}" method="POST">
                @csrf
                <div class="space-y-4 mb-6">
                    <div>
                        <label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-1.5">Role Identifier (slug) <span class="text-red-500">*</span></label>
                        <input type="text" name="name" required placeholder="e.g. fleet_manager"
                            class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-medium focus:ring-2 focus:ring-accent/20 outline-none transition">
                        <p class="text-[10px] text-gray-400 mt-1">Lowercase letters and underscores only. This cannot be changed later.</p>
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-1.5">Display Name</label>
                        <input type="text" name="label" placeholder="e.g. Fleet Manager"
                            class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-medium focus:ring-2 focus:ring-accent/20 outline-none transition">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-1.5">Description</label>
                        <textarea name="description" rows="2" placeholder="What this role is responsible for..."
                            class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-medium focus:ring-2 focus:ring-accent/20 outline-none transition resize-none"></textarea>
                    </div>
                </div>
                <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                    <button type="button" @click="showNewRole = false" class="px-5 py-2.5 text-sm font-bold text-brand-muted hover:text-brand transition">Cancel</button>
                    <button type="submit" class="px-6 py-2.5 bg-brand text-white font-black rounded-lg hover:bg-brand-light transition text-sm shadow-md">Create Role</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ═══ NEW PERMISSION MODAL ═══ --}}
    <div x-show="showNewPerm" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm" @click.self="showNewPerm = false">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg mx-4 p-6" @click.stop>
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                </div>
                <div>
                    <h3 class="text-lg font-black text-brand">Create New Permission</h3>
                    <p class="text-xs text-brand-muted">Add a custom permission node to the access matrix.</p>
                </div>
            </div>
            <form action="{{ route('orchestrator.permissions.store') }}" method="POST">
                @csrf
                <div class="space-y-4 mb-6">
                    <div>
                        <label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-1.5">Permission Key <span class="text-red-500">*</span></label>
                        <input type="text" name="name" required placeholder="e.g. reports.generate"
                            class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-medium focus:ring-2 focus:ring-accent/20 outline-none transition">
                        <p class="text-[10px] text-gray-400 mt-1">Format: module.action (e.g. drivers.approve, financials.export)</p>
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-1.5">Module Group <span class="text-red-500">*</span></label>
                        <select name="module" required class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-medium focus:ring-2 focus:ring-accent/20 outline-none transition">
                            <option value="">-- Select Module --</option>
                            @foreach($permissions->keys() as $mod)
                            <option value="{{ $mod }}">{{ $mod }}</option>
                            @endforeach
                            <option value="Custom">Custom Module...</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-1.5">Display Label</label>
                        <input type="text" name="label" placeholder="e.g. Generate Reports"
                            class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-medium focus:ring-2 focus:ring-accent/20 outline-none transition">
                    </div>
                </div>
                <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                    <button type="button" @click="showNewPerm = false" class="px-5 py-2.5 text-sm font-bold text-brand-muted hover:text-brand transition">Cancel</button>
                    <button type="submit" class="px-6 py-2.5 bg-brand text-white font-black rounded-lg hover:bg-brand-light transition text-sm shadow-md">Create Permission</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
