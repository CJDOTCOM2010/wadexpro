@extends('admin.layout')
@section('title', 'Global Queue')
@section('content')

<div class="mb-8 flex items-center justify-between">
    <div>
        <h2 class="text-2xl font-black text-brand tracking-tight">Global Queue</h2>
        <p class="text-brand-muted font-medium mt-1">Real-time oversight of all platform transactions, orders, and fulfillment pipelines.</p>
    </div>
    <div class="flex gap-4">
        <a href="{{ route('orchestrator.dispatcher') }}" class="px-6 py-3 bg-brand text-white font-bold rounded-lg hover:bg-brand-light transition flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
            Manual Dispatch
        </a>
    </div>
</div>

<!-- Stats Bar -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    <div class="bg-white p-6 rounded-lg border border-gray-100 shadow-sm relative overflow-hidden group">
        <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-accent/5 rounded-full group-hover:scale-150 transition-transform duration-500"></div>
        <p class="text-[10px] font-black text-brand-muted uppercase tracking-widest relative z-10">Active Pipeline</p>
        <p class="text-3xl font-black text-brand tracking-tight mt-2 relative z-10">{{ number_format($stats['active_pipeline']) }}</p>
    </div>
    <div class="bg-white p-6 rounded-lg border border-gray-100 shadow-sm relative overflow-hidden group">
        <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-blue-500/5 rounded-full group-hover:scale-150 transition-transform duration-500"></div>
        <p class="text-[10px] font-black text-brand-muted uppercase tracking-widest relative z-10">Awaiting Node</p>
        <p class="text-3xl font-black text-blue-600 tracking-tight mt-2 relative z-10">{{ number_format($stats['awaiting_node']) }}</p>
    </div>
    <div class="bg-white p-6 rounded-lg border border-gray-100 shadow-sm relative overflow-hidden group">
        <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-green-500/5 rounded-full group-hover:scale-150 transition-transform duration-500"></div>
        <p class="text-[10px] font-black text-brand-muted uppercase tracking-widest relative z-10">In Progress</p>
        <p class="text-3xl font-black text-green-600 tracking-tight mt-2 relative z-10">{{ number_format($stats['in_progress']) }}</p>
    </div>
    <div class="bg-white p-6 rounded-lg border border-gray-100 shadow-sm relative overflow-hidden group">
        <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-red-500/5 rounded-full group-hover:scale-150 transition-transform duration-500"></div>
        <p class="text-[10px] font-black text-brand-muted uppercase tracking-widest relative z-10">Anomalies/Delayed</p>
        <p class="text-3xl font-black text-red-500 tracking-tight mt-2 relative z-10">{{ number_format($stats['anomalies']) }}</p>
    </div>
</div>

