@extends('admin.layout')
@section('title', 'Gallery Assets')
@section('content')

<div class="p-6 lg:p-8" x-data="assetManager()" x-init="init()">
    <!-- Header Section -->
    <div class="mb-8">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 text-xs font-bold text-brand-muted mb-2">
                    <a href="{{ route('orchestrator.settings') }}" class="hover:text-brand transition-colors">Settings</a>
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    <span class="text-accent">Gallery Assets</span>
                </div>
                <h2 class="text-3xl font-black text-brand tracking-tight">Gallery Assets</h2>
                <p class="text-brand-muted font-medium mt-1">Manage your media library, images, and file assets.</p>
            </div>
            <div class="flex items-center gap-3">
                <button @click="viewMode = viewMode === 'grid' ? 'list' : 'grid'" class="p-3 bg-white border border-gray-100 rounded-xl hover:border-brand/20 hover:shadow-md transition-all">
                    <svg x-show="viewMode === 'grid'" class="w-5 h-5 text-brand-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                    <svg x-show="viewMode === 'list'" class="w-5 h-5 text-brand-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                </button>
                <button @click="openUploadModal()" class="px-6 py-3 bg-brand text-white font-bold rounded-xl hover:bg-brand-light transition shadow-lg shadow-brand/20 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                    Upload Assets
                </button>
            </div>
        </div>
    </div>

    <!-- Alerts -->
    @if(session('error'))
    <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl flex items-center gap-3">
        <svg class="w-5 h-5 text-red-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        <p class="text-sm font-medium text-red-700">{{ session('error') }}</p>
    </div>
    @endif

    @if(session('success'))
    <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-xl flex items-center gap-3">
        <svg class="w-5 h-5 text-green-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <p class="text-sm font-medium text-green-700">{{ session('success') }}</p>
    </div>
    @endif

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-brand rounded-2xl p-6 text-white shadow-xl hover:shadow-2xl transition-all hover:-translate-y-1 border border-white/10 relative overflow-hidden">
            <div class="absolute top-0 right-0 w-32 h-32 bg-white/5 rounded-bl-[100px] -mr-8 -mt-8 transition-transform"></div>
            <div class="flex items-center justify-between mb-4 relative z-10">
                <div class="w-14 h-14 bg-white/10 rounded-2xl flex items-center justify-center backdrop-blur-md border border-white/20">
                    <svg class="w-7 h-7 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
                <span class="text-[11px] font-black bg-accent text-brand px-3 py-1.5 rounded-lg uppercase tracking-wider shadow-sm">Total</span>
            </div>
            <p class="text-4xl font-black text-white relative z-10">{{ $stats['total_files'] }}</p>
            <p class="text-sm font-bold text-white/70 mt-1 relative z-10">Files Stored</p>
        </div>

        <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-md hover:shadow-xl hover:border-brand/30 transition-all hover:-translate-y-1 group relative overflow-hidden">
            <div class="absolute top-0 right-0 w-32 h-32 bg-brand/5 rounded-bl-[100px] -mr-8 -mt-8 transition-transform group-hover:scale-110"></div>
            <div class="flex items-center justify-between mb-4 relative z-10">
                <div class="w-14 h-14 bg-brand/10 rounded-2xl flex items-center justify-center text-brand group-hover:bg-brand group-hover:text-accent transition-all duration-300">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <p class="text-4xl font-black text-brand relative z-10">{{ $stats['total_dirs'] }}</p>
            <p class="text-sm font-bold text-brand-muted mt-1 relative z-10">Folders</p>
        </div>

        <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-md hover:shadow-xl hover:border-accent/50 transition-all hover:-translate-y-1 group relative overflow-hidden">
            <div class="absolute top-0 right-0 w-32 h-32 bg-accent/10 rounded-bl-[100px] -mr-8 -mt-8 transition-transform group-hover:scale-110"></div>
            <div class="flex items-center justify-between mb-4 relative z-10">
                <div class="w-14 h-14 bg-accent/20 rounded-2xl flex items-center justify-center text-brand group-hover:bg-accent group-hover:text-brand transition-all duration-300">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/></svg>
                </div>
            </div>
            <p class="text-4xl font-black text-brand uppercase relative z-10">{{ $stats['storage_driver'] }}</p>
            <p class="text-sm font-bold text-brand-muted mt-1 relative z-10">Storage Driver</p>
        </div>

        <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-md hover:shadow-xl hover:border-brand/30 transition-all hover:-translate-y-1 group relative overflow-hidden">
            <div class="absolute top-0 right-0 w-32 h-32 bg-brand/5 rounded-bl-[100px] -mr-8 -mt-8 transition-transform group-hover:scale-110"></div>
            <div class="flex items-center justify-between mb-4 relative z-10">
                <div class="w-14 h-14 bg-brand/10 rounded-2xl flex items-center justify-center text-brand group-hover:bg-brand group-hover:text-white transition-all duration-300">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                </div>
            </div>
            <p class="text-4xl font-black text-brand relative z-10">{{ strtoupper($stats['disk']) }}</p>
            <p class="text-sm font-bold text-brand-muted mt-1 relative z-10">Active Disk</p>
        </div>
    </div>

    <!-- Main Content -->
    <div class="bg-white rounded-2xl border border-gray-200 shadow-xl overflow-hidden">
        <!-- Toolbar -->
        <div class="p-5 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-gray-50/80">
            <!-- Breadcrumb -->
            <div class="flex items-center gap-2 text-sm">
                <a href="{{ route('orchestrator.settings.assets') }}" class="font-black text-brand hover:text-accent transition-colors">ROOT</a>
                @if($directory)
                    @php $parts = explode('/', $directory); $currentPath = ''; @endphp
                    @foreach($parts as $part)
                        @php $currentPath .= ($currentPath ? '/' : '') . $part; @endphp
                        <svg class="w-4 h-4 text-brand-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        <a href="{{ route('orchestrator.settings.assets', ['disk' => $disk, 'path' => $currentPath]) }}" class="font-bold text-brand hover:text-accent transition-colors uppercase tracking-wide">{{ $part }}</a>
                    @endforeach
                @endif
                @if($directory)
                <button @click="goToParent()" class="ml-3 px-3 py-1 bg-white border border-gray-200 rounded-lg text-brand hover:text-accent hover:border-accent transition-colors flex items-center gap-1 shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    <span class="text-xs font-bold">UP</span>
                </button>
                @endif
            </div>

            <!-- Search & Actions -->
            <div class="flex items-center gap-3">
                <div class="relative">
                    <input type="text" x-model="searchQuery" @input="filterAssets()" placeholder="Search assets..." class="bg-white border border-gray-200 rounded-xl py-2.5 pl-10 pr-4 text-sm font-bold text-brand outline-none focus:border-brand focus:ring-2 focus:ring-brand/10 transition-all w-64 shadow-sm placeholder:text-gray-400 placeholder:font-medium">
                    <svg class="w-5 h-5 text-brand absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <select x-model="filterType" @change="filterAssets()" class="bg-white border border-gray-200 rounded-xl py-2.5 px-4 text-sm font-bold text-brand outline-none focus:border-brand focus:ring-2 focus:ring-brand/10 transition-all shadow-sm cursor-pointer">
                    <option value="all">ALL TYPES</option>
                    <option value="image">IMAGES</option>
                    <option value="video">VIDEOS</option>
                    <option value="document">DOCUMENTS</option>
                    <option value="audio">AUDIO</option>
                </select>
            </div>
        </div>

        <!-- Content Area -->
        <div class="p-6 min-h-[500px]">
            @if(empty($files) && empty($directories))
                <!-- Empty State -->
                <div class="flex flex-col items-center justify-center py-24 text-center">
                    <div class="w-28 h-28 bg-brand/5 rounded-full flex items-center justify-center mb-6">
                        <svg class="w-14 h-14 text-brand" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg>
                    </div>
                    <h3 class="text-2xl font-black text-brand mb-2">No Assets Found</h3>
                    <p class="text-brand-muted font-medium mb-8 max-w-sm mx-auto">This folder is currently empty. Upload your first media asset or document to get started.</p>
                    <button @click="openUploadModal()" class="px-8 py-3 bg-brand text-white font-bold rounded-xl hover:bg-accent hover:text-brand transition-all shadow-xl shadow-brand/20 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                        UPLOAD ASSET
                    </button>
                </div>
            @else
                <!-- Grid View -->
                <div x-show="viewMode === 'grid'" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-6">
                    <!-- Folders -->
                    @foreach($directories as $dir)
                    <div @click="navigateTo('{{ $dir['path'] }}')" class="group bg-white rounded-2xl p-5 border-2 border-gray-100 hover:border-brand hover:shadow-xl hover:-translate-y-1 transition-all duration-300 cursor-pointer text-center relative overflow-hidden">
                        <div class="absolute top-0 right-0 w-16 h-16 bg-brand/5 rounded-bl-full -mr-4 -mt-4 transition-transform group-hover:scale-110"></div>
                        <div class="aspect-square bg-surface rounded-xl flex items-center justify-center mb-4 group-hover:bg-brand/10 transition-colors">
                            <svg class="w-14 h-14 text-brand" fill="currentColor" viewBox="0 0 24 24"><path d="M10 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2h-8l-2-2z"/></svg>
                        </div>
                        <h5 class="text-sm font-black text-brand truncate group-hover:text-accent transition-colors">{{ $dir['name'] }}</h5>
                        <p class="text-[11px] font-bold text-brand-muted uppercase tracking-wider mt-1">Folder</p>
                    </div>
                    @endforeach

                    <!-- Files -->
                    @foreach($files as $file)
                    <div class="group bg-white rounded-2xl p-4 border-2 border-gray-100 hover:border-accent hover:shadow-xl hover:-translate-y-1 transition-all duration-300 relative flex flex-col" x-data="{ selected: false }">
                        <!-- Preview -->
                        <div class="aspect-square bg-surface rounded-xl flex items-center justify-center mb-3 overflow-hidden relative">
                            @if($file['is_image'])
                                <img src="{{ $file['url'] }}" alt="{{ $file['name'] }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                            @else
                                <div class="text-brand-muted">
                                    @if(in_array($file['extension'], ['pdf']))
                                        <svg class="w-12 h-12 text-red-400" fill="currentColor" viewBox="0 0 24 24"><path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/></svg>
                                    @elseif(in_array($file['extension'], ['doc','docx']))
                                        <svg class="w-12 h-12 text-blue-400" fill="currentColor" viewBox="0 0 24 24"><path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/></svg>
                                    @else
                                        <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                    @endif
                                </div>
                            @endif

                            <!-- Hover Overlay -->
                            <div class="absolute inset-0 bg-brand/90 opacity-0 group-hover:opacity-100 transition-opacity flex flex-col items-center justify-center gap-2 p-2 rounded-xl">
                                <button @click="previewAsset(@js($file))" class="w-full py-2 bg-white text-brand text-xs font-bold rounded-lg hover:bg-gray-100 transition-colors">Preview</button>
                                <button @click="copyUrl('{{ $file['url'] }}')" class="w-full py-2 bg-accent text-brand text-xs font-bold rounded-lg hover:bg-accent/80 transition-colors">Copy URL</button>
                                <a href="{{ $file['url'] }}" download="{{ $file['name'] }}" class="w-full py-2 bg-green-500 text-white text-xs font-bold rounded-lg hover:bg-green-600 transition-colors text-center">Download</a>
                                <button @click="confirmDelete(@js($file))" class="w-full py-2 bg-red-500 text-white text-xs font-bold rounded-lg hover:bg-red-600 transition-colors">Delete</button>
                            </div>

                            <!-- Image Badge -->
                            @if($file['is_image'])
                            <span class="absolute top-2 left-2 px-2 py-1 bg-black/50 text-white text-[8px] font-bold rounded-lg backdrop-blur-sm">IMG</span>
                            @endif
                        </div>

                        <!-- Info -->
                        <h5 class="text-xs font-bold text-brand truncate" title="{{ $file['name'] }}">{{ $file['name'] }}</h5>
                        <div class="flex items-center justify-between mt-2">
                            <span class="text-[9px] font-black text-brand-muted uppercase bg-surface px-2 py-0.5 rounded">{{ $file['extension'] }}</span>
                            <span class="text-[9px] font-bold text-brand-muted">{{ $file['size'] }}</span>
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- List View -->
                <div x-show="viewMode === 'list'" class="space-y-2">
                    <!-- Headers -->
                    <div class="grid grid-cols-12 gap-4 px-4 py-2 bg-surface rounded-lg text-[10px] font-black text-brand-muted uppercase">
                        <div class="col-span-1">Type</div>
                        <div class="col-span-4">Name</div>
                        <div class="col-span-2">Size</div>
                        <div class="col-span-2">Modified</div>
                        <div class="col-span-3 text-right">Actions</div>
                    </div>

                    <!-- Folders -->
                    @foreach($directories as $dir)
                    <div @click="navigateTo('{{ $dir['path'] }}')" class="grid grid-cols-12 gap-4 px-4 py-4 bg-white border border-gray-100 rounded-xl hover:border-brand hover:shadow-lg transition-all cursor-pointer items-center group relative overflow-hidden">
                        <div class="absolute left-0 top-0 bottom-0 w-1 bg-brand opacity-0 group-hover:opacity-100 transition-opacity"></div>
                        <div class="col-span-1">
                            <div class="w-12 h-12 bg-brand/5 rounded-xl flex items-center justify-center group-hover:bg-brand/10 transition-colors">
                                <svg class="w-6 h-6 text-brand" fill="currentColor" viewBox="0 0 24 24"><path d="M10 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2h-8l-2-2z"/></svg>
                            </div>
                        </div>
                        <div class="col-span-4">
                            <p class="text-sm font-black text-brand group-hover:text-accent transition-colors">{{ $dir['name'] }}</p>
                            <p class="text-[11px] font-bold text-brand-muted uppercase tracking-wider mt-0.5">Folder</p>
                        </div>
                        <div class="col-span-2">
                            <span class="text-xs font-bold text-brand-muted">—</span>
                        </div>
                        <div class="col-span-2">
                            <span class="text-xs font-bold text-brand-muted">—</span>
                        </div>
                        <div class="col-span-3 flex justify-end gap-2">
                            <button class="p-2.5 text-brand-muted hover:text-accent hover:bg-brand rounded-lg transition-colors shadow-sm border border-transparent hover:border-brand">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/></svg>
                            </button>
                        </div>
                    </div>
                    @endforeach

                    <!-- Files -->
                    @foreach($files as $file)
                    <div class="grid grid-cols-12 gap-4 px-4 py-4 bg-white rounded-xl hover:border-accent hover:shadow-lg transition-all items-center group border-2 border-gray-100 relative overflow-hidden">
                        <div class="absolute left-0 top-0 bottom-0 w-1 bg-accent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                        <div class="col-span-1">
                            @if($file['is_image'])
                                <img src="{{ $file['url'] }}" class="w-12 h-12 rounded-xl object-cover shadow-sm">
                            @else
                                <div class="w-12 h-12 bg-surface border border-gray-200 rounded-xl flex items-center justify-center">
                                    <svg class="w-6 h-6 text-brand-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                </div>
                            @endif
                        </div>
                        <div class="col-span-4">
                            <p class="text-sm font-black text-brand group-hover:text-accent transition-colors truncate">{{ $file['name'] }}</p>
                            <p class="text-[11px] font-bold text-brand-muted uppercase tracking-wider mt-0.5">{{ $file['extension'] }}</p>
                        </div>
                        <div class="col-span-2">
                            <span class="text-xs font-bold text-brand">{{ $file['size'] }}</span>
                        </div>
                        <div class="col-span-2">
                            <span class="text-xs font-bold text-brand-muted">{{ $file['last_modified'] }}</span>
                        </div>
                        <div class="col-span-3 flex justify-end gap-2">
                            <button @click="previewAsset(@js($file))" class="p-2.5 text-brand bg-surface hover:text-accent hover:bg-brand rounded-lg transition-colors" title="Preview">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </button>
                            <button @click="copyUrl('{{ $file['url'] }}')" class="p-2.5 text-brand bg-surface hover:text-accent hover:bg-brand rounded-lg transition-colors" title="Copy URL">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/></svg>
                            </button>
                            <a href="{{ $file['url'] }}" download="{{ $file['name'] }}" class="p-2.5 text-brand bg-surface hover:text-accent hover:bg-brand rounded-lg transition-colors" title="Download">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                            </a>
                            <button @click="confirmDelete(@js($file))" class="p-2.5 text-red-500 bg-red-50 hover:bg-red-500 hover:text-white rounded-lg transition-colors" title="Delete">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </div>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <!-- Upload Modal -->
    <div x-show="uploadModalOpen" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-brand/60 backdrop-blur-sm" @click="uploadModalOpen = false"></div>
        <div class="bg-white rounded-3xl w-full max-w-2xl relative z-10 shadow-2xl overflow-hidden animate-fade-in-up">
            <div class="p-6 border-b border-gray-50 flex items-center justify-between">
                <div>
                    <h3 class="text-xl font-black text-brand">Upload Assets</h3>
                    <p class="text-xs text-brand-muted font-medium mt-1">Upload to: <span class="text-accent font-bold">{{ $directory ?: 'Root' }}</span></p>
                </div>
                <button @click="uploadModalOpen = false" class="text-gray-300 hover:text-brand transition-colors p-2">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form action="{{ route('orchestrator.settings.assets.upload') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
                @csrf
                <input type="hidden" name="disk" value="{{ $disk }}">
                <input type="hidden" name="path" value="{{ $directory }}">

                <!-- Drop Zone -->
                <div class="border-4 border-dashed border-gray-100 rounded-2xl p-12 text-center group hover:border-accent hover:bg-accent/5 transition-all cursor-pointer relative">
                    <input type="file" name="files[]" multiple class="absolute inset-0 opacity-0 cursor-pointer" id="file-input" @change="handleFileSelect($event)">
                    <div class="pointer-events-none">
                        <div class="w-20 h-20 bg-accent/10 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform">
                            <svg class="w-10 h-10 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                        </div>
                        <p class="text-lg font-black text-brand mb-2">Drop files here or click to browse</p>
                        <p class="text-xs text-brand-muted font-bold uppercase tracking-widest">Max file size: 10MB per file</p>
                    </div>
                </div>

                <!-- File List -->
                <div x-show="selectedFiles.length > 0" class="space-y-3">
                    <div class="flex items-center justify-between">
                        <p class="text-sm font-bold text-brand">Selected Files (<span x-text="selectedFiles.length"></span>)</p>
                        <button type="button" @click="clearFiles()" class="text-xs font-bold text-red-500 hover:text-red-600">Clear All</button>
                    </div>
                    <div class="max-h-48 overflow-y-auto space-y-2 pr-2">
                        <template x-for="(file, index) in selectedFiles" :key="index">
                            <div class="flex items-center justify-between p-3 bg-surface rounded-xl">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center">
                                        <svg class="w-5 h-5 text-brand-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-brand truncate max-w-[200px]" x-text="file.name"></p>
                                        <p class="text-[10px] text-brand-muted" x-text="formatFileSize(file.size)"></p>
                                    </div>
                                </div>
                                <button type="button" @click="removeFile(index)" class="p-1 text-brand-muted hover:text-red-500 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>
                        </template>
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-4">
                    <button type="button" @click="uploadModalOpen = false" class="px-6 py-3 text-sm font-bold text-brand-muted hover:text-brand transition-colors uppercase">Cancel</button>
                    <button type="submit" :disabled="selectedFiles.length === 0" class="px-8 py-3 bg-brand text-white text-sm font-bold rounded-xl shadow-lg hover:shadow-brand/20 transition-all uppercase disabled:opacity-50 disabled:cursor-not-allowed">Upload <span x-text="selectedFiles.length"></span> Files</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Preview Modal -->
    <div x-show="previewModalOpen" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-brand/80 backdrop-blur-sm" @click="previewModalOpen = false"></div>
        <div class="bg-white rounded-3xl w-full max-w-4xl max-h-[90vh] relative z-10 shadow-2xl overflow-hidden flex flex-col">
            <div class="p-6 border-b border-gray-50 flex items-center justify-between shrink-0">
                <div>
                    <h3 class="text-xl font-black text-brand" x-text="previewingAsset?.name"></h3>
                    <p class="text-xs text-brand-muted font-medium mt-1">
                        <span x-text="previewingAsset?.extension?.toUpperCase()"></span> • <span x-text="previewingAsset?.size"></span>
                    </p>
                </div>
                <button @click="previewModalOpen = false" class="text-gray-300 hover:text-brand transition-colors p-2">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="flex-1 p-6 bg-surface/50 flex items-center justify-center overflow-hidden">
                <template x-if="previewingAsset?.is_image">
                    <img :src="previewingAsset?.url" class="max-w-full max-h-[60vh] object-contain rounded-xl shadow-lg">
                </template>
                <template x-if="!previewingAsset?.is_image">
                    <div class="text-center">
                        <div class="w-24 h-24 bg-white rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-12 h-12 text-brand-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                        </div>
                        <p class="text-brand-muted font-medium">Preview not available for this file type</p>
                    </div>
                </template>
            </div>
            <div class="p-6 border-t border-gray-50 flex justify-between shrink-0">
                <div class="flex gap-2">
                    <button @click="copyUrl(previewingAsset?.url)" class="px-4 py-2 bg-surface text-brand text-sm font-bold rounded-xl hover:bg-gray-100 transition-colors flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        Copy URL
                    </button>
                </div>
                <div class="flex gap-2">
                    <a :href="previewingAsset?.url" :download="previewingAsset?.name" class="px-4 py-2 bg-green-500 text-white text-sm font-bold rounded-xl hover:bg-green-600 transition-colors flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        Download
                    </a>
                    <button @click="previewModalOpen = false; confirmDelete(previewingAsset)" class="px-4 py-2 bg-red-500 text-white text-sm font-bold rounded-xl hover:bg-red-600 transition-colors flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        Delete
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div x-show="deleteModalOpen" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-brand/60 backdrop-blur-sm" @click="deleteModalOpen = false"></div>
        <div class="bg-white rounded-3xl w-full max-w-md relative z-10 shadow-2xl overflow-hidden animate-fade-in-up p-8 text-center">
            <div class="w-20 h-20 bg-red-50 text-red-600 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            </div>
            <h3 class="text-xl font-black text-brand mb-2">Delete Asset?</h3>
            <p class="text-sm text-brand-muted font-medium mb-2">This action is <span class="text-red-500 font-bold">irreversible</span>.</p>
            <p class="text-sm font-bold text-brand bg-surface rounded-lg py-2 px-4 mb-8" x-text="deletingAsset?.name"></p>
            
            <form action="{{ route('orchestrator.settings.assets.delete') }}" method="POST">
                @csrf
                <input type="hidden" name="disk" value="{{ $disk }}">
                <input type="hidden" name="path" x-model="deletingAsset?.path">
                
                <div class="flex items-center gap-3">
                    <button type="button" @click="deleteModalOpen = false" class="flex-1 py-4 bg-surface text-brand-muted text-sm font-bold rounded-xl hover:bg-gray-100 transition-all uppercase">Cancel</button>
                    <button type="submit" class="flex-1 py-4 bg-red-500 text-white text-sm font-bold rounded-xl shadow-lg shadow-red-200 hover:bg-red-600 transition-all uppercase">Confirm Delete</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Toast Notification -->
    <div x-show="toast.show" x-cloak class="fixed bottom-6 right-6 z-[100] bg-brand text-white px-6 py-4 rounded-2xl shadow-2xl flex items-center gap-3 animate-fade-in-up">
        <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        <span class="text-sm font-bold" x-text="toast.message"></span>
    </div>
