@extends('admin.layout')
@section('title', 'FAQ Manager')
@section('content')

<!-- Error Alert -->
@if(session('error'))
<div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl flex items-center gap-3">
    <svg class="w-5 h-5 text-red-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
    <p class="text-sm font-medium text-red-700">{{ session('error') }}</p>
</div>
@endif

<!-- Success Alert -->
@if(session('success'))
<div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-xl flex items-center gap-3">
    <svg class="w-5 h-5 text-green-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    <p class="text-sm font-medium text-green-700">{{ session('success') }}</p>
</div>
@endif

<div class="mb-8 flex items-center justify-between">
    <div>
        <h2 class="text-2xl font-black text-brand tracking-tight">FAQ Manager</h2>
        <p class="text-brand-muted font-medium mt-1">Manage Help Center questions for customers and drivers.</p>
    </div>
    <div class="flex gap-4">
        <button onclick="document.getElementById('add-modal').classList.remove('hidden')" class="px-6 py-3 bg-brand text-white font-bold rounded-xl hover:bg-brand-light transition flex items-center gap-2 shadow-lg shadow-brand/20">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Add FAQ
        </button>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    
    <!-- Customer FAQs -->
    <div>
        <h3 class="text-[10px] font-black text-brand-muted uppercase tracking-widest mb-4 pl-2 border-l-2 border-brand">Customer Help Center</h3>
        <div class="space-y-4">
            @php $customerFaqs = $faqs->filter(fn($f) => in_array($f->audience, ['customer', 'all'])); @endphp
            
            @forelse($customerFaqs as $faq)
            <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm group relative hover:shadow-md transition-all">
                <div class="flex justify-between items-start mb-2">
                    <h4 class="text-sm font-bold text-brand pr-8">{{ $faq->question }}</h4>
                    <form action="{{ route('orchestrator.cms.faq.destroy', $faq->id) }}" method="POST" class="absolute top-4 right-4 opacity-0 group-hover:opacity-100 transition" onsubmit="return confirm('Delete this FAQ?');">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-red-400 hover:text-red-600"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>
                    </form>
                </div>
                <p class="text-xs text-brand-muted leading-relaxed">{{ $faq->answer }}</p>
                <div class="mt-3 pt-3 border-t border-gray-50 text-[10px] font-bold text-gray-400 uppercase tracking-widest flex items-center justify-between">
                    <span>Category: {{ $faq->category ?? 'General' }}</span>
                    <span>Order: {{ $faq->sort_order }}</span>
                </div>
            </div>
            @empty
            <div class="p-6 bg-white rounded border border-dashed border-gray-200 text-center text-gray-400">
                No customer FAQs found.
            </div>
            @endforelse
        </div>
    </div>

    <!-- Driver FAQs -->
    <div>
        <h3 class="text-[10px] font-black text-brand-muted uppercase tracking-widest mb-4 pl-2 border-l-2 border-accent">Driver Help Center</h3>
        <div class="space-y-4">
            @php $driverFaqs = $faqs->filter(fn($f) => in_array($f->audience, ['driver', 'all'])); @endphp
            
            @forelse($driverFaqs as $faq)
            <div class="bg-white rounded border border-gray-100 p-5 shadow-sm group relative">
                <div class="flex justify-between items-start mb-2">
                    <h4 class="text-sm font-bold text-brand pr-8">{{ $faq->question }}</h4>
                    <form action="{{ route('orchestrator.cms.faq.destroy', $faq->id) }}" method="POST" class="absolute top-4 right-4 opacity-0 group-hover:opacity-100 transition" onsubmit="return confirm('Delete this FAQ?');">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-red-400 hover:text-red-600"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>
                    </form>
                </div>
                <p class="text-xs text-brand-muted leading-relaxed">{{ $faq->answer }}</p>
                <div class="mt-3 pt-3 border-t border-gray-50 text-[10px] font-bold text-gray-400 uppercase tracking-widest flex items-center justify-between">
                    <span>Category: {{ $faq->category ?? 'General' }}</span>
                    <span>Order: {{ $faq->sort_order }}</span>
                </div>
            </div>
            @empty
            <div class="p-6 bg-white rounded border border-dashed border-gray-200 text-center text-gray-400">
                No driver FAQs found.
            </div>
            @endforelse
        </div>
    </div>

</div>

<!-- Add Modal -->
<div id="add-modal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-xl max-w-lg w-full p-6">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-lg font-black text-brand">Add FAQ</h3>
            <button onclick="document.getElementById('add-modal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
        </div>
        <form action="{{ route('orchestrator.cms.faq.store') }}" method="POST">
            @csrf
            <div class="space-y-4 mb-6">
                <div>
                    <label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-1">Question</label>
                    <input type="text" name="question" required class="w-full bg-surface border border-gray-200 rounded p-2 text-sm focus:ring-2 focus:ring-brand/20 outline-none">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-1">Answer</label>
                    <textarea name="answer" rows="4" required class="w-full bg-surface border border-gray-200 rounded p-2 text-sm focus:ring-2 focus:ring-brand/20 outline-none"></textarea>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-1">Audience</label>
                        <select name="audience" required class="w-full bg-surface border border-gray-200 rounded p-2 text-sm focus:ring-2 focus:ring-brand/20 outline-none cursor-pointer">
                            <option value="customer">Customers Only</option>
                            <option value="driver">Drivers Only</option>
                            <option value="all">Both (All Users)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-1">Category</label>
                        <input type="text" name="category" placeholder="e.g. Payments" class="w-full bg-surface border border-gray-200 rounded p-2 text-sm focus:ring-2 focus:ring-brand/20 outline-none">
                    </div>
                </div>
            </div>
            <div class="flex justify-end pt-4 border-t border-gray-100 gap-3">
                <button type="button" onclick="document.getElementById('add-modal').classList.add('hidden')" class="px-4 py-2 text-brand font-bold text-sm">Cancel</button>
                <button type="submit" class="px-6 py-2.5 bg-brand text-white font-bold rounded shadow-sm hover:bg-brand-light transition">Save FAQ</button>
            </div>
        </form>
    </div>
</div>

@endsection