<!-- Queue List -->
<div class="bg-white rounded-lg border border-gray-100 shadow-sm overflow-hidden">
    <div class="p-6 border-b border-gray-50 flex items-center justify-between bg-surface/30">
        <div class="flex items-center gap-6">
            <h3 class="text-sm font-black text-brand uppercase tracking-widest">Live Transaction Stream</h3>
            <div class="flex gap-2">
                <span class="w-2.5 h-2.5 bg-green-500 rounded-full animate-pulse shadow-[0_0_8px_rgba(34,197,94,0.4)] mt-0.5"></span>
                <span class="text-[10px] font-bold text-green-600 uppercase tracking-widest">Live</span>
            </div>
        </div>
        <div class="flex items-center gap-4">
            <form action="{{ route('orchestrator.orders') }}" method="GET" class="relative">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search Order ID, Client..." class="bg-white border border-gray-100 rounded-lg pl-10 pr-4 py-2 text-sm font-medium outline-none focus:ring-2 focus:ring-accent/20 w-72">
                <svg class="w-4 h-4 text-gray-400 absolute left-4 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </form>
        </div>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-surface/10 border-b border-gray-50">
                    <th class="px-6 py-4 text-[11px] font-black text-brand-muted uppercase tracking-widest">Order ID & Time</th>
                    <th class="px-6 py-4 text-[11px] font-black text-brand-muted uppercase tracking-widest">Routing Nodes (From → To)</th>
                    <th class="px-6 py-4 text-[11px] font-black text-brand-muted uppercase tracking-widest">Financials</th>
                    <th class="px-6 py-4 text-[11px] font-black text-brand-muted uppercase tracking-widest">Driver</th>
                    <th class="px-6 py-4 text-[11px] font-black text-brand-muted uppercase tracking-widest">State</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($orders as $order)
                <tr class="hover:bg-surface/30 transition-colors group cursor-pointer {{ $order->status === 'cancelled' ? 'bg-red-50/30' : '' }}">
                    <td class="px-6 py-5">
                        <p class="text-sm font-black text-brand">{{ $order->reference }}</p>
                        <p class="text-[10px] text-brand-muted mt-0.5 font-mono uppercase">{{ $order->created_at->diffForHumans() }}</p>
                        <span class="inline-block mt-2 px-2 py-0.5 bg-surface border border-gray-100 text-brand text-[9px] font-black rounded uppercase tracking-wider">{{ $order->priority ?? 'standard' }}</span>
                    </td>
                    <td class="px-6 py-5">
                        <div class="flex flex-col gap-1">
                            <div class="flex items-center gap-2">
                                <div class="w-1.5 h-1.5 rounded-full bg-accent"></div>
                                <p class="text-xs font-bold text-brand w-48 truncate" title="{{ $order->pickup_address }}">{{ Str::limit($order->pickup_address, 30) }}</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <div class="w-1.5 h-1.5 rounded-full border border-brand"></div>
                                <p class="text-xs font-medium text-brand-muted w-48 truncate">To: {{ $order->stops->last()->dropoff_address ?? 'TBD' }}</p>
                            </div>
                            <div class="text-[9px] font-black text-brand-muted uppercase tracking-widest mt-1">Client: {{ $order->customer->name ?? 'Guest' }}</div>
                        </div>
                    </td>
                    <td class="px-6 py-5">
                        <p class="text-sm font-black {{ $order->status === 'cancelled' ? 'text-brand' : 'text-green-600' }}">₵{{ number_format($order->total_amount, 2) }}</p>
                        <p class="text-[10px] text-brand-muted mt-0.5 uppercase tracking-widest">{{ $order->payment_method ?? 'Cash' }} ({{ $order->payment_status ?? 'Pending' }})</p>
                    </td>
                    <td class="px-6 py-5">
                        @if($order->driver)
                            <div class="flex items-center gap-2">
                                <div class="w-6 h-6 rounded-full bg-brand/10 text-brand flex items-center justify-center text-[10px] font-bold">
                                    {{ strtoupper(substr($order->driver->user->name ?? 'D', 0, 2)) }}
                                </div>
                                <div>
                                    <p class="text-xs font-bold text-brand">{{ $order->driver->user->name ?? 'Unknown' }}</p>
                                    <p class="text-[9px] text-brand-muted font-mono uppercase">{{ $order->driver->user->phone ?? 'No Phone' }}</p>
                                </div>
                            </div>
                        @else
                            <span class="text-xs font-bold text-amber-500 bg-amber-50 px-2 py-1 rounded">Unassigned</span>
                        @endif
                    </td>
                    <td class="px-6 py-5">
                        @if($order->status === 'completed')
                            <div class="flex items-center gap-2 text-green-500">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                <span class="text-[10px] font-black uppercase tracking-tighter">Completed</span>
                            </div>
                        @elseif($order->status === 'cancelled')
                            <div class="flex items-center gap-2 text-red-500">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                <span class="text-[10px] font-black uppercase tracking-tighter">Cancelled</span>
                            </div>
                        @elseif($order->status === 'pending')
                            <div class="flex items-center gap-2 text-blue-500">
                                <div class="flex gap-0.5">
                                    <div class="w-1 h-1 bg-blue-500 rounded-full animate-bounce"></div>
                                    <div class="w-1 h-1 bg-blue-500 rounded-full animate-bounce" style="animation-delay: 100ms"></div>
                                    <div class="w-1 h-1 bg-blue-500 rounded-full animate-bounce" style="animation-delay: 200ms"></div>
                                </div>
                                <span class="text-[10px] font-black uppercase tracking-tighter">Locating Node</span>
                            </div>
                        @else
                            <div class="flex items-center gap-2 text-brand">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                <span class="text-[10px] font-black uppercase tracking-tighter">{{ str_replace('_', ' ', $order->status) }}</span>
                            </div>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                        <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        No orders found in the global queue.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div class="p-4 bg-surface/10 border-t border-gray-50 flex justify-center">
        {{ $orders->appends(request()->except('page'))->links() }}
    </div>
</div>

@endsection