</div>

<script>
function assetManager() {
    return {
        viewMode: 'grid',
        searchQuery: '',
        filterType: 'all',
        uploadModalOpen: false,
        previewModalOpen: false,
        deleteModalOpen: false,
        selectedFiles: [],
        previewingAsset: null,
        deletingAsset: null,
        currentPath: '{{ $directory }}',
        currentDisk: '{{ $disk }}',
        toast: { show: false, message: '' },

        init() {
            // Initialize
        },

        openUploadModal() {
            this.uploadModalOpen = true;
            this.selectedFiles = [];
        },

        handleFileSelect(event) {
            const files = Array.from(event.target.files);
            this.selectedFiles = [...this.selectedFiles, ...files];
        },

        removeFile(index) {
            this.selectedFiles.splice(index, 1);
        },

        clearFiles() {
            this.selectedFiles = [];
            document.getElementById('file-input').value = '';
        },

        formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        },

        navigateTo(path) {
            window.location.href = "{{ route('orchestrator.settings.assets') }}?disk={{ $disk }}&path=" + encodeURIComponent(path);
        },

        goToParent() {
            const parts = this.currentPath.split('/').filter(p => p);
            parts.pop();
            const newPath = parts.join('/');
            window.location.href = "{{ route('orchestrator.settings.assets') }}?disk={{ $disk }}&path=" + encodeURIComponent(newPath);
        },

        previewAsset(file) {
            this.previewingAsset = file;
            this.previewModalOpen = true;
        },

        copyUrl(url) {
            navigator.clipboard.writeText(url).then(() => {
                this.showToast('URL copied to clipboard!');
            });
        },

        confirmDelete(file) {
            this.deletingAsset = file;
            this.deleteModalOpen = true;
        },

        filterAssets() {
            // Filtering handled by view
        },

        showToast(message) {
            this.toast.message = message;
            this.toast.show = true;
            setTimeout(() => {
                this.toast.show = false;
            }, 3000);
        }
    }
}
</script>

<style>
@keyframes fade-in-up {
    0% { opacity: 0; transform: translateY(20px); }
    100% { opacity: 1; transform: translateY(0); }
}
.animate-fade-in-up {
    animation: fade-in-up 0.3s ease-out forwards;
}
[x-cloak] { display: none !important; }
</style>

@endsection