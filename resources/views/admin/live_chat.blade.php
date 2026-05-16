@extends('admin.layout')
@section('title', 'Live Chat Support')
@section('content')

@if(session('error'))
<div class="mb-4 p-3.5 bg-red-50 border border-red-200 rounded-lg flex items-center gap-2.5">
    <svg class="w-4 h-4 text-red-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    <p class="text-sm font-medium text-red-700">{{ session('error') }}</p>
</div>
@endif

<div class="max-w-6xl mx-auto">
    {{-- Header --}}
    <div class="flex items-center gap-3 mb-6">
        <div>
            <div class="flex items-center gap-3">
                <h2 class="text-2xl font-black text-brand tracking-tight">Live Chat Support</h2>
                <span class="flex items-center gap-1.5 px-2.5 py-1 bg-green-100 text-green-700 text-[10px] font-bold rounded-full">
                    <span class="w-1.5 h-1.5 bg-green-500 rounded-full animate-pulse"></span> LIVE
                </span>
            </div>
            <p class="text-sm text-brand-muted font-medium mt-0.5">Real-time assistance for riders and drivers.</p>
        </div>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <div class="bg-white border border-gray-100 rounded-xl p-4 flex items-center gap-3.5">
            <div class="w-10 h-10 bg-green-50 rounded-lg flex items-center justify-center text-green-600 shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"/></svg>
            </div>
            <div>
                <p class="text-lg font-black text-brand">{{ $stats['active'] ?? 0 }}</p>
                <p class="text-[10px] font-bold text-brand-muted uppercase tracking-wider">Active Chats</p>
            </div>
        </div>
        <div class="bg-white border border-gray-100 rounded-xl p-4 flex items-center gap-3.5">
            <div class="w-10 h-10 bg-amber-50 rounded-lg flex items-center justify-center text-amber-600 shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <p class="text-lg font-black text-amber-600">{{ $stats['waiting'] ?? 0 }}</p>
                <p class="text-[10px] font-bold text-brand-muted uppercase tracking-wider">Waiting</p>
            </div>
        </div>
        <div class="bg-white border border-gray-100 rounded-xl p-4 flex items-center gap-3.5">
            <div class="w-10 h-10 bg-gray-50 rounded-lg flex items-center justify-center text-gray-500 shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            </div>
            <div>
                <p class="text-lg font-black text-gray-600">{{ $stats['closed'] ?? 0 }}</p>
                <p class="text-[10px] font-bold text-brand-muted uppercase tracking-wider">Closed Today</p>
            </div>
        </div>
    </div>

    {{-- Conversation List --}}
    <div class="bg-white border border-gray-100 rounded-xl overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 bg-surface/20 flex items-center justify-between">
            <h3 class="text-sm font-bold text-brand">Recent Conversations</h3>
            <span class="text-xs font-bold text-brand-muted">{{ $conversations->total() }} total</span>
        </div>
        <div class="divide-y divide-gray-50">
            @forelse($conversations as $chat)
            @php 
            $customer = $chat->participants->where('user_type', '!=', 'admin')->first(); 
            $user = $customer ? $customer->user : null;
            $initial = $user ? strtoupper(substr($user->name, 0, 1)) : 'U';
            $msg = $chat->latestMessage ? $chat->latestMessage->content : 'No messages yet.';
            $isUnread = $chat->latestMessage && $chat->latestMessage->sender_id !== auth('admin')->id();
            @endphp
            <a href="{{ route('orchestrator.livechat.show', $chat->id) }}" class="flex items-center gap-4 px-5 py-4 hover:bg-surface/20 transition-colors group {{ $isUnread ? 'bg-accent/[0.02]' : '' }}">
                <div class="w-10 h-10 rounded-lg flex items-center justify-center font-bold text-sm shrink-0 {{ $chat->status === 'active' ? 'bg-green-50 text-green-700' : ($chat->status === 'waiting' ? 'bg-amber-50 text-amber-600' : 'bg-gray-50 text-gray-400') }}">
                    {{ $initial }}
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between gap-2">
                        <span class="text-sm font-bold text-brand truncate">{{ $user ? $user->name : 'Unknown User' }}</span>
                        <div class="flex items-center gap-2 shrink-0">
                            @if($chat->status === 'active')
                            <span class="px-1.5 py-0.5 bg-green-50 text-green-700 text-[9px] font-bold rounded">Active</span>
                            @elseif($chat->status === 'waiting')
                            <span class="px-1.5 py-0.5 bg-amber-50 text-amber-700 text-[9px] font-bold rounded">Waiting</span>
                            @else
                            <span class="px-1.5 py-0.5 bg-gray-50 text-gray-500 text-[9px] font-bold rounded">Closed</span>
                            @endif
                            <span class="text-[10px] text-brand-muted whitespace-nowrap">{{ $chat->updated_at->diffForHumans() }}</span>
                        </div>
                    </div>
                    <p class="text-xs text-brand-muted truncate mt-0.5">{{ Str::limit($msg, 80) }}</p>
                </div>
            </a>
            @empty
            <div class="flex flex-col items-center justify-center py-16 text-brand-muted">
                <svg class="w-14 h-14 mb-3 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"/></svg>
                <p class="text-sm font-bold">No active chats</p>
                <p class="text-xs mt-1">Conversations will appear here when users reach out.</p>
            </div>
            @endforelse
        </div>
        @if($conversations->hasPages())
        <div class="px-5 py-3 border-t border-gray-100 bg-surface/20">{{ $conversations->links() }}</div>
        @endif
    </div>
</div>
@endsection