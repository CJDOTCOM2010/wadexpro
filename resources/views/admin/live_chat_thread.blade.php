@extends('admin.layout')

@section('title', 'Live Chat')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div class="flex items-center gap-4">
        <a href="{{ route('orchestrator.livechat') }}" class="text-brand-muted hover:text-brand transition">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <h2 class="text-2xl font-black text-brand tracking-tight">Support Conversation</h2>
    </div>
    
    <div class="flex gap-2">
        @if($conversation->status !== 'closed')
            <form action="{{ route('orchestrator.livechat.close', $conversation->id) }}" method="POST">
                @csrf @method('PATCH')
                <button type="submit" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded text-sm transition">Close Chat</button>
            </form>
        @else
            <form action="{{ route('orchestrator.livechat.reopen', $conversation->id) }}" method="POST">
                @csrf @method('PATCH')
                <button type="submit" class="px-4 py-2 bg-accent hover:bg-accent/90 text-white font-bold rounded text-sm transition">Reopen Chat</button>
            </form>
        @endif
    </div>
</div>

<div class="flex h-[75vh] bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    {{-- Sidebar (Active Chats) --}}
    <div class="w-1/3 border-r border-gray-100 flex flex-col hidden md:flex">
        <div class="p-4 border-b border-gray-100 bg-surface/30">
            <h3 class="font-bold text-brand text-sm uppercase tracking-widest">Active Chats</h3>
        </div>
        <div class="flex-1 overflow-y-auto">
            @foreach($conversations as $chat)
                @php 
                    $customer = $chat->participants->where('user_type', '!=', 'admin')->first(); 
                    $user = $customer ? $customer->user : null;
                @endphp
                <a href="{{ route('orchestrator.livechat.show', $chat->id) }}" class="block p-4 border-b border-gray-50 hover:bg-surface/50 transition {{ $chat->id === $conversation->id ? 'bg-surface border-l-4 border-l-accent' : '' }}">
                    <h4 class="font-bold text-sm text-brand truncate">{{ $user ? $user->name : 'Unknown User' }}</h4>
                    <p class="text-xs text-brand-muted truncate mt-1">{{ $chat->latestMessage ? $chat->latestMessage->content : '' }}</p>
                </a>
            @endforeach
        </div>
    </div>

    {{-- Main Chat Area --}}
    <div class="flex-1 flex flex-col bg-gray-50/50">
        {{-- Chat Header --}}
        @php 
            $currentCustomer = $conversation->participants->where('user_type', '!=', 'admin')->first(); 
            $currentUser = $currentCustomer ? $currentCustomer->user : null;
        @endphp
        <div class="p-4 bg-white border-b border-gray-100 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-accent/20 text-accent flex items-center justify-center font-bold">
                    {{ $currentUser ? substr($currentUser->name, 0, 1) : 'U' }}
                </div>
                <div>
                    <h3 class="font-bold text-brand">{{ $currentUser ? $currentUser->name : 'Customer' }}</h3>
                    <p class="text-xs text-brand-muted">{{ $currentUser ? $currentUser->email : '' }}</p>
                </div>
            </div>
            <div>
                @if($conversation->status === 'active')
                    <span class="px-2 py-1 bg-green-100 text-green-700 rounded text-[10px] font-bold uppercase tracking-wider">Active</span>
                @else
                    <span class="px-2 py-1 bg-gray-100 text-gray-600 rounded text-[10px] font-bold uppercase tracking-wider">{{ $conversation->status }}</span>
                @endif
            </div>
        </div>

        {{-- Messages --}}
        <div class="flex-1 p-6 overflow-y-auto flex flex-col gap-4" id="chat-messages">
            @forelse($messages as $msg)
                @php 
                    $isAdmin = $msg->sender && $msg->sender->user_type === 'admin'; 
                    $isSystem = $msg->message_type === 'system';
                @endphp
                
                @if($isSystem)
                    <div class="flex justify-center my-2">
                        <span class="px-3 py-1 bg-gray-200 text-gray-600 text-[10px] font-bold uppercase tracking-widest rounded-full">
                            {{ $msg->content }}
                        </span>
                    </div>
                @else
                    <div class="flex {{ $isAdmin ? 'justify-end' : 'justify-start' }}">
                        <div class="max-w-[70%]">
                            <div class="flex items-center gap-2 mb-1 {{ $isAdmin ? 'flex-row-reverse' : '' }}">
                                <span class="text-[10px] font-bold text-gray-500">{{ $msg->sender ? $msg->sender->name : 'Unknown' }}</span>
                                <span class="text-[9px] text-gray-400">{{ $msg->created_at->format('H:i') }}</span>
                            </div>
                            <div class="px-4 py-2 rounded-2xl {{ $isAdmin ? 'bg-brand text-white rounded-tr-sm' : 'bg-white border border-gray-100 shadow-sm text-gray-800 rounded-tl-sm' }}">
                                <p class="text-sm whitespace-pre-wrap">{{ $msg->content }}</p>
                            </div>
                        </div>
                    </div>
                @endif
            @empty
                <div class="flex-1 flex items-center justify-center text-gray-400">
                    No messages yet.
                </div>
            @endforelse
        </div>

        {{-- Input Area --}}
        @if($conversation->status !== 'closed')
            <div class="p-4 bg-white border-t border-gray-100">
                <form action="{{ route('orchestrator.livechat.reply', $conversation->id) }}" method="POST" class="flex gap-2">
                    @csrf
                    <input type="text" name="message" required placeholder="Type your reply..." autofocus
                           class="flex-1 bg-surface border border-gray-200 rounded-lg px-4 py-3 text-sm focus:ring-2 focus:ring-accent/20 outline-none">
                    <button type="submit" class="px-6 py-3 bg-accent text-white font-bold rounded-lg hover:bg-accent/90 transition shadow-sm flex items-center gap-2">
                        <span>Send</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                    </button>
                </form>
            </div>
        @else
            <div class="p-4 bg-gray-50 border-t border-gray-100 text-center text-sm text-gray-500">
                This conversation is closed. Reopen to send a message.
            </div>
        @endif
    </div>
</div>

<script>
    // Auto-scroll to bottom of chat
    const chatContainer = document.getElementById('chat-messages');
    if(chatContainer) {
        chatContainer.scrollTop = chatContainer.scrollHeight;
    }
</script>
@endsection
