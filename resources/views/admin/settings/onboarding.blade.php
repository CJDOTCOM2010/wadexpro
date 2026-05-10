@extends('admin.layout')
@section('title', $label . ' Onboarding')
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
            <div class="flex items-center gap-2 text-[10px] font-black text-accent uppercase tracking-[0.2em] mb-2">
                <a href="{{ route('orchestrator.settings') }}" class="hover:text-brand transition-colors">Settings Hub</a>
                <span class="text-gray-300">/</span>
                <span>{{ $label }} Onboarding</span>
            </div>
            <h2 class="text-3xl font-black text-brand tracking-tight">{{ $label }} Onboarding Slides</h2>
            <p class="text-sm text-brand-muted mt-1">Design the first-launch experience for your {{ strtolower($label) }} mobile app.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('orchestrator.settings') }}" class="bg-surface text-brand-muted hover:bg-gray-100 px-6 py-3 rounded-lg text-xs font-bold transition-all flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Back to Hub
            </a>
            <button @click="openAddModal()" 
                class="flex items-center gap-3 px-6 py-3 bg-brand text-white rounded-lg hover:bg-brand-hover transition shadow-lg shadow-black/10 group">
                <svg class="w-5 h-5 text-accent transition-transform group-hover:rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                <span class="text-sm font-black tracking-tight">Add New Slide</span>
            </button>
        </div>
    </div>

    {{-- Success Flash --}}
    @if(session('success'))
    <div class="mb-8 p-4 bg-green-50 border border-green-100 text-green-600 rounded-lg flex items-center gap-3 animate-fade-in">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        <span class="text-xs font-bold">{{ session('success') }}</span>
    </div>
    @endif

    {{-- Error Flash --}}
    @if($errors->any())
    <div class="mb-8 p-4 bg-red-50 border border-red-100 text-red-600 rounded-lg flex items-start gap-3 animate-fade-in">
        <svg class="w-5 h-5 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        <div>
            <span class="text-xs font-bold">Please correct the following errors:</span>
            <ul class="text-xs list-disc pl-4 mt-2 space-y-1">
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
    <div class="flex items-center gap-4 mb-8 bg-surface p-1 rounded-xl w-fit">
        <button @click="activeTab = 'slides'" 
                :class="activeTab === 'slides' ? 'bg-white text-brand shadow-sm' : 'text-brand-muted hover:text-brand'"
                class="px-6 py-2.5 rounded-lg text-xs font-black transition-all">
            Onboarding Slides
        </button>
        <button @click="activeTab = 'splash'" 
                :class="activeTab === 'splash' ? 'bg-white text-brand shadow-sm' : 'text-brand-muted hover:text-brand'"
                class="px-6 py-2.5 rounded-lg text-xs font-black transition-all">
            Splash Screen
        </button>
    </div>

    {{-- ================================================================ --}}
    {{-- MAIN CONTENT GRID --}}
    {{-- ================================================================ --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        {{-- LEFT COLUMN: Slides or Splash Settings --}}
        <div class="lg:col-span-2 space-y-4">
            
            {{-- ONBOARDING SLIDES TAB --}}
            <div x-show="activeTab === 'slides'" id="slides-list" class="space-y-4">
                <div class="flex items-center justify-between mb-4">
                    <p class="text-[10px] font-black text-brand-muted uppercase tracking-widest">{{ $slides->count() }} Slide{{ $slides->count() !== 1 ? 's' : '' }} Configured</p>
                </div>

                @forelse($slides as $slide)
                <div class="bg-white rounded-lg border border-gray-100 shadow-sm hover:shadow-md transition-all overflow-hidden group slide-item" 
                     data-id="{{ $slide->id }}">
                    <div class="flex items-stretch">
                        {{-- Drag Handle --}}
                        <div class="w-10 shrink-0 bg-gray-50 flex items-center justify-center cursor-move hover:bg-gray-100 transition-colors drag-handle">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                        </div>
                        {{-- Image Thumbnail --}}
                        <div class="w-40 shrink-0 relative overflow-hidden bg-gray-900">
                            @if($slide->image_url)
                                @if($slide->media_type === 'video')
                                    <div class="relative w-full h-full">
                                        <video src="{{ $slide->image_url }}" class="w-full h-full object-cover opacity-90 group-hover:scale-105 transition-transform duration-500" style="min-height: 140px;"></video>
                                        <div class="absolute inset-0 flex items-center justify-center bg-black/30">
                                            <svg class="w-8 h-8 text-white opacity-80" fill="currentColor" viewBox="0 0 20 20"><path d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z"/></svg>
                                        </div>
                                    </div>
                                @else
                                    <img src="{{ $slide->image_url }}" alt="{{ $slide->title }}" class="w-full h-full object-cover opacity-90 group-hover:scale-105 transition-transform duration-500" style="min-height: 140px;">
                                @endif
                            @else
                            <div class="w-full h-full flex items-center justify-center bg-gray-200 text-gray-400" style="min-height: 140px;">
                                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </div>
                            @endif
                            <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/80 to-transparent p-3">
                                <p class="text-white text-[10px] font-black truncate">{{ $slide->title }}</p>
                            </div>
                        </div>
                        {{-- Slide Info --}}
                        <div class="flex-1 p-5 flex flex-col justify-between">
                            <div>
                                <div class="flex items-center gap-2 mb-2">
                                    <span class="text-[10px] font-black uppercase tracking-widest {{ $slide->is_active ? 'text-emerald-600' : 'text-red-500' }}">
                                        {{ $slide->is_active ? '● Active' : '○ Inactive' }}
                                    </span>
                                    <span class="text-[10px] font-bold text-gray-300">|</span>
                                    <span class="text-[10px] font-black text-brand-muted uppercase tracking-widest">Slide #{{ $slide->sort_order + 1 }}</span>
                                </div>
                                <h4 class="text-lg font-black text-brand mb-1 tracking-tight">{{ $slide->title }}</h4>
                                <p class="text-xs text-brand-muted leading-relaxed">{{ $slide->description ?: 'No description set.' }}</p>
                            </div>
                            <div class="flex items-center gap-2 mt-4">
                                <button type="button" @click.stop="viewSlide('{{ $slide->id }}')" class="px-3 py-2 bg-brand/5 text-brand hover:bg-brand hover:text-white rounded-lg text-[11px] font-bold transition-all flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    View
                                </button>
                                <button type="button" @click.stop="openEditModal('{{ $slide->id }}')" class="px-3 py-2 bg-surface text-brand-muted hover:bg-gray-200 rounded-lg text-[11px] font-bold transition-all flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                    Edit
                                </button>
                                <form action="{{ route('orchestrator.onboarding.toggle', $slide->id) }}" method="POST" @mousedown.stop @click.stop>
                                    @csrf @method('PATCH')
                                    <button type="submit" class="px-3 py-2 {{ $slide->is_active ? 'bg-orange-50 text-orange-600 hover:bg-orange-100' : 'bg-emerald-50 text-emerald-600 hover:bg-emerald-100' }} rounded-lg text-[11px] font-bold transition-all">
                                        {{ $slide->is_active ? 'Deactivate' : 'Activate' }}
                                    </button>
                                </form>
                                <form action="{{ route('orchestrator.onboarding.destroy', $slide->id) }}" method="POST" onsubmit="return confirm('Delete this slide?')" @mousedown.stop @click.stop>
                                    @csrf @method('DELETE')
                                    <button type="submit" class="px-3 py-2 bg-red-50 text-red-500 hover:bg-red-100 rounded-lg text-[11px] font-bold transition-all">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="bg-white rounded-lg border border-dashed border-gray-200 p-16 text-center">
                    <h3 class="text-xl font-black text-brand mb-2">No Slides Yet</h3>
                    <p class="text-xs text-brand-muted mb-6">Create your first onboarding slide for the {{ $label }} app.</p>
                    <button @click="openAddModal()" class="px-8 py-3 bg-brand text-white text-xs font-black rounded-lg hover:bg-brand-hover transition-all">Create Slides</button>
                </div>
                @endforelse
            </div>

            {{-- SPLASH SCREEN TAB --}}
            <div x-show="activeTab === 'splash'" x-cloak class="space-y-6">
                <form action="{{ route('orchestrator.onboarding.splash.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="app_type" value="{{ $appType }}">
                    
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-8 space-y-8">
                        <div>
                            <h3 class="text-lg font-black text-brand mb-1">Launch Identity</h3>
                            <p class="text-xs text-brand-muted">Control the first impressions as the app launches.</p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="space-y-6">
                                <div>
                                    <label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-2">Launch Tagline</label>
                                    <input type="text" name="tagline" x-model="splash.tagline" class="w-full bg-surface border-2 border-transparent focus:border-accent focus:bg-white rounded-lg py-4 px-6 text-sm font-bold outline-none transition-all">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-2">Visibility Duration (<span x-text="splash.duration_ms"></span>ms)</label>
                                    <input type="range" name="duration_ms" min="1000" max="10000" step="500" x-model="splash.duration_ms" class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-brand">
                                    <div class="flex justify-between mt-2 text-[8px] font-black text-gray-400">
                                        <span>1s</span><span>5s</span><span>10s</span>
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-2">Backdrop Color</label>
                                        <div class="flex items-center gap-3 bg-surface p-2 rounded-lg border-2 border-transparent focus-within:border-accent">
                                            <input type="color" name="bg_color" x-model="splash.bg_color" class="w-8 h-8 rounded-md border-none cursor-pointer">
                                            <input type="text" x-model="splash.bg_color" class="bg-transparent border-none text-[10px] font-black w-full outline-none">
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-2">Accent/Ripple</label>
                                        <div class="flex items-center gap-3 bg-surface p-2 rounded-lg border-2 border-transparent focus-within:border-accent">
                                            <input type="color" name="secondary_color" x-model="splash.secondary_color" class="w-8 h-8 rounded-md border-none cursor-pointer">
                                            <input type="text" x-model="splash.secondary_color" class="bg-transparent border-none text-[10px] font-black w-full outline-none">
                                        </div>
                                    </div>
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div class="flex items-center justify-between p-4 bg-surface rounded-xl">
                                        <div>
                                            <p class="text-[10px] font-black text-brand uppercase tracking-widest">Logo Shield</p>
                                            <p class="text-[9px] text-brand-muted mt-0.5">Show brand logo/shield.</p>
                                        </div>
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="checkbox" name="show_logo" value="1" class="sr-only peer" :checked="splash.show_logo" @change="splash.show_logo = $event.target.checked">
                                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-brand/20 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-brand"></div>
                                        </label>
                                    </div>
                                    <div class="flex items-center justify-between p-4 bg-surface rounded-xl">
                                        <div>
                                            <p class="text-[10px] font-black text-brand uppercase tracking-widest">Backdrop Media</p>
                                            <p class="text-[9px] text-brand-muted mt-0.5">Show background image/video.</p>
                                        </div>
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="checkbox" name="show_background" value="1" class="sr-only peer" :checked="splash.show_background" @change="splash.show_background = $event.target.checked">
                                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-brand/20 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-brand"></div>
                                        </label>
                                    </div>
                                    <div class="flex items-center justify-between p-4 bg-surface rounded-xl">
                                        <div>
                                            <p class="text-[10px] font-black text-brand uppercase tracking-widest">Animated Ripple</p>
                                            <p class="text-[9px] text-brand-muted mt-0.5">Pulsing logo circles.</p>
                                        </div>
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="checkbox" name="show_ripple" value="1" class="sr-only peer" :checked="splash.show_ripple" @change="splash.show_ripple = $event.target.checked">
                                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-brand/20 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-brand"></div>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-6">
                                <div>
                                    <label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-2">Splash-Specific Logo</label>
                                    <div class="relative group">
                                        <div class="w-full h-32 bg-surface border-2 border-dashed border-gray-200 rounded-xl flex flex-col items-center justify-center overflow-hidden transition-all group-hover:border-brand">
                                            <template x-if="splash.logo_url">
                                                <div class="w-full h-full flex items-center justify-center">
                                                    <template x-if="splash.logo_media_type === 'video' || (splash.logo_url && splash.logo_url.startsWith('blob:') && splash.logo_is_video)">
                                                        <video :src="splash.logo_url" class="h-full w-full object-cover z-10" autoplay loop muted></video>
                                                    </template>
                                                    <template x-if="splash.logo_media_type !== 'video' && !(splash.logo_url && splash.logo_url.startsWith('blob:') && splash.logo_is_video)">
                                                        <img :src="splash.logo_url" class="h-16 w-auto object-contain z-10">
                                                    </template>
                                                </div>
                                            </template>
                                            <template x-if="!splash.logo_url">
                                                <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                            </template>
                                            <input type="file" name="logo" accept="image/png,image/jpeg,image/webp,image/gif,video/mp4,video/webm,video/quicktime" 
                                                   @change="handleSplashImageChange($event, 'logo')"
                                                   class="absolute inset-0 opacity-0 cursor-pointer">
                                        </div>
                                        <p class="text-[8px] font-bold text-gray-400 mt-2">Recommended: Transparent PNG, 512x512px</p>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-2">Dynamic Backdrop Image (Optional)</label>
                                    <div class="relative group">
                                        <div class="w-full h-32 bg-surface border-2 border-dashed border-gray-200 rounded-xl flex flex-col items-center justify-center overflow-hidden transition-all group-hover:border-brand">
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
                                                <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                            </template>
                                            <input type="file" name="background" accept="image/png,image/jpeg,image/webp,image/gif,video/mp4,video/webm,video/quicktime" 
                                                   @change="handleSplashImageChange($event, 'background')"
                                                   class="absolute inset-0 opacity-0 cursor-pointer">
                                        </div>
                                        <p class="text-[8px] font-bold text-gray-400 mt-2">Recommended: High Resolution (1080x2400px)</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="pt-4 border-t border-gray-50 flex justify-end">
                            <button type="submit" class="px-12 py-4 bg-brand text-white rounded-xl font-black text-xs hover:bg-brand-hover shadow-xl transition-all">
                                Save Splash Mission
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- RIGHT COLUMN: Phone Preview --}}
        <div class="lg:col-span-1">
            <div class="sticky top-32">
                <p class="text-[10px] font-black text-brand-muted uppercase tracking-widest mb-4">Live Preview</p>
                <div class="relative mx-auto" style="width: 260px;">
                    <div class="relative bg-gray-900 rounded-[2.5rem] p-3 shadow-2xl shadow-black/30 border-4 border-gray-800">
                        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-28 h-6 bg-gray-900 rounded-b-2xl z-20"></div>
                        <div class="relative bg-gray-900 rounded-[2rem] overflow-hidden" style="height: 460px;">
                            
                            {{-- Splash Preview --}}
                            <template x-if="activeTab === 'splash'">
                                <div class="h-full flex flex-col items-center justify-center p-12 text-center relative" :style="{ backgroundColor: splash.bg_color }">
                                    <template x-if="splash.show_background && splash.background_url">
                                        <div class="absolute inset-0 w-full h-full">
                                            <template x-if="splash.background_media_type === 'video' || (splash.background_url.startsWith('blob:') && splash.background_is_video)">
                                                <video :src="splash.background_url" class="absolute inset-0 w-full h-full object-cover opacity-40 z-0" autoplay loop muted></video>
                                            </template>
                                            <template x-if="splash.background_media_type !== 'video' && !(splash.background_url.startsWith('blob:') && splash.background_is_video)">
                                                <img :src="splash.background_url" class="absolute inset-0 w-full h-full object-cover opacity-40 z-0">
                                            </template>
                                        </div>
                                    </template>
                                    <div class="relative z-10 space-y-6" x-show="splash.show_logo" x-transition>
                                        <template x-if="splash.logo_url">
                                            <div class="w-20 h-20 mx-auto">
                                                <template x-if="splash.logo_media_type === 'video' || (splash.logo_url.startsWith('blob:') && splash.logo_is_video)">
                                                    <video :src="splash.logo_url" class="w-full h-full object-cover" autoplay loop muted></video>
                                                </template>
                                                <template x-if="splash.logo_media_type !== 'video' && !(splash.logo_url.startsWith('blob:') && splash.logo_is_video)">
                                                    <img :src="splash.logo_url" class="w-full h-full object-contain">
                                                </template>
                                            </div>
                                        </template>
                                        <template x-if="!splash.logo_url">
                                            <div class="w-16 h-16 rounded-full mx-auto flex items-center justify-center bg-white shadow-xl">
                                                <span class="text-brand font-black text-2xl">W</span>
                                            </div>
                                        </template>
                                    </div>
                                    <h2 class="text-xl font-black text-white relative z-10 mt-6" x-text="splash.tagline"></h2>
                                    <template x-if="splash.show_ripple">
                                        <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                                            <div class="w-56 h-56 rounded-full border border-white/10 animate-ping"></div>
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
                <p class="text-center text-[10px] text-brand-muted mt-4 font-bold">{{ $label }} App Preview</p>
            </div>
        </div>
    </div>
    {{-- END MAIN GRID --}}

    {{-- ================================================================ --}}
    {{-- ADD SLIDE MODAL --}}
    {{-- ================================================================ --}}
    <div x-show="showAddModal" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" class="fixed inset-0 z-50 flex items-center justify-center p-6" x-cloak>
        <div class="absolute inset-0 bg-brand/60 backdrop-blur-sm" @click="closeAddModal()"></div>
        <div class="relative bg-white rounded-lg p-8 w-full max-w-4xl shadow-2xl z-10 max-h-[90vh] overflow-y-auto">
            <h3 class="text-xl font-black text-brand mb-6">Add New Slide</h3>
            <form action="{{ route('orchestrator.onboarding.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="app_type" value="{{ $appType }}">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-5">
                        <div>
                            <label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-2">Slide Title</label>
                            <input type="text" name="title" x-model="activePreview.title" required placeholder="e.g. Welcome to WADEXPRO" class="w-full bg-surface border-2 border-transparent focus:border-accent focus:bg-white rounded-lg py-4 px-6 text-sm font-bold outline-none transition-all">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-2">Description (Optional)</label>
                            <textarea name="description" x-model="activePreview.description" rows="3" placeholder="Short tagline for this slide..." class="w-full bg-surface border-2 border-transparent focus:border-accent focus:bg-white rounded-lg py-4 px-6 text-sm font-bold outline-none transition-all resize-none"></textarea>
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-2">Action Button Text</label>
                            <input type="text" name="button_text" x-model="activePreview.button_text" placeholder="e.g. Get Started" class="w-full bg-surface border-2 border-transparent focus:border-accent focus:bg-white rounded-lg py-4 px-6 text-sm font-bold outline-none transition-all">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-2">Background Image/Video</label>
                            <input type="file" name="image" accept="image/jpeg,image/png,image/webp,image/gif,video/mp4,video/webm,video/quicktime" required 
                                   @change="handleImageChange($event)"
                                   class="w-full bg-surface rounded-lg py-3 px-4 text-sm font-bold file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-black file:bg-brand file:text-white file:cursor-pointer hover:file:bg-brand-hover">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-2">Button Strategy</label>
                            <div class="space-y-2">
                                @foreach(\App\Modules\Admin\Models\OnboardingSlide::BUTTON_TYPES as $type => $label_text)
                                <label class="flex items-center gap-3 p-3 bg-surface rounded-lg cursor-pointer hover:bg-gray-50 transition-all border-2 border-transparent focus-within:border-accent">
                                    <input type="radio" name="button_type" value="{{ $type }}" x-model="activePreview.button_type" class="w-4 h-4 text-brand focus:ring-accent border-gray-300">
                                    <span class="text-xs font-bold text-brand uppercase tracking-tight">{{ $label_text }}</span>
                                </label>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-2">Select Layout Style</label>
                        <div class="grid grid-cols-2 gap-3 mb-6">
                            @foreach(\App\Modules\Admin\Models\OnboardingSlide::LAYOUT_STYLES as $key => $name)
                            <label class="relative cursor-pointer group">
                                <input type="radio" name="layout_style" value="{{ $key }}" class="peer hidden" x-model="activePreview.layout_style">
                                <div class="p-3 border-2 border-gray-100 rounded-lg group-hover:bg-gray-50 peer-checked:border-brand peer-checked:bg-brand/5 transition-all">
                                    <p class="text-[10px] font-black leading-tight text-brand-muted peer-checked:text-brand">{{ $name }}</p>
                                </div>
                                <div class="absolute top-1 right-1 opacity-0 peer-checked:opacity-100">
                                    <div class="w-3 h-3 bg-brand rounded-full flex items-center justify-center">
                                        <svg class="w-2 h-2 text-white" fill="currentColor" viewBox="0 0 20 20"><path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/></svg>
                                    </div>
                                </div>
                            </label>
                            @endforeach
                        </div>

                        <div class="space-y-4 pt-4 border-t border-gray-100">
                            <h4 class="text-[10px] font-black text-brand-muted uppercase tracking-widest">Custom Colors</h4>
                            <div class="grid grid-cols-1 gap-4">
                                <div>
                                    <label class="block text-[9px] font-black text-gray-400 uppercase mb-2">Background Color</label>
                                    <div class="flex items-center gap-3 bg-surface p-2 rounded-lg border-2 border-transparent focus-within:border-accent">
                                        <input type="color" name="bg_color" x-model="activePreview.bg_color" class="w-8 h-8 rounded-md border-none cursor-pointer">
                                        <input type="text" x-model="activePreview.bg_color" class="bg-transparent border-none text-[10px] font-black w-full outline-none">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-[9px] font-black text-gray-400 uppercase mb-2">Title/Text Color</label>
                                    <div class="flex items-center gap-3 bg-surface p-2 rounded-lg border-2 border-transparent focus-within:border-accent">
                                        <input type="color" name="text_color" x-model="activePreview.text_color" class="w-8 h-8 rounded-md border-none cursor-pointer">
                                        <input type="text" x-model="activePreview.text_color" class="bg-transparent border-none text-[10px] font-black w-full outline-none">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-[9px] font-black text-gray-400 uppercase mb-2">Primary Button Color</label>
                                    <div class="flex items-center gap-3 bg-surface p-2 rounded-lg border-2 border-transparent focus-within:border-accent">
                                        <input type="color" name="button_color" x-model="activePreview.button_color" class="w-8 h-8 rounded-md border-none cursor-pointer">
                                        <input type="text" x-model="activePreview.button_color" class="bg-transparent border-none text-[10px] font-black w-full outline-none">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex gap-4 mt-8">
                    <button type="button" @click="closeAddModal()" class="flex-1 py-3 bg-gray-100 rounded-lg font-bold text-sm hover:bg-gray-200 transition-all">Cancel</button>
                    <button type="submit" class="flex-1 py-3 bg-brand text-white rounded-lg font-bold text-sm hover:bg-brand-hover transition-all shadow-lg">Create Slide</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ================================================================ --}}
    {{-- EDIT SLIDE MODAL --}}
    {{-- ================================================================ --}}
    <div x-show="showEditModal" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 z-50 flex items-center justify-center p-6" x-cloak>
        <div class="absolute inset-0 bg-brand/60 backdrop-blur-sm" @click="closeEditModal()"></div>
        <div class="relative bg-white rounded-lg p-8 w-full max-w-4xl shadow-2xl z-10 max-h-[90vh] overflow-y-auto">
            <h3 class="text-xl font-black text-brand mb-6">Edit Slide</h3>
            <form :action="'{{ route('orchestrator.onboarding.update', ['id' => 'ID']) }}'.replace('ID', editSlide ? editSlide.id : '')" method="POST" enctype="multipart/form-data">
                @csrf @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-5">
                        <div>
                            <label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-2">Slide Title</label>
                            <input type="text" name="title" x-model="activePreview.title" required class="w-full bg-surface border-2 border-transparent focus:border-accent focus:bg-white rounded-lg py-4 px-6 text-sm font-bold outline-none transition-all">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-2">Description</label>
                            <textarea name="description" rows="3" x-model="activePreview.description" class="w-full bg-surface border-2 border-transparent focus:border-accent focus:bg-white rounded-lg py-4 px-6 text-sm font-bold outline-none transition-all resize-none"></textarea>
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-2">Action Button Text</label>
                            <input type="text" name="button_text" x-model="activePreview.button_text" class="w-full bg-surface border-2 border-transparent focus:border-accent focus:bg-white rounded-lg py-4 px-6 text-sm font-bold outline-none transition-all">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-2">Replace Image/Video (Optional)</label>
                            <input type="file" name="image" accept="image/jpeg,image/png,image/webp,image/gif,video/mp4,video/webm,video/quicktime" 
                                   @change="handleImageChange($event)"
                                   class="w-full bg-surface rounded-lg py-3 px-4 text-sm font-bold file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-black file:bg-brand file:text-white file:cursor-pointer hover:file:bg-brand-hover">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-2">Button Strategy</label>
                            <div class="space-y-2">
                                @foreach(\App\Modules\Admin\Models\OnboardingSlide::BUTTON_TYPES as $type => $label_text)
                                <label class="flex items-center gap-3 p-3 bg-surface rounded-lg cursor-pointer hover:bg-gray-50 transition-all border-2 border-transparent focus-within:border-accent">
                                    <input type="radio" name="button_type" value="{{ $type }}" x-model="activePreview.button_type" class="w-4 h-4 text-brand focus:ring-accent border-gray-300">
                                    <span class="text-xs font-bold text-brand uppercase tracking-tight">{{ $label_text }}</span>
                                </label>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-2">Change Layout Style</label>
                        <div class="grid grid-cols-2 gap-3 mb-6">
                            @foreach(\App\Modules\Admin\Models\OnboardingSlide::LAYOUT_STYLES as $key => $name)
                            <label class="relative cursor-pointer group">
                                <input type="radio" name="layout_style" value="{{ $key }}" class="peer hidden" x-model="activePreview.layout_style">
                                <div class="p-3 border-2 border-gray-100 rounded-lg group-hover:bg-gray-50 peer-checked:border-brand peer-checked:bg-brand/5 transition-all">
                                    <p class="text-[10px] font-black leading-tight text-brand-muted peer-checked:text-brand">{{ $name }}</p>
                                </div>
                                <div class="absolute top-1 right-1 opacity-0 peer-checked:opacity-100">
                                    <div class="w-3 h-3 bg-brand rounded-full flex items-center justify-center">
                                        <svg class="w-2 h-2 text-white" fill="currentColor" viewBox="0 0 20 20"><path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/></svg>
                                    </div>
                                </div>
                            </label>
                            @endforeach
                        </div>

                        <div class="space-y-4 pt-4 border-t border-gray-100">
                            <h4 class="text-[10px] font-black text-brand-muted uppercase tracking-widest">Custom Colors</h4>
                            <div class="grid grid-cols-1 gap-4">
                                <div>
                                    <label class="block text-[9px] font-black text-gray-400 uppercase mb-2">Background Color</label>
                                    <div class="flex items-center gap-3 bg-surface p-2 rounded-lg border-2 border-transparent focus-within:border-accent">
                                        <input type="color" name="bg_color" x-model="activePreview.bg_color" class="w-8 h-8 rounded-md border-none cursor-pointer">
                                        <input type="text" x-model="activePreview.bg_color" class="bg-transparent border-none text-[10px] font-black w-full outline-none">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-[9px] font-black text-gray-400 uppercase mb-2">Title/Text Color</label>
                                    <div class="flex items-center gap-3 bg-surface p-2 rounded-lg border-2 border-transparent focus-within:border-accent">
                                        <input type="color" name="text_color" x-model="activePreview.text_color" class="w-8 h-8 rounded-md border-none cursor-pointer">
                                        <input type="text" x-model="activePreview.text_color" class="bg-transparent border-none text-[10px] font-black w-full outline-none">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-[9px] font-black text-gray-400 uppercase mb-2">Primary Button Color</label>
                                    <div class="flex items-center gap-3 bg-surface p-2 rounded-lg border-2 border-transparent focus-within:border-accent">
                                        <input type="color" name="button_color" x-model="activePreview.button_color" class="w-8 h-8 rounded-md border-none cursor-pointer">
                                        <input type="text" x-model="activePreview.button_color" class="bg-transparent border-none text-[10px] font-black w-full outline-none">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex gap-4 mt-8">
                    <button type="button" @click="closeEditModal()" class="flex-1 py-3 bg-gray-100 rounded-lg font-bold text-sm hover:bg-gray-200 transition-all">Cancel</button>
                    <button type="submit" class="flex-1 py-3 bg-brand text-white rounded-lg font-bold text-sm hover:bg-brand-hover transition-all shadow-lg">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ================================================================ --}}
    {{-- FULL SCREEN PREVIEW MODAL --}}
    {{-- ================================================================ --}}
    <div x-show="showViewModal" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" class="fixed inset-0 z-[100] flex items-center justify-center p-4 md:p-12" x-cloak>
        <div class="absolute inset-0 bg-brand/90 backdrop-blur-md" @click="closeViewModal()"></div>
        <div class="relative bg-white rounded-2xl overflow-hidden shadow-2xl h-[85vh] max-h-[750px] aspect-[9/19] z-10 border-[8px] md:border-[12px] border-gray-900 ring-4 ring-white/10 flex-shrink-0">
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
            <button @click="closeViewModal()" class="absolute top-4 right-4 bg-brand/50 text-white p-2 rounded-full hover:bg-brand transition-all z-30">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
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
