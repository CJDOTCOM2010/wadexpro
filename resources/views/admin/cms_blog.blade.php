@extends('admin.layout')
@section('title', 'Blog Manager')
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

<div x-data="blogManager()" class="max-w-6xl mx-auto">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-black text-brand tracking-tight">Blog & News</h2>
            <p class="text-sm text-brand-muted font-medium mt-0.5">Publish articles, press releases, and company updates.</p>
        </div>
        <button @click="openCreate()" class="px-5 py-2.5 bg-brand text-white rounded-lg text-xs font-bold hover:bg-brand-light transition-colors flex items-center gap-2 shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Write Post
        </button>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <div class="bg-white border border-gray-100 rounded-xl p-4 flex items-center gap-3.5">
            <div class="w-10 h-10 bg-green-50 rounded-lg flex items-center justify-center text-green-600 shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <p class="text-lg font-black text-brand">{{ number_format($stats['published'] ?? 0) }}</p>
                <p class="text-[10px] font-bold text-brand-muted uppercase tracking-wider">Published</p>
            </div>
        </div>
        <div class="bg-white border border-gray-100 rounded-xl p-4 flex items-center gap-3.5">
            <div class="w-10 h-10 bg-amber-50 rounded-lg flex items-center justify-center text-amber-600 shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
            </div>
            <div>
                <p class="text-lg font-black text-brand">{{ number_format($stats['draft'] ?? 0) }}</p>
                <p class="text-[10px] font-bold text-brand-muted uppercase tracking-wider">Drafts</p>
            </div>
        </div>
        <div class="bg-white border border-gray-100 rounded-xl p-4 flex items-center gap-3.5">
            <div class="w-10 h-10 bg-brand/5 rounded-lg flex items-center justify-center text-brand shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
            </div>
            <div>
                <p class="text-lg font-black text-brand">{{ number_format($stats['total_views'] ?? 0) }}</p>
                <p class="text-[10px] font-bold text-brand-muted uppercase tracking-wider">Total Views</p>
            </div>
        </div>
    </div>

    {{-- Table --}}
    <div class="bg-white border border-gray-100 rounded-xl overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-surface/20">
            <form action="{{ route('orchestrator.cms.blog') }}" method="GET" class="flex items-center gap-3 flex-1">
                <div class="relative flex-1 max-w-xs">
                    <svg class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search articles..." class="w-full bg-white border border-gray-200 rounded-lg pl-9 pr-3 py-2 text-sm outline-none focus:ring-2 focus:ring-accent/20 transition-shadow">
                </div>
                <select name="status" onchange="this.form.submit()" class="bg-white border border-gray-200 rounded-lg px-3 py-2 text-xs font-bold outline-none focus:ring-2 focus:ring-accent/20">
                    <option value="">All Statuses</option>
                    <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>Published</option>
                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                </select>
                @if(request('search'))<input type="hidden" name="search" value="{{ request('search') }}">@endif
                @if(request('search') || request('status'))
                <a href="{{ route('orchestrator.cms.blog') }}" class="text-xs font-bold text-brand-muted hover:text-brand shrink-0">Clear</a>
                @endif
            </form>
        </div>

        <div class="divide-y divide-gray-50">
            @forelse($posts as $post)
            <div class="px-5 py-4 hover:bg-surface/20 transition-colors">
                <div class="flex items-start gap-4">
                    <div class="w-14 h-11 rounded-lg bg-surface overflow-hidden border border-gray-100 shrink-0 flex items-center justify-center text-gray-300">
                        @if($post->cover_image_url)
                        <img src="{{ asset('storage/'.$post->cover_image_url) }}" class="w-full h-full object-cover">
                        @else
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0 grid grid-cols-1 lg:grid-cols-5 gap-2 lg:gap-4">
                        <div class="lg:col-span-2">
                            <p class="text-sm font-bold text-brand truncate">{{ $post->title }}</p>
                            <p class="text-[10px] text-brand-muted mt-0.5">
                                {{ $post->status == 'published' ? 'Published '.$post->published_at?->format('M d, Y') : 'Edited '.$post->updated_at->diffForHumans() }}
                                @if($post->is_featured)<span class="text-accent font-bold ml-1">· Featured</span>@endif
                            </p>
                        </div>
                        <div>
                            <span class="px-2 py-0.5 bg-surface border border-gray-100 text-[10px] font-bold text-brand rounded">{{ $post->category ?? 'General' }}</span>
                        </div>
                        <div>
                            @if($post->status == 'published')
                            <span class="text-[10px] font-bold text-green-600 bg-green-50 px-2 py-0.5 rounded">Published</span>
                            @else
                            <span class="text-[10px] font-bold text-amber-600 bg-amber-50 px-2 py-0.5 rounded">Draft</span>
                            @endif
                        </div>
                        <div class="flex items-center justify-between lg:justify-end gap-2">
                            <span class="text-xs font-bold {{ $post->view_count > 0 ? 'text-brand' : 'text-gray-400' }}">{{ number_format($post->view_count) }} views</span>
                            <div class="flex items-center gap-1">
                                <button @click="openEdit('{{ $post->id }}', '{{ addslashes($post->title) }}', '{{ $post->category ?? '' }}', '{{ $post->status }}', '{{ $post->is_featured ? '1' : '0' }}', '{{ addslashes($post->excerpt ?? '') }}', '{{ addslashes($post->meta_title ?? '') }}', '{{ addslashes($post->meta_description ?? '') }}', '{{ $post->slug }}')" class="px-2.5 py-1 text-[10px] font-bold text-accent hover:bg-accent/5 rounded-lg transition-colors">Edit</button>
                                <button @click="confirmDelete('{{ $post->id }}', '{{ addslashes($post->title) }}')" class="px-2.5 py-1 text-[10px] font-bold text-red-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors">Delete</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="flex flex-col items-center justify-center py-16 text-brand-muted">
                <svg class="w-14 h-14 mb-3 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
                <p class="text-sm font-bold">No blog posts found</p>
                <p class="text-xs mt-1">Create your first post to get started.</p>
            </div>
            @endforelse
        </div>

        @if($posts->hasPages())
        <div class="px-5 py-3 border-t border-gray-100 bg-surface/20">{{ $posts->links() }}</div>
        @endif
    </div>

    {{-- Create/Edit Modal --}}
    <div x-show="showForm" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-brand/50 backdrop-blur-sm" @click="showForm = false"></div>
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl relative z-10 max-h-[90vh] overflow-y-auto" @click.outside="showForm = false">
            <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between sticky top-0 bg-white">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 bg-accent/10 rounded-lg flex items-center justify-center text-accent">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-brand" x-text="form.id ? 'Edit Post' : 'Write Post'"></h3>
                        <p class="text-xs text-brand-muted" x-text="form.id ? 'Update your article.' : 'Create a new blog article.'"></p>
                    </div>
                </div>
                <button @click="showForm = false" class="w-7 h-7 bg-surface rounded-lg flex items-center justify-center text-brand-muted hover:text-brand transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form :action="form.action" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
                @csrf
                <input type="hidden" name="_method" x-model="form.method">
                <div>
                    <label class="text-[10px] font-bold text-brand-muted uppercase tracking-wider mb-1.5 block">Title <span class="text-red-500">*</span></label>
                    <input type="text" name="title" x-model="form.title" required placeholder="e.g. How Wadex is Transforming Urban Mobility" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-medium outline-none focus:ring-2 focus:ring-accent/20 transition-shadow">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-[10px] font-bold text-brand-muted uppercase tracking-wider mb-1.5 block">Category</label>
                        <input type="text" name="category" x-model="form.category" placeholder="e.g. News, Tech" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-medium outline-none focus:ring-2 focus:ring-accent/20 transition-shadow">
                    </div>
                    <div>
                        <label class="text-[10px] font-bold text-brand-muted uppercase tracking-wider mb-1.5 block">Status</label>
                        <select name="status" x-model="form.status" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-medium outline-none focus:ring-2 focus:ring-accent/20">
                            <option value="draft">Draft</option>
                            <option value="published">Published</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="text-[10px] font-bold text-brand-muted uppercase tracking-wider mb-1.5 block">Excerpt</label>
                    <textarea name="excerpt" x-model="form.excerpt" rows="2" placeholder="Short summary for previews and social sharing..." class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-medium outline-none focus:ring-2 focus:ring-accent/20 transition-shadow resize-none"></textarea>
                </div>
                <div>
                    <label class="text-[10px] font-bold text-brand-muted uppercase tracking-wider mb-1.5 block">Content</label>
                    <textarea name="content" x-model="form.content" rows="8" placeholder="Write your article content here..." class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-medium outline-none focus:ring-2 focus:ring-accent/20 transition-shadow resize-none font-mono"></textarea>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-[10px] font-bold text-brand-muted uppercase tracking-wider mb-1.5 block">Cover Image</label>
                        <input type="file" name="cover_image" accept="image/*" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-medium outline-none focus:ring-2 focus:ring-accent/20 transition-shadow file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-[10px] file:font-bold file:bg-brand file:text-white hover:file:bg-brand-light">
                    </div>
                    <div>
                        <label class="text-[10px] font-bold text-brand-muted uppercase tracking-wider mb-1.5 block">Tags (comma-separated)</label>
                        <input type="text" name="tags" x-model="form.tags" placeholder="e.g. mobility, ghana, tech" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-medium outline-none focus:ring-2 focus:ring-accent/20 transition-shadow">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4 pt-2 border-t border-gray-100">
                    <label class="flex items-center gap-3 p-3 bg-surface rounded-lg cursor-pointer hover:bg-accent/5 transition-colors">
                        <input type="checkbox" name="is_featured" value="1" x-model="form.is_featured" class="w-4 h-4 rounded border-gray-300 text-accent focus:ring-accent/30">
                        <span class="text-xs font-bold text-brand">Featured post</span>
                    </label>
                    <div>
                        <label class="text-[10px] font-bold text-brand-muted uppercase tracking-wider mb-1.5 block">Slug</label>
                        <input type="text" name="slug" x-model="form.slug" placeholder="Auto-generated if empty" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-medium font-mono outline-none focus:ring-2 focus:ring-accent/20 transition-shadow">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4 pt-2 border-t border-gray-100">
                    <div>
                        <label class="text-[10px] font-bold text-brand-muted uppercase tracking-wider mb-1.5 block">Meta Title</label>
                        <input type="text" name="meta_title" x-model="form.meta_title" placeholder="SEO title (defaults to post title)" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-medium outline-none focus:ring-2 focus:ring-accent/20 transition-shadow">
                    </div>
                    <div>
                        <label class="text-[10px] font-bold text-brand-muted uppercase tracking-wider mb-1.5 block">Meta Description</label>
                        <input type="text" name="meta_description" x-model="form.meta_description" placeholder="SEO description" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-medium outline-none focus:ring-2 focus:ring-accent/20 transition-shadow">
                    </div>
                </div>
                <div class="flex justify-end gap-2 pt-2 border-t border-gray-100">
                    <button type="button" @click="showForm = false" class="px-4 py-2 text-xs font-bold text-brand-muted hover:text-brand transition-colors">Cancel</button>
                    <button type="submit" class="px-5 py-2 bg-brand text-white rounded-lg text-xs font-bold hover:bg-brand-light transition-colors">Save Post</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Delete Confirmation --}}
    <div x-show="deleteStep > 0" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-brand/60 backdrop-blur-sm" @click="closeDelete()"></div>
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-md relative z-10" @click.outside="closeDelete()">
            <template x-if="deleteStep === 1">
                <div class="p-6">
                    <div class="w-14 h-14 bg-red-50 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-7 h-7 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <h3 class="text-lg font-bold text-brand text-center mb-2">Delete Post?</h3>
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
function blogManager() {
    return {
        showForm: false,
        form: { id: null, action: '', method: 'POST', title: '', category: '', status: 'draft', excerpt: '', content: '', tags: '', is_featured: false, slug: '', meta_title: '', meta_description: '' },
        deleteStep: 0, deleteId: '', deleteLabel: '', deleteConfirm: '',
        openCreate() {
            this.form = { id: null, action: '{{ route('orchestrator.cms.blog.store') }}', method: 'POST', title: '', category: '', status: 'draft', excerpt: '', content: '', tags: '', is_featured: false, slug: '', meta_title: '', meta_description: '' };
            this.showForm = true;
        },
        openEdit(id, title, category, status, featured, excerpt, metaTitle, metaDesc, slug) {
            this.form = { id, action: '/orchestrator/cms/blog/' + id, method: 'PUT', title, category, status, excerpt, content: '', tags: '', is_featured: featured === '1', slug, meta_title: metaTitle, meta_description: metaDesc };
            this.showForm = true;
        },
        confirmDelete(id, label) { this.deleteId = id; this.deleteLabel = label; this.deleteStep = 1; this.deleteConfirm = ''; },
        closeDelete() { this.deleteStep = 0; this.deleteConfirm = ''; },
        executeDelete() {
            if (this.deleteConfirm !== 'DELETE') return;
            const f = document.createElement('form'); f.method = 'POST'; f.action = '/orchestrator/cms/blog/' + this.deleteId;
            const c = document.createElement('input'); c.type = 'hidden'; c.name = '_token'; c.value = '{{ csrf_token() }}'; f.appendChild(c);
            const m = document.createElement('input'); m.type = 'hidden'; m.name = '_method'; m.value = 'DELETE'; f.appendChild(m);
            document.body.appendChild(f); f.submit();
        }
    };
}
</script>
<style>[x-cloak] { display: none !important; }</style>
@endsection