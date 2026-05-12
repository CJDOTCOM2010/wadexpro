@extends('admin.layout')
@section('title', 'Blog Manager')
@section('content')

<div class="mb-8 flex items-center justify-between">
    <div>
        <h2 class="text-2xl font-black text-brand tracking-tight">Blog & News</h2>
        <p class="text-brand-muted font-medium mt-1">Publish articles, press releases, and company updates.</p>
    </div>
    <div class="flex gap-4">
        <button class="px-6 py-3 bg-brand text-white font-bold rounded-lg hover:bg-brand-light transition flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Write Post
        </button>
    </div>
</div>

<div class="bg-white rounded-lg border border-gray-100 shadow-sm overflow-hidden">
    <div class="p-6 border-b border-gray-50 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="relative w-full md:w-96">
            <input type="text" placeholder="Search articles..." class="w-full bg-surface border border-gray-100 rounded-lg pl-10 pr-4 py-2.5 text-sm font-medium outline-none focus:ring-2 focus:ring-brand/20 transition-all">
            <svg class="w-4 h-4 text-gray-400 absolute left-4 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
        </div>
        <div class="flex gap-2">
            <select class="bg-surface border border-gray-100 rounded-lg px-4 py-2 text-sm font-bold text-brand outline-none cursor-pointer">
                <option>All Statuses</option>
                <option>Published</option>
                <option>Draft</option>
            </select>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-surface/30 border-b border-gray-50">
                    <th class="px-6 py-4 text-[10px] font-black text-brand-muted uppercase tracking-widest whitespace-nowrap">Article</th>
                    <th class="px-6 py-4 text-[10px] font-black text-brand-muted uppercase tracking-widest whitespace-nowrap">Category</th>
                    <th class="px-6 py-4 text-[10px] font-black text-brand-muted uppercase tracking-widest whitespace-nowrap">Status</th>
                    <th class="px-6 py-4 text-[10px] font-black text-brand-muted uppercase tracking-widest whitespace-nowrap">Views</th>
                    <th class="px-6 py-4 text-[10px] font-black text-brand-muted uppercase tracking-widest text-right whitespace-nowrap">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                <tr class="hover:bg-surface/20 transition-colors group">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-4">
                            <div class="w-16 h-12 bg-surface rounded overflow-hidden border border-gray-100 shrink-0">
                                <img src="https://via.placeholder.com/150" class="w-full h-full object-cover">
                            </div>
                            <div>
                                <p class="text-sm font-bold text-brand">WADEXPRO Expands to Kumasi</p>
                                <p class="text-[10px] text-brand-muted mt-0.5">Published May 10, 2026</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 bg-surface border border-gray-200 text-brand text-[10px] font-bold rounded">Company News</span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-[10px] font-black uppercase tracking-widest text-green-600">Published</span>
                    </td>
                    <td class="px-6 py-4 text-sm font-bold text-brand">
                        1,245
                    </td>
                    <td class="px-6 py-4 text-right">
                        <button class="text-accent font-bold text-xs hover:text-accent-light transition">Edit</button>
                    </td>
                </tr>
                <tr class="hover:bg-surface/20 transition-colors group">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-4">
                            <div class="w-16 h-12 bg-surface rounded overflow-hidden border border-gray-100 shrink-0 flex items-center justify-center text-gray-300">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-brand">Safety Tips for Night Riders</p>
                                <p class="text-[10px] text-brand-muted mt-0.5">Last edited 2 hrs ago</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 bg-surface border border-gray-200 text-brand text-[10px] font-bold rounded">Safety</span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-[10px] font-black uppercase tracking-widest text-amber-600">Draft</span>
                    </td>
                    <td class="px-6 py-4 text-sm font-bold text-gray-400">
                        —
                    </td>
                    <td class="px-6 py-4 text-right">
                        <button class="text-accent font-bold text-xs hover:text-accent-light transition">Edit</button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

@endsection
