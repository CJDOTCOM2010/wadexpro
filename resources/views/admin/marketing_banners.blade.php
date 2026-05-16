@extends('admin.layout')
@section('title', 'Banner Manager')
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

<div x-data="bannerManager()" class="max-w-6xl mx-auto">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-black text-brand tracking-tight">App Banners</h2>
            <p class="text-sm text-brand-muted font-medium mt-0.5">Manage promotional carousels and announcements inside the mobile app.</p>
        </div>
        <button @click="showAdd = true" class="px-5 py-2.5 bg-brand text-white rounded-lg text-xs font-bold hover:bg-brand-light transition-colors flex items-center gap-2 shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            Upload Banner
        </button>
    </div>

    {{-- Banners Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        @forelse($banners as $banner)
        <div class="bg-white border border-gray-100 rounded-xl overflow-hidden {{ !$banner->is_active ? 'opacity-60' : '' }}">
            <div class="aspect-[21/9] bg-surface relative overflow-hidden border-b border-gray-100">
                <img src="{{ asset('storage/'.$banner->image_url) }}" class="w-full h-full object-cover">
                <div class="absolute top-3 left-3">
                    @if($banner->is_active)
                    <span class="px-2 py-0.5 bg-green-500 text-white text-[9px] font-bold rounded">Live</span>
                    @else
                    <span class="px-2 py-0.5 bg-gray-500 text-white text-[9px] font-bold rounded">Hidden</span>
                    @endif
                </div>
                <div class="absolute top-3 right-3 flex gap-1.5">
                    <form action="{{ route('orchestrator.marketing.banners.toggle', $banner->id) }}" method="POST" class="inline">
                        @csrf @method('PATCH')
                        <button type="submit" class="w-7 h-7 bg-white rounded-lg flex items-center justify-center shadow-sm hover:bg-surface transition-colors" title="{{ $banner->is_active ? 'Hide' : 'Show' }}">
                            @if($banner->is_active)
                            <svg class="w-3.5 h-3.5 text-brand" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                            @else
                            <svg class="w-3.5 h-3.5 text-brand" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            @endif
                        </button>
                    </form>
                    <button @click="confirmDelete('{{ $banner->id }}', '{{ addslashes($banner->title) }}')" class="w-7 h-7 bg-white rounded-lg flex items-center justify-center shadow-sm hover:bg-red-50 transition-colors" title="Delete">
                        <svg class="w-3.5 h-3.5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </button>
                </div>
            </div>
            <div class="px-5 py-4">
                <div class="flex items-center justify-between mb-0.5">
                    <h3 class="text-sm font-bold text-brand truncate">{{ $banner->title }}</h3>
                    <span class="px-1.5 py-0.5 bg-surface border border-gray-100 text-[9px] font-bold rounded shrink-0 ml-2">{{ ucfirst($banner->audience) }}</span>
                </div>
                <p class="text-[11px] text-brand-muted">Placement: {{ ucfirst($banner->placement) }}</p>
                <div class="flex items-center gap-4 mt-2 text-[10px] text-brand-muted">
                    <span>Link: <strong class="text-brand font-mono">{{ $banner->link_url ?? 'None' }}</strong></span>
                    <span>Order: <strong class="text-brand">{{ $banner->sort_order }}</strong></span>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full flex flex-col items-center justify-center py-16 text-brand-muted bg-white border border-gray-100 rounded-xl">
            <svg class="w-14 h-14 mb-3 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            <p class="text-sm font-bold">No banners uploaded yet</p>
            <p class="text-xs mt-1">Upload your first banner to display in mobile apps.</p>
        </div>
        @endforelse
    </div>

    @if($banners->hasPages())
    <div class="mt-4">{{ $banners->links() }}</div>
    @endif

    {{-- Add Modal --}}
    <div x-show="showAdd" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-brand/50 backdrop-blur-sm" @click="showAdd = false"></div>
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg relative z-10" @click.outside="showAdd = false">
            <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 bg-accent/10 rounded-lg flex items-center justify-center text-accent">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-brand">Upload Banner</h3>
                        <p class="text-xs text-brand-muted">Add a promotional banner to the mobile app.</p>
                    </div>
                </div>
                <button @click="showAdd = false" class="w-7 h-7 bg-surface rounded-lg flex items-center justify-center text-brand-muted hover:text-brand transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form action="{{ route('orchestrator.marketing.banners.store') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
                @csrf
                <div>
                    <label class="text-[10px] font-bold text-brand-muted uppercase tracking-wider mb-1.5 block">Title <span class="text-red-500">*</span></label>
                    <input type="text" name="title" required placeholder="e.g. Weekend Promo" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-medium outline-none focus:ring-2 focus:ring-accent/20 transition-shadow">
                </div>
                <div>
                    <label class="text-[10px] font-bold text-brand-muted uppercase tracking-wider mb-1.5 block">Image <span class="text-red-500">*</span></label>
                    <input type="file" name="image" accept="image/*" required class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-medium outline-none focus:ring-2 focus:ring-accent/20 transition-shadow file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-[10px] file:font-bold file:bg-brand file:text-white hover:file:bg-brand-light">
                    <p class="text-[10px] text-brand-muted mt-1">Recommended: 21:9 aspect ratio</p>
                </div>
                <div>
                    <label class="text-[10px] font-bold text-brand-muted uppercase tracking-wider mb-1.5 block">Deep Link URL</label>
                    <input type="text" name="link_url" placeholder="e.g. wadex://promo/weekend" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-medium outline-none focus:ring-2 focus:ring-accent/20 transition-shadow">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-[10px] font-bold text-brand-muted uppercase tracking-wider mb-1.5 block">App Audience</label>
                        <select name="audience" required class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-medium outline-none focus:ring-2 focus:ring-accent/20">
                            <option value="customer">Customer App</option>
                            <option value="driver">Driver App</option>
                            <option value="all">Both Apps</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-[10px] font-bold text-brand-muted uppercase tracking-wider mb-1.5 block">Placement</label>
                        <select name="placement" required class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-medium outline-none focus:ring-2 focus:ring-accent/20">
                            <option value="home">Home Screen</option>
                            <option value="promotions">Promotions Tab</option>
                            <option value="dashboard">Dashboard</option>
                        </select>
                    </div>
                </div>
                <div class="flex justify-end gap-2 pt-2 border-t border-gray-100">
                    <button type="button" @click="showAdd = false" class="px-4 py-2 text-xs font-bold text-brand-muted hover:text-brand transition-colors">Cancel</button>
                    <button type="submit" class="px-5 py-2 bg-brand text-white rounded-lg text-xs font-bold hover:bg-brand-light transition-colors">Upload Banner</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Multi-step Delete --}}
    <div x-show="deleteStep > 0" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-brand/60 backdrop-blur-sm" @click="closeDelete()"></div>
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-md relative z-10" @click.outside="closeDelete()">
            <template x-if="deleteStep === 1">
                <div class="p-6">
                    <div class="w-14 h-14 bg-red-50 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-7 h-7 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <h3 class="text-lg font-bold text-brand text-center mb-2">Delete Banner?</h3>
                    <p class="text-sm text-brand-muted text-center mb-6">Permanently delete <strong class="text-brand" x-text="deleteLabel"></strong>?</p>
                    <div class="flex gap-2">
                        <button type="button" @click="closeDelete()" class="flex-1 px-4 py-2.5 bg-surface text-brand-muted rounded-lg text-xs font-bold hover:bg-gray-100">Cancel</button>
                        <button type="button" @click="deleteStep = 2" class="flex-1 px-4 py-2.5 bg-red-600 text-white rounded-lg text-xs font-bold hover:bg-red-700">Continue</button>
                    </div>
                </div>
            </template>
            <template x-if="deleteStep === 2">
                <div class="p-6">
                    <div class="w-14 h-14 bg-red-50 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-7 h-7 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <h3 class="text-lg font-bold text-brand text-center mb-2">Type DELETE to confirm</h3>
                    <input type="text" x-model="deleteConfirm" @input="deleteConfirm = deleteConfirm.toUpperCase()" placeholder="Type DELETE" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-bold text-center outline-none focus:ring-2 focus:ring-red-300 transition-shadow mb-6 uppercase tracking-widest">
                    <div class="flex gap-2">
                        <button type="button" @click="deleteStep = 1" class="flex-1 px-4 py-2.5 bg-surface text-brand-muted rounded-lg text-xs font-bold hover:bg-gray-100">Back</button>
                        <button type="button" @click="executeDelete()" :disabled="deleteConfirm !== 'DELETE'" class="flex-1 px-4 py-2.5 rounded-lg text-xs font-bold" :class="deleteConfirm === 'DELETE' ? 'bg-red-600 text-white hover:bg-red-700' : 'bg-gray-100 text-gray-400 cursor-not-allowed'">Confirm</button>
                    </div>
                </div>
            </template>
        </div>
    </div>
</div>

<script>
function bannerManager() {
    return {
        showAdd: false,
        deleteStep: 0, deleteId: '', deleteLabel: '', deleteConfirm: '',
        confirmDelete(id, label) { this.deleteId = id; this.deleteLabel = label; this.deleteStep = 1; this.deleteConfirm = ''; },
        closeDelete() { this.deleteStep = 0; this.deleteConfirm = ''; },
        executeDelete() {
            if (this.deleteConfirm !== 'DELETE') return;
            const f = document.createElement('form'); f.method = 'POST'; f.action = '/orchestrator/marketing/banners/' + this.deleteId;
            const c = document.createElement('input'); c.type = 'hidden'; c.name = '_token'; c.value = '{{ csrf_token() }}'; f.appendChild(c);
            const m = document.createElement('input'); m.type = 'hidden'; m.name = '_method'; m.value = 'DELETE'; f.appendChild(m);
            document.body.appendChild(f); f.submit();
        }
    };
}
</script>
<style>[x-cloak] { display: none !important; }</style>
@endsection