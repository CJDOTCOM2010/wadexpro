@extends('admin.layout')
@section('title', 'Support Tickets')
@section('content')

@php
$currentQueue = request('queue', '');
$currentStatus = request('status', '');
$currentCategory = request('category', '');
$openCount = $counts['open'] ?? 0;
$urgentCount = $tickets->filter(fn($t) => $t->priority === 'urgent' && $t->status !== 'closed')->count();
$unassignedCount = $counts['unassigned'] ?? 0;
@endphp

<div x-data="{ showCompose: false, searchUser: '' }" class="max-w-6xl mx-auto">

    {{-- Header --}}
    <div class="mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="text-2xl font-black text-brand tracking-tight">Support Tickets</h2>
                <p class="text-sm text-brand-muted font-medium mt-0.5">Resolve customer and driver issues, disputes, and inquiries.</p>
            </div>
            <button @click="showCompose = true" class="px-5 py-2.5 bg-brand text-white rounded-lg text-xs font-bold hover:bg-brand-light transition-colors flex items-center gap-2 shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                New Ticket
            </button>
        </div>
        {{-- Stats --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mt-4">
            <div class="bg-white border border-gray-100 rounded-xl p-3.5">
                <p class="text-xl font-black text-brand">{{ $tickets->total() }}</p>
                <p class="text-[10px] font-bold text-brand-muted uppercase tracking-wider">Total Tickets</p>
            </div>
            <div class="bg-white border border-gray-100 rounded-xl p-3.5">
                <p class="text-xl font-black text-amber-600">{{ $urgentCount }}</p>
                <p class="text-[10px] font-bold text-brand-muted uppercase tracking-wider">Urgent</p>
            </div>
            <div class="bg-white border border-gray-100 rounded-xl p-3.5">
                <p class="text-xl font-black text-blue-600">{{ $openCount }}</p>
                <p class="text-[10px] font-bold text-brand-muted uppercase tracking-wider">Open</p>
            </div>
            <div class="bg-white border border-gray-100 rounded-xl p-3.5">
                <p class="text-xl font-black text-brand">{{ $unassignedCount }}</p>
                <p class="text-[10px] font-bold text-brand-muted uppercase tracking-wider">Unassigned</p>
            </div>
        </div>
    </div>

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

    {{-- Main Panel --}}
    <div class="bg-white border border-gray-100 rounded-xl overflow-hidden">
        <div class="flex flex-col lg:flex-row">
            {{-- Sidebar Filters --}}
            <div class="lg:w-56 border-b lg:border-b-0 lg:border-r border-gray-100 bg-surface/20 shrink-0">
                <div class="p-4 border-b border-gray-100">
                    <button @click="showCompose = true" class="w-full py-2.5 bg-brand text-white rounded-lg text-xs font-bold hover:bg-brand-light transition-colors flex items-center justify-center gap-2">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        New Ticket
                    </button>
                </div>
                <div class="p-3 space-y-0.5">
                    <p class="px-2 mb-2 text-[9px] font-bold text-brand-muted uppercase tracking-wider">Queues</p>
                    <a href="{{ route('orchestrator.support.tickets', ['queue' => 'unassigned']) }}" class="flex items-center justify-between px-3 py-2 rounded-lg text-xs font-bold transition-colors {{ $currentQueue === 'unassigned' ? 'bg-brand text-white' : 'text-brand-muted hover:text-brand hover:bg-surface' }}">
                        <span>Unassigned</span>
                        @if($unassignedCount > 0)<span class="text-[10px] font-black {{ $currentQueue === 'unassigned' ? 'text-white/70' : 'text-brand-muted' }}">{{ $unassignedCount }}</span>@endif
                    </a>
                    <a href="{{ route('orchestrator.support.tickets', ['queue' => 'mine']) }}" class="flex items-center justify-between px-3 py-2 rounded-lg text-xs font-bold transition-colors {{ $currentQueue === 'mine' ? 'bg-brand text-white' : 'text-brand-muted hover:text-brand hover:bg-surface' }}">
                        <span>My Tickets</span>
                        @if($counts['mine'] > 0)<span class="text-[10px] font-black {{ $currentQueue === 'mine' ? 'text-white/70' : 'text-brand-muted' }}">{{ $counts['mine'] }}</span>@endif
                    </a>
                    <a href="{{ route('orchestrator.support.tickets') }}" class="flex items-center justify-between px-3 py-2 rounded-lg text-xs font-bold transition-colors {{ !$currentQueue && !$currentStatus ? 'bg-brand text-white' : 'text-brand-muted hover:text-brand hover:bg-surface' }}">
                        <span>All Tickets</span>
                    </a>
                    <a href="{{ route('orchestrator.support.tickets', ['status' => 'closed']) }}" class="flex items-center justify-between px-3 py-2 rounded-lg text-xs font-bold transition-colors {{ $currentStatus === 'closed' ? 'bg-brand text-white' : 'text-brand-muted hover:text-brand hover:bg-surface' }}">
                        <span>Closed</span>
                    </a>
                </div>
                <div class="p-3 border-t border-gray-100 space-y-0.5">
                    <p class="px-2 mb-2 text-[9px] font-bold text-brand-muted uppercase tracking-wider">Categories</p>
                    @foreach([
                        ['billing', 'Billing', 'bg-red-500'],
                        ['driver_behavior', 'Driver Behavior', 'bg-blue-500'],
                        ['app_issue', 'App Issues', 'bg-purple-500'],
                        ['account', 'Account', 'bg-emerald-500'],
                        ['other', 'General', 'bg-gray-400'],
                    ] as [$cat, $label, $color])
                    <a href="{{ route('orchestrator.support.tickets', ['category' => $cat]) }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs font-medium transition-colors {{ $currentCategory === $cat ? 'bg-brand/10 text-brand font-bold' : 'text-brand-muted hover:text-brand hover:bg-surface' }}">
                        <span class="w-1.5 h-1.5 rounded-full {{ $color }}"></span>
                        {{ $label }}
                    </a>
                    @endforeach
                </div>
            </div>

            {{-- Ticket List --}}
            <div class="flex-1 flex flex-col min-w-0">
                {{-- Search & Toolbar --}}
                <div class="px-4 py-3 border-b border-gray-100 flex items-center gap-3 flex-wrap">
                    <form action="{{ route('orchestrator.support.tickets') }}" method="GET" class="flex-1 min-w-0">
                        @if($currentQueue) <input type="hidden" name="queue" value="{{ $currentQueue }}"> @endif
                        @if($currentStatus) <input type="hidden" name="status" value="{{ $currentStatus }}"> @endif
                        @if($currentCategory) <input type="hidden" name="category" value="{{ $currentCategory }}"> @endif
                        <div class="relative">
                            <svg class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search tickets..." class="w-full bg-surface border border-gray-100 rounded-lg pl-9 pr-3 py-2 text-sm outline-none focus:ring-2 focus:ring-accent/20 transition-shadow">
                        </div>
                    </form>
                </div>

                {{-- List --}}
                <div class="divide-y divide-gray-50">
                    @forelse($tickets as $ticket)
                    <a href="{{ route('orchestrator.support.ticket.show', $ticket->id) }}" class="block px-4 py-3.5 hover:bg-surface/30 transition-colors group">
                        <div class="flex items-start gap-3">
                            {{-- Priority bar --}}
                            <div class="w-0.5 h-12 rounded-full shrink-0 mt-0.5
                                @if($ticket->status === 'closed') bg-gray-200
                                @elseif($ticket->priority === 'urgent') bg-red-500
                                @elseif($ticket->priority === 'high') bg-orange-500
                                @elseif($ticket->priority === 'medium') bg-amber-500
                                @else bg-blue-500 @endif">
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between gap-2 mb-0.5">
                                    <div class="flex items-center gap-2 min-w-0">
                                        <span class="text-xs font-bold text-brand truncate">{{ $ticket->user->name ?? 'Unknown' }}</span>
                                        @if($ticket->status !== 'closed')
                                        <span class="px-1.5 py-0.5 rounded text-[9px] font-bold uppercase tracking-wider shrink-0
                                            @if($ticket->priority === 'urgent') bg-red-100 text-red-700
                                            @elseif($ticket->priority === 'high') bg-orange-100 text-orange-700
                                            @elseif($ticket->priority === 'medium') bg-amber-100 text-amber-700
                                            @else bg-blue-100 text-blue-700 @endif">
                                            {{ $ticket->priority }}
                                        </span>
                                        @else
                                        <span class="px-1.5 py-0.5 rounded text-[9px] font-bold uppercase tracking-wider bg-gray-100 text-gray-500 shrink-0">Closed</span>
                                        @endif
                                        <span class="text-[10px] text-brand-muted hidden sm:inline">{{ $ticket->user_type ?? 'user' }}</span>
                                    </div>
                                    <span class="text-[10px] text-brand-muted whitespace-nowrap shrink-0">{{ $ticket->created_at->diffForHumans() }}</span>
                                </div>
                                <h4 class="text-sm font-bold text-brand truncate group-hover:text-accent transition-colors">{{ $ticket->subject }}</h4>
                                <div class="flex items-center gap-3 mt-1 flex-wrap">
                                    <span class="text-[10px] font-bold text-brand-muted font-mono">{{ $ticket->ticket_number ?? 'N/A' }}</span>
                                    <span class="flex items-center gap-1 text-[10px] font-bold text-brand-muted">
                                        <span class="w-1.5 h-1.5 rounded-full
                                            @if($ticket->category === 'billing') bg-red-500
                                            @elseif($ticket->category === 'driver_behavior') bg-blue-500
                                            @elseif($ticket->category === 'app_issue') bg-purple-500
                                            @else bg-gray-400 @endif">
                                        </span>
                                        {{ ucfirst(str_replace('_', ' ', $ticket->category ?? 'general')) }}
                                    </span>
                                    @if($ticket->assignedTo)
                                    <span class="flex items-center gap-1 text-[10px] font-bold text-accent">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                        {{ $ticket->assignedTo->name ?? 'Assigned' }}
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <svg class="w-4 h-4 text-gray-300 group-hover:text-accent transition-colors shrink-0 mt-1 hidden sm:block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </div>
                    </a>
                    @empty
                    <div class="flex flex-col items-center justify-center py-16 text-brand-muted">
                        <svg class="w-14 h-14 mb-3 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 19v-8.93a2 2 0 01.89-1.664l7-4.666a2 2 0 012.22 0l7 4.666A2 2 0 0121 10.07V19M3 19a2 2 0 002 2h14a2 2 0 002-2M3 19l6.75-4.5M21 19l-6.75-4.5M3 10l6.75 4.5M21 10l-6.75 4.5m0 0l-1.14.76a2 2 0 01-2.22 0l-1.14-.76"/></svg>
                        <p class="text-sm font-bold">No tickets found</p>
                        <p class="text-xs mt-1">Try a different filter or create a new ticket.</p>
                    </div>
                    @endforelse
                </div>

                {{-- Pagination --}}
                @if($tickets->hasPages())
                <div class="px-4 py-3 border-t border-gray-100 bg-surface/20">
                    {{ $tickets->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Compose Modal --}}
    <div x-show="showCompose" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-brand/50 backdrop-blur-sm" @click="showCompose = false"></div>
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl relative z-10 max-h-[90vh] overflow-y-auto" @click.outside="showCompose = false">
            <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 bg-accent/10 rounded-lg flex items-center justify-center text-accent">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-brand">New Support Ticket</h3>
                        <p class="text-xs text-brand-muted">Create a support thread for a customer or driver.</p>
                    </div>
                </div>
                <button @click="showCompose = false" class="w-7 h-7 bg-surface rounded-lg flex items-center justify-center text-brand-muted hover:text-brand transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form action="{{ route('orchestrator.support.tickets.store') }}" method="POST" class="p-6 space-y-4">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="text-[10px] font-bold text-brand-muted uppercase tracking-wider mb-1.5 block">Customer / Driver <span class="text-red-500">*</span></label>
                        <select name="user_id" required class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-medium outline-none focus:ring-2 focus:ring-accent/20 transition-shadow">
                            <option value="">Select user...</option>
                            @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }} ({{ ucfirst($user->user_type) }} - {{ $user->phone }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-[10px] font-bold text-brand-muted uppercase tracking-wider mb-1.5 block">Category <span class="text-red-500">*</span></label>
                        <select name="category" required class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-medium outline-none focus:ring-2 focus:ring-accent/20 transition-shadow">
                            <option value="billing">Billing & Payments</option>
                            <option value="driver_behavior">Driver Conduct</option>
                            <option value="app_issue">Technical / App Issue</option>
                            <option value="account">Account Access</option>
                            <option value="other">General Inquiry</option>
                        </select>
                    </div>
                    <div class="sm:col-span-2">
                        <label class="text-[10px] font-bold text-brand-muted uppercase tracking-wider mb-1.5 block">Subject <span class="text-red-500">*</span></label>
                        <input type="text" name="subject" required placeholder="e.g. Refund request for order #1234" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-medium outline-none focus:ring-2 focus:ring-accent/20 transition-shadow">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="text-[10px] font-bold text-brand-muted uppercase tracking-wider mb-1.5 block">Priority</label>
                        <div class="flex gap-2">
                            @foreach(['low' => 'bg-blue-100 text-blue-700', 'medium' => 'bg-amber-100 text-amber-700', 'high' => 'bg-orange-100 text-orange-700', 'urgent' => 'bg-red-100 text-red-700'] as $p => $classes)
                            <label class="flex-1 cursor-pointer">
                                <input type="radio" name="priority" value="{{ $p }}" {{ $p === 'medium' ? 'checked' : '' }} class="hidden peer">
                                <div class="text-center py-2 text-[10px] font-bold uppercase rounded-lg border border-gray-200 peer-checked:ring-2 peer-checked:ring-accent peer-checked:border-accent hover:bg-surface transition-all {{ $classes }}">{{ $p }}</div>
                            </label>
                            @endforeach
                        </div>
                    </div>
                    <div class="sm:col-span-2">
                        <label class="text-[10px] font-bold text-brand-muted uppercase tracking-wider mb-1.5 block">Message <span class="text-red-500">*</span></label>
                        <textarea name="message" required rows="4" placeholder="Describe the issue in detail..." class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-medium outline-none focus:ring-2 focus:ring-accent/20 transition-shadow resize-none"></textarea>
                    </div>
                </div>
                <div class="flex justify-end gap-2 pt-2 border-t border-gray-100">
                    <button type="button" @click="showCompose = false" class="px-4 py-2 text-xs font-bold text-brand-muted hover:text-brand transition-colors">Cancel</button>
                    <button type="submit" class="px-5 py-2 bg-brand text-white rounded-lg text-xs font-bold hover:bg-brand-light transition-colors flex items-center gap-2">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Create Ticket
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection