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
                    <span class="px-2 py-0.5 rounded bg-red-100 text-red-700 text-[10px] font-black uppercase tracking-widest">Urgent</span>
                    <span class="text-xs text-brand-muted font-mono font-medium">#TKT-8921</span>
                </div>
                <h2 class="text-xl font-black text-brand tracking-tight">Driver demanded extra cash and refused to drop me</h2>
            </div>
            <div class="flex gap-2">
                <button class="px-4 py-2 bg-surface text-brand text-xs font-bold rounded border border-gray-200 hover:bg-gray-100 transition">Assign to Me</button>
                <button class="px-4 py-2 bg-green-50 text-green-700 text-xs font-bold rounded border border-green-200 hover:bg-green-100 transition">Mark Resolved</button>
            </div>
        </div>

        <!-- Thread -->
        <div class="flex-1 overflow-y-auto p-6 space-y-6 bg-surface/10">
            
            <!-- Customer Message -->
            <div class="flex gap-4">
                <div class="w-10 h-10 rounded-full bg-brand/10 text-brand font-bold flex items-center justify-center shrink-0 border border-brand/20">SJ</div>
                <div class="flex-1">
                    <div class="flex items-baseline gap-2 mb-1">
                        <span class="text-sm font-bold text-brand">Sarah Jenkins</span>
                        <span class="text-[10px] text-brand-muted uppercase tracking-widest">Customer • Today, 10:42 AM</span>
                    </div>
                    <div class="bg-white p-4 rounded-lg rounded-tl-none border border-gray-100 shadow-sm text-sm text-brand/80 leading-relaxed">
                        The driver (Kwame M.) stopped halfway and said the app fare is too small. He locked the doors until I transferred an extra 50 GHS to his MoMo account. This is unacceptable and extremely unsafe! I have the MoMo receipt attached.
                    </div>
                    <div class="mt-2 flex gap-2">
                        <div class="flex items-center gap-2 px-3 py-1.5 bg-white border border-gray-100 rounded text-xs font-bold text-brand w-max cursor-pointer hover:bg-surface transition">
                            <svg class="w-4 h-4 text-brand-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                            receipt_momo.jpg
                        </div>
                    </div>
                </div>
            </div>

            <!-- Internal Note -->
            <div class="flex gap-4">
                <div class="w-10 h-10 rounded-full bg-gray-200 text-gray-500 flex items-center justify-center shrink-0"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg></div>
                <div class="flex-1">
                    <div class="flex items-baseline gap-2 mb-1">
                        <span class="text-sm font-bold text-gray-700">System Bot</span>
                        <span class="text-[10px] text-gray-400 uppercase tracking-widest">Internal • Today, 10:43 AM</span>
                    </div>
                    <div class="bg-amber-50 p-4 rounded-lg rounded-tl-none border border-amber-100 text-sm text-amber-800 leading-relaxed font-medium">
                        ⚠️ High Risk Pattern Detected. Driver Kwame M. has 3 similar complaints in the last 14 days. Auto-flagging for review.
                    </div>
                </div>
            </div>

        </div>

        <!-- Reply Box -->
        <div class="p-4 border-t border-gray-50 bg-white">
            <div class="flex items-center gap-4 mb-2">
                <button class="text-xs font-bold text-brand pb-1 border-b-2 border-brand">Reply to Customer</button>
                <button class="text-xs font-bold text-brand-muted pb-1 border-b-2 border-transparent hover:text-brand transition">Add Internal Note</button>
            </div>
            <textarea class="w-full bg-surface border border-gray-100 rounded-lg p-3 text-sm outline-none focus:ring-2 focus:ring-brand/20 transition-all resize-none" rows="3" placeholder="Type your response here..."></textarea>
            <div class="flex items-center justify-between mt-3">
                <button class="text-gray-400 hover:text-brand transition"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg></button>
                <button class="px-6 py-2 bg-brand text-white text-sm font-bold rounded hover:bg-brand-light transition">Send Reply</button>
            </div>
        </div>

    </div>

    <!-- Metadata Sidebar -->
    <div class="xl:col-span-1 space-y-6">
        
        <!-- User Context -->
        <div class="bg-white rounded-lg border border-gray-100 shadow-sm p-6">
            <h3 class="text-[10px] font-black text-brand-muted uppercase tracking-widest mb-4">Customer Details</h3>
            <div class="flex items-center gap-3 mb-4">
                <div class="w-12 h-12 rounded-full bg-brand/10 text-brand font-bold flex items-center justify-center shrink-0 border border-brand/20">SJ</div>
                <div>
                    <p class="text-base font-bold text-brand">Sarah Jenkins</p>
                    <p class="text-xs text-brand-muted">+233 24 000 1111</p>
                </div>
            </div>
            <div class="space-y-3 pt-4 border-t border-gray-50">
                <div class="flex justify-between text-sm">
                    <span class="text-brand-muted">Total Rides</span>
                    <span class="font-bold text-brand">42</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-brand-muted">Rating</span>
                    <span class="font-bold text-brand flex items-center gap-1"><svg class="w-3 h-3 text-accent" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg> 4.8</span>
                </div>
            </div>
        </div>

        <!-- Associated Ride -->
        <div class="bg-white rounded-lg border border-gray-100 shadow-sm p-6">
            <h3 class="text-[10px] font-black text-brand-muted uppercase tracking-widest mb-4">Associated Ride</h3>
            <div class="bg-surface rounded p-3 mb-4 border border-gray-100">
                <div class="flex justify-between items-center mb-2">
                    <span class="text-xs font-bold text-brand">ORD-9982-A</span>
                    <span class="text-[9px] font-black uppercase tracking-widest text-green-600 bg-green-100 px-2 py-0.5 rounded">Completed</span>
                </div>
                <div class="text-xs text-brand-muted space-y-1">
                    <p><strong>From:</strong> Accra Mall</p>
                    <p><strong>To:</strong> East Legon</p>
                    <p><strong>Fare:</strong> ₵ 35.00</p>
                </div>
            </div>
            <button class="w-full py-2 bg-surface text-brand text-xs font-bold rounded border border-gray-200 hover:bg-gray-100 transition">View Full Order</button>
        </div>

        <!-- Driver Actions -->
        <div class="bg-white rounded-lg border border-red-100 shadow-sm p-6 relative overflow-hidden">
            <div class="absolute inset-0 bg-red-500/5 pointer-events-none"></div>
            <h3 class="text-[10px] font-black text-red-600 uppercase tracking-widest mb-4 relative z-10">Driver Actions</h3>
            <div class="flex items-center gap-3 mb-4 relative z-10">
                <img src="https://i.pravatar.cc/150?u=1" class="w-10 h-10 rounded-full border border-gray-200">
                <div>
                    <p class="text-sm font-bold text-brand">Kwame Mensah</p>
                    <p class="text-xs text-brand-muted flex items-center gap-1"><svg class="w-3 h-3 text-accent" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg> 4.9</p>
                </div>
            </div>
            <div class="space-y-2 relative z-10">
                <button class="w-full py-2 bg-white text-red-600 text-xs font-bold rounded border border-red-200 hover:bg-red-50 transition">Suspend Driver Account</button>
                <button class="w-full py-2 bg-white text-brand text-xs font-bold rounded border border-gray-200 hover:bg-gray-50 transition">Deduct Wallet (Penalty)</button>
            </div>
        </div>

    </div>
</div>

@endsection
