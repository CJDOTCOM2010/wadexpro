@extends('admin.layout')
@section('title', $label . ' Onboarding')
@push('styles')
<style>
    @keyframes shimmer {
        0% { background-position: -200% 0; }
        100% { background-position: 200% 0; }
    }
    @keyframes ripple-pulse {
        0% { transform: scale(1); opacity: 0.5; }
        50% { transform: scale(1.15); opacity: 0.2; }
        100% { transform: scale(1); opacity: 0.5; }
    }
    @keyframes float-up {
        0% { transform: translateY(8px); opacity: 0; }
        100% { transform: translateY(0); opacity: 1; }
    }
    @keyframes glow-pulse {
        0%, 100% { box-shadow: 0 0 6px rgba(248, 184, 3, 0.3); }
        50% { box-shadow: 0 0 18px rgba(248, 184, 3, 0.6); }
    }
    .premium-toggle {
        position: relative;
        display: inline-flex;
        align-items: center;
        cursor: pointer;
    }
    .premium-toggle input {
        position: absolute;
        opacity: 0;
        width: 0;
        height: 0;
    }
    .premium-toggle .track {
        width: 44px;
        height: 24px;
        background: #E0E0E0;
        border-radius: 12px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        box-shadow: inset 0 1px 3px rgba(0,0,0,0.1);
    }
    .premium-toggle .track::before {
        content: '';
        position: absolute;
        top: 2px;
        left: 2px;
        width: 20px;
        height: 20px;
        background: white;
        border-radius: 50%;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 2px 4px rgba(0,0,0,0.15), 0 0 0 1px rgba(0,0,0,0.05);
        z-index: 2;
    }
    .premium-toggle .track::after {
        content: '';
        position: absolute;
        top: 6px;
        left: 6px;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background: transparent;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        z-index: 1;
    }
    .premium-toggle input:checked + .track {
        background: linear-gradient(135deg, #0A0A0A, #1A1A1A);
        box-shadow: inset 0 1px 3px rgba(0,0,0,0.2), 0 0 10px rgba(248, 184, 3, 0.25);
    }
    .premium-toggle input:checked + .track::before {
        transform: translateX(20px);
        background: #F8B803;
        box-shadow: 0 2px 8px rgba(248, 184, 3, 0.4), 0 0 0 1px rgba(248, 184, 3, 0.2);
    }
    .premium-toggle input:checked + .track::after {
        background: rgba(248, 184, 3, 0.3);
    }
    .premium-toggle input:focus-visible + .track {
        outline: 2px solid rgba(248, 184, 3, 0.5);
        outline-offset: 2px;
    }
    .glass-card {
        background: linear-gradient(135deg, rgba(255,255,255,0.95), rgba(255,255,255,0.85));
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border: 1px solid rgba(255,255,255,0.8);
    }
    .glass-card-dark {
        background: linear-gradient(135deg, rgba(10,10,10,0.9), rgba(26,26,26,0.8));
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border: 1px solid rgba(255,255,255,0.08);
    }
    .glass-panel {
        background: linear-gradient(135deg, rgba(255,255,255,0.6), rgba(246,246,246,0.4));
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
        border: 1px solid rgba(255,255,255,0.5);
    }
    .gradient-border {
        position: relative;
    }
    .gradient-border::before {
        content: '';
        position: absolute;
        inset: 0;
        border-radius: inherit;
        padding: 1px;
        background: linear-gradient(135deg, rgba(248,184,3,0.3), rgba(248,184,3,0.05), rgba(248,184,3,0.3));
        -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
        -webkit-mask-composite: xor;
        mask-composite: exclude;
        pointer-events: none;
    }
    .phone-glass {
        background: linear-gradient(180deg, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0.03) 100%);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
    }
    .slide-item {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .slide-item:hover {
        transform: translateY(-2px);
    }
    .shimmer-bg {
        background: linear-gradient(90deg, transparent, rgba(248,184,3,0.05), transparent);
        background-size: 200% 100%;
        animation: shimmer 3s infinite;
    }
    input[type="range"] {
        -webkit-appearance: none;
        appearance: none;
        height: 6px;
        border-radius: 3px;
        outline: none;
    }
    input[type="range"]::-webkit-slider-thumb {
        -webkit-appearance: none;
        appearance: none;
        width: 20px;
        height: 20px;
        border-radius: 50%;
        background: linear-gradient(135deg, #F8B803, #FFD60A);
        cursor: pointer;
        box-shadow: 0 2px 8px rgba(248,184,3,0.4);
        border: 2px solid white;
        transition: all 0.2s;
    }
    input[type="range"]::-webkit-slider-thumb:hover {
        transform: scale(1.15);
    }
    input[type="range"]::-moz-range-thumb {
        width: 20px;
        height: 20px;
        border-radius: 50%;
        background: linear-gradient(135deg, #F8B803, #FFD60A);
        cursor: pointer;
        border: 2px solid white;
    }
    .tab-btn {
        position: relative;
        overflow: hidden;
    }
    .tab-btn::after {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(135deg, rgba(248,184,3,0.08), transparent);
        opacity: 0;
        transition: opacity 0.3s;
    }
    .tab-btn:hover::after {
        opacity: 1;
    }
    .tab-btn-active {
        box-shadow: 0 2px 12px rgba(0,0,0,0.08);
    }
    .file-input-btn {
        position: relative;
        overflow: hidden;
    }
    .file-input-btn::after {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(135deg, rgba(255,255,255,0.08), transparent);
        opacity: 0;
        transition: opacity 0.3s;
    }
    .file-input-btn:hover::after {
        opacity: 1;
    }
    .modal-content {
        animation: float-up 0.3s ease-out;
    }
    .premium-shadow {
        box-shadow: 0 4px 24px rgba(0,0,0,0.06), 0 1px 4px rgba(0,0,0,0.04);
    }
    .premium-shadow-hover:hover {
        box-shadow: 0 8px 32px rgba(0,0,0,0.1), 0 2px 8px rgba(0,0,0,0.05);
    }
</style>
@endpush
@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
@endpush
@section('content')

<div class="p-8 lg:p-12 max-w-6xl mx-auto"
     x-data="onboardingManager()"
     x-init="initSortable()">

    {{-- ================================================================ --}}
    {{-- HEADER / BREADCRUMB --}}
    {{-- ================================================================ --}}
    <div class="flex items-center justify-between mb-12">
        <div>
            <div class="flex items-center gap-2 text-[10px] font-black text-accent uppercase tracking-[0.2em] mb-3">
                <a href="{{ route('orchestrator.settings') }}" class="hover:text-accent-hover transition-all duration-300 relative after:absolute after:bottom-0 after:left-0 after:h-0.5 after:w-0 hover:after:w-full after:bg-accent after:transition-all after:duration-300">Settings Hub</a>
                <span class="text-gray-300">/</span>
                <span class="text-brand-muted">{{ $label }} Onboarding</span>
            </div>
            <h2 class="text-4xl font-black text-brand tracking-tight leading-tight">{{ $label }} Onboarding <span class="text-accent">Slides</span></h2>
            <p class="text-sm text-brand-muted mt-2 max-w-xl leading-relaxed">Design the first-launch experience for your <span class="font-bold text-brand">{{ strtolower($label) }}</span> mobile app. Drag to reorder, customize every pixel.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('orchestrator.settings') }}" class="group relative overflow-hidden bg-surface text-brand-muted hover:text-brand px-6 py-3.5 rounded-xl text-xs font-bold transition-all duration-300 flex items-center gap-2 border border-gray-100/80 premium-shadow hover:premium-shadow-hover">
                <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/50 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-700"></div>
                <svg class="w-4 h-4 transition-transform group-hover:-translate-x-1 duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Back to Hub
            </a>
            <button @click="openAddModal()"
                class="group relative overflow-hidden flex items-center gap-3 px-6 py-3.5 bg-brand text-white rounded-xl hover:shadow-2xl hover:shadow-black/20 transition-all duration-300 premium-shadow">
                <div class="absolute inset-0 bg-gradient-to-r from-accent/20 via-accent/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                <div class="absolute inset-0 shimmer-bg opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                <svg class="w-5 h-5 text-accent transition-all duration-500 group-hover:rotate-90 group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                <span class="text-sm font-black tracking-tight relative z-10">Add New Slide</span>
            </button>
        </div>
    </div>

    {{-- Success Flash --}}
    @if(session('success'))
    <div class="mb-8 p-5 bg-gradient-to-r from-green-50 to-emerald-50/50 border border-green-100 text-green-700 rounded-2xl flex items-center gap-4 animate-[float-up_0.3s_ease-out] premium-shadow">
        <div class="w-10 h-10 rounded-xl bg-green-100 flex items-center justify-center shrink-0">
            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        </div>
        <span class="text-sm font-bold">{{ session('success') }}</span>
    </div>
    @endif

    {{-- Error Flash --}}
    @if($errors->any())
    <div class="mb-8 p-5 bg-gradient-to-r from-red-50 to-rose-50/50 border border-red-100 text-red-700 rounded-2xl flex items-start gap-4 animate-[float-up_0.3s_ease-out] premium-shadow">
        <div class="w-10 h-10 rounded-xl bg-red-100 flex items-center justify-center shrink-0 mt-0.5">
            <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        </div>
        <div>
            <span class="text-sm font-bold">Please correct the following errors:</span>
            <ul class="text-xs list-disc pl-4 mt-2 space-y-1 text-red-600/80">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
    @endif

    {{-- ================================================================ --}}
    {{-- TAB SWITCHER (outside grid so it spans full width) --}}
    {{-- ================================================================ --}}
    <div class="flex items-center gap-2 mb-10 bg-white p-1.5 rounded-2xl premium-shadow border border-gray-50 w-fit">
        <button @click="activeTab = 'slides'"
                :class="activeTab === 'slides' ? 'bg-brand text-white shadow-lg shadow-brand/20 tab-btn-active' : 'text-brand-muted hover:text-brand hover:bg-surface/50'"
                class="tab-btn px-7 py-3 rounded-xl text-xs font-black transition-all duration-300 flex items-center gap-2.5">
            <svg class="w-4 h-4" :class="activeTab === 'slides' ? 'text-accent' : 'text-gray-300'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
            Onboarding Slides
        </button>
        <button @click="activeTab = 'splash'"
                :class="activeTab === 'splash' ? 'bg-brand text-white shadow-lg shadow-brand/20 tab-btn-active' : 'text-brand-muted hover:text-brand hover:bg-surface/50'"
                class="tab-btn px-7 py-3 rounded-xl text-xs font-black transition-all duration-300 flex items-center gap-2.5">
            <svg class="w-4 h-4" :class="activeTab === 'splash' ? 'text-accent' : 'text-gray-300'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Launch Identity
        </button>
    </div>

    {{-- ================================================================ --}}
    {{-- MAIN CONTENT GRID --}}
    {{-- ================================================================ --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">

        {{-- LEFT COLUMN: Slides or Splash Settings --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- ONBOARDING SLIDES TAB --}}
            <div x-show="activeTab === 'slides'" id="slides-list" class="space-y-5">
                <div class="flex items-center justify-between mb-2">
                    <div class="flex items-center gap-3">
                        <p class="text-[10px] font-black text-brand-muted uppercase tracking-widest">{{ $slides->count() }} Slide{{ $slides->count() !== 1 ? 's' : '' }} Configured</p>
                        <div class="w-1.5 h-1.5 rounded-full bg-gray-200"></div>
                        <p class="text-[10px] font-black text-brand-muted uppercase tracking-widest flex items-center gap-1.5">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                            Drag to reorder
                        </p>
                    </div>
                    <div x-show="savingOrder" class="flex items-center gap-2 text-[10px] font-bold text-accent">
                        <svg class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                        Saving order...
                    </div>
                </div>

                @forelse($slides as $slide)
                <div class="bg-white rounded-2xl premium-shadow border border-gray-50/80 hover:premium-shadow-hover transition-all duration-300 overflow-hidden group slide-item"
                     data-id="{{ $slide->id }}">
                    <div class="flex items-stretch">
                        {{-- Drag Handle --}}
                        <div class="w-12 shrink-0 bg-gradient-to-b from-gray-50 to-white flex items-center justify-center cursor-move hover:from-accent/5 hover:to-accent/10 transition-all duration-300 drag-handle border-r border-gray-50">
                            <svg class="w-4 h-4 text-gray-300 group-hover:text-accent transition-colors duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                        </div>
                        {{-- Image Thumbnail with Gradient Overlay --}}
                        <div class="w-44 shrink-0 relative overflow-hidden bg-gray-900">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/10 to-transparent z-10"></div>
                            @if($slide->image_url)
                                @if($slide->media_type === 'video')
                                    <div class="relative w-full h-full">
                                        <video src="{{ $slide->image_url }}" class="w-full h-full object-cover opacity-90 group-hover:scale-110 transition-transform duration-700" style="min-height: 150px;"></video>
                                        <div class="absolute inset-0 flex items-center justify-center bg-black/20 z-20">
                                            <div class="w-10 h-10 rounded-full bg-white/20 backdrop-blur-sm flex items-center justify-center">
                                                <svg class="w-5 h-5 text-white ml-0.5" fill="currentColor" viewBox="0 0 20 20"><path d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z"/></svg>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <img src="{{ $slide->image_url }}" alt="{{ $slide->title }}" class="w-full h-full object-cover opacity-90 group-hover:scale-110 transition-transform duration-700" style="min-height: 150px;">
                                @endif
                            @else
                            <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-gray-100 to-gray-200 text-gray-300" style="min-height: 150px;">
                                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </div>
                            @endif
                            <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/90 via-black/60 to-transparent p-4 pt-8 z-20">
                                <p class="text-white text-xs font-black truncate drop-shadow-lg">{{ $slide->title }}</p>
                            </div>
                            <div class="absolute top-2 right-2 z-20">
                                <span class="px-2 py-1 rounded-lg text-[9px] font-black uppercase tracking-wider backdrop-blur-sm {{ $slide->is_active ? 'bg-emerald-500/80 text-white' : 'bg-red-500/60 text-white' }}">
                                    {{ $slide->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                        </div>
                        {{-- Slide Info --}}
                        <div class="flex-1 p-6 flex flex-col justify-between bg-gradient-to-br from-white to-gray-50/30">
                            <div>
                                <div class="flex items-center gap-2.5 mb-3">
                                    <span class="text-[10px] font-black text-brand-muted uppercase tracking-widest flex items-center gap-1.5">
                                        <svg class="w-3 h-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                                        Slide #{{ $slide->sort_order + 1 }}
                                    </span>
                                </div>
                                <h4 class="text-xl font-black text-brand mb-2 tracking-tight">{{ $slide->title }}</h4>
                                <p class="text-xs text-brand-muted leading-relaxed line-clamp-2">{{ $slide->description ?: 'No description set.' }}</p>
                            </div>
                            <div class="flex items-center gap-2 mt-5 pt-4 border-t border-gray-50">
                                <button type="button" @click.stop="viewSlide('{{ $slide->id }}')" class="group/btn relative overflow-hidden px-4 py-2.5 bg-gradient-to-r from-brand/5 to-brand/10 text-brand hover:from-brand hover:to-brand-light hover:text-white rounded-xl text-[11px] font-bold transition-all duration-300 flex items-center gap-1.5 border border-brand/5">
                                    <div class="absolute inset-0 bg-gradient-to-r from-accent/20 to-transparent opacity-0 group-hover/btn:opacity-100 transition-opacity duration-500"></div>
                                    <svg class="w-3.5 h-3.5 relative z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    <span class="relative z-10">Preview</span>
                                </button>
                                <button type="button" @click.stop="openEditModal('{{ $slide->id }}')" class="group/btn relative overflow-hidden px-4 py-2.5 bg-surface text-brand-muted hover:bg-gray-200 hover:text-brand rounded-xl text-[11px] font-bold transition-all duration-300 flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                    Edit
                                </button>
                                <form action="{{ route('orchestrator.onboarding.toggle', $slide->id) }}" method="POST" @mousedown.stop @click.stop>
                                    @csrf @method('PATCH')
                                    <button type="submit" class="group/btn relative overflow-hidden px-4 py-2.5 rounded-xl text-[11px] font-bold transition-all duration-300 flex items-center gap-1.5 {{ $slide->is_active ? 'bg-amber-50 text-amber-700 hover:bg-amber-100' : 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100' }}">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                        {{ $slide->is_active ? 'Deactivate' : 'Activate' }}
                                    </button>
                                </form>
                                <form action="{{ route('orchestrator.onboarding.destroy', $slide->id) }}" method="POST" onsubmit="return confirm('Delete this slide?')" @mousedown.stop @click.stop>
                                    @csrf @method('DELETE')
                                    <button type="submit" class="group/btn relative overflow-hidden px-4 py-2.5 bg-red-50 text-red-500 hover:bg-red-100 hover:text-red-600 rounded-xl text-[11px] font-bold transition-all duration-300 flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="bg-white rounded-2xl border-2 border-dashed border-gray-200 p-20 text-center premium-shadow">
                    <div class="w-20 h-20 mx-auto mb-6 rounded-2xl bg-gradient-to-br from-accent/10 to-accent/5 flex items-center justify-center">
                        <svg class="w-10 h-10 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                    <h3 class="text-2xl font-black text-brand mb-3">No Slides Yet</h3>
                    <p class="text-sm text-brand-muted mb-8 max-w-md mx-auto leading-relaxed">Create your first onboarding slide for the {{ $label }} app. Each slide appears in sequence when the app launches for the first time.</p>
                    <button @click="openAddModal()" class="group relative overflow-hidden inline-flex items-center gap-3 px-8 py-4 bg-brand text-white text-xs font-black rounded-xl hover:shadow-2xl hover:shadow-brand/30 transition-all duration-300">
                        <div class="absolute inset-0 shimmer-bg opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                        <svg class="w-5 h-5 text-accent relative z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                        <span class="relative z-10">Create First Slide</span>
                    </button>
                </div>
                @endforelse
            </div>

            {{-- SPLASH SCREEN TAB --}}
            <div x-show="activeTab === 'splash'" x-cloak class="space-y-6">
                <form action="{{ route('orchestrator.onboarding.splash.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="app_type" value="{{ $appType }}">

                    <div class="glass-card rounded-3xl premium-shadow p-8 lg:p-10 space-y-8 gradient-border">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-accent/20 to-accent/5 flex items-center justify-center">
                                <svg class="w-6 h-6 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <div>
                                <h3 class="text-xl font-black text-brand tracking-tight">Launch Identity</h3>
                                <p class="text-xs text-brand-muted mt-0.5">Control the first impressions as the app launches.</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
                            <div class="space-y-7">
                                <div>
                                    <label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-2.5">Launch Tagline</label>
                                    <div class="relative group">
                                        <div class="absolute inset-0 bg-gradient-to-r from-accent/5 to-transparent rounded-xl opacity-0 group-focus-within:opacity-100 transition-opacity duration-500"></div>
                                        <input type="text" name="tagline" x-model="splash.tagline" class="w-full bg-surface/80 border border-gray-100 focus:border-accent/50 focus:bg-white rounded-xl py-4 px-6 text-sm font-bold outline-none transition-all duration-300 premium-shadow focus:shadow-lg focus:shadow-accent/5">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-2.5">Visibility Duration (<span class="text-accent" x-text="splash.duration_ms"></span>ms)</label>
                                    <input type="range" name="duration_ms" min="1000" max="10000" step="500" x-model="splash.duration_ms" class="w-full">
                                    <div class="flex justify-between mt-2.5 text-[8px] font-black text-gray-400">
                                        <span>1s</span><span>5s</span><span>10s</span>
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-5">
                                    <div>
                                        <label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-2.5">Backdrop Color</label>
                                        <div class="flex items-center gap-3 bg-surface/80 p-2 rounded-xl border border-gray-100 focus-within:border-accent/50 focus-within:shadow-lg focus-within:shadow-accent/5 transition-all duration-300">
                                            <input type="color" name="bg_color" x-model="splash.bg_color" class="w-9 h-9 rounded-lg border-none cursor-pointer">
                                            <input type="text" x-model="splash.bg_color" class="bg-transparent border-none text-[10px] font-black w-full outline-none">
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-2.5">Accent/Ripple</label>
                                        <div class="flex items-center gap-3 bg-surface/80 p-2 rounded-xl border border-gray-100 focus-within:border-accent/50 focus-within:shadow-lg focus-within:shadow-accent/5 transition-all duration-300">
                                            <input type="color" name="secondary_color" x-model="splash.secondary_color" class="w-9 h-9 rounded-lg border-none cursor-pointer">
                                            <input type="text" x-model="splash.secondary_color" class="bg-transparent border-none text-[10px] font-black w-full outline-none">
                                        </div>
                                    </div>
                                </div>

                                {{-- Toggle Switches --}}
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <div class="glass-panel rounded-2xl p-5 gradient-border">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-start gap-3">
                                                <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-accent/15 to-accent/5 flex items-center justify-center shrink-0">
                                                    <svg class="w-4 h-4 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                                </div>
                                                <div>
                                                    <p class="text-xs font-black text-brand">Logo Shield</p>
                                                    <p class="text-[9px] text-brand-muted mt-0.5">Show brand logo/shield.</p>
                                                </div>
                                            </div>
                                            <input type="hidden" name="show_logo" :value="splash.show_logo ? 1 : 0">
                                            <label class="premium-toggle">
                                                <input type="checkbox" x-model="splash.show_logo" value="1">
                                                <div class="track"></div>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="glass-panel rounded-2xl p-5 gradient-border">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-start gap-3">
                                                <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-accent/15 to-accent/5 flex items-center justify-center shrink-0">
                                                    <svg class="w-4 h-4 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                                </div>
                                                <div>
                                                    <p class="text-xs font-black text-brand">Backdrop Media</p>
                                                    <p class="text-[9px] text-brand-muted mt-0.5">Show background image/video.</p>
                                                </div>
                                            </div>
                                            <input type="hidden" name="show_background" :value="splash.show_background ? 1 : 0">
                                            <label class="premium-toggle">
                                                <input type="checkbox" x-model="splash.show_background" value="1">
                                                <div class="track"></div>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="glass-panel rounded-2xl p-5 gradient-border sm:col-span-2">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-start gap-3">
                                                <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-accent/15 to-accent/5 flex items-center justify-center shrink-0">
                                                    <svg class="w-4 h-4 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                </div>
                                                <div>
                                                    <p class="text-xs font-black text-brand">Animated Ripple</p>
                                                    <p class="text-[9px] text-brand-muted mt-0.5">Pulsing logo circles on the splash screen.</p>
                                                </div>
                                            </div>
                                            <input type="hidden" name="show_ripple" :value="splash.show_ripple ? 1 : 0">
                                            <label class="premium-toggle">
                                                <input type="checkbox" x-model="splash.show_ripple" value="1">
                                                <div class="track"></div>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="glass-panel rounded-2xl p-5 gradient-border sm:col-span-2">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-start gap-3">
                                                <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-accent/15 to-accent/5 flex items-center justify-center shrink-0">
                                                    <svg class="w-4 h-4 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/></svg>
                                                </div>
                                                <div>
                                                    <p class="text-xs font-black text-brand">Launch Tagline</p>
                                                    <p class="text-[9px] text-brand-muted mt-0.5">Show tagline text on the splash screen.</p>
                                                </div>
                                            </div>
                                            <input type="hidden" name="show_tagline" :value="splash.show_tagline ? 1 : 0">
                                            <label class="premium-toggle">
                                                <input type="checkbox" x-model="splash.show_tagline" value="1">
                                                <div class="track"></div>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-7">
                                <div>
                                    <label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-2.5">Splash-Specific Logo</label>
                                    <div class="relative group">
                                        <div class="w-full h-36 bg-surface/80 border-2 border-dashed border-gray-200 rounded-2xl flex flex-col items-center justify-center overflow-hidden transition-all duration-300 group-hover:border-accent/50 group-hover:bg-accent/[0.02] premium-shadow">
                                            <template x-if="splash.logo_url">
                                                <div class="w-full h-full flex items-center justify-center">
                                                    <template x-if="splash.logo_media_type === 'video' || (splash.logo_url && splash.logo_url.startsWith('blob:') && splash.logo_is_video)">
                                                        <video :src="splash.logo_url" class="h-full w-full object-cover z-10" autoplay loop muted></video>
                                                    </template>
                                                    <template x-if="splash.logo_media_type !== 'video' && !(splash.logo_url && splash.logo_url.startsWith('blob:') && splash.logo_is_video)">
                                                        <img :src="splash.logo_url" class="h-20 w-auto object-contain z-10">
                                                    </template>
                                                </div>
                                            </template>
                                            <template x-if="!splash.logo_url">
                                                <div class="flex flex-col items-center gap-2">
                                                    <div class="w-12 h-12 rounded-xl bg-accent/10 flex items-center justify-center">
                                                        <svg class="w-6 h-6 text-accent/60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                                    </div>
                                                    <span class="text-[10px] font-bold text-gray-400">Drop logo here or click to upload</span>
                                                </div>
                                            </template>
                                            <input type="file" name="logo" accept="image/png,image/jpeg,image/webp,image/gif,video/mp4,video/webm,video/quicktime"
                                                   @change="handleSplashImageChange($event, 'logo')"
                                                   class="absolute inset-0 opacity-0 cursor-pointer">
                                        </div>
                                        <p class="text-[9px] font-bold text-gray-400 mt-2.5 flex items-center gap-1.5">
                                            <svg class="w-3 h-3 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            Recommended: Transparent PNG, 512x512px
                                        </p>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-2.5">Dynamic Backdrop Image <span class="text-gray-300 font-normal normal-case tracking-normal">(Optional)</span></label>
                                    <div class="relative group">
                                        <div class="w-full h-36 bg-surface/80 border-2 border-dashed border-gray-200 rounded-2xl flex flex-col items-center justify-center overflow-hidden transition-all duration-300 group-hover:border-accent/50 group-hover:bg-accent/[0.02] premium-shadow">
                                            <template x-if="splash.background_url">
                                                <div class="w-full h-full">
                                                    <template x-if="splash.background_media_type === 'video' || (splash.background_url && splash.background_url.startsWith('blob:') && splash.background_is_video)">
                                                        <video :src="splash.background_url" class="w-full h-full object-cover z-10" autoplay loop muted></video>
                                                    </template>
                                                    <template x-if="splash.background_media_type !== 'video' && !(splash.background_url && splash.background_url.startsWith('blob:') && splash.background_is_video)">
                                                        <img :src="splash.background_url" class="w-full h-full object-cover z-10">
                                                    </template>
                                                </div>
                                            </template>
                                            <template x-if="!splash.background_url">
                                                <div class="flex flex-col items-center gap-2">
                                                    <div class="w-12 h-12 rounded-xl bg-accent/10 flex items-center justify-center">
                                                        <svg class="w-6 h-6 text-accent/60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                                    </div>
                                                    <span class="text-[10px] font-bold text-gray-400">Drop background here or click to upload</span>
                                                </div>
                                            </template>
                                            <input type="file" name="background" accept="image/png,image/jpeg,image/webp,image/gif,video/mp4,video/webm,video/quicktime"
                                                   @change="handleSplashImageChange($event, 'background')"
                                                   class="absolute inset-0 opacity-0 cursor-pointer">
                                        </div>
                                        <p class="text-[9px] font-bold text-gray-400 mt-2.5 flex items-center gap-1.5">
                                            <svg class="w-3 h-3 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            Recommended: High Resolution (1080x2400px)
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="pt-6 border-t border-gray-50/80 flex items-center justify-between">
                            <p class="text-[9px] text-brand-muted flex items-center gap-1.5">
                                <svg class="w-3 h-3 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Changes apply to the next app launch
                            </p>
                            <button type="submit" class="group relative overflow-hidden px-12 py-4 bg-brand text-white rounded-xl font-black text-xs hover:shadow-2xl hover:shadow-brand/30 transition-all duration-300 flex items-center gap-3">
                                <div class="absolute inset-0 shimmer-bg opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                                <svg class="w-4 h-4 text-accent relative z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                <span class="relative z-10">Save Splash Mission</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- RIGHT COLUMN: Phone Preview --}}
        <div class="lg:col-span-1">
            <div class="sticky top-32">
                <div class="flex items-center gap-2 mb-5">
                    <div class="w-6 h-6 rounded-lg bg-gradient-to-br from-accent/20 to-accent/5 flex items-center justify-center">
                        <svg class="w-3.5 h-3.5 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                    <p class="text-[10px] font-black text-brand-muted uppercase tracking-widest">Live Preview</p>
                </div>
                <div class="relative mx-auto" style="width: 280px;">
                    {{-- Phone Shadow Glow --}}
                    <div class="absolute -inset-4 bg-gradient-to-b from-accent/5 via-transparent to-transparent rounded-[3rem] blur-2xl opacity-60"></div>
                    <div class="relative bg-gradient-to-b from-gray-900 to-gray-950 rounded-[3rem] p-3.5 shadow-2xl shadow-black/40 border border-gray-800/50">
                        {{-- Notch --}}
                        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-32 h-7 bg-gradient-to-b from-gray-900 to-gray-800 rounded-b-2xl z-20 border-b border-gray-700/30"></div>
                        <div class="absolute top-1.5 left-1/2 -translate-x-1/2 w-16 h-1.5 bg-gray-800 rounded-full z-30"></div>
                        <div class="relative bg-gray-900 rounded-[2rem] overflow-hidden" style="height: 500px;">
                            {{-- Inner Glass Overlay --}}
                            <div class="absolute inset-0 phone-glass pointer-events-none z-20"></div>

                            {{-- Splash Preview --}}
                            <template x-if="activeTab === 'splash'">
                                <div class="h-full flex flex-col items-center justify-center p-10 text-center relative overflow-hidden" :style="{ backgroundColor: splash.bg_color }">
                                    {{-- Gradient overlay on background --}}
                                    <div class="absolute inset-0 bg-gradient-to-b from-white/5 to-transparent pointer-events-none"></div>

                                    <template x-if="splash.show_background && splash.background_url">
                                        <div class="absolute inset-0 w-full h-full">
                                            <template x-if="splash.background_media_type === 'video' || (splash.background_url.startsWith('blob:') && splash.background_is_video)">
                                                <video :src="splash.background_url" class="absolute inset-0 w-full h-full object-cover opacity-30 z-0" autoplay loop muted></video>
                                            </template>
                                            <template x-if="splash.background_media_type !== 'video' && !(splash.background_url.startsWith('blob:') && splash.background_is_video)">
                                                <img :src="splash.background_url" class="absolute inset-0 w-full h-full object-cover opacity-30 z-0">
                                            </template>
                                        </div>
                                    </template>

                                    <div class="relative z-10 space-y-8" x-show="splash.show_logo" x-transition.duration.500ms>
                                        <template x-if="splash.logo_url">
                                            <div class="w-24 h-24 mx-auto rounded-2xl overflow-hidden shadow-2xl shadow-black/20 ring-2 ring-white/10">
                                                <template x-if="splash.logo_media_type === 'video' || (splash.logo_url.startsWith('blob:') && splash.logo_is_video)">
                                                    <video :src="splash.logo_url" class="w-full h-full object-cover" autoplay loop muted></video>
                                                </template>
                                                <template x-if="splash.logo_media_type !== 'video' && !(splash.logo_url.startsWith('blob:') && splash.logo_is_video)">
                                                    <img :src="splash.logo_url" class="w-full h-full object-contain p-2">
                                                </template>
                                            </div>
                                        </template>
                                        <template x-if="!splash.logo_url">
                                            <div class="w-20 h-20 mx-auto flex items-center justify-center bg-gradient-to-br from-brand to-gray-800 shadow-2xl shadow-black/30 ring-2 ring-white/10 rounded-2xl">
                                                <span class="text-accent font-black text-3xl">W</span>
                                            </div>
                                        </template>
                                    </div>

                                    <h2 class="text-2xl font-black relative z-10 mt-6 drop-shadow-lg leading-tight" :style="{ color: splash.secondary_color || '#FFFFFF' }" x-text="splash.tagline"></h2>

                                    {{-- Ripple Effect --}}
                                    <template x-if="splash.show_ripple">
                                        <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                                            <div class="w-64 h-64 rounded-full" :style="{ borderColor: (splash.secondary_color || '#F8B803') + '20', borderWidth: '1px', animation: 'ripple-pulse 3s ease-in-out infinite' }"></div>
                                            <div class="w-48 h-48 rounded-full" :style="{ borderColor: (splash.secondary_color || '#F8B803') + '15', borderWidth: '1px', animation: 'ripple-pulse 3s ease-in-out infinite 0.5s' }"></div>
                                            <div class="w-32 h-32 rounded-full" :style="{ borderColor: (splash.secondary_color || '#F8B803') + '10', borderWidth: '1px', animation: 'ripple-pulse 3s ease-in-out infinite 1s' }"></div>
                                        </div>
                                    </template>
                                </div>
                            </template>

                            {{-- Slides Preview --}}
                            <template x-if="activeTab === 'slides'">
                                 <div class="h-full w-full relative overflow-hidden"
                                      :class="getLayoutClasses(activePreview.is_editing ? activePreview.layout_style : (previewSlide ? previewSlide.layout_style : currentLayout))"
                                      :style="{ backgroundColor: activePreview.is_editing ? activePreview.bg_color : (previewSlide ? previewSlide.bg_color : '') }">

                                    {{-- Background media --}}
                                    <div class="h-full w-full">
                                        <template x-if="activePreview.is_editing ? activePreview.media_type === 'video' : (previewSlide && previewSlide.media_type === 'video')">
                                            <video :src="activePreview.is_editing ? (activePreview.image_url || '/images/placeholder.png') : (previewSlide ? previewSlide.image_url : '/images/placeholder.png')"
                                                 class="object-cover w-full h-full"
                                                 :class="getImageClasses(activePreview.is_editing ? activePreview.layout_style : (previewSlide ? previewSlide.layout_style : currentLayout))"
                                                 autoplay loop muted></video>
                                        </template>
                                        <template x-if="activePreview.is_editing ? activePreview.media_type !== 'video' : (previewSlide && previewSlide.media_type !== 'video')">
                                            <img :src="activePreview.is_editing ? (activePreview.image_url || '/images/placeholder.png') : (previewSlide ? previewSlide.image_url : '/images/placeholder.png')"
                                                 class="object-cover"
                                                 :class="getImageClasses(activePreview.is_editing ? activePreview.layout_style : (previewSlide ? previewSlide.layout_style : currentLayout))">
                                        </template>
                                    </div>

                                    {{-- Background overlay --}}
                                    <div class="absolute inset-0 z-[1]" :class="getBackgroundLayerClasses(activePreview.is_editing ? activePreview.layout_style : (previewSlide ? previewSlide.layout_style : currentLayout))"></div>

                                    {{-- Content --}}
                                    <div class="relative z-10 flex flex-col"
                                         :class="getContentContainerClasses(activePreview.is_editing ? activePreview.layout_style : (previewSlide ? previewSlide.layout_style : currentLayout))">
                                        <h3 class="font-black leading-tight mb-2 transition-all duration-300"
                                            :class="getTextClasses(activePreview.is_editing ? activePreview.layout_style : (previewSlide ? previewSlide.layout_style : currentLayout), 'title')"
                                            :style="{ color: activePreview.is_editing ? activePreview.text_color : (previewSlide ? previewSlide.text_color : '') }"
                                            x-text="activePreview.is_editing ? (activePreview.title || 'Slide Title') : (previewSlide ? previewSlide.title : 'Slide Title')"></h3>

                                        <p class="leading-relaxed mb-4 transition-all duration-300 opacity-90"
                                           :class="getTextClasses(activePreview.is_editing ? activePreview.layout_style : (previewSlide ? previewSlide.layout_style : currentLayout), 'description')"
                                           :style="{ color: activePreview.is_editing ? activePreview.text_color : (previewSlide ? previewSlide.text_color : '') }"
                                           x-text="activePreview.is_editing ? (activePreview.description || 'Welcome to the platform...') : (previewSlide ? previewSlide.description : 'Welcome to the platform...')"></p>

                                        <button x-show="(activePreview.is_editing ? activePreview.button_type : (previewSlide ? previewSlide.button_type : 'action_below_text')) === 'action_below_text'"
                                                class="w-full py-2.5 rounded-lg font-black text-[10px] uppercase tracking-wider shadow-lg transition-all"
                                                :class="getTextClasses(activePreview.is_editing ? activePreview.layout_style : (previewSlide ? previewSlide.layout_style : currentLayout), 'button')"
                                                :style="{ backgroundColor: activePreview.is_editing ? activePreview.button_color : (previewSlide ? previewSlide.button_color : '') }"
                                                x-text="activePreview.is_editing ? (activePreview.button_text || 'Next') : (previewSlide ? previewSlide.button_text : 'Next')"></button>

                                        <div class="mt-auto flex items-center justify-between">
                                            <div class="flex items-center gap-1.5">
                                                <div class="w-6 h-1.5 rounded-full" :style="{ backgroundColor: activePreview.is_editing ? activePreview.button_color : (previewSlide ? previewSlide.button_color : '') }"></div>
                                                <div class="w-1.5 h-1.5 bg-gray-300/50 rounded-full"></div>
                                                <div class="w-1.5 h-1.5 bg-gray-300/50 rounded-full"></div>
                                            </div>

                                            {{-- Bottom Arrow Preview --}}
                                            <template x-if="(activePreview.is_editing ? activePreview.button_type : (previewSlide ? previewSlide.button_type : 'action_below_text')) === 'bottom_arrow'">
                                                <div class="w-8 h-8 rounded-full border-2 flex items-center justify-center" :style="{ borderColor: activePreview.is_editing ? activePreview.button_color : (previewSlide ? previewSlide.button_color : '') }">
                                                    <svg class="w-4 h-4" :style="{ color: activePreview.is_editing ? activePreview.button_color : (previewSlide ? previewSlide.button_color : '') }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </template>

                        </div>
                    </div>
                </div>
                <p class="text-center text-[10px] text-brand-muted mt-5 font-bold flex items-center justify-center gap-2">
                    <span class="w-1 h-1 rounded-full bg-accent"></span>
                    {{ $label }} App Preview
                </p>
            </div>
        </div>
    </div>
    {{-- END MAIN GRID --}}

    {{-- ================================================================ --}}
    {{-- ADD SLIDE MODAL --}}
    {{-- ================================================================ --}}
    <div x-show="showAddModal" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" class="fixed inset-0 z-50 flex items-center justify-center p-6" x-cloak>
        <div class="absolute inset-0 bg-brand/70 backdrop-blur-md" @click="closeAddModal()"></div>
        <div class="relative bg-white rounded-3xl p-8 lg:p-10 w-full max-w-4xl shadow-2xl z-10 max-h-[90vh] overflow-y-auto modal-content border border-gray-50">
            <div class="flex items-center gap-4 mb-8">
                <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-accent/20 to-accent/5 flex items-center justify-center">
                    <svg class="w-5 h-5 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                </div>
                <div>
                    <h3 class="text-xl font-black text-brand tracking-tight">Add New Slide</h3>
                    <p class="text-xs text-brand-muted mt-0.5">Configure a new onboarding experience screen.</p>
                </div>
                <button @click="closeAddModal()" class="ml-auto p-2 hover:bg-surface rounded-xl transition-all duration-300">
                    <svg class="w-5 h-5 text-brand-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form action="{{ route('orchestrator.onboarding.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="app_type" value="{{ $appType }}">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-5">
                        <div>
                            <label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-2.5">Slide Title</label>
                            <input type="text" name="title" x-model="activePreview.title" required placeholder="e.g. Welcome to WADEXPRO" class="w-full bg-surface/80 border border-gray-100 focus:border-accent/50 focus:bg-white rounded-xl py-4 px-6 text-sm font-bold outline-none transition-all duration-300 premium-shadow focus:shadow-lg focus:shadow-accent/5">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-2.5">Description <span class="text-gray-300 font-normal normal-case tracking-normal">(Optional)</span></label>
                            <textarea name="description" x-model="activePreview.description" rows="3" placeholder="Short tagline for this slide..." class="w-full bg-surface/80 border border-gray-100 focus:border-accent/50 focus:bg-white rounded-xl py-4 px-6 text-sm font-bold outline-none transition-all duration-300 premium-shadow focus:shadow-lg focus:shadow-accent/5 resize-none"></textarea>
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-2.5">Action Button Text</label>
                            <input type="text" name="button_text" x-model="activePreview.button_text" placeholder="e.g. Get Started" class="w-full bg-surface/80 border border-gray-100 focus:border-accent/50 focus:bg-white rounded-xl py-4 px-6 text-sm font-bold outline-none transition-all duration-300 premium-shadow focus:shadow-lg focus:shadow-accent/5">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-2.5">Background Image/Video</label>
                            <input type="file" name="image" accept="image/jpeg,image/png,image/webp,image/gif,video/mp4,video/webm,video/quicktime" required
                                   @change="handleImageChange($event)"
                                   class="file-input-btn w-full bg-surface/80 rounded-xl py-3 px-4 text-sm font-bold border border-gray-100 file:mr-4 file:py-2.5 file:px-5 file:rounded-xl file:border-0 file:text-xs file:font-black file:bg-gradient-to-r file:from-brand file:to-brand-light file:text-white file:cursor-pointer hover:file:shadow-lg hover:file:shadow-brand/20 transition-all duration-300">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-2.5">Button Strategy</label>
                            <div class="space-y-2.5">
                                @foreach(\App\Modules\Admin\Models\OnboardingSlide::BUTTON_TYPES as $type => $label_text)
                                <label class="flex items-center gap-3 p-3.5 bg-surface/80 rounded-xl cursor-pointer hover:bg-gray-50 transition-all duration-300 border border-gray-100 focus-within:border-accent/50 focus-within:shadow-lg focus-within:shadow-accent/5">
                                    <input type="radio" name="button_type" value="{{ $type }}" x-model="activePreview.button_type" class="w-4 h-4 text-brand focus:ring-accent border-gray-300">
                                    <span class="text-xs font-bold text-brand uppercase tracking-tight">{{ $label_text }}</span>
                                </label>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="space-y-5">
                        <div>
                            <label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-3">Select Layout Style</label>
                            <div class="grid grid-cols-2 gap-3">
                                @foreach(\App\Modules\Admin\Models\OnboardingSlide::LAYOUT_STYLES as $key => $name)
                                <label class="relative cursor-pointer group">
                                    <input type="radio" name="layout_style" value="{{ $key }}" class="peer hidden" x-model="activePreview.layout_style">
                                    <div class="p-4 border-2 border-gray-100 rounded-2xl group-hover:border-accent/30 group-hover:bg-accent/[0.02] peer-checked:border-accent peer-checked:bg-accent/5 transition-all duration-300 premium-shadow">
                                        <p class="text-[11px] font-black leading-tight text-brand-muted peer-checked:text-brand">{{ $name }}</p>
                                    </div>
                                    <div class="absolute top-2 right-2 opacity-0 peer-checked:opacity-100 transition-opacity duration-300">
                                        <div class="w-5 h-5 bg-brand rounded-full flex items-center justify-center shadow-lg shadow-brand/20">
                                            <svg class="w-3 h-3 text-accent" fill="currentColor" viewBox="0 0 20 20"><path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/></svg>
                                        </div>
                                    </div>
                                </label>
                                @endforeach
                            </div>
                        </div>

                        <div class="space-y-5 pt-5 border-t border-gray-50">
                            <h4 class="text-[10px] font-black text-brand-muted uppercase tracking-widest flex items-center gap-2">
                                <svg class="w-3 h-3 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/></svg>
                                Custom Colors
                            </h4>
                            <div class="grid grid-cols-1 gap-4">
                                <div>
                                    <label class="block text-[9px] font-black text-gray-400 uppercase mb-2">Background Color</label>
                                    <div class="flex items-center gap-3 bg-surface/80 p-2 rounded-xl border border-gray-100 focus-within:border-accent/50 transition-all duration-300">
                                        <input type="color" name="bg_color" x-model="activePreview.bg_color" class="w-9 h-9 rounded-lg border-none cursor-pointer">
                                        <input type="text" x-model="activePreview.bg_color" class="bg-transparent border-none text-[10px] font-black w-full outline-none">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-[9px] font-black text-gray-400 uppercase mb-2">Title/Text Color</label>
                                    <div class="flex items-center gap-3 bg-surface/80 p-2 rounded-xl border border-gray-100 focus-within:border-accent/50 transition-all duration-300">
                                        <input type="color" name="text_color" x-model="activePreview.text_color" class="w-9 h-9 rounded-lg border-none cursor-pointer">
                                        <input type="text" x-model="activePreview.text_color" class="bg-transparent border-none text-[10px] font-black w-full outline-none">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-[9px] font-black text-gray-400 uppercase mb-2">Primary Button Color</label>
                                    <div class="flex items-center gap-3 bg-surface/80 p-2 rounded-xl border border-gray-100 focus-within:border-accent/50 transition-all duration-300">
                                        <input type="color" name="button_color" x-model="activePreview.button_color" class="w-9 h-9 rounded-lg border-none cursor-pointer">
                                        <input type="text" x-model="activePreview.button_color" class="bg-transparent border-none text-[10px] font-black w-full outline-none">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex gap-4 mt-8 pt-6 border-t border-gray-50">
                    <button type="button" @click="closeAddModal()" class="flex-1 py-3.5 bg-surface/80 rounded-xl font-bold text-sm hover:bg-gray-200 transition-all duration-300 border border-gray-100">Cancel</button>
                    <button type="submit" class="group relative overflow-hidden flex-1 py-3.5 bg-brand text-white rounded-xl font-bold text-sm hover:shadow-2xl hover:shadow-brand/30 transition-all duration-300">
                        <div class="absolute inset-0 shimmer-bg opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                        <span class="relative z-10">Create Slide</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ================================================================ --}}
    {{-- EDIT SLIDE MODAL --}}
    {{-- ================================================================ --}}
    <div x-show="showEditModal" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 z-50 flex items-center justify-center p-6" x-cloak>
        <div class="absolute inset-0 bg-brand/70 backdrop-blur-md" @click="closeEditModal()"></div>
        <div class="relative bg-white rounded-3xl p-8 lg:p-10 w-full max-w-4xl shadow-2xl z-10 max-h-[90vh] overflow-y-auto modal-content border border-gray-50">
            <div class="flex items-center gap-4 mb-8">
                <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-accent/20 to-accent/5 flex items-center justify-center">
                    <svg class="w-5 h-5 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                </div>
                <div>
                    <h3 class="text-xl font-black text-brand tracking-tight">Edit Slide</h3>
                    <p class="text-xs text-brand-muted mt-0.5">Modify the onboarding slide and preview changes in real-time.</p>
                </div>
                <button @click="closeEditModal()" class="ml-auto p-2 hover:bg-surface rounded-xl transition-all duration-300">
                    <svg class="w-5 h-5 text-brand-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form :action="'{{ route('orchestrator.onboarding.update', ['id' => 'ID']) }}'.replace('ID', editSlide ? editSlide.id : '')" method="POST" enctype="multipart/form-data">
                @csrf @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-5">
                        <div>
                            <label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-2.5">Slide Title</label>
                            <input type="text" name="title" x-model="activePreview.title" required class="w-full bg-surface/80 border border-gray-100 focus:border-accent/50 focus:bg-white rounded-xl py-4 px-6 text-sm font-bold outline-none transition-all duration-300 premium-shadow focus:shadow-lg focus:shadow-accent/5">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-2.5">Description</label>
                            <textarea name="description" rows="3" x-model="activePreview.description" class="w-full bg-surface/80 border border-gray-100 focus:border-accent/50 focus:bg-white rounded-xl py-4 px-6 text-sm font-bold outline-none transition-all duration-300 premium-shadow focus:shadow-lg focus:shadow-accent/5 resize-none"></textarea>
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-2.5">Action Button Text</label>
                            <input type="text" name="button_text" x-model="activePreview.button_text" class="w-full bg-surface/80 border border-gray-100 focus:border-accent/50 focus:bg-white rounded-xl py-4 px-6 text-sm font-bold outline-none transition-all duration-300 premium-shadow focus:shadow-lg focus:shadow-accent/5">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-2.5">Replace Image/Video <span class="text-gray-300 font-normal normal-case tracking-normal">(Optional)</span></label>
                            <input type="file" name="image" accept="image/jpeg,image/png,image/webp,image/gif,video/mp4,video/webm,video/quicktime"
                                   @change="handleImageChange($event)"
                                   class="file-input-btn w-full bg-surface/80 rounded-xl py-3 px-4 text-sm font-bold border border-gray-100 file:mr-4 file:py-2.5 file:px-5 file:rounded-xl file:border-0 file:text-xs file:font-black file:bg-gradient-to-r file:from-brand file:to-brand-light file:text-white file:cursor-pointer hover:file:shadow-lg hover:file:shadow-brand/20 transition-all duration-300">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-2.5">Button Strategy</label>
                            <div class="space-y-2.5">
                                @foreach(\App\Modules\Admin\Models\OnboardingSlide::BUTTON_TYPES as $type => $label_text)
                                <label class="flex items-center gap-3 p-3.5 bg-surface/80 rounded-xl cursor-pointer hover:bg-gray-50 transition-all duration-300 border border-gray-100 focus-within:border-accent/50 focus-within:shadow-lg focus-within:shadow-accent/5">
                                    <input type="radio" name="button_type" value="{{ $type }}" x-model="activePreview.button_type" class="w-4 h-4 text-brand focus:ring-accent border-gray-300">
                                    <span class="text-xs font-bold text-brand uppercase tracking-tight">{{ $label_text }}</span>
                                </label>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="space-y-5">
                        <div>
                            <label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-3">Change Layout Style</label>
                            <div class="grid grid-cols-2 gap-3">
                                @foreach(\App\Modules\Admin\Models\OnboardingSlide::LAYOUT_STYLES as $key => $name)
                                <label class="relative cursor-pointer group">
                                    <input type="radio" name="layout_style" value="{{ $key }}" class="peer hidden" x-model="activePreview.layout_style">
                                    <div class="p-4 border-2 border-gray-100 rounded-2xl group-hover:border-accent/30 group-hover:bg-accent/[0.02] peer-checked:border-accent peer-checked:bg-accent/5 transition-all duration-300 premium-shadow">
                                        <p class="text-[11px] font-black leading-tight text-brand-muted peer-checked:text-brand">{{ $name }}</p>
                                    </div>
                                    <div class="absolute top-2 right-2 opacity-0 peer-checked:opacity-100 transition-opacity duration-300">
                                        <div class="w-5 h-5 bg-brand rounded-full flex items-center justify-center shadow-lg shadow-brand/20">
                                            <svg class="w-3 h-3 text-accent" fill="currentColor" viewBox="0 0 20 20"><path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/></svg>
                                        </div>
                                    </div>
                                </label>
                                @endforeach
                            </div>
                        </div>

                        <div class="space-y-5 pt-5 border-t border-gray-50">
                            <h4 class="text-[10px] font-black text-brand-muted uppercase tracking-widest flex items-center gap-2">
                                <svg class="w-3 h-3 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/></svg>
                                Custom Colors
                            </h4>
                            <div class="grid grid-cols-1 gap-4">
                                <div>
                                    <label class="block text-[9px] font-black text-gray-400 uppercase mb-2">Background Color</label>
                                    <div class="flex items-center gap-3 bg-surface/80 p-2 rounded-xl border border-gray-100 focus-within:border-accent/50 transition-all duration-300">
                                        <input type="color" name="bg_color" x-model="activePreview.bg_color" class="w-9 h-9 rounded-lg border-none cursor-pointer">
                                        <input type="text" x-model="activePreview.bg_color" class="bg-transparent border-none text-[10px] font-black w-full outline-none">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-[9px] font-black text-gray-400 uppercase mb-2">Title/Text Color</label>
                                    <div class="flex items-center gap-3 bg-surface/80 p-2 rounded-xl border border-gray-100 focus-within:border-accent/50 transition-all duration-300">
                                        <input type="color" name="text_color" x-model="activePreview.text_color" class="w-9 h-9 rounded-lg border-none cursor-pointer">
                                        <input type="text" x-model="activePreview.text_color" class="bg-transparent border-none text-[10px] font-black w-full outline-none">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-[9px] font-black text-gray-400 uppercase mb-2">Primary Button Color</label>
                                    <div class="flex items-center gap-3 bg-surface/80 p-2 rounded-xl border border-gray-100 focus-within:border-accent/50 transition-all duration-300">
                                        <input type="color" name="button_color" x-model="activePreview.button_color" class="w-9 h-9 rounded-lg border-none cursor-pointer">
                                        <input type="text" x-model="activePreview.button_color" class="bg-transparent border-none text-[10px] font-black w-full outline-none">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex gap-4 mt-8 pt-6 border-t border-gray-50">
                    <button type="button" @click="closeEditModal()" class="flex-1 py-3.5 bg-surface/80 rounded-xl font-bold text-sm hover:bg-gray-200 transition-all duration-300 border border-gray-100">Cancel</button>
                    <button type="submit" class="group relative overflow-hidden flex-1 py-3.5 bg-brand text-white rounded-xl font-bold text-sm hover:shadow-2xl hover:shadow-brand/30 transition-all duration-300">
                        <div class="absolute inset-0 shimmer-bg opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                        <span class="relative z-10">Save Changes</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ================================================================ --}}
    {{-- FULL SCREEN PREVIEW MODAL --}}
    {{-- ================================================================ --}}
    <div x-show="showViewModal" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" class="fixed inset-0 z-[100] flex items-center justify-center p-4 md:p-12" x-cloak>
        <div class="absolute inset-0 bg-brand/90 backdrop-blur-xl" @click="closeViewModal()"></div>
        <div class="relative bg-white rounded-3xl overflow-hidden shadow-2xl h-[85vh] max-h-[750px] aspect-[9/19] z-10 border-[8px] md:border-[12px] border-gray-900 ring-4 ring-white/10 flex-shrink-0">
            {{-- Inner Glass Overlay --}}
            <div class="absolute inset-0 phone-glass pointer-events-none z-20"></div>
            <div class="h-full w-full relative overflow-hidden"
                 :class="getLayoutClasses(viewModalSlide ? viewModalSlide.layout_style : 'full_bleed')"
                 :style="{ backgroundColor: viewModalSlide ? viewModalSlide.bg_color : '' }">

                {{-- Media --}}
                <template x-if="viewModalSlide && viewModalSlide.image_url">
                    <div class="h-full w-full">
                        <template x-if="viewModalSlide.media_type === 'video'">
                            <video :src="viewModalSlide.image_url" class="object-cover w-full h-full" :class="getImageClasses(viewModalSlide.layout_style)" autoplay loop muted></video>
                        </template>
                        <template x-if="viewModalSlide.media_type !== 'video'">
                            <img :src="viewModalSlide.image_url" class="object-cover" :class="getImageClasses(viewModalSlide.layout_style)">
                        </template>
                    </div>
                </template>

                {{-- Background Layer --}}
                <div class="absolute inset-0 z-[1]" :class="getBackgroundLayerClasses(viewModalSlide ? viewModalSlide.layout_style : 'full_bleed')"></div>

                {{-- Content --}}
                <div class="relative z-10 flex flex-col h-full w-full" :class="getContentContainerClasses(viewModalSlide ? viewModalSlide.layout_style : 'full_bleed')">
                    <div class="mb-4">
                        <span class="text-amber-400 text-xs font-black tracking-widest uppercase mb-2 block">Premium Onboarding</span>
                        <h3 class="font-black leading-tight mb-4 transition-all duration-500"
                            :class="getTextClasses(viewModalSlide ? viewModalSlide.layout_style : 'full_bleed', 'title', true)"
                            :style="{ color: (viewModalSlide && viewModalSlide.text_color) ? viewModalSlide.text_color : '' }"
                            x-text="viewModalSlide ? viewModalSlide.title : ''"></h3>
                        <p class="leading-relaxed opacity-90 transition-all duration-500"
                           :class="getTextClasses(viewModalSlide ? viewModalSlide.layout_style : 'full_bleed', 'description', true)"
                           :style="{ color: (viewModalSlide && viewModalSlide.text_color) ? viewModalSlide.text_color : '' }"
                           x-text="viewModalSlide ? viewModalSlide.description : ''"></p>
                    </div>

                     <div class="mt-auto w-full">
                        <template x-if="(viewModalSlide ? viewModalSlide.button_type : 'action_below_text') === 'action_below_text'">
                            <button class="w-full py-4 rounded-xl font-black text-xs uppercase tracking-widest shadow-2xl transition-all"
                                    :class="getTextClasses(viewModalSlide ? viewModalSlide.layout_style : 'full_bleed', 'button')"
                                    :style="{ backgroundColor: (viewModalSlide && viewModalSlide.button_color) ? viewModalSlide.button_color : '' }"
                                    x-text="viewModalSlide ? (viewModalSlide.button_text || 'Continue Experience') : 'Continue Experience'">
                            </button>
                        </template>

                        <div class="flex items-center" :class="(viewModalSlide ? viewModalSlide.button_type : 'action_below_text') === 'bottom_arrow' ? 'justify-between' : 'justify-center'">
                            <div class="flex items-center gap-2" :class="(viewModalSlide ? viewModalSlide.button_type : 'action_below_text') === 'action_below_text' ? 'mb-8' : ''">
                                <div class="w-8 h-2 rounded-full" :style="{ backgroundColor: (viewModalSlide && viewModalSlide.button_color) ? viewModalSlide.button_color : '#FFB800' }"></div>
                                <div class="w-2 h-2 bg-gray-300/20 rounded-full"></div>
                                <div class="w-2 h-2 bg-gray-300/20 rounded-full"></div>
                            </div>

                            <template x-if="(viewModalSlide ? viewModalSlide.button_type : 'action_below_text') === 'bottom_arrow'">
                                <div class="w-12 h-12 rounded-full border-2 flex items-center justify-center" :style="{ borderColor: (viewModalSlide && viewModalSlide.button_color) ? viewModalSlide.button_color : '#FFB800' }">
                                    <svg class="w-6 h-6" :style="{ color: (viewModalSlide && viewModalSlide.button_color) ? viewModalSlide.button_color : '#FFB800' }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
            <button @click="closeViewModal()" class="absolute top-4 right-4 bg-brand/50 backdrop-blur-sm text-white p-2.5 rounded-full hover:bg-brand transition-all duration-300 z-30 hover:scale-110">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    </div>

</div>

@endsection

@push('scripts')
<script>
    function onboardingManager() {
        return {
            activeTab: 'slides',
            showAddModal: false,
            showEditModal: false,
            showViewModal: false,
            editSlide: null,
            viewModalSlide: null,
            savingOrder: false,
            currentLayout: '{{ $slides->count() > 0 ? $slides->first()->layout_style : "top_image" }}',
            previewSlide: {!! $slides->count() > 0 ? json_encode($slides->first()) : 'null' !!},
            splash: {!! json_encode($splash) !!},
            allSlides: {!! $slides->keyBy('id')->toJson() !!},
            
            // Reactive state for Live Preview (updates as you type in modals)
            activePreview: {
                title: '',
                description: '',
                layout_style: 'top_image',
                image_url: null,
                button_text: 'Next',
                button_type: 'action_below_text',
                bg_color: '#FFFFFF',
                button_color: '#FFB800',
                media_type: 'image',
                is_editing: false
            },

            // ─── Sortable ─────────────────────────────────────────────
            initSortable() {
                const el = document.getElementById('slides-list');
                if (!el) return;
                Sortable.create(el, {
                    handle: '.drag-handle',
                    filter: 'button, a, input, textarea, form',
                    preventOnFilter: false,
                    animation: 150,
                    ghostClass: 'opacity-50',
                    onEnd: () => this.saveOrder()
                });
            },

            saveOrder() {
                const order = Array.from(document.querySelectorAll('.slide-item')).map(el => el.dataset.id);
                this.savingOrder = true;
                axios.post('{{ route("orchestrator.onboarding.reorder") }}', { order })
                    .catch(err => { console.error('Reorder failed:', err); alert('Failed to save slide order.'); })
                    .finally(() => { this.savingOrder = false; });
            },

            // ─── Modal Openers / Closers ──────────────────────────────
            openAddModal() {
                this.activePreview = {
                    title: '',
                    description: '',
                    layout_style: 'top_image',
                    image_url: '/images/placeholder.png',
                    button_text: 'Next',
                    button_type: 'action_below_text',
                    bg_color: '#FFFFFF',
                    button_color: '#FFB800',
                    media_type: 'image',
                    is_editing: true
                };
                this.showAddModal = true;
            },

            closeAddModal() {
                this.showAddModal = false;
                this.activePreview.is_editing = false;
            },

            openEditModal(slideId) {
                const slide = this.allSlides[slideId];
                if (!slide) return;
                this.editSlide = { ...slide };
                this.activePreview = {
                    title: slide.title,
                    description: slide.description,
                    layout_style: slide.layout_style,
                    image_url: slide.image_url,
                    button_text: slide.button_text || 'Next',
                    button_type: slide.button_type || 'action_below_text',
                    bg_color: slide.bg_color || '#FFFFFF',
                    text_color: slide.text_color || '#000B1E',
                    button_color: slide.button_color || '#FFB800',
                    media_type: slide.media_type || 'image',
                    is_editing: true
                };
                this.showEditModal = true;
            },

            closeEditModal() {
                this.showEditModal = false;
                setTimeout(() => {
                    this.editSlide = null;
                    this.activePreview.is_editing = false;
                }, 300);
            },

            viewSlide(slideId) {
                this.viewModalSlide = this.allSlides[slideId] || null;
                if (this.viewModalSlide) {
                    this.showViewModal = true;
                }
            },
            
            closeViewModal() {
                this.showViewModal = false;
                setTimeout(() => {
                    this.viewModalSlide = null;
                }, 300);
            },

            handleImageChange(event) {
                const file = event.target.files[0];
                if (file) {
                    this.activePreview.image_url = URL.createObjectURL(file);
                    this.activePreview.media_type = file.type.startsWith('video/') ? 'video' : 'image';
                    this.activePreview.is_video = file.type.startsWith('video/');
                }
            },

            handleSplashImageChange(event, type) {
                const file = event.target.files[0];
                if (file) {
                    const url = URL.createObjectURL(file);
                    const isVideo = file.type.startsWith('video/');
                    if (type === 'logo') {
                        this.splash.logo_url = url;
                        this.splash.logo_is_video = isVideo;
                        this.splash.show_logo = true;
                    }
                    if (type === 'background') {
                        this.splash.background_url = url;
                        this.splash.background_is_video = isVideo;
                        this.splash.show_background = true;
                    }
                }
            },

            // ─── Layout Helpers ───────────────────────────────────────
            getLayoutClasses(style) {
                const layouts = {
                    'full_bleed': 'bg-gray-900 relative',
                    'top_image': 'bg-white flex flex-col',
                    'bottom_image': 'bg-white flex flex-col-reverse',
                    'floating_card': 'bg-gray-900 relative',
                    'centered_mini': 'bg-white flex flex-col items-center justify-center',
                    'dark_premium': 'bg-gray-950 text-white flex flex-col',
                    'glassmorphic': 'bg-brand relative',
                    'industrial': 'bg-zinc-100 flex flex-col',
                    'side_by_side': 'bg-white flex flex-row items-center',
                    'clean_vector': 'bg-white flex flex-col'
                };
                return layouts[style] || layouts['top_image'];
            },

            getImageClasses(style) {
                const styles = {
                    'full_bleed': 'absolute inset-0 w-full h-full opacity-40',
                    'top_image': 'h-3/5 w-full flex-shrink-0',
                    'bottom_image': 'h-3/5 w-full flex-shrink-0',
                    'floating_card': 'absolute inset-0 w-full h-full opacity-30',
                    'centered_mini': 'w-32 h-32 rounded-full mb-6 border-4 border-white shadow-xl flex-shrink-0',
                    'dark_premium': 'h-3/5 w-full flex-shrink-0 grayscale contrast-125',
                    'glassmorphic': 'absolute inset-0 w-full h-full opacity-20',
                    'industrial': 'h-1/2 w-full flex-shrink-0 sepia-[.3]',
                    'side_by_side': 'w-1/2 h-full flex-shrink-0',
                    'clean_vector': 'h-2/5 w-full flex-shrink-0 object-contain p-8'
                };
                return styles[style] || 'h-1/2 w-full flex-shrink-0';
            },

            getBackgroundLayerClasses(style) {
                if (style === 'full_bleed') return 'bg-gradient-to-t from-brand via-brand/50 to-transparent';
                if (style === 'floating_card') return 'bg-gradient-to-t from-black/80 to-transparent';
                if (style === 'glassmorphic') return 'bg-gradient-to-br from-purple-500/20 to-blue-500/20 backdrop-blur-sm';
                if (style === 'dark_premium') return 'bg-gradient-to-b from-transparent to-black/60';
                return '';
            },

            getContentContainerClasses(style) {
                const containers = {
                    'full_bleed': 'absolute inset-0 flex flex-col justify-end p-8 text-center',
                    'top_image': 'flex-1 p-8 flex flex-col justify-center text-center',
                    'bottom_image': 'flex-1 p-8 flex flex-col justify-center text-center',
                    'floating_card': 'absolute inset-x-4 bottom-6 bg-white/95 backdrop-blur rounded-2xl shadow-xl p-6 text-center',
                    'centered_mini': 'p-8 text-center',
                    'dark_premium': 'flex-1 p-8 flex flex-col justify-end text-center',
                    'glassmorphic': 'absolute inset-6 flex flex-col justify-center items-center text-center bg-white/10 backdrop-blur-xl rounded-2xl border border-white/20 p-8',
                    'industrial': 'flex-1 p-8 flex flex-col justify-center border-l-8 border-accent',
                    'side_by_side': 'w-1/2 p-6 flex flex-col justify-center',
                    'clean_vector': 'flex-1 p-8 flex flex-col justify-center text-center'
                };
                return containers[style] || containers['top_image'];
            },

            getTextClasses(style, type, isLarge = false) {
                const titleSize = isLarge ? 'text-3xl' : 'text-xl';
                const descSize = isLarge ? 'text-sm' : 'text-[11px]';
                const isDark = ['dark_premium', 'glassmorphic', 'full_bleed', 'floating_card'].includes(style);

                if (type === 'title') {
                    if (['clean_vector','top_image','centered_mini','side_by_side','bottom_image','industrial'].includes(style))
                        return `${titleSize} text-brand font-black`;
                    return `${titleSize} text-white font-black`;
                }
                if (type === 'description') {
                    if (['clean_vector','top_image','centered_mini','side_by_side','bottom_image'].includes(style))
                        return `${descSize} text-brand-muted font-medium`;
                    if (style === 'floating_card') return `${descSize} text-brand-muted font-medium`;
                    return `${descSize} text-white/70 font-medium`;
                }
                if (type === 'button') {
                    if (style === 'dark_premium') return 'bg-amber-400 text-black';
                    if (style === 'industrial') return 'bg-white text-brand';
                    if (style === 'glassmorphic') return 'bg-white text-brand shadow-white/30';
                    if (style === 'floating_card') return 'bg-brand text-white';
                    return 'bg-amber-400 text-brand';
                }
                return '';
            }
        }
    }
</script>
@endpush
