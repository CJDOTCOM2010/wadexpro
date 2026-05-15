@extends('admin.layout')
@section('title', 'Document Approvals')
@section('content')

<!-- Error Alert -->
@if(session('error'))
<div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl flex items-center gap-3">
    <svg class="w-5 h-5 text-red-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
    <p class="text-sm font-medium text-red-700">{{ session('error') }}</p>
</div>
@endif

<!-- Success Alert -->
@if(session('success'))
<div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-xl flex items-center gap-3">
    <svg class="w-5 h-5 text-green-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    <p class="text-sm font-medium text-green-700">{{ session('success') }}</p>
</div>
@endif

<div class="mb-6">
    <a href="{{ route('orchestrator.driver.management') }}" class="text-sm font-bold text-brand-muted hover:text-brand transition flex items-center gap-1">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        Back to Registry
    </a>
</div>

<div class="grid grid-cols-1 xl:grid-cols-4 gap-8">
    
    <!-- Queue Sidebar -->
    <div class="xl:col-span-1 bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden flex flex-col h-[700px]">
        <div class="p-4 border-b border-gray-50 bg-surface/30">
            <h3 class="text-[10px] font-black text-brand-muted uppercase tracking-widest flex items-center justify-between">
                Review Queue 
                <span class="bg-amber-100 text-amber-700 px-2 py-0.5 rounded-full">{{ $queue->count() }}</span>
            </h3>
        </div>
        <div class="flex-1 overflow-y-auto divide-y divide-gray-50">
            @forelse($queue as $qItem)
            <a href="{{ route('orchestrator.driver.documents', ['driver' => $qItem->id]) }}" class="block p-4 hover:bg-surface transition {{ $selected && $selected->id === $qItem->id ? 'bg-surface border-l-4 border-brand' : '' }}">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-brand/10 text-brand font-bold flex items-center justify-center shrink-0">
                        {{ substr($qItem->user->name ?? 'D', 0, 2) }}
                    </div>
                    <div>
                        <p class="text-sm font-bold text-brand">{{ $qItem->user->name ?? 'Unknown' }}</p>
                        <p class="text-[10px] text-brand-muted font-mono mt-0.5">Applied: {{ $qItem->created_at->diffForHumans() }}</p>
                    </div>
                </div>
            </a>
            @empty
            <div class="p-8 text-center text-gray-500">
                <p class="text-sm font-medium">Queue is empty.</p>
            </div>
            @endforelse
        </div>
    </div>

    <!-- Review Panel -->
    <div class="xl:col-span-3">
        @if($selected)
        <div class="bg-white rounded-lg border border-gray-100 shadow-sm overflow-hidden">
            <!-- Header -->
            <div class="p-6 border-b border-gray-50 flex items-center justify-between bg-surface/30">
                <div>
                    <h2 class="text-xl font-black text-brand tracking-tight">{{ $selected->user->name }}</h2>
                    <p class="text-sm text-brand-muted font-mono mt-1">{{ $selected->user->email }} • {{ $selected->user->phone }}</p>
                </div>
                <div class="flex gap-3">
                    <button type="button" onclick="document.getElementById('reject-modal').classList.remove('hidden')" class="px-6 py-2.5 bg-white text-red-600 font-bold rounded shadow-sm border border-red-100 hover:bg-red-50 transition">Reject & Request Upload</button>
                    <form action="{{ route('orchestrator.driver.approve', $selected->id) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="px-6 py-2.5 bg-green-600 text-white font-bold rounded shadow-sm hover:bg-green-700 transition flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Approve Account
                        </button>
                    </form>
                </div>
            </div>

            <!-- Documents Split View -->
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-8 bg-surface/10">
                
                <!-- Ghana Card -->
                <div>
                    <h3 class="text-[10px] font-black text-brand-muted uppercase tracking-widest mb-3 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/></svg>
                        National ID (Ghana Card)
                    </h3>
                    <div class="bg-white rounded-lg p-2 border border-gray-200 shadow-sm">
                        @if($selected->id_card_front_url)
                            <div class="aspect-[1.6/1] bg-gray-100 rounded overflow-hidden relative group cursor-zoom-in">
                                <img src="{{ asset('storage/'.$selected->id_card_front_url) }}" class="w-full h-full object-contain">
                            </div>
                        @else
                            <div class="aspect-[1.6/1] bg-gray-50 border-2 border-dashed border-gray-200 rounded flex items-center justify-center text-gray-400">
                                No ID uploaded
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Driver's License -->
                <div>
                    <h3 class="text-[10px] font-black text-brand-muted uppercase tracking-widest mb-3 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 9h3.75M15 12h3.75M15 15h3.75M4.5 19.5h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5zm6-10.125a1.875 1.875 0 11-3.75 0 1.875 1.875 0 013.75 0zm1.294 6.336a6.721 6.721 0 01-3.17.789 6.721 6.721 0 01-3.168-.789 3.376 3.376 0 016.338 0z"/></svg>
                        Driver's License
                    </h3>
                    <div class="bg-white rounded-lg p-2 border border-gray-200 shadow-sm">
                        @if($selected->driver_photo_url) <!-- Assuming this holds license photo, or use appropriate field -->
                            <div class="aspect-[1.6/1] bg-gray-100 rounded overflow-hidden relative group cursor-zoom-in">
                                <img src="{{ asset('storage/'.$selected->driver_photo_url) }}" class="w-full h-full object-contain">
                            </div>
                        @else
                            <div class="aspect-[1.6/1] bg-gray-50 border-2 border-dashed border-gray-200 rounded flex items-center justify-center text-gray-400">
                                No license uploaded
                            </div>
                        @endif
                        <div class="mt-4 p-4 bg-surface rounded border border-gray-100">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <span class="block text-[9px] font-black text-brand-muted uppercase tracking-widest">License Number</span>
                                    <span class="font-mono text-brand font-bold">{{ $selected->license_number ?? 'N/A' }}</span>
                                </div>
                                <div>
                                    <span class="block text-[9px] font-black text-brand-muted uppercase tracking-widest">Expiry Date</span>
                                    <span class="font-mono text-brand font-bold">{{ $selected->license_expires_at ? $selected->license_expires_at->format('M d, Y') : 'N/A' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        
        <!-- Reject Modal (Hidden by default) -->
        <div id="reject-modal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-lg shadow-xl max-w-lg w-full p-6">
                <h3 class="text-lg font-black text-brand mb-4">Reject Documents</h3>
                <form action="{{ route('orchestrator.driver.reject', $selected->id) }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-bold text-brand mb-1">Reason for rejection (Sent to driver)</label>
                        <textarea name="reason" rows="3" class="w-full bg-surface border border-gray-200 rounded p-3 text-sm focus:ring-2 focus:ring-brand/20 outline-none" required placeholder="E.g., The Ghana Card uploaded is blurry and unreadable. Please upload a clear picture."></textarea>
                    </div>
                    <div class="flex justify-end gap-3">
                        <button type="button" onclick="document.getElementById('reject-modal').classList.add('hidden')" class="px-4 py-2 text-brand font-bold text-sm">Cancel</button>
                        <button type="submit" class="px-4 py-2 bg-red-600 text-white font-bold rounded shadow-sm hover:bg-red-700">Reject & Notify</button>
                    </div>
                </form>
            </div>
        </div>
        @else
        <div class="bg-white rounded-lg border border-gray-100 shadow-sm h-[700px] flex flex-col items-center justify-center text-gray-400">
            <svg class="w-16 h-16 mb-4 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            <p class="text-lg font-medium">Select a driver from the queue to review.</p>
        </div>
        @endif
    </div>

</div>

@endsection
