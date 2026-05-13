@extends('admin.layout')
@section('title', 'Ticket Inbox')
@section('content')

<div x-data="{ showComposeModal: false, searchUser: '' }">
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-black text-brand tracking-tight">Support Tickets</h2>
            <p class="text-brand-muted font-medium mt-1">Resolve customer and driver issues, disputes, and inquiries.</p>
        </div>
    </div>

    <div class="bg-white rounded-lg border border-gray-100 shadow-sm overflow-hidden flex h-[700px]">
        
        <!-- Sidebar / Filters -->
        <div class="w-64 border-r border-gray-50 flex flex-col bg-surface/30 shrink-0">
            <div class="p-4 border-b border-gray-50">
                <button @click="showComposeModal = true" class="w-full py-2.5 bg-brand text-white font-bold rounded hover:bg-brand-light transition text-sm flex items-center justify-center gap-2 shadow-lg shadow-black/10">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Compose New
                </button>
            </div>
            <div class="flex-1 overflow-y-auto py-4">
                <div class="px-4 mb-2 text-[10px] font-black text-brand-muted uppercase tracking-widest">Queues</div>
                
                <a href="{{ route('orchestrator.support.tickets', ['queue' => 'unassigned']) }}" class="flex items-center justify-between px-4 py-2 hover:bg-white transition {{ request('queue') == 'unassigned' ? 'bg-white border-l-2 border-brand text-brand' : 'text-brand/70' }}">
                    <span class="text-sm font-bold">Unassigned</span>
                    @if($counts['unassigned'] > 0)
                    <span class="bg-brand text-white text-[10px] font-black px-2 py-0.5 rounded-full">{{ $counts['unassigned'] }}</span>
                    @endif
                </a>
                
                <a href="{{ route('orchestrator.support.tickets', ['queue' => 'mine']) }}" class="flex items-center justify-between px-4 py-2 hover:bg-white transition {{ request('queue') == 'mine' ? 'bg-white border-l-2 border-brand text-brand' : 'text-brand/70' }}">
                    <span class="text-sm font-bold">My Tickets</span>
                    @if($counts['mine'] > 0)
                    <span class="bg-surface border border-gray-200 text-brand-muted text-[10px] font-black px-2 py-0.5 rounded-full">{{ $counts['mine'] }}</span>
                    @endif
                </a>
                
                <a href="{{ route('orchestrator.support.tickets') }}" class="flex items-center justify-between px-4 py-2 hover:bg-white transition {{ !request('queue') && !request('status') ? 'bg-white border-l-2 border-brand text-brand' : 'text-brand/70' }}">
                    <span class="text-sm font-bold">All Tickets</span>
                </a>

                <a href="{{ route('orchestrator.support.tickets', ['status' => 'closed']) }}" class="flex items-center justify-between px-4 py-2 hover:bg-white transition {{ request('status') == 'closed' ? 'bg-white border-l-2 border-brand text-brand' : 'text-brand/70' }}">
                    <span class="text-sm font-bold">Resolved / Closed</span>
                </a>
                
                <div class="px-4 mt-8 mb-2 text-[10px] font-black text-brand-muted uppercase tracking-widest">Categories</div>
                <a href="{{ route('orchestrator.support.tickets', ['category' => 'billing']) }}" class="flex items-center gap-2 px-4 py-2 text-brand/70 hover:bg-white transition text-sm font-medium {{ request('category') == 'billing' ? 'font-bold text-brand bg-white' : '' }}">
                    <span class="w-2 h-2 rounded-full bg-red-500"></span> Billing Disputes
                </a>
                <a href="{{ route('orchestrator.support.tickets', ['category' => 'driver_behavior']) }}" class="flex items-center gap-2 px-4 py-2 text-brand/70 hover:bg-white transition text-sm font-medium {{ request('category') == 'driver_behavior' ? 'font-bold text-brand bg-white' : '' }}">
                    <span class="w-2 h-2 rounded-full bg-blue-500"></span> Driver Behavior
                </a>
                <a href="{{ route('orchestrator.support.tickets', ['category' => 'app_issue']) }}" class="flex items-center gap-2 px-4 py-2 text-brand/70 hover:bg-white transition text-sm font-medium {{ request('category') == 'app_issue' ? 'font-bold text-brand bg-white' : '' }}">
                    <span class="w-2 h-2 rounded-full bg-purple-500"></span> App Issues
                </a>
            </div>
        </div>

        <!-- Ticket List -->
        <div class="flex-1 flex flex-col">
            <!-- Toolbar -->
            <div class="p-4 border-b border-gray-50 flex items-center justify-between bg-white">
                <form action="{{ route('orchestrator.support.tickets') }}" method="GET" class="relative w-64">
                    @if(request('queue')) <input type="hidden" name="queue" value="{{ request('queue') }}"> @endif
                    @if(request('status')) <input type="hidden" name="status" value="{{ request('status') }}"> @endif
                    @if(request('category')) <input type="hidden" name="category" value="{{ request('category') }}"> @endif
                    
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search tickets..." class="w-full bg-surface border border-gray-100 rounded pl-9 pr-3 py-1.5 text-sm font-medium outline-none focus:ring-1 focus:ring-brand/20 transition-all">
                    <svg class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </form>
                <div class="flex items-center gap-2">
                    <button class="p-1.5 text-gray-400 hover:text-brand transition"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg></button>
                </div>
            </div>

            <!-- List -->
            <div class="flex-1 overflow-y-auto bg-surface/10 divide-y divide-gray-50">
                @forelse($tickets as $ticket)
                <a href="{{ route('orchestrator.support.ticket.show', $ticket->id) }}" class="block p-4 bg-white hover:bg-surface/50 transition border-l-4 {{ $ticket->status == 'closed' ? 'border-gray-300 opacity-60' : 'border-'.$ticket->priorityColor().'-500' }}">
                    <div class="flex items-start justify-between mb-1">
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-bold text-brand">{{ ucfirst($ticket->user_type) }}: {{ $ticket->user->name ?? 'Unknown' }}</span>
                            @if($ticket->status != 'closed')
                                <span class="px-2 py-0.5 rounded text-[9px] font-black uppercase tracking-widest bg-{{ $ticket->priorityColor() }}-100 text-{{ $ticket->priorityColor() }}-700">{{ $ticket->priority }}</span>
                            @else
                                <span class="px-2 py-0.5 rounded text-[9px] font-black uppercase tracking-widest bg-gray-100 text-gray-600">Closed</span>
                            @endif
                        </div>
                        <span class="text-xs text-brand-muted whitespace-nowrap">{{ $ticket->created_at->diffForHumans() }}</span>
                    </div>
                    <h4 class="text-sm font-bold text-brand mb-1 truncate">{{ $ticket->subject }}</h4>
                    <div class="mt-3 flex items-center gap-4 text-[10px] font-bold uppercase tracking-widest text-brand-muted">
                        <span>{{ $ticket->ticket_number }}</span>
                        <span class="flex items-center gap-1">
                            @if($ticket->category == 'billing')
                                <span class="w-2 h-2 rounded-full bg-red-500"></span> Billing
                            @elseif($ticket->category == 'driver_behavior')
                                <span class="w-2 h-2 rounded-full bg-blue-500"></span> Driver Behavior
                            @else
                                <span class="w-2 h-2 rounded-full bg-gray-400"></span> {{ ucfirst(str_replace('_', ' ', $ticket->category)) }}
                            @endif
                        </span>
                        @if($ticket->assignedTo)
                            <span class="flex items-center gap-1 text-accent"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg> {{ $ticket->assignedTo->name }}</span>
                        @endif
                    </div>
                </a>
                @empty
                <div class="p-12 flex flex-col items-center justify-center text-gray-400 h-full">
                    <svg class="w-16 h-16 mb-4 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 19v-8.93a2 2 0 01.89-1.664l7-4.666a2 2 0 012.22 0l7 4.666A2 2 0 0121 10.07V19M3 19a2 2 0 002 2h14a2 2 0 002-2M3 19l6.75-4.5M21 19l-6.75-4.5M3 10l6.75 4.5M21 10l-6.75 4.5m0 0l-1.14.76a2 2 0 01-2.22 0l-1.14-.76"/></svg>
                    <p class="text-sm font-bold text-brand-muted">No tickets found in this view.</p>
                </div>
                @endforelse
                
                <div class="p-4 border-t border-gray-50 bg-white">
                    {{ $tickets->links() }}
                </div>
            </div>
        </div>
    </div>

    <!-- Compose New Ticket Modal -->
    <div x-show="showComposeModal" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center p-4">
        <div @click="showComposeModal = false" class="absolute inset-0 bg-brand/60 backdrop-blur-sm"></div>
        <div class="relative bg-white w-full max-w-2xl rounded-2xl shadow-2xl overflow-hidden animate-fade-in-down" @click.stop>
            <div class="p-8 border-b border-gray-100 flex items-center justify-between bg-surface/30">
                <div>
                    <h3 class="text-2xl font-black text-brand tracking-tight">Compose New Ticket</h3>
                    <p class="text-sm text-brand-muted font-medium">Initiate a formal support thread for a user.</p>
                </div>
                <button @click="showComposeModal = false" class="p-2 hover:bg-white rounded-full transition">
                    <svg class="w-6 h-6 text-brand-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form action="{{ route('orchestrator.support.tickets.store') }}" method="POST" class="p-8 space-y-6">
                @csrf
                
                <div class="grid grid-cols-2 gap-6">
                    <!-- User Selection -->
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-brand-muted uppercase tracking-widest">Involved Party (Customer/Driver)</label>
                        <select name="user_id" required class="w-full bg-surface border border-gray-100 rounded-lg px-4 py-3 text-sm font-bold focus:ring-2 focus:ring-accent outline-none transition">
                            <option value="">Select User...</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }} ({{ ucfirst($user->user_type) }} - {{ $user->phone }})</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Category -->
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-brand-muted uppercase tracking-widest">Support Category</label>
                        <select name="category" required class="w-full bg-surface border border-gray-100 rounded-lg px-4 py-3 text-sm font-bold focus:ring-2 focus:ring-accent outline-none transition">
                            <option value="billing">Billing & Payments</option>
                            <option value="driver_behavior">Driver Conduct</option>
                            <option value="app_issue">Technical / App Issue</option>
                            <option value="account">Account Access</option>
                            <option value="other">General Inquiry</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-6">
                    <!-- Subject -->
                    <div class="space-y-2 col-span-1">
                        <label class="text-[10px] font-black text-brand-muted uppercase tracking-widest">Subject Line</label>
                        <input type="text" name="subject" required placeholder="e.g. Refund request for order #1234" class="w-full bg-surface border border-gray-100 rounded-lg px-4 py-3 text-sm font-bold focus:ring-2 focus:ring-accent outline-none transition">
                    </div>

                    <!-- Priority -->
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-brand-muted uppercase tracking-widest">Severity / Priority</label>
                        <div class="flex gap-2">
                            @foreach(['low', 'medium', 'high', 'urgent'] as $p)
                                <label class="flex-1">
                                    <input type="radio" name="priority" value="{{ $p }}" {{ $p == 'medium' ? 'checked' : '' }} class="hidden peer">
                                    <div class="text-center py-2 text-[10px] font-black uppercase rounded-lg border border-gray-100 cursor-pointer transition peer-checked:bg-accent peer-checked:text-brand peer-checked:border-accent hover:bg-surface">
                                        {{ $p }}
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Message -->
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-brand-muted uppercase tracking-widest">Initial Incident Report / Message</label>
                    <textarea name="message" required rows="5" placeholder="Describe the issue in detail..." class="w-full bg-surface border border-gray-100 rounded-lg px-4 py-4 text-sm font-medium focus:ring-2 focus:ring-accent outline-none transition resize-none"></textarea>
                </div>

                <div class="pt-4 flex items-center justify-end gap-4">
                    <button type="button" @click="showComposeModal = false" class="px-6 py-3 text-sm font-bold text-brand-muted hover:text-brand transition">Discard</button>
                    <button type="submit" class="px-8 py-3 bg-brand text-white font-black rounded-lg hover:bg-brand-light transition shadow-xl shadow-black/20 flex items-center gap-2">
                        Create Ticket
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
