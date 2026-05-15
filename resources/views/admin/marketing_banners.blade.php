@extends('admin.layout')
@section('title', 'Banner Manager')
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

<div class="mb-8 flex items-center justify-between">
    <div>
        <h2 class="text-2xl font-black text-brand tracking-tight">App Banners</h2>
        <p class="text-brand-muted font-medium mt-1">Manage promotional carousels and announcements inside the mobile app.</p>
    </div>
    <div class="flex gap-4">
        <button onclick="document.getElementById('add-modal').classList.remove('hidden')" class="px-6 py-3 bg-brand text-white font-bold rounded-xl hover:bg-brand-light transition shadow-lg shadow-brand/20 flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            Upload Banner
        </button>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    @forelse($banners as $banner)
    <!-- Banner Item -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden flex flex-col group hover:shadow-lg transition-all {{ !$banner->is_active ? 'opacity-75' : '' }}">
        <div class="aspect-[21/9] bg-surface relative overflow-hidden border-b border-gray-50">
            <img src="{{ asset('storage/'.$banner->image_url) }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
            <div class="absolute top-4 left-4">
                @if($banner->is_active)
                    <span class="px-2 py-1 bg-green-500 text-white text-[9px] font-black uppercase tracking-widest rounded shadow-sm">Live in App</span>
                @else
                    <span class="px-2 py-1 bg-gray-500 text-white text-[9px] font-black uppercase tracking-widest rounded shadow-sm">Hidden</span>
                @endif
            </div>
            <div class="absolute top-4 right-4 flex gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                <form action="{{ route('orchestrator.marketing.banners.toggle', $banner->id) }}" method="POST">
                    @csrf @method('PATCH')
                    <button type="submit" class="w-8 h-8 bg-white text-brand rounded-full flex items-center justify-center shadow-lg hover:scale-110 transition" title="{{ $banner->is_active ? 'Hide Banner' : 'Show Banner' }}">
                        @if($banner->is_active)
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                        @else
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        @endif
                    </button>
                </form>
                <form action="{{ route('orchestrator.marketing.banners.destroy', $banner->id) }}" method="POST" onsubmit="return confirm('Delete this banner permanently?');">
                    @csrf @method('DELETE')
                    <button type="submit" class="w-8 h-8 bg-white text-red-500 rounded-full flex items-center justify-center shadow-lg hover:scale-110 transition" title="Delete">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </button>
                </form>
            </div>
        </div>
        <div class="p-6">
            <div class="flex items-start justify-between mb-1">
                <h3 class="text-lg font-black text-brand">{{ $banner->title }}</h3>
                <span class="px-2 py-0.5 bg-surface border border-gray-200 text-[9px] font-black uppercase tracking-widest rounded text-brand">{{ ucfirst($banner->audience) }} App</span>
            </div>
            <p class="text-sm text-brand-muted mb-4">Placement: {{ ucfirst($banner->placement) }}</p>
            
            <div class="space-y-2 pt-4 border-t border-gray-50">
                <div class="flex justify-between text-sm">
                    <span class="text-brand-muted font-medium">Link Target</span>
                    <span class="font-bold text-accent">{{ $banner->link_url ?? 'None' }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-brand-muted font-medium">Sort Order</span>
                    <span class="font-bold text-brand">{{ $banner->sort_order }}</span>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="col-span-full p-12 text-center bg-white rounded-lg border border-gray-100 shadow-sm text-gray-400">
        <p class="font-medium">No banners uploaded yet.</p>
    </div>
    @endforelse
</div>

<div class="mt-4">
    {{ $banners->links() }}
</div>

<!-- Add Modal -->
<div id="add-modal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-xl max-w-lg w-full p-6 max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-lg font-black text-brand">Upload Banner</h3>
            <button onclick="document.getElementById('add-modal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
        </div>
        <form action="{{ route('orchestrator.marketing.banners.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="space-y-4 mb-6">
                <div>
                    <label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-1">Banner Title (Internal)</label>
                    <input type="text" name="title" required class="w-full bg-surface border border-gray-200 rounded p-2 text-sm focus:ring-2 focus:ring-brand/20 outline-none">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-1">Image File (Recommended: 21:9 Aspect Ratio)</label>
                    <input type="file" name="image" accept="image/*" required class="w-full bg-surface border border-gray-200 rounded p-2 text-sm focus:ring-2 focus:ring-brand/20 outline-none">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-1">Deep Link Target (Optional)</label>
                    <input type="text" name="link_url" placeholder="e.g. wadex://promo/weekend" class="w-full bg-surface border border-gray-200 rounded p-2 text-sm focus:ring-2 focus:ring-brand/20 outline-none">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-1">App Audience</label>
                        <select name="audience" required class="w-full bg-surface border border-gray-200 rounded p-2 text-sm focus:ring-2 focus:ring-brand/20 outline-none cursor-pointer">
                            <option value="customer">Customer App</option>
                            <option value="driver">Driver App</option>
                            <option value="all">Both Apps</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-1">Placement Screen</label>
                        <select name="placement" required class="w-full bg-surface border border-gray-200 rounded p-2 text-sm focus:ring-2 focus:ring-brand/20 outline-none cursor-pointer">
                            <option value="home">Home Screen</option>
                            <option value="promotions">Promotions Tab</option>
                            <option value="dashboard">Dashboard</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="flex justify-end pt-4 border-t border-gray-100 gap-3">
                <button type="button" onclick="document.getElementById('add-modal').classList.add('hidden')" class="px-4 py-2 text-brand font-bold text-sm">Cancel</button>
                <button type="submit" class="px-6 py-2.5 bg-brand text-white font-bold rounded shadow-sm hover:bg-brand-light transition">Upload</button>
            </div>
        </form>
    </div>
</div>

@endsection
