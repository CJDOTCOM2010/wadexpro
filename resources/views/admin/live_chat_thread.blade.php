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
    
    <div class="flex gap-2 items-center">
        <div id="connection-status" class="flex items-center gap-2 px-3 py-1.5 rounded-full text-[10px] font-bold uppercase tracking-widest bg-gray-100 text-gray-500">
            <span class="w-2 h-2 rounded-full bg-gray-400"></span>
            <span>Connecting...</span>
        </div>
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
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-accent/20 text-accent flex items-center justify-center font-bold text-sm shrink-0">
                            {{ $user ? substr($user->name, 0, 1) : 'U' }}
                        </div>
                        <div class="min-w-0">
                            <h4 class="font-bold text-sm text-brand truncate">{{ $user ? $user->name : 'Unknown User' }}</h4>
                            <p class="text-xs text-brand-muted truncate mt-0.5">{{ $chat->latestMessage ? $chat->latestMessage->content : '' }}</p>
                        </div>
                    </div>
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
                <div class="flex gap-2" id="chat-input-wrapper">
                    <input type="text" id="chat-input" placeholder="Type your reply..." autofocus
                           class="flex-1 bg-surface border border-gray-200 rounded-lg px-4 py-3 text-sm focus:ring-2 focus:ring-accent/20 outline-none">
                    <button type="button" id="send-btn" class="px-6 py-3 bg-accent text-white font-bold rounded-lg hover:bg-accent/90 transition shadow-sm flex items-center gap-2 disabled:opacity-50" disabled>
                        <span>Send</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                    </button>
                </div>
            </div>
        @else
            <div class="p-4 bg-gray-50 border-t border-gray-100 text-center text-sm text-gray-500">
                This conversation is closed. Reopen to send a message.
            </div>
        @endif
    </div>
</div>

@endsection

@push('scripts')
<script>
(function() {
    const conversationId = '{{ $conversation->id }}';
    const adminId = '{{ auth("admin")->id() }}';
    const adminName = '{{ auth("admin")->user()->name ?? "Agent" }}';
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const chatContainer = document.getElementById('chat-messages');
    const chatInput = document.getElementById('chat-input');
    const sendBtn = document.getElementById('send-btn');
    const statusEl = document.getElementById('connection-status');

    // ── Scroll to bottom ──
    function scrollToBottom() {
        if (chatContainer) {
            chatContainer.scrollTop = chatContainer.scrollHeight;
        }
    }
    scrollToBottom();

    // ── Append a new message bubble ──
    function appendMessage(data) {
        const isAdmin = data.fromType === 'admin';
        const time = new Date(data.timestamp).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        
        const wrapper = document.createElement('div');
        wrapper.className = `flex ${isAdmin ? 'justify-end' : 'justify-start'}`;
        wrapper.innerHTML = `
            <div class="max-w-[70%]">
                <div class="flex items-center gap-2 mb-1 ${isAdmin ? 'flex-row-reverse' : ''}">
                    <span class="text-[10px] font-bold text-gray-500">${data.fromName || 'Unknown'}</span>
                    <span class="text-[9px] text-gray-400">${time}</span>
                </div>
                <div class="px-4 py-2 rounded-2xl ${isAdmin ? 'bg-brand text-white rounded-tr-sm' : 'bg-white border border-gray-100 shadow-sm text-gray-800 rounded-tl-sm'}">
                    <p class="text-sm whitespace-pre-wrap">${escapeHtml(data.message)}</p>
                </div>
            </div>
        `;

        // Animate in
        wrapper.style.opacity = '0';
        wrapper.style.transform = 'translateY(10px)';
        chatContainer.appendChild(wrapper);
        requestAnimationFrame(() => {
            wrapper.style.transition = 'opacity 0.3s, transform 0.3s';
            wrapper.style.opacity = '1';
            wrapper.style.transform = 'translateY(0)';
        });
        
        scrollToBottom();
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function setStatus(connected) {
        if (!statusEl) return;
        if (connected) {
            statusEl.className = 'flex items-center gap-2 px-3 py-1.5 rounded-full text-[10px] font-bold uppercase tracking-widest bg-green-100 text-green-700';
            statusEl.innerHTML = '<span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span><span>Live</span>';
        } else {
            statusEl.className = 'flex items-center gap-2 px-3 py-1.5 rounded-full text-[10px] font-bold uppercase tracking-widest bg-red-100 text-red-600';
            statusEl.innerHTML = '<span class="w-2 h-2 rounded-full bg-red-500"></span><span>Disconnected</span>';
        }
    }

    // ── Socket.IO Connection ──
    let socket = null;
    try {
        socket = io(window.WADEX_SOCKET_URL + '/support', {
            transports: ['websocket', 'polling'],
            auth: {
                // Admin uses a special internal token for socket auth
                token: 'admin_internal_{{ auth("admin")->id() }}'
            },
            reconnection: true,
            reconnectionDelay: 2000,
            reconnectionAttempts: 10,
        });

        socket.on('connect', () => {
            console.log('Admin connected to /support namespace');
            setStatus(true);
            // Subscribe to this conversation's room
            socket.emit('chat:subscribe', conversationId);
        });

        socket.on('disconnect', () => {
            console.log('Disconnected from /support namespace');
            setStatus(false);
        });

        socket.on('connect_error', (err) => {
            console.warn('Socket connection error:', err.message);
            setStatus(false);
        });

        // Listen for incoming messages in this conversation
        socket.on('chat:message', (data) => {
            // Only render messages for this conversation
            if (data.conversationId !== conversationId) return;
            // Don't duplicate messages we just sent
            if (data.from === adminId && data._local) return;
            appendMessage(data);
        });

        // Listen for new customer messages (notification badge)
        socket.on('chat:new_customer_message', (data) => {
            if (data.conversationId === conversationId) {
                // Already handled by chat:message
                return;
            }
            // Could add a notification badge to sidebar here
            console.log('New message in another conversation:', data.conversationId);
        });

    } catch (e) {
        console.warn('Socket.IO init failed, falling back to HTTP:', e);
        setStatus(false);
    }

    // ── Send Message ──
    if (sendBtn && chatInput) {
        // Enable send button when input has content
        chatInput.addEventListener('input', () => {
            sendBtn.disabled = chatInput.value.trim().length === 0;
        });

        // Enter key to send
        chatInput.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                if (chatInput.value.trim()) sendMessage();
            }
        });

        sendBtn.addEventListener('click', sendMessage);
    }

    function sendMessage() {
        const message = chatInput.value.trim();
        if (!message) return;

        // Optimistic UI — immediately show the message
        appendMessage({
            conversationId: conversationId,
            from: adminId,
            fromName: adminName,
            fromType: 'admin',
            message: message,
            timestamp: new Date().toISOString(),
            _local: true,
        });

        // Clear input
        chatInput.value = '';
        sendBtn.disabled = true;
        chatInput.focus();

        // Persist via REST (which also triggers socket broadcast to Flutter)
        axios.post('{{ route("orchestrator.livechat.reply", $conversation->id) }}', {
            message: message
        }, {
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            }
        }).then(response => {
            console.log('Message persisted:', response.data);
        }).catch(err => {
            console.error('Failed to persist message:', err);
            // Show error indicator
            const errorEl = document.createElement('div');
            errorEl.className = 'flex justify-center my-1';
            errorEl.innerHTML = '<span class="px-3 py-1 bg-red-100 text-red-600 text-[10px] font-bold rounded-full">⚠ Failed to send. Please retry.</span>';
            chatContainer.appendChild(errorEl);
            scrollToBottom();
        });
    }
})();
</script>
@endpush
