@extends('admin.layout')
@section('title', 'Roles & Permissions')
@section('content')
<div x-data="{ tab: 'roles', editRole: null, showNewRole: false, showNewPerm: false }">

    <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-black text-brand tracking-tight">Access Control Matrix</h2>
            <p class="text-brand-muted font-medium mt-1 text-sm">Manage roles, permissions, and staff access levels from the dashboard.</p>
        </div>
        <div class="flex gap-2">
            <button @click="showNewPerm = true" class="px-5 py-2.5 bg-white border border-gray-200 text-brand font-bold rounded-lg hover:shadow-md transition text-sm">+ Permission</button>
            <button @click="showNewRole = true" class="px-5 py-2.5 bg-brand text-white font-bold rounded-lg hover:bg-brand-light transition text-sm">+ New Role</button>
        </div>
    </div>

    @if(session('success'))<div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-lg font-bold text-sm">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg font-bold text-sm">{{ session('error') }}</div>@endif

    {{-- Tabs --}}
    <div class="flex gap-2 mb-8 border-b border-gray-100 pb-4">
        <button @click="tab='roles'" :class="tab==='roles' ? 'bg-brand text-white' : 'bg-white text-brand border border-gray-100'"
            class="px-5 py-2.5 rounded-lg font-black text-xs uppercase tracking-wider transition">🔐 Roles ({{ $roles->count() }})</button>
        <button @click="tab='permissions'" :class="tab==='permissions' ? 'bg-brand text-white' : 'bg-white text-brand border border-gray-100'"
            class="px-5 py-2.5 rounded-lg font-black text-xs uppercase tracking-wider transition">🛡️ Permissions</button>
    </div>

    {{-- ═══ ROLES TAB ═══ --}}
    <div x-show="tab==='roles'" x-cloak>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            @foreach($roles as $role)
            <div class="bg-white rounded-lg border border-gray-100 shadow-sm overflow-hidden">
                <div class="p-5 border-b border-gray-50 flex items-center justify-between">
                    <div>
                        <h3 class="font-black text-brand text-base">{{ $role->label ?? $role->name }}</h3>
                        <p class="text-xs text-brand-muted mt-0.5">{{ $role->description ?? 'No description' }} · <span class="text-accent font-bold">{{ $role->users_count }} user(s)</span></p>
                    </div>
                    <div class="flex items-center gap-2">
                        @if($role->is_system)
                        <span class="px-2 py-1 bg-amber-50 text-amber-600 text-[10px] font-black rounded uppercase">System</span>
                        @else
                        <form action="{{ route('orchestrator.roles.destroy', $role->id) }}" method="POST" onsubmit="return confirm('Delete this role?');">
                            @csrf @method('DELETE')
                            <button class="p-1.5 text-gray-300 hover:text-red-500 transition"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>
                        </form>
                        @endif
                    </div>
                </div>
                <form action="{{ route('orchestrator.roles.update', $role->id) }}" method="POST">
                    @csrf @method('PUT')
                    <div class="p-5">
                        <p class="text-[10px] font-black text-brand-muted uppercase tracking-widest mb-3">Assigned Permissions</p>
                        <div class="grid grid-cols-2 gap-2 max-h-60 overflow-y-auto pr-2">
                            @foreach($permissions as $module => $perms)
                                @foreach($perms as $perm)
                                <label class="flex items-center gap-2 py-1 cursor-pointer group">
                                    <input type="checkbox" name="permissions[]" value="{{ $perm->id }}"
                                        {{ $role->permissions->contains('id', $perm->id) ? 'checked' : '' }}
                                        class="w-3.5 h-3.5 rounded text-accent focus:ring-accent/30">
                                    <span class="text-xs font-medium text-brand-muted group-hover:text-brand transition truncate" title="{{ $perm->label }}">{{ $perm->label ?? $perm->name }}</span>
                                </label>
                                @endforeach
                            @endforeach
                        </div>
                    </div>
                    <div class="px-5 py-3 bg-surface/30 border-t border-gray-50">
                        <button type="submit" class="w-full py-2 bg-brand text-white text-xs font-black uppercase rounded-lg hover:bg-brand-light transition tracking-widest">Save Permissions</button>
                    </div>
                </form>
            </div>
            @endforeach
        </div>
    </div>

    {{-- ═══ PERMISSIONS TAB ═══ --}}
    <div x-show="tab==='permissions'" x-cloak>
        @foreach($permissions as $module => $perms)
        <div class="mb-6">
            <h3 class="text-sm font-black text-brand uppercase tracking-widest mb-3 flex items-center gap-2">
                <span class="w-2 h-2 bg-accent rounded-full"></span> {{ ucfirst($module) }}
            </h3>
            <div class="bg-white rounded-lg border border-gray-100 shadow-sm overflow-hidden">
                <table class="w-full text-left">
                    <thead><tr class="bg-surface/30 border-b border-gray-100">
                        <th class="px-5 py-3 text-[10px] font-black text-brand-muted uppercase tracking-widest">Permission Key</th>
                        <th class="px-5 py-3 text-[10px] font-black text-brand-muted uppercase tracking-widest">Label</th>
                        <th class="px-5 py-3 text-[10px] font-black text-brand-muted uppercase tracking-widest">Roles Using</th>
                        <th class="px-5 py-3 text-[10px] font-black text-brand-muted uppercase tracking-widest text-right">Action</th>
                    </tr></thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($perms as $perm)
                        <tr class="hover:bg-surface/20 transition">
                            <td class="px-5 py-3 text-xs font-mono font-bold text-brand">{{ $perm->name }}</td>
                            <td class="px-5 py-3 text-xs text-brand-muted">{{ $perm->label ?? '-' }}</td>
                            <td class="px-5 py-3 text-xs text-brand-muted">{{ $perm->roles->count() }}</td>
                            <td class="px-5 py-3 text-right">
                                <form action="{{ route('orchestrator.permissions.destroy', $perm->id) }}" method="POST" class="inline" onsubmit="return confirm('Remove this permission?');">
                                    @csrf @method('DELETE')
                                    <button class="text-gray-300 hover:text-red-500 transition"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>
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
    <div x-show="showNewRole" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" @click.self="showNewRole = false">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg mx-4 p-6">
            <h3 class="text-lg font-black text-brand mb-4">Create New Role</h3>
            <form action="{{ route('orchestrator.roles.store') }}" method="POST">
                @csrf
                <div class="space-y-4 mb-6">
                    <div>
                        <label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-1">Role Name (slug) *</label>
                        <input type="text" name="name" required placeholder="e.g. fleet_manager" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-medium focus:ring-2 focus:ring-accent/20 outline-none">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-1">Display Label</label>
                        <input type="text" name="label" placeholder="e.g. Fleet Manager" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-medium focus:ring-2 focus:ring-accent/20 outline-none">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-1">Description</label>
                        <textarea name="description" rows="2" placeholder="What this role is responsible for..." class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-medium focus:ring-2 focus:ring-accent/20 outline-none"></textarea>
                    </div>
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" @click="showNewRole = false" class="px-5 py-2.5 text-sm font-bold text-brand-muted hover:text-brand transition">Cancel</button>
                    <button type="submit" class="px-6 py-2.5 bg-brand text-white font-black rounded-lg hover:bg-brand-light transition text-sm">Create Role</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ═══ NEW PERMISSION MODAL ═══ --}}
    <div x-show="showNewPerm" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" @click.self="showNewPerm = false">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg mx-4 p-6">
            <h3 class="text-lg font-black text-brand mb-4">Create New Permission</h3>
            <form action="{{ route('orchestrator.permissions.store') }}" method="POST">
                @csrf
                <div class="space-y-4 mb-6">
                    <div>
                        <label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-1">Permission Key *</label>
                        <input type="text" name="name" required placeholder="e.g. financials.view" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-medium focus:ring-2 focus:ring-accent/20 outline-none">
                        <p class="text-[10px] text-gray-400 mt-1">Use format: module.action (e.g. drivers.approve)</p>
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-1">Module *</label>
                        <input type="text" name="module" required placeholder="e.g. financials" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-medium focus:ring-2 focus:ring-accent/20 outline-none">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-1">Display Label</label>
                        <input type="text" name="label" placeholder="e.g. View Financial Reports" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-medium focus:ring-2 focus:ring-accent/20 outline-none">
                    </div>
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" @click="showNewPerm = false" class="px-5 py-2.5 text-sm font-bold text-brand-muted hover:text-brand transition">Cancel</button>
                    <button type="submit" class="px-6 py-2.5 bg-brand text-white font-black rounded-lg hover:bg-brand-light transition text-sm">Create Permission</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
