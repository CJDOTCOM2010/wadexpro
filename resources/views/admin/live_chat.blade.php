@extends('admin.layout')

@section('title', 'Live Chat Support')

@section('content')
<!-- Error Alert -->
@if(session('error'))
<div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl flex items-center gap-3">
    <svg class="w-5 h-5 text-red-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
    <p class="text-sm font-medium text-red-700">{{ session('error') }}</p>
</div>
@endif

<div class="mb-8">
    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center gap-3">
                <h2 class="text-3xl font-black text-brand tracking-tight">Live Chat Support</h2>
                <span class="px-3 py-1 bg-green-100 text-green-700 text-xs font-bold rounded-full flex items-center gap-1">
                    <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span> LIVE
                </span>
            </div>
            <p class="text-brand-muted font-medium mt-1">Real-time assistance for riders and drivers.</p>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex items-center hover:shadow-md transition-all group">
        <div class="w-14 h-14 rounded-2xl bg-green-100 flex items-center justify-center text-green-600 mr-4 group-hover:bg-green-500 group-hover:text-white transition-all">
            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"/></svg>
        </div>
        <div>
            <p class="text-xs font-bold text-brand-muted uppercase tracking-widest">Active Chats</p>
            <p class="text-3xl font-black text-brand">{{ $stats['active'] ?? 0 }}</p>
        </div>
    </div>
    
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex items-center hover:shadow-md transition-all group">
        <div class="w-14 h-14 rounded-2xl bg-amber-100 flex items-center justify-center text-amber-600 mr-4 group-hover:bg-amber-500 group-hover:text-white transition-all">
            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div>
            <p class="text-xs font-bold text-amber-600 uppercase tracking-widest">Waiting</p>
            <p class="text-3xl font-black text-amber-700">{{ $stats['waiting'] ?? 0 }}</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex items-center hover:shadow-md transition-all group">
        <div class="w-14 h-14 rounded-2xl bg-gray-100 flex items-center justify-center text-gray-500 mr-4 group-hover:bg-gray-500 group-hover:text-white transition-all">
            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        </div>
        <div>
            <p class="text-xs font-bold text-gray-500 uppercase tracking-widest">Closed Today</p>
            <p class="text-3xl font-black text-gray-700">{{ $stats['closed'] ?? 0 }}</p>
        </div>
    </div>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="p-5 border-b border-gray-100 flex justify-between items-center bg-surface/30">
        <h3 class="text-lg font-black text-brand">Recent Conversations</h3>
        <span class="text-xs font-bold text-brand-muted">{{ $conversations->total() }} Total</span>
    </div>
    
    <div class="divide-y divide-gray-50">
        @forelse($conversations as $chat)
            @php 
                $customer = $chat->participants->where('user_type', '!=', 'admin')->first(); 
                $user = $customer ? $customer->user : null;
            @endphp
            <a href="{{ route('orchestrator.livechat.show', $chat->id) }}" class="block hover:bg-surface/50 transition p-5 group">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-full bg-accent/20 text-accent flex items-center justify-center font-bold text-lg">
                            {{ $user ? substr($user->name, 0, 1) : 'U' }}
                        </div>
                        <div>
                            <h4 class="font-bold text-brand">{{ $user ? $user->name : 'Unknown User' }}</h4>
                            <p class="text-sm text-brand-muted truncate max-w-md">
                                {{ $chat->latestMessage ? $chat->latestMessage->content : 'No messages yet.' }}
                            </p>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="mb-2">
                            @if($chat->status === 'active')
                                <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-bold">Active</span>
                            @elseif($chat->status === 'waiting')
                                <span class="px-3 py-1 bg-amber-100 text-amber-700 rounded-full text-xs font-bold">Waiting</span>
                            @else
                                <span class="px-3 py-1 bg-gray-100 text-gray-600 rounded-full text-xs font-bold">Closed</span>
                            @endif
                        </div>
                        <p class="text-xs text-gray-400 font-medium">
                            {{ $chat->updated_at->diffForHumans() }}
                        </p>
                    </div>
                </div>
            </a>
        @empty
            <div class="p-12 text-center text-gray-500">
                No active support chats at the moment.
            </div>
        @endforelse
    </div>
    <div class="p-4 border-t border-gray-100">
        {{ $conversations->links() }}
    </div>
</div>

@endsection
