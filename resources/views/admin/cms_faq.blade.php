@extends('admin.layout')
@section('title', 'FAQ Manager')
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

@php
$allFaqs = $faqs ?? collect();
$total = $allFaqs->count();
$customerFaqs = $allFaqs->filter(fn($f) => in_array($f->audience, ['customer', 'all']));
$driverFaqs = $allFaqs->filter(fn($f) => in_array($f->audience, ['driver', 'all']));
$categories = $allFaqs->pluck('category')->filter()->unique()->values();
@endphp

<div x-data="faqManager()" class="max-w-6xl mx-auto">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-black text-brand tracking-tight">FAQ Manager</h2>
            <p class="text-sm text-brand-muted font-medium mt-0.5">Manage Help Center questions for customers and drivers.</p>
        </div>
        <div class="flex items-center gap-2">
            @if($categories->count() > 0)
            <select x-model="filterCategory" class="bg-white border border-gray-200 rounded-lg px-3 py-2 text-xs font-bold outline-none focus:ring-2 focus:ring-accent/20">
                <option value="">All Categories</option>
                @foreach($categories as $cat)
                <option value="{{ $cat }}">{{ $cat }}</option>
                @endforeach
            </select>
            @endif
            <button @click="openAdd()" class="px-5 py-2.5 bg-brand text-white rounded-lg text-xs font-bold hover:bg-brand-light transition-colors flex items-center gap-2 shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Add FAQ
            </button>
        </div>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <div class="bg-white border border-gray-100 rounded-xl p-4 flex items-center gap-3.5">
            <div class="w-10 h-10 bg-brand/5 rounded-lg flex items-center justify-center text-brand shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
            </div>
            <div>
                <p class="text-lg font-black text-brand">{{ $total }}</p>
                <p class="text-[10px] font-bold text-brand-muted uppercase tracking-wider">Total FAQs</p>
            </div>
        </div>
        <div class="bg-white border border-gray-100 rounded-xl p-4 flex items-center gap-3.5">
            <div class="w-10 h-10 bg-blue-50 rounded-lg flex items-center justify-center text-blue-600 shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
            </div>
            <div>
                <p class="text-lg font-black text-brand">{{ $customerFaqs->count() }}</p>
                <p class="text-[10px] font-bold text-brand-muted uppercase tracking-wider">Customer</p>
            </div>
        </div>
        <div class="bg-white border border-gray-100 rounded-xl p-4 flex items-center gap-3.5">
            <div class="w-10 h-10 bg-amber-50 rounded-lg flex items-center justify-center text-amber-600 shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1"/></svg>
            </div>
            <div>
                <p class="text-lg font-black text-brand">{{ $driverFaqs->count() }}</p>
                <p class="text-[10px] font-bold text-brand-muted uppercase tracking-wider">Driver</p>
            </div>
        </div>
    </div>

    {{-- FAQ Columns --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Customer --}}
        <div>
            <div class="flex items-center gap-2 mb-4">
                <span class="w-1 h-1 bg-brand rounded-full"></span>
                <h3 class="text-xs font-bold text-brand uppercase tracking-wider">Customer Help Center</h3>
                <span class="text-[10px] font-bold text-brand-muted bg-surface px-2 py-0.5 rounded">{{ $customerFaqs->count() }}</span>
            </div>
            <div class="space-y-3">
                @forelse($customerFaqs as $faq)
                <div x-show="filterCategory === '' || '{{ $faq->category }}' === filterCategory" class="bg-white border border-gray-100 rounded-xl overflow-hidden {{ !$faq->is_active ? 'opacity-50' : '' }}">
                    <div class="px-5 py-4">
                        <div class="flex items-start justify-between gap-3 mb-1.5">
                            <h4 class="text-sm font-bold text-brand">{{ $faq->question }}</h4>
                            <div class="flex items-center gap-1 shrink-0 ml-2">
                                <button @click="openEdit('{{ $faq->id }}', '{{ addslashes($faq->question) }}', '{{ addslashes($faq->answer) }}', '{{ $faq->audience }}', '{{ $faq->category ?? '' }}', '{{ $faq->sort_order ?? 0 }}', '{{ $faq->is_active ? '1' : '0' }}')" class="w-7 h-7 rounded-lg flex items-center justify-center text-gray-400 hover:text-brand hover:bg-surface transition-colors" title="Edit">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                </button>
                                <button @click="confirmDelete('{{ $faq->id }}', '{{ addslashes($faq->question) }}')" class="w-7 h-7 rounded-lg flex items-center justify-center text-gray-300 hover:text-red-500 hover:bg-red-50 transition-colors" title="Delete">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        </div>
                        <p class="text-xs text-brand-muted leading-relaxed">{{ $faq->answer }}</p>
                        <div class="flex items-center gap-4 mt-3 text-[10px] text-brand-muted">
                            @if($faq->category)<span class="px-1.5 py-0.5 bg-surface border border-gray-100 rounded font-bold">{{ $faq->category }}</span>@endif
                            <span>Order: <strong>{{ $faq->sort_order }}</strong></span>
                            @if(!$faq->is_active)<span class="text-amber-600 font-bold">Disabled</span>@endif
                        </div>
                    </div>
                </div>
                @empty
                <div class="flex flex-col items-center justify-center py-10 text-brand-muted bg-white border border-dashed border-gray-200 rounded-xl">
                    <svg class="w-10 h-10 mb-2 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                    <p class="text-xs font-bold">No customer FAQs</p>
                </div>
                @endforelse
            </div>
        </div>

        {{-- Driver --}}
        <div>
            <div class="flex items-center gap-2 mb-4">
                <span class="w-1 h-1 bg-accent rounded-full"></span>
                <h3 class="text-xs font-bold text-brand uppercase tracking-wider">Driver Help Center</h3>
                <span class="text-[10px] font-bold text-brand-muted bg-surface px-2 py-0.5 rounded">{{ $driverFaqs->count() }}</span>
            </div>
            <div class="space-y-3">
                @forelse($driverFaqs as $faq)
                <div x-show="filterCategory === '' || '{{ $faq->category }}' === filterCategory" class="bg-white border border-gray-100 rounded-xl overflow-hidden {{ !$faq->is_active ? 'opacity-50' : '' }}">
                    <div class="px-5 py-4">
                        <div class="flex items-start justify-between gap-3 mb-1.5">
                            <h4 class="text-sm font-bold text-brand">{{ $faq->question }}</h4>
                            <div class="flex items-center gap-1 shrink-0 ml-2">
                                <button @click="openEdit('{{ $faq->id }}', '{{ addslashes($faq->question) }}', '{{ addslashes($faq->answer) }}', '{{ $faq->audience }}', '{{ $faq->category ?? '' }}', '{{ $faq->sort_order ?? 0 }}', '{{ $faq->is_active ? '1' : '0' }}')" class="w-7 h-7 rounded-lg flex items-center justify-center text-gray-400 hover:text-brand hover:bg-surface transition-colors" title="Edit">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                </button>
                                <button @click="confirmDelete('{{ $faq->id }}', '{{ addslashes($faq->question) }}')" class="w-7 h-7 rounded-lg flex items-center justify-center text-gray-300 hover:text-red-500 hover:bg-red-50 transition-colors" title="Delete">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        </div>
                        <p class="text-xs text-brand-muted leading-relaxed">{{ $faq->answer }}</p>
                        <div class="flex items-center gap-4 mt-3 text-[10px] text-brand-muted">
                            @if($faq->category)<span class="px-1.5 py-0.5 bg-surface border border-gray-100 rounded font-bold">{{ $faq->category }}</span>@endif
                            <span>Order: <strong>{{ $faq->sort_order }}</strong></span>
                            @if(!$faq->is_active)<span class="text-amber-600 font-bold">Disabled</span>@endif
                        </div>
                    </div>
                </div>
                @empty
                <div class="flex flex-col items-center justify-center py-10 text-brand-muted bg-white border border-dashed border-gray-200 rounded-xl">
                    <svg class="w-10 h-10 mb-2 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1"/></svg>
                    <p class="text-xs font-bold">No driver FAQs</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    @if($allFaqs->hasPages())
    <div class="mt-4">{{ $allFaqs->links() }}</div>
    @endif

    {{-- FAQ Form Modal --}}
    <div x-show="showForm" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-brand/50 backdrop-blur-sm" @click="showForm = false"></div>
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg relative z-10" @click.outside="showForm = false">
            <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 bg-accent/10 rounded-lg flex items-center justify-center text-accent">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-brand" x-text="form.id ? 'Edit FAQ' : 'Add FAQ'"></h3>
                        <p class="text-xs text-brand-muted" x-text="form.id ? 'Update this help center entry.' : 'Add a new help center entry.'"></p>
                    </div>
                </div>
                <button @click="showForm = false" class="w-7 h-7 bg-surface rounded-lg flex items-center justify-center text-brand-muted hover:text-brand transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form :action="form.action" method="POST" class="p-6 space-y-4">
                @csrf
                <input type="hidden" name="_method" x-model="form.method">
                <div>
                    <label class="text-[10px] font-bold text-brand-muted uppercase tracking-wider mb-1.5 block">Question <span class="text-red-500">*</span></label>
                    <input type="text" name="question" x-model="form.question" required placeholder="e.g. How do I request a ride?" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-medium outline-none focus:ring-2 focus:ring-accent/20 transition-shadow">
                </div>
                <div>
                    <label class="text-[10px] font-bold text-brand-muted uppercase tracking-wider mb-1.5 block">Answer <span class="text-red-500">*</span></label>
                    <textarea name="answer" x-model="form.answer" rows="4" required placeholder="Detailed answer..." class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-medium outline-none focus:ring-2 focus:ring-accent/20 transition-shadow resize-none"></textarea>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-[10px] font-bold text-brand-muted uppercase tracking-wider mb-1.5 block">Audience</label>
                        <select name="audience" x-model="form.audience" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-medium outline-none focus:ring-2 focus:ring-accent/20">
                            <option value="customer">Customers Only</option>
                            <option value="driver">Drivers Only</option>
                            <option value="all">Both (All Users)</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-[10px] font-bold text-brand-muted uppercase tracking-wider mb-1.5 block">Category</label>
                        <input type="text" name="category" x-model="form.category" placeholder="e.g. Payments" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-medium outline-none focus:ring-2 focus:ring-accent/20 transition-shadow">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-[10px] font-bold text-brand-muted uppercase tracking-wider mb-1.5 block">Sort Order</label>
                        <input type="number" name="sort_order" x-model="form.sort_order" placeholder="0" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-medium outline-none focus:ring-2 focus:ring-accent/20 transition-shadow">
                    </div>
                    <label class="flex items-center gap-3 p-3.5 bg-surface rounded-lg cursor-pointer hover:bg-accent/5 transition-colors self-end">
                        <input type="checkbox" name="is_active" value="1" x-model="form.is_active" class="w-4 h-4 rounded border-gray-300 text-accent focus:ring-accent/30">
                        <span class="text-xs font-bold text-brand">Active</span>
                    </label>
                </div>
                <div class="flex justify-end gap-2 pt-2 border-t border-gray-100">
                    <button type="button" @click="showForm = false" class="px-4 py-2 text-xs font-bold text-brand-muted hover:text-brand transition-colors">Cancel</button>
                    <button type="submit" class="px-5 py-2 bg-brand text-white rounded-lg text-xs font-bold hover:bg-brand-light transition-colors">Save FAQ</button>
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
                    <h3 class="text-lg font-bold text-brand text-center mb-2">Delete FAQ?</h3>
                    <p class="text-sm text-brand-muted text-center mb-6">Permanently delete this question from the help center?</p>
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
function faqManager() {
    return {
        showForm: false,
        filterCategory: '',
        form: { id: null, action: '', method: 'POST', question: '', answer: '', audience: 'customer', category: '', sort_order: 0, is_active: true },
        deleteStep: 0, deleteId: '', deleteLabel: '', deleteConfirm: '',
        openAdd() {
            this.form = { id: null, action: '{{ route('orchestrator.cms.faq.store') }}', method: 'POST', question: '', answer: '', audience: 'customer', category: '', sort_order: 0, is_active: true };
            this.showForm = true;
        },
        openEdit(id, question, answer, audience, category, sortOrder, isActive) {
            this.form = { id, action: '/orchestrator/cms/faq/' + id, method: 'PUT', question, answer, audience, category, sort_order: parseInt(sortOrder) || 0, is_active: isActive === '1' };
            this.showForm = true;
        },
        confirmDelete(id, label) { this.deleteId = id; this.deleteLabel = label; this.deleteStep = 1; this.deleteConfirm = ''; },
        closeDelete() { this.deleteStep = 0; this.deleteConfirm = ''; },
        executeDelete() {
            if (this.deleteConfirm !== 'DELETE') return;
            const f = document.createElement('form'); f.method = 'POST'; f.action = '/orchestrator/cms/faq/' + this.deleteId;
            const c = document.createElement('input'); c.type = 'hidden'; c.name = '_token'; c.value = '{{ csrf_token() }}'; f.appendChild(c);
            const m = document.createElement('input'); m.type = 'hidden'; m.name = '_method'; m.value = 'DELETE'; f.appendChild(m);
            document.body.appendChild(f); f.submit();
        }
    };
}
</script>
<style>[x-cloak] { display: none !important; }</style>
@endsection