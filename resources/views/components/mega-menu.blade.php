{{-- ============================================================
     DYNAMIC MEGA MENU COMPONENT
     Renders from database via Menu model. Zero hardcoded content.
     ============================================================ --}}

@php
    use App\Modules\CMS\Models\Menu;
    use Illuminate\Support\Facades\Cache;

    $menuSlug = $slug ?? 'main-nav';
    $menuData = Cache::remember("menu_data.{$menuSlug}", 3600, function () use ($menuSlug) {
        $menu = Menu::active()->where('slug', $menuSlug)->first();
        return $menu ? [
            'tree' => $menu->getTree(),
            'alignment' => $menu->alignment ?? 'center'
        ] : null;
    });

    $menuTree = $menuData['tree'] ?? [];
    $alignment = $menuData['alignment'] ?? 'center';

    $alignmentClasses = [
        'left' => 'justify-start',
        'center' => 'justify-center',
        'right' => 'justify-end'
    ];

    // Mode handling
    $mode = $mode ?? 'desktop'; // Default to desktop for backward compatibility
    
    // Theme Classes
    $textTheme = ($theme ?? 'dark') === 'dark' ? 'text-white/80 hover:text-white hover:bg-white/10' : 'text-brand/70 hover:text-brand hover:bg-surface';
    $activeTheme = ($theme ?? 'dark') === 'dark' ? 'text-white bg-white/10' : 'text-brand bg-surface';

    // SVG icon map for common icon names
    $icons = [
        'car'            => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>',
        'location'       => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>',
        'clock'          => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>',
        'plane'          => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>',
        'steering-wheel' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>',
        'money'          => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>',
        'checklist'      => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>',
        'shield'         => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>',
        'package'        => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>',
        'truck'          => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/>',
        'building'       => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>',
        'briefcase'      => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>',
        'code'           => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/>',
        'info'           => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>',
        'heart'          => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>',
        'users'          => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>',
        'document'       => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>',
    ];
@endphp

@if(count($menuTree) > 0)
    @if($mode === 'desktop')
        {{-- Desktop Navigation --}}
        <div class="hidden lg:flex items-center gap-1 {{ $alignmentClasses[$alignment] }} flex-1">
            @foreach($menuTree as $item)
                @if($item['type'] === 'group' && !empty($item['children']))
                {{-- MEGA MENU DROPDOWN --}}
                <div class="relative group/mega" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false">
                    <button class="px-4 py-2 text-[15px] font-medium transition-colors flex items-center gap-1.5 rounded-full {{ $textTheme }}"
                            :class="{ '{{ $activeTheme }}': open }">
                        {{ $item['label'] }}
                        <svg class="w-3.5 h-3.5 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    {{-- Dropdown Panel --}}
                    @php
                        $layout = $item['layout'] ?? 'standard';
                        $panelWidth = match($layout) {
                            'extended_grid' => 'w-[90vw] max-w-[1200px]',
                            'split_promo'   => 'w-[80vw] max-w-[1000px]',
                            'left_showcase' => 'w-[80vw] max-w-[1000px]',
                            'icon_grid'     => 'min-w-[500px]',
                            default        => 'min-w-[340px]'
                        };
                    @endphp
                    <div class="absolute top-100 left-1/2 -translate-x-1/2 mt-2 opacity-0 invisible group-hover/mega:opacity-100 group-hover/mega:visible transition-all duration-200 z-50 {{ $panelWidth }}"
                         x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
                        
                        {{-- Standard Radii applied to ALL layouts --}}
                        <div class="bg-white rounded-lg shadow-2xl shadow-black/10 border border-gray-100 overflow-hidden">
                            
                            @if($layout === 'extended_grid')
                                {{-- ========================================== --}}
                                {{-- EXTENDED E-COMMERCE GRID LAYOUT            --}}
                                {{-- ========================================== --}}
                                <div class="flex w-full min-h-[400px]">
                                    {{-- Left Hero Banner --}}
                                    @if($item['image_url'])
                                        <div class="w-1/3 relative bg-cover bg-center shrink-0 p-10 flex flex-col justify-end" style="background-image: url('{{ $item['image_url'] }}');">
                                            <div class="absolute inset-0 bg-gradient-to-t from-brand/90 to-transparent"></div>
                                            <div class="relative z-10 text-white">
                                                @if($item['description'])
                                                    <h3 class="text-3xl font-black leading-tight">{!! nl2br(e($item['description'])) !!}</h3>
                                                @endif
                                                @if($item['url'])
                                                    <a href="{{ $item['url'] }}" class="inline-block mt-6 px-8 py-3 bg-white text-brand text-xs font-bold uppercase tracking-wider rounded-lg hover:bg-surface transition-colors">Learn More</a>
                                                @endif
                                            </div>
                                        </div>
                                    @endif
                                    
                                    {{-- Right Columns Grid --}}
                                    <div class="flex-1 p-10 grid grid-cols-3 gap-8">
                                        @foreach($item['children'] as $column)
                                            <div class="flex flex-col space-y-4">
                                                <h4 class="text-[11px] font-bold uppercase tracking-widest text-brand">{{ $column['group_label'] ?: $column['label'] }}</h4>
                                                <div class="space-y-4">
                                                    @if(!empty($column['children']))
                                                        @foreach($column['children'] as $child)
                                                            <a href="{{ $child['url'] }}" class="flex gap-4 group/rich hover:bg-gray-50 -ml-2 p-2 rounded-lg transition-colors">
                                                                @if($child['image_url'])
                                                                    <img src="{{ $child['image_url'] }}" alt="" class="w-14 h-14 object-cover rounded-lg shrink-0">
                                                                @endif
                                                                <div class="min-w-0 flex-1">
                                                                    <p class="text-sm font-semibold text-brand truncate">{{ $child['label'] }}</p>
                                                                    @if(!empty($child['description']))
                                                                        <p class="text-[10px] text-brand-muted mt-0.5 line-clamp-2">{{ $child['description'] }}</p>
                                                                    @endif
                                                                </div>
                                                            </a>
                                                        @endforeach
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                             @elseif($layout === 'left_showcase')
                                {{-- ========================================== --}}
                                {{-- LEFT SHOWCASE LAYOUT (NEW for Business)  --}}
                                {{-- ========================================== --}}
                                <div class="flex w-full min-h-[360px]">
                                    <div class="w-2/5 relative bg-brand overflow-hidden group/showcase">
                                        @if($item['image_url'])
                                            <img src="{{ $item['image_url'] }}" alt="" class="absolute inset-0 w-full h-full object-cover opacity-60 group-hover/showcase:scale-110 transition-transform duration-700">
                                        @endif
                                        <div class="absolute inset-0 bg-gradient-to-r from-brand to-transparent"></div>
                                        <div class="relative z-10 p-10 h-full flex flex-col justify-center">
                                            <div class="w-12 h-12 bg-accent rounded-lg flex items-center justify-center mb-6 text-brand">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">{!! $icons[$item['icon'] ?? 'briefcase'] !!}</svg>
                                            </div>
                                            <h3 class="text-3xl font-black text-white leading-tight mb-2">{{ $item['label'] }}</h3>
                                            @if($item['description'])
                                                <p class="text-white/70 text-sm leading-relaxed max-w-xs mb-6">{{ $item['description'] }}</p>
                                            @endif
                                            @if($item['url'])
                                                <a href="{{ $item['url'] }}" class="inline-flex items-center gap-2 text-accent font-bold text-xs uppercase tracking-widest hover:gap-4 transition-all">
                                                    Get Started <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="flex-1 p-10 bg-white grid grid-cols-2 gap-x-8 gap-y-4">
                                        @foreach($item['children'] as $child)
                                            <a href="{{ $child['url'] }}" class="flex items-start gap-4 p-4 rounded-lg hover:bg-surface transition-all group/item shadow-sm hover:shadow-md border border-gray-50">
                                                <div class="w-10 h-10 rounded-lg bg-gray-50 flex items-center justify-center shrink-0 group-hover/item:bg-brand group-hover/item:text-white transition-colors">
                                                    <svg class="w-5 h-5 text-brand group-hover/item:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">{!! $icons[$child['icon'] ?? 'document'] !!}</svg>
                                                </div>
                                                <div class="min-w-0 flex-1">
                                                    <p class="text-sm font-bold text-brand group-hover/item:translate-x-1 transition-transform">{{ $child['label'] }}</p>
                                                    @if($child['description'])
                                                        <p class="text-[11px] text-brand-muted mt-0.5 line-clamp-2 leading-relaxed">{{ $child['description'] }}</p>
                                                    @endif
                                                </div>
                                            </a>
                                        @endforeach
                                    </div>
                                </div>

                            @elseif($layout === 'split_promo')
                                {{-- ========================================== --}}
                                {{-- SPLIT PROMO LAYOUT (NEW)                  --}}
                                {{-- ========================================== --}}
                                <div class="flex w-full">
                                    <div class="flex-1 p-8 grid grid-cols-2 gap-x-8 gap-y-4">
                                        @foreach($item['children'] as $child)
                                            <a href="{{ $child['url'] }}" class="flex items-center gap-4 p-3 rounded-lg hover:bg-surface transition-colors border border-transparent hover:border-gray-100">
                                                <div class="w-10 h-10 bg-brand/5 rounded-lg flex items-center justify-center text-brand">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">{!! $icons[$child['icon'] ?? 'info'] !!}</svg>
                                                </div>
                                                <div>
                                                    <p class="text-sm font-bold text-brand">{{ $child['label'] }}</p>
                                                    <p class="text-xs text-brand-muted line-clamp-1">{{ $child['description'] }}</p>
                                                </div>
                                            </a>
                                        @endforeach
                                    </div>
                                    <div class="w-[320px] bg-brand p-8 text-white flex flex-col justify-between">
                                        @if($item['image_url'])
                                            <img src="{{ $item['image_url'] }}" alt="" class="w-full h-32 object-cover rounded-lg mb-6 shadow-lg border border-white/10">
                                        @endif
                                        <div>
                                            <h4 class="font-black text-xl mb-2">{{ $item['label'] }} Highlights</h4>
                                            <p class="text-sm text-white/70 mb-6">{{ $item['description'] ?? 'Discover our latest platform features and modules optimized for your growth.' }}</p>
                                            <a href="{{ $item['url'] ?? '#' }}" class="inline-flex items-center gap-2 text-xs font-bold uppercase tracking-widest text-white hover:translate-x-1 transition-transform">
                                                Explore All <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                                            </a>
                                        </div>
                                    </div>
                                </div>

                            @elseif($layout === 'icon_grid')
                                {{-- ========================================== --}}
                                {{-- ICON GRID LAYOUT (NEW)                    --}}
                                {{-- ========================================== --}}
                                <div class="p-4 grid grid-cols-2 gap-2">
                                    @foreach($item['children'] as $child)
                                        <a href="{{ $child['url'] }}" class="flex items-center gap-4 p-4 rounded-lg hover:bg-brand hover:text-white transition-all group/iconitem">
                                            <div class="w-12 h-12 bg-gray-50 rounded-lg flex items-center justify-center text-brand group-hover/iconitem:bg-white/20 group-hover/iconitem:text-white transition-colors">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">{!! $icons[$child['icon'] ?? 'info'] !!}</svg>
                                            </div>
                                            <div>
                                                <p class="text-[15px] font-bold">{{ $child['label'] }}</p>
                                                <p class="text-xs opacity-60 line-clamp-1">{{ $child['description'] }}</p>
                                            </div>
                                        </a>
                                    @endforeach
                                </div>

                            @else
                                {{-- ========================================== --}}
                                {{-- STANDARD POPOVER LAYOUT                    --}}
                                {{-- ========================================== --}}
                                <div class="p-6">
                                    @if($item['group_label'] ?? false)
                                        <p class="text-[10px] font-bold uppercase tracking-widest text-brand-muted mb-4 px-1">{{ $item['group_label'] }}</p>
                                    @endif
                                    <div class="space-y-1">
                                        @foreach($item['children'] as $child)
                                            @if($child['type'] === 'divider')
                                                <hr class="my-3 border-gray-100">
                                            @elseif($child['type'] === 'cta_button')
                                                <div class="pt-3 mt-2 border-t border-gray-100">
                                                    <a href="{{ $child['url'] }}" class="block w-full text-center px-5 py-3 bg-brand text-white text-sm font-bold rounded-lg hover:bg-brand-light transition-colors">
                                                        {{ $child['label'] }}
                                                    </a>
                                                </div>
                                            @else
                                                <a href="{{ $child['url'] }}" class="flex items-start gap-4 p-3 rounded-lg hover:bg-surface transition-colors group/item">
                                                    @if(!empty($child['icon']) && isset($icons[$child['icon']]))
                                                        <div class="w-10 h-10 bg-surface rounded-lg flex items-center justify-center shrink-0 group-hover/item:bg-white transition-colors">
                                                            <svg class="w-5 h-5 text-brand" fill="none" stroke="currentColor" viewBox="0 0 24 24">{!! $icons[$child['icon']] !!}</svg>
                                                        </div>
                                                    @endif
                                                    <div class="min-w-0 flex-1">
                                                        <div class="flex items-center gap-2">
                                                            <p class="text-sm font-semibold text-brand">{{ $child['label'] }}</p>
                                                            @if(!empty($child['badge_text']))
                                                                <span class="text-[9px] font-black uppercase text-white px-1.5 py-0.5 rounded {{ $child['badge_color'] ?? 'bg-accent' }}">{{ $child['badge_text'] }}</span>
                                                            @endif
                                                        </div>
                                                        @if(!empty($child['description']))
                                                            <p class="text-xs text-brand-muted mt-0.5 leading-relaxed">{{ $child['description'] }}</p>
                                                        @endif
                                                    </div>
                                                    @if(!empty($child['image_url']))
                                                        <img src="{{ $child['image_url'] }}" alt="" class="w-14 h-14 rounded-lg object-cover shrink-0">
                                                    @endif
                                                </a>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @else
                {{-- SIMPLE LINK --}}
                <a href="{{ $item['url'] }}" class="px-4 py-2 text-[15px] font-medium transition-colors rounded-full {{ $textTheme }}">
                    {{ $item['label'] }}
                </a>
            @endif
        @endforeach
    </div>
    @else
        {{-- Mobile Navigation (Accordion) --}}
        <div class="space-y-1" id="mobileMegaMenu">
            @foreach($menuTree as $item)
                @if($item['type'] === 'group' && !empty($item['children']))
                    <div x-data="{ expanded: false }" class="border-b border-gray-50 last:border-0">
                        <button @click="expanded = !expanded" 
                                class="flex items-center justify-between w-full px-4 py-4 text-[17px] font-bold rounded-xl hover:bg-surface transition-all active:scale-[0.98]">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-lg bg-surface flex items-center justify-center text-brand">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">{!! $icons[$item['icon'] ?? 'info'] !!}</svg>
                                </div>
                                <span>{{ $item['label'] }}</span>
                            </div>
                            <svg class="w-4 h-4 transition-transform duration-300" :class="{ 'rotate-180': expanded }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        
                        <div x-show="expanded" x-collapse x-cloak class="bg-surface/30 rounded-xl mb-2 mx-1 overflow-hidden">
                            <div class="p-2 space-y-1">
                                @foreach($item['children'] as $child)
                                    @if($child['type'] === 'cta_button')
                                        <a href="{{ $child['url'] }}" class="block w-full text-center px-5 py-3.5 bg-brand text-white text-sm font-black rounded-lg mt-2 mb-1 shadow-lg shadow-brand/10">
                                            {{ $child['label'] }}
                                        </a>
                                    @elseif($child['type'] !== 'divider')
                                        <a href="{{ $child['url'] }}" class="flex items-center gap-4 px-4 py-3.5 rounded-lg hover:bg-white transition-all group">
                                            @if($child['icon'] && isset($icons[$child['icon']]))
                                                <div class="w-9 h-9 rounded-lg bg-white flex items-center justify-center shadow-sm group-hover:bg-brand group-hover:text-white transition-colors">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">{!! $icons[$child['icon']] !!}</svg>
                                                </div>
                                            @endif
                                            <div>
                                                <p class="text-[15px] font-bold text-brand">{{ $child['label'] }}</p>
                                                @if($child['description'])
                                                    <p class="text-[11px] text-brand-muted line-clamp-1 leading-none mt-1">{{ $child['description'] }}</p>
                                                @endif
                                            </div>
                                        </a>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>
                @else
                    <a href="{{ $item['url'] }}" class="flex items-center gap-4 px-4 py-4 text-[17px] font-bold rounded-xl hover:bg-surface transition-all active:scale-[0.98]">
                        <div class="w-10 h-10 rounded-lg bg-surface flex items-center justify-center text-brand">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">{!! $icons[$item['icon'] ?? 'info'] !!}</svg>
                        </div>
                        <span>{{ $item['label'] }}</span>
                    </a>
                @endif
            @endforeach
        </div>
    @endif
@endif
