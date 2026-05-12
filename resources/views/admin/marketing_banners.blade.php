@extends('admin.layout')
@section('title', 'Banner Manager')
@section('content')

<div class="mb-8 flex items-center justify-between">
    <div>
        <h2 class="text-2xl font-black text-brand tracking-tight">App Banners</h2>
        <p class="text-brand-muted font-medium mt-1">Manage promotional carousels and announcements inside the mobile app.</p>
    </div>
    <div class="flex gap-4">
        <button class="px-6 py-3 bg-brand text-white font-bold rounded-lg hover:bg-brand-light transition flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            Upload Banner
        </button>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

    <!-- Banner 1 -->
    <div class="bg-white rounded-lg border border-gray-100 shadow-sm overflow-hidden flex flex-col group">
        <div class="aspect-[21/9] bg-surface relative overflow-hidden border-b border-gray-50">
            <img src="https://via.placeholder.com/800x400/0b1c3c/ffffff?text=Promo+Banner+Graphic" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
            <div class="absolute top-4 left-4">
                <span class="px-2 py-1 bg-green-500 text-white text-[9px] font-black uppercase tracking-widest rounded shadow-sm">Live in App</span>
            </div>
            <div class="absolute top-4 right-4 flex gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                <button class="w-8 h-8 bg-white text-brand rounded-full flex items-center justify-center shadow-lg hover:scale-110 transition"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg></button>
                <button class="w-8 h-8 bg-white text-red-500 rounded-full flex items-center justify-center shadow-lg hover:scale-110 transition"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>
            </div>
        </div>
        <div class="p-6">
            <h3 class="text-lg font-black text-brand mb-1">Weekend 50% Off Banner</h3>
            <p class="text-sm text-brand-muted mb-4">Displayed on Customer Home Screen</p>
            
            <div class="space-y-2 pt-4 border-t border-gray-50">
                <div class="flex justify-between text-sm">
                    <span class="text-brand-muted font-medium">Link Target</span>
                    <span class="font-bold text-accent">wadex://promo/weekend</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-brand-muted font-medium">Sort Order</span>
                    <span class="font-bold text-brand">1</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Banner 2 -->
    <div class="bg-white rounded-lg border border-gray-100 shadow-sm overflow-hidden flex flex-col group">
        <div class="aspect-[21/9] bg-surface relative overflow-hidden border-b border-gray-50">
            <img src="https://via.placeholder.com/800x400/eeeeee/aaaaaa?text=Driver+Bonus+Graphic" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
            <div class="absolute top-4 left-4">
                <span class="px-2 py-1 bg-brand text-white text-[9px] font-black uppercase tracking-widest rounded shadow-sm">Live in App</span>
            </div>
            <div class="absolute top-4 right-4 flex gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                <button class="w-8 h-8 bg-white text-brand rounded-full flex items-center justify-center shadow-lg hover:scale-110 transition"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg></button>
                <button class="w-8 h-8 bg-white text-red-500 rounded-full flex items-center justify-center shadow-lg hover:scale-110 transition"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>
            </div>
        </div>
        <div class="p-6">
            <h3 class="text-lg font-black text-brand mb-1">Driver Weekly Goal Bonus</h3>
            <p class="text-sm text-brand-muted mb-4">Displayed on Driver Dashboard</p>
            
            <div class="space-y-2 pt-4 border-t border-gray-50">
                <div class="flex justify-between text-sm">
                    <span class="text-brand-muted font-medium">Link Target</span>
                    <span class="font-bold text-accent">wadex-driver://earnings/goals</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-brand-muted font-medium">Sort Order</span>
                    <span class="font-bold text-brand">1</span>
                </div>
            </div>
        </div>
    </div>

</div>

@endsection
