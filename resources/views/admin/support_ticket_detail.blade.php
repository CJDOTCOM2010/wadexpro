@extends('admin.layout')
@section('title', 'Ticket Detail')
@section('content')

<div class="mb-6">
    <a href="{{ route('orchestrator.support.tickets') }}" class="text-sm font-bold text-brand-muted hover:text-brand transition flex items-center gap-1">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        Back to Inbox
    </a>
</div>

<div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
    
    <!-- Main Conversation -->
    <div class="xl:col-span-2 flex flex-col h-[700px] bg-white rounded-lg border border-gray-100 shadow-sm overflow-hidden">
        
        <!-- Header -->
        <div class="p-6 border-b border-gray-50 flex items-start justify-between bg-surface/30">
            <div>
                <div class="flex items-center gap-2 mb-2">
                    @if($ticket->status != 'closed')
                        <span class="px-2 py-0.5 rounded bg-{{ $ticket->priorityColor() }}-100 text-{{ $ticket->priorityColor() }}-700 text-[10px] font-black uppercase tracking-widest">{{ $ticket->priority }}</span>
                    @else
                        <span class="px-2 py-0.5 rounded bg-gray-200 text-gray-700 text-[10px] font-black uppercase tracking-widest">Closed</span>
                    @endif
                    <span class="text-xs text-brand-muted font-mono font-medium">{{ $ticket->ticket_number }}</span>
                </div>
                <h2 class="text-xl font-black text-brand tracking-tight">{{ $ticket->subject }}</h2>
            </div>
            <div class="flex gap-2">
                @if(!$ticket->assigned_to && $ticket->isOpen())
                    <form action="{{ route('orchestrator.support.ticket.assign', $ticket->id) }}" method="POST">
                        @csrf
                        <input type="hidden" name="assigned_to" value="{{ auth('admin')->id() }}">
                        <button type="submit" class="px-4 py-2 bg-surface text-brand text-xs font-bold rounded border border-gray-200 hover:bg-gray-100 transition">Assign to Me</button>
                    </form>
                @endif
                
                @if($ticket->status == 'in_progress' || $ticket->status == 'open')
                    <form action="{{ route('orchestrator.support.ticket.resolve', $ticket->id) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="px-4 py-2 bg-green-50 text-green-700 text-xs font-bold rounded border border-green-200 hover:bg-green-100 transition">Mark Resolved</button>
                    </form>
                @endif

                @if($ticket->status != 'closed')
                    <form action="{{ route('orchestrator.support.ticket.close', $ticket->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to completely close this ticket?');">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="px-4 py-2 bg-white text-gray-500 text-xs font-bold rounded border border-gray-200 hover:bg-gray-50 transition">Close Ticket</button>
                    </form>
                @endif
            </div>
        </div>

        <!-- Thread -->
        <div class="flex-1 overflow-y-auto p-6 space-y-6 bg-surface/10" id="chat-thread">
            
            <!-- Original Message (Simulated here if no replies yet, or first reply represents it) -->
            @forelse($ticket->replies as $reply)
                @if($reply->is_internal)
                    <!-- Internal Note -->
                    <div class="flex gap-4">
                        <div class="w-10 h-10 rounded-full bg-amber-100 text-amber-600 flex items-center justify-center shrink-0 border border-amber-200"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg></div>
                        <div class="flex-1">
                            <div class="flex items-baseline gap-2 mb-1">
                                <span class="text-sm font-bold text-amber-800">{{ $reply->sender->name ?? 'System' }}</span>
                                <span class="text-[10px] text-amber-600/70 uppercase tracking-widest">Internal Note • {{ $reply->created_at->diffForHumans() }}</span>
                            </div>
                            <div class="bg-amber-50 p-4 rounded-lg rounded-tl-none border border-amber-100 text-sm text-amber-900 leading-relaxed font-medium">
                                {{ $reply->message }}
                            </div>
                        </div>
                    </div>
                @else
                    <!-- Public Message -->
                    <div class="flex gap-4 {{ $reply->sender_type == 'admin' ? 'flex-row-reverse' : '' }}">
                        <div class="w-10 h-10 rounded-full {{ $reply->sender_type == 'admin' ? 'bg-brand text-white' : 'bg-brand/10 text-brand border border-brand/20' }} font-bold flex items-center justify-center shrink-0">
                            {{ strtoupper(substr($reply->sender->name ?? 'U', 0, 2)) }}
                        </div>
                        <div class="flex-1 {{ $reply->sender_type == 'admin' ? 'text-right' : '' }}">
                            <div class="flex items-baseline gap-2 mb-1 {{ $reply->sender_type == 'admin' ? 'justify-end' : '' }}">
                                <span class="text-sm font-bold text-brand">{{ $reply->sender->name ?? 'Unknown' }}</span>
                                <span class="text-[10px] text-brand-muted uppercase tracking-widest">{{ ucfirst($reply->sender_type) }} • {{ $reply->created_at->diffForHumans() }}</span>
                            </div>
                            <div class="{{ $reply->sender_type == 'admin' ? 'bg-brand text-white rounded-tr-none ml-12' : 'bg-white text-brand/80 rounded-tl-none mr-12' }} p-4 rounded-lg border border-gray-100 shadow-sm text-sm leading-relaxed text-left">
                                {{ nl2br(e($reply->message)) }}
                            </div>
                        </div>
                    </div>
                @endif
            @empty
                <div class="text-center text-gray-500 py-12">
                    <p class="font-medium">No replies yet. This ticket is newly opened.</p>
                </div>
            @endforelse
        </div>

        <!-- Reply Box -->
        @if($ticket->isOpen())
        <div class="p-4 border-t border-gray-50 bg-white">
            <form action="{{ route('orchestrator.support.ticket.reply', $ticket->id) }}" method="POST">
                @csrf
                <div class="flex items-center gap-4 mb-2">
                    <label class="inline-flex items-center cursor-pointer">
                        <input type="radio" name="is_internal" value="0" checked class="text-brand focus:ring-brand">
                        <span class="ml-2 text-xs font-bold text-brand">Reply to Customer</span>
                    </label>
                    <label class="inline-flex items-center cursor-pointer">
                        <input type="radio" name="is_internal" value="1" class="text-amber-500 focus:ring-amber-500">
                        <span class="ml-2 text-xs font-bold text-amber-600">Add Internal Note</span>
                    </label>
                </div>
                <textarea name="message" class="w-full bg-surface border border-gray-100 rounded-lg p-3 text-sm outline-none focus:ring-2 focus:ring-brand/20 transition-all resize-none" rows="3" placeholder="Type your response here..." required></textarea>
                <div class="flex items-center justify-between mt-3">
                    <button type="button" class="text-gray-400 hover:text-brand transition"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg></button>
                    <button type="submit" class="px-6 py-2 bg-brand text-white text-sm font-bold rounded hover:bg-brand-light transition">Send Reply</button>
                </div>
            </form>
        </div>
        @else
        <div class="p-4 border-t border-gray-50 bg-surface/50 text-center">
            <p class="text-sm font-bold text-gray-500">This ticket is closed and cannot be replied to.</p>
        </div>
        @endif

    </div>

    <!-- Metadata Sidebar -->
    <div class="xl:col-span-1 space-y-6">
        
        <!-- User Context -->
        <div class="bg-white rounded-lg border border-gray-100 shadow-sm p-6">
            <h3 class="text-[10px] font-black text-brand-muted uppercase tracking-widest mb-4">Customer Details</h3>
            <div class="flex items-center gap-3 mb-4">
                <div class="w-12 h-12 rounded-full bg-brand/10 text-brand font-bold flex items-center justify-center shrink-0 border border-brand/20">
                    {{ strtoupper(substr($ticket->user->name ?? 'U', 0, 2)) }}
                </div>
                <div>
                    <p class="text-base font-bold text-brand">{{ $ticket->user->name ?? 'Unknown User' }}</p>
                    <p class="text-xs text-brand-muted">{{ $ticket->user->phone ?? 'No Phone' }}</p>
                </div>
            </div>
            <div class="space-y-3 pt-4 border-t border-gray-50">
                <div class="flex justify-between text-sm">
                    <span class="text-brand-muted">Account Type</span>
                    <span class="font-bold text-brand capitalize">{{ $ticket->user_type }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-brand-muted">Joined</span>
                    <span class="font-bold text-brand">{{ $ticket->user ? $ticket->user->created_at->format('M Y') : 'N/A' }}</span>
                </div>
            </div>
        </div>

        <!-- Associated Ride -->
        @if($ticket->order)
        <div class="bg-white rounded-lg border border-gray-100 shadow-sm p-6">
            <h3 class="text-[10px] font-black text-brand-muted uppercase tracking-widest mb-4">Associated Ride</h3>
            <div class="bg-surface rounded p-3 mb-4 border border-gray-100">
                <div class="flex justify-between items-center mb-2">
                    <span class="text-xs font-bold text-brand">{{ $ticket->order->tracking_code }}</span>
                    <span class="text-[9px] font-black uppercase tracking-widest text-brand bg-brand/10 px-2 py-0.5 rounded">{{ $ticket->order->status }}</span>
                </div>
                <div class="text-xs text-brand-muted space-y-1">
                    <p><strong>Fare:</strong> ₵ {{ number_format($ticket->order->final_fare, 2) }}</p>
                    <p><strong>Date:</strong> {{ $ticket->order->created_at->format('M d, Y') }}</p>
                </div>
            </div>
            <button class="w-full py-2 bg-surface text-brand text-xs font-bold rounded border border-gray-200 hover:bg-gray-100 transition">View Full Order</button>
        </div>
        @endif

        <!-- Assigned Agent -->
        <div class="bg-white rounded-lg border border-gray-100 shadow-sm p-6">
            <h3 class="text-[10px] font-black text-brand-muted uppercase tracking-widest mb-4">Assigned Agent</h3>
            @if($ticket->assignedTo)
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-accent text-white font-bold flex items-center justify-center shrink-0">
                        {{ strtoupper(substr($ticket->assignedTo->name, 0, 2)) }}
                    </div>
                    <div>
                        <p class="text-sm font-bold text-brand">{{ $ticket->assignedTo->name }}</p>
                        <p class="text-xs text-brand-muted">{{ ucfirst($ticket->assignedTo->role) }}</p>
                    </div>
                </div>
            @else
                <p class="text-sm text-gray-500 italic">Unassigned</p>
            @endif
            
            <form action="{{ route('orchestrator.support.ticket.assign', $ticket->id) }}" method="POST" class="mt-4 pt-4 border-t border-gray-50">
                @csrf
                <select name="assigned_to" onchange="this.form.submit()" class="w-full bg-surface border border-gray-100 rounded p-2 text-sm outline-none cursor-pointer">
                    <option value="">-- Reassign Ticket --</option>
                    @foreach($agents as $agent)
                        <option value="{{ $agent->id }}" {{ $ticket->assigned_to == $agent->id ? 'selected' : '' }}>{{ $agent->name }}</option>
                    @endforeach
                </select>
            </form>
        </div>

    </div>
</div>

<script>
    // Auto-scroll thread to bottom
    const thread = document.getElementById('chat-thread');
    if (thread) {
        thread.scrollTop = thread.scrollHeight;
    }
</script>

@endsection
