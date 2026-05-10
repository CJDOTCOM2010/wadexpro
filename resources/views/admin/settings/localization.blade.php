@extends('admin.layout')
@section('title', 'Localization Settings')
@section('content')

<div class="p-8 lg:p-12 max-w-5xl mx-auto">
    <!-- Header/Breadcrumb -->
    <div class="flex items-center justify-between mb-12">
        <div>
            <div class="flex items-center gap-2 text-[10px] font-black text-accent uppercase tracking-[0.2em] mb-2">
                <a href="{{ route('orchestrator.settings') }}" class="hover:text-brand transition-colors">Settings Hub</a>
                <span class="text-gray-300">/</span>
                <span>Localization</span>
            </div>
            <h2 class="text-3xl font-black text-brand tracking-tight">Regional Optimization</h2>
        </div>
        <a href="{{ route('orchestrator.settings') }}" class="bg-surface text-brand-muted hover:bg-gray-100 px-6 py-3 rounded-lg text-xs font-bold transition-all flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Back to Hub
        </a>
    </div>

    <div class="bg-white rounded-lg border border-gray-50 shadow-2xl p-20 text-center relative overflow-hidden">
        <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-transparent via-accent to-transparent opacity-20"></div>
        
        <div class="w-32 h-32 bg-brand/5 rounded-full flex items-center justify-center mx-auto mb-10 group cursor-help transition-all hover:bg-brand hover:scale-110 duration-500">
            <svg class="w-16 h-16 text-brand/20 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        
        <h3 class="text-2xl font-black text-brand mb-4">Localization Engine Initializing</h3>
        <p class="text-sm font-bold text-brand-muted max-w-sm mx-auto leading-relaxed">Dynamic regional parameters, currency syntax, and language nodes are managed via the Global expansion module.</p>
        
        <div class="mt-12 flex justify-center gap-4">
            <div class="px-6 py-3 bg-surface rounded-lg text-[10px] font-black text-brand-muted uppercase tracking-widest border border-gray-100">Regional Parity</div>
            <div class="px-6 py-3 bg-surface rounded-lg text-[10px] font-black text-brand-muted uppercase tracking-widest border border-gray-100">Currency Nodes</div>
            <div class="px-6 py-3 bg-surface rounded-lg text-[10px] font-black text-brand-muted uppercase tracking-widest border border-gray-100">Language Mesh</div>
        </div>
    </div>
</div>

@endsection
