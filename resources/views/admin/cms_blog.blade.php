@extends('admin.layout')
@section('title', 'Blog Manager')
@section('content')

<div class="mb-8 flex items-center justify-between">
    <div>
        <h2 class="text-2xl font-black text-brand tracking-tight">Blog & News</h2>
        <p class="text-brand-muted font-medium mt-1">Publish articles, press releases, and company updates.</p>
    </div>
    <div class="flex gap-4">
        <button onclick="document.getElementById('add-modal').classList.remove('hidden')" class="px-6 py-3 bg-brand text-white font-bold rounded-lg hover:bg-brand-light transition flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Write Post
        </button>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
    <div class="bg-white p-4 rounded-lg border border-gray-100 shadow-sm flex items-center gap-4">
        <div class="w-12 h-12 rounded-full bg-green-50 text-green-600 flex items-center justify-center shrink-0">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div>
            <p class="text-[10px] font-black text-brand-muted uppercase tracking-widest">Published</p>
            <p class="text-xl font-bold text-brand mt-0.5">{{ number_format($stats['published']) }}</p>
        </div>
    </div>
    <div class="bg-white p-4 rounded-lg border border-gray-100 shadow-sm flex items-center gap-4">
        <div class="w-12 h-12 rounded-full bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
        </div>
        <div>
            <p class="text-[10px] font-black text-brand-muted uppercase tracking-widest">Drafts</p>
            <p class="text-xl font-bold text-brand mt-0.5">{{ number_format($stats['draft']) }}</p>
        </div>
    </div>
    <div class="bg-white p-4 rounded-lg border border-gray-100 shadow-sm flex items-center gap-4">
        <div class="w-12 h-12 rounded-full bg-brand/5 text-brand flex items-center justify-center shrink-0">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
        </div>
        <div>
            <p class="text-[10px] font-black text-brand-muted uppercase tracking-widest">Total Views</p>
            <p class="text-xl font-bold text-brand mt-0.5">{{ number_format($stats['total_views']) }}</p>
        </div>
    </div>
</div>

<div class="bg-white rounded-lg border border-gray-100 shadow-sm overflow-hidden">
    <div class="p-6 border-b border-gray-50 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <form action="{{ route('orchestrator.cms.blog') }}" method="GET" class="relative w-full md:w-96 flex">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search articles..." class="w-full bg-surface border border-gray-100 rounded-l-lg pl-10 pr-4 py-2.5 text-sm font-medium outline-none focus:ring-2 focus:ring-brand/20 transition-all">
            <svg class="w-4 h-4 text-gray-400 absolute left-4 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <button type="submit" class="bg-brand text-white px-4 rounded-r-lg font-bold text-sm">Search</button>
        </form>
        <form action="{{ route('orchestrator.cms.blog') }}" method="GET">
            <select name="status" onchange="this.form.submit()" class="bg-surface border border-gray-100 rounded-lg px-4 py-2.5 text-sm font-bold text-brand outline-none cursor-pointer">
                <option value="">All Statuses</option>
                <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>Published</option>
                <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
            </select>
            @if(request('search')) <input type="hidden" name="search" value="{{ request('search') }}"> @endif
        </form>
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
                @forelse($posts as $post)
                <tr class="hover:bg-surface/20 transition-colors group">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-4">
                            <div class="w-16 h-12 bg-surface rounded overflow-hidden border border-gray-100 shrink-0 flex items-center justify-center text-gray-300">
                                @if($post->cover_image_url)
                                    <img src="{{ asset('storage/'.$post->cover_image_url) }}" class="w-full h-full object-cover">
                                @else
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                @endif
                            </div>
                            <div>
                                <p class="text-sm font-bold text-brand">{{ $post->title }}</p>
                                <p class="text-[10px] text-brand-muted mt-0.5">
                                    {{ $post->status == 'published' ? 'Published ' . $post->published_at->format('M d, Y') : 'Last edited ' . $post->updated_at->diffForHumans() }}
                                </p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 bg-surface border border-gray-200 text-brand text-[10px] font-bold rounded">{{ $post->category ?? 'General' }}</span>
                    </td>
                    <td class="px-6 py-4">
                        @if($post->status == 'published')
                            <span class="text-[10px] font-black uppercase tracking-widest text-green-600 bg-green-50 px-2 py-1 rounded">Published</span>
                        @else
                            <span class="text-[10px] font-black uppercase tracking-widest text-amber-600 bg-amber-50 px-2 py-1 rounded">Draft</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm font-bold {{ $post->view_count > 0 ? 'text-brand' : 'text-gray-400' }}">
                        {{ number_format($post->view_count) }}
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-3">
                            <button class="text-accent font-bold text-xs hover:text-accent-light transition">Edit</button>
                            <form action="{{ route('orchestrator.cms.blog.destroy', $post->id) }}" method="POST" class="inline" onsubmit="return confirm('Delete this post permanently?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-500 font-bold text-xs hover:text-red-700 transition">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                        <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
                        <p class="font-medium">No blog posts found.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div class="p-4 border-t border-gray-50">
        {{ $posts->links() }}
    </div>
</div>

<!-- Add Modal -->
<div id="add-modal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-xl max-w-3xl w-full p-6 max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-lg font-black text-brand">Write New Post</h3>
            <button onclick="document.getElementById('add-modal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
        </div>
        <form action="{{ route('orchestrator.cms.blog.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="space-y-4 mb-6">
                <div>
                    <label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-1">Title</label>
                    <input type="text" name="title" required class="w-full bg-surface border border-gray-200 rounded p-2 text-sm focus:ring-2 focus:ring-brand/20 outline-none">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-1">Category</label>
                        <input type="text" name="category" class="w-full bg-surface border border-gray-200 rounded p-2 text-sm focus:ring-2 focus:ring-brand/20 outline-none">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-1">Status</label>
                        <select name="status" class="w-full bg-surface border border-gray-200 rounded p-2 text-sm focus:ring-2 focus:ring-brand/20 outline-none cursor-pointer">
                            <option value="draft">Save as Draft</option>
                            <option value="published">Publish Immediately</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-1">Cover Image</label>
                    <input type="file" name="cover_image" accept="image/*" class="w-full bg-surface border border-gray-200 rounded p-2 text-sm focus:ring-2 focus:ring-brand/20 outline-none">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-1">Content (HTML/Markdown)</label>
                    <textarea name="content" rows="10" required class="w-full bg-surface border border-gray-200 rounded p-2 text-sm focus:ring-2 focus:ring-brand/20 outline-none"></textarea>
                </div>
            </div>
            <div class="flex justify-end pt-4 border-t border-gray-100 gap-3">
                <button type="button" onclick="document.getElementById('add-modal').classList.add('hidden')" class="px-4 py-2 text-brand font-bold text-sm">Cancel</button>
                <button type="submit" class="px-6 py-2.5 bg-brand text-white font-bold rounded shadow-sm hover:bg-brand-light transition">Save Post</button>
            </div>
        </form>
    </div>
</div>

@endsection
