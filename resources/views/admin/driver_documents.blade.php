@extends('admin.layout')
@section('title', 'Document Approvals')
@section('content')

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

<div x-data="{ showReject: false }" class="max-w-6xl mx-auto">
    {{-- Header --}}
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('orchestrator.driver.management') }}" class="w-8 h-8 bg-surface rounded-lg flex items-center justify-center text-brand-muted hover:text-brand hover:bg-gray-200 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <div>
            <h2 class="text-2xl font-black text-brand tracking-tight">Document Approvals</h2>
            <p class="text-sm text-brand-muted font-medium mt-0.5">Review and verify driver KYC documents.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-4 gap-6">
        {{-- Queue Sidebar --}}
        <div class="bg-white border border-gray-100 rounded-xl overflow-hidden flex flex-col">
            <div class="px-5 py-4 border-b border-gray-100 bg-surface/20">
                <div class="flex items-center justify-between">
                    <h3 class="text-sm font-bold text-brand">Review Queue</h3>
                    <span class="px-2 py-0.5 bg-amber-50 text-amber-600 text-[10px] font-bold rounded">{{ $queue->count() }}</span>
                </div>
            </div>
            <div class="overflow-y-auto divide-y divide-gray-50 flex-1" style="max-height: 600px;">
                @forelse($queue as $qItem)
                <a href="{{ route('orchestrator.driver.documents', ['driver' => $qItem->id]) }}" class="flex items-center gap-3 px-5 py-4 hover:bg-surface/20 transition-colors {{ $selected && $selected->id === $qItem->id ? 'bg-accent/5 border-l-2 border-accent' : 'border-l-2 border-transparent' }}">
                    <div class="w-9 h-9 rounded-lg bg-brand/10 flex items-center justify-center text-xs font-bold text-brand shrink-0">
                        {{ substr($qItem->user->name ?? 'D', 0, 2) }}
                    </div>
                    <div class="min-w-0">
                        <p class="text-sm font-bold text-brand truncate">{{ $qItem->user->name ?? 'Unknown' }}</p>
                        <p class="text-[10px] text-brand-muted">{{ $qItem->created_at->diffForHumans() }}</p>
                    </div>
                </a>
                @empty
                <div class="flex flex-col items-center justify-center py-12 text-brand-muted">
                    <svg class="w-10 h-10 mb-3 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <p class="text-xs font-bold">Queue is empty</p>
                </div>
                @endforelse
            </div>
        </div>

        {{-- Review Panel --}}
        <div class="xl:col-span-3">
            @if($selected)
            <div class="bg-white border border-gray-100 rounded-xl overflow-hidden">
                {{-- Header --}}
                <div class="px-6 py-5 border-b border-gray-100 bg-surface/20">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-xl bg-brand/10 flex items-center justify-center text-lg font-bold text-brand">
                                {{ substr($selected->user->name ?? 'D', 0, 2) }}
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-brand">{{ $selected->user->name }}</h3>
                                <p class="text-xs text-brand-muted font-mono">{{ $selected->user->email }} · {{ $selected->user->phone }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <button @click="showReject = true" class="px-4 py-2 bg-white border border-red-200 text-red-600 rounded-lg text-xs font-bold hover:bg-red-50 transition-colors">Reject</button>
                            <form action="{{ route('orchestrator.driver.approve', $selected->id) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="px-5 py-2 bg-green-600 text-white rounded-lg text-xs font-bold hover:bg-green-700 transition-colors flex items-center gap-2">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    Approve
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- Documents --}}
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- National ID --}}
                    <div>
                        <div class="flex items-center gap-2 mb-3">
                            <svg class="w-4 h-4 text-brand-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/></svg>
                            <span class="text-xs font-bold text-brand-muted uppercase tracking-wider">National ID (Ghana Card)</span>
                        </div>
                        <div class="bg-surface rounded-lg border border-gray-100 overflow-hidden">
                            @if($selected->id_card_front_url)
                            <img src="{{ asset('storage/'.$selected->id_card_front_url) }}" class="w-full h-48 object-contain bg-white">
                            @else
                            <div class="h-48 flex items-center justify-center text-brand-muted text-xs">Not uploaded</div>
                            @endif
                        </div>
                    </div>

                    {{-- Driver's License --}}
                    <div>
                        <div class="flex items-center gap-2 mb-3">
                            <svg class="w-4 h-4 text-brand-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 9h3.75M15 12h3.75M15 15h3.75M4.5 19.5h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5zm6-10.125a1.875 1.875 0 11-3.75 0 1.875 1.875 0 013.75 0zm1.294 6.336a6.721 6.721 0 01-3.17.789 6.721 6.721 0 01-3.168-.789 3.376 3.376 0 016.338 0z"/></svg>
                            <span class="text-xs font-bold text-brand-muted uppercase tracking-wider">Driver's License</span>
                        </div>
                        <div class="bg-surface rounded-lg border border-gray-100 overflow-hidden">
                            @if($selected->driver_photo_url)
                            <img src="{{ asset('storage/'.$selected->driver_photo_url) }}" class="w-full h-48 object-contain bg-white">
                            @else
                            <div class="h-48 flex items-center justify-center text-brand-muted text-xs">Not uploaded</div>
                            @endif
                        </div>
                        <div class="mt-3 p-3 bg-surface rounded-lg border border-gray-100">
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <span class="block text-[9px] font-bold text-brand-muted uppercase tracking-wider">License #</span>
                                    <span class="text-xs font-bold font-mono text-brand">{{ $selected->license_number ?? 'N/A' }}</span>
                                </div>
                                <div>
                                    <span class="block text-[9px] font-bold text-brand-muted uppercase tracking-wider">Expires</span>
                                    <span class="text-xs font-bold font-mono text-brand">{{ $selected->license_expires_at ? $selected->license_expires_at->format('M d, Y') : 'N/A' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Reject Modal --}}
            <div x-show="showReject" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
                <div class="absolute inset-0 bg-brand/50 backdrop-blur-sm" @click="showReject = false"></div>
                <div class="bg-white rounded-xl shadow-2xl w-full max-w-md relative z-10" @click.outside="showReject = false">
                    <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 bg-red-50 rounded-lg flex items-center justify-center text-red-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-brand">Reject Documents</h3>
                                <p class="text-xs text-brand-muted">This will notify the driver to re-upload.</p>
                            </div>
                        </div>
                        <button @click="showReject = false" class="w-7 h-7 bg-surface rounded-lg flex items-center justify-center text-brand-muted hover:text-brand transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                    <form action="{{ route('orchestrator.driver.reject', $selected->id) }}" method="POST" class="p-6 space-y-4">
                        @csrf
                        <div>
                            <label class="text-[10px] font-bold text-brand-muted uppercase tracking-wider mb-1.5 block">Reason (sent to driver)</label>
                            <textarea name="reason" rows="3" required placeholder="E.g., The ID uploaded is blurry. Please upload a clear picture." class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-medium outline-none focus:ring-2 focus:ring-red-300 transition-shadow resize-none"></textarea>
                        </div>
                        <div class="flex justify-end gap-2 pt-2 border-t border-gray-100">
                            <button type="button" @click="showReject = false" class="px-4 py-2 text-xs font-bold text-brand-muted hover:text-brand transition-colors">Cancel</button>
                            <button type="submit" class="px-5 py-2 bg-red-600 text-white rounded-lg text-xs font-bold hover:bg-red-700 transition-colors">Reject & Notify</button>
                        </div>
                    </form>
                </div>
            </div>
            @else
            <div class="bg-white border border-gray-100 rounded-xl flex flex-col items-center justify-center py-20 text-brand-muted" style="min-height: 500px;">
                <svg class="w-14 h-14 mb-4 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <p class="text-sm font-bold">Select a driver from the queue</p>
                <p class="text-xs mt-1">Choose a driver on the left to review their documents.</p>
            </div>
            @endif
        </div>
    </div>
</div>

<style>[x-cloak] { display: none !important; }</style>
@endsection