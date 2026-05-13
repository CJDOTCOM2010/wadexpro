@extends('admin.layout')
@section('title', 'Global Entity Node Matrix')
@section('content')

<div class="mb-8 flex items-center justify-between">
    <div>
        <h2 class="text-2xl font-black text-brand tracking-tight">User Matrix Control</h2>
        <p class="text-brand-muted font-medium mt-1">Personnel oversight of the WADEXPRO distributed human node network.</p>
    </div>
    <button class="px-6 py-3 bg-brand text-white font-bold rounded-lg hover:bg-brand-light transition flex items-center gap-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
        Provision New Entity
    </button>
</div>

<!-- Stats Bar -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="bg-white p-6 rounded-lg border border-gray-100 shadow-sm flex items-center gap-4">
        <div class="w-12 h-12 bg-accent/10 rounded-lg flex items-center justify-center text-accent">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
        </div>
        <div>
            <p class="text-[10px] font-black text-brand-muted uppercase tracking-widest">Total Nodes</p>
            <p class="text-2xl font-black text-brand tracking-tight">{{ number_format($stats['total_nodes']) }}</p>
        </div>
    </div>
    <div class="bg-white p-6 rounded-lg border border-gray-100 shadow-sm flex items-center gap-4">
        <div class="w-12 h-12 bg-green-50 rounded-lg flex items-center justify-center text-green-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
        </div>
        <div>
            <p class="text-[10px] font-black text-brand-muted uppercase tracking-widest">Active Sessions (24H)</p>
            <p class="text-2xl font-black text-brand tracking-tight">{{ number_format($stats['active_sessions']) }}</p>
        </div>
    </div>
    <div class="bg-white p-6 rounded-lg border border-gray-100 shadow-sm flex items-center gap-4">
        <div class="w-12 h-12 bg-brand/5 rounded-lg flex items-center justify-center text-brand">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
        </div>
        <div>
            <p class="text-[10px] font-black text-brand-muted uppercase tracking-widest">Revoked Access</p>
            <p class="text-2xl font-black text-brand tracking-tight">{{ number_format($stats['revoked_access']) }}</p>
        </div>
    </div>
</div>

<!-- Table Area -->
<div class="bg-white rounded-lg border border-gray-100 shadow-sm overflow-hidden">
    <div class="p-6 border-b border-gray-50 flex items-center justify-between bg-surface/30">
        <form action="{{ route('orchestrator.users') }}" method="GET" class="flex items-center gap-4">
            <select name="type" onchange="this.form.submit()" class="bg-white border border-gray-100 rounded-lg px-4 py-2 text-sm font-bold outline-none focus:ring-2 focus:ring-accent/20">
                <option value="">All User Types</option>
                <option value="admin" {{ request('type') == 'admin' ? 'selected' : '' }}>Admin</option>
                <option value="driver" {{ request('type') == 'driver' ? 'selected' : '' }}>Driver</option>
                <option value="customer" {{ request('type') == 'customer' ? 'selected' : '' }}>Customer</option>
            </select>
            <div class="relative">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search Identity, Email..." class="bg-white border border-gray-100 rounded-lg pl-10 pr-4 py-2 text-sm font-medium outline-none focus:ring-2 focus:ring-accent/20 w-72">
                <svg class="w-4 h-4 text-gray-400 absolute left-4 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
        </form>
        <p class="text-xs font-bold text-brand-muted">Showing {{ $users->firstItem() ?? 0 }} - {{ $users->lastItem() ?? 0 }} of {{ $users->total() }} entries</p>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-surface/10 border-b border-gray-50">
                    <th class="px-6 py-4 text-[11px] font-black text-brand-muted uppercase tracking-widest">Identity / Node</th>
                    <th class="px-6 py-4 text-[11px] font-black text-brand-muted uppercase tracking-widest">Type</th>
                    <th class="px-6 py-4 text-[11px] font-black text-brand-muted uppercase tracking-widest">Clearance</th>
                    <th class="px-6 py-4 text-[11px] font-black text-brand-muted uppercase tracking-widest">State</th>
                    <th class="px-6 py-4 text-[11px] font-black text-brand-muted uppercase tracking-widest text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($users as $user)
                <tr class="hover:bg-surface/30 transition-colors group">
                    <td class="px-6 py-5">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 {{ $user->user_type == 'admin' ? 'bg-brand text-accent' : 'bg-surface text-brand' }} rounded-lg flex items-center justify-center font-black text-sm uppercase">
                                {{ strtoupper(substr($user->name, 0, 2)) }}
                            </div>
                            <div>
                                <p class="text-sm font-bold text-brand">{{ $user->name }}</p>
                                <p class="text-xs text-brand-muted mt-0.5">{{ $user->email }} | {{ $user->phone }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-5 uppercase text-[10px] font-black tracking-widest text-brand-muted">{{ $user->user_type ?? 'customer' }}</td>
                    <td class="px-6 py-5">
                        @if($user->user_type == 'admin')
                            <span class="px-2 py-1 bg-brand text-accent text-[10px] font-black rounded-lg uppercase">Root Access</span>
                        @elseif($user->user_type == 'driver')
                            <span class="px-2 py-1 bg-surface text-brand text-[10px] font-black rounded-lg uppercase">Level 1 (KYC)</span>
                        @else
                            <span class="px-2 py-1 bg-gray-100 text-gray-500 text-[10px] font-black rounded-lg uppercase">Standard</span>
                        @endif
                    </td>
                    <td class="px-6 py-5">
                        <div class="flex items-center gap-2">
                            @if($user->is_active)
                                <div class="w-2 h-2 bg-green-500 rounded-full shadow-[0_0_8px_rgba(34,197,94,0.4)]"></div>
                                <span class="text-xs font-bold text-brand uppercase tracking-tighter">Active</span>
                            @else
                                <div class="w-2 h-2 bg-red-500 rounded-full"></div>
                                <span class="text-xs font-bold text-red-500 uppercase tracking-tighter">Suspended</span>
                            @endif
                        </div>
                    </td>
                    <td class="px-6 py-5 text-right">
                        <button class="p-2 text-gray-300 hover:text-brand transition"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h.01M12 12h.01M19 12h.01M6 12a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0z"/></svg></button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                        <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        No users found matching the criteria.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div class="p-6 bg-surface/10 border-t border-gray-50 flex items-center justify-center">
        {{ $users->appends(request()->except('page'))->links() }}
    </div>
</div>

@endsection
