@extends('admin.layout')
@section('title', 'Document Approvals')
@section('content')

<div class="mb-8 flex items-center justify-between">
    <div>
        <h2 class="text-2xl font-black text-brand tracking-tight">KYC & Document Approvals</h2>
        <p class="text-brand-muted font-medium mt-1">Review pending driver identities, vehicle documents, and compliance forms.</p>
    </div>
    <div class="flex gap-4">
        <button class="px-6 py-3 bg-brand text-white font-bold rounded-lg hover:bg-brand-light transition flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            Approve Selected
        </button>
    </div>
</div>

<div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
    
    <!-- Pending Documents Queue -->
    <div class="xl:col-span-1 space-y-4">
        <h3 class="text-sm font-black text-brand uppercase tracking-widest flex items-center gap-2">
            Verification Queue
            <span class="bg-amber-100 text-amber-700 text-[10px] px-2 py-0.5 rounded-full">45 Pending</span>
        </h3>
        
        <!-- Queue Item (Active) -->
        <div class="bg-white border-2 border-brand rounded-lg p-4 cursor-pointer relative overflow-hidden shadow-lg shadow-brand/10">
            <div class="absolute right-0 top-0 w-1.5 h-full bg-brand"></div>
            <div class="flex items-center gap-3 mb-3">
                <div class="w-8 h-8 rounded-full bg-brand/10 flex items-center justify-center text-brand font-bold text-xs shrink-0">OA</div>
                <div>
                    <p class="text-sm font-bold text-brand">Osei Appiah</p>
                    <p class="text-[10px] text-brand-muted uppercase tracking-widest mt-0.5">Submitted 2h ago</p>
                </div>
            </div>
            <div class="flex gap-2">
                <span class="px-2 py-1 bg-surface border border-gray-100 text-[9px] font-black uppercase text-brand tracking-widest rounded">ID Card</span>
                <span class="px-2 py-1 bg-surface border border-gray-100 text-[9px] font-black uppercase text-brand tracking-widest rounded">License</span>
            </div>
        </div>

        <!-- Queue Item -->
        <div class="bg-white border border-gray-100 rounded-lg p-4 cursor-pointer hover:border-brand/50 transition">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-8 h-8 rounded-full bg-brand/10 flex items-center justify-center text-brand font-bold text-xs shrink-0">CA</div>
                <div>
                    <p class="text-sm font-bold text-brand">Cynthia Anim</p>
                    <p class="text-[10px] text-brand-muted uppercase tracking-widest mt-0.5">Submitted 4h ago</p>
                </div>
            </div>
            <div class="flex gap-2">
                <span class="px-2 py-1 bg-surface border border-gray-100 text-[9px] font-black uppercase text-brand tracking-widest rounded">Insurance</span>
                <span class="px-2 py-1 bg-surface border border-gray-100 text-[9px] font-black uppercase text-brand tracking-widest rounded">Roadworthy</span>
            </div>
        </div>
        
        <!-- Queue Item -->
        <div class="bg-white border border-gray-100 rounded-lg p-4 cursor-pointer hover:border-brand/50 transition">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-8 h-8 rounded-full bg-brand/10 flex items-center justify-center text-brand font-bold text-xs shrink-0">DK</div>
                <div>
                    <p class="text-sm font-bold text-brand">David Kumi</p>
                    <p class="text-[10px] text-brand-muted uppercase tracking-widest mt-0.5">Submitted 1d ago</p>
                </div>
            </div>
            <div class="flex gap-2">
                <span class="px-2 py-1 bg-red-50 border border-red-100 text-[9px] font-black uppercase text-red-600 tracking-widest rounded">Re-Upload: License</span>
            </div>
        </div>
    </div>

    <!-- Review Panel -->
    <div class="xl:col-span-2">
        <div class="bg-white rounded-lg border border-gray-100 shadow-sm p-6 lg:p-8">
            <div class="flex items-start justify-between mb-8">
                <div>
                    <h3 class="text-xl font-black text-brand tracking-tight">Osei Appiah <span class="text-sm text-brand-muted font-mono tracking-normal font-medium ml-2">#DRV-1024</span></h3>
                    <p class="text-sm text-brand-muted mt-1">+233 20 987 6543  •  osei@example.com</p>
                </div>
                <div class="flex items-center gap-2">
                    <span class="px-3 py-1 bg-amber-100 text-amber-700 text-xs font-black uppercase tracking-widest rounded-full">Pending</span>
                </div>
            </div>

            <!-- Documents Grid -->
            <div class="space-y-8">
                
                <!-- Document 1 -->
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h4 class="text-sm font-bold text-brand">National ID Card (Ghana Card)</h4>
                            <p class="text-[10px] text-brand-muted uppercase tracking-widest mt-0.5">GHA-123456789-0</p>
                        </div>
                        <div class="flex gap-2">
                            <button class="w-8 h-8 flex items-center justify-center rounded-full bg-green-50 text-green-600 hover:bg-green-100 transition"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg></button>
                            <button class="w-8 h-8 flex items-center justify-center rounded-full bg-red-50 text-red-600 hover:bg-red-100 transition"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="aspect-video bg-surface border border-gray-200 rounded-lg flex items-center justify-center relative group overflow-hidden">
                            <img src="https://via.placeholder.com/600x400/eeeeee/aaaaaa?text=ID+Front" class="w-full h-full object-cover">
                            <div class="absolute inset-0 bg-brand/80 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity backdrop-blur-sm cursor-zoom-in">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"/></svg>
                            </div>
                        </div>
                        <div class="aspect-video bg-surface border border-gray-200 rounded-lg flex items-center justify-center relative group overflow-hidden">
                            <img src="https://via.placeholder.com/600x400/eeeeee/aaaaaa?text=ID+Back" class="w-full h-full object-cover">
                            <div class="absolute inset-0 bg-brand/80 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity backdrop-blur-sm cursor-zoom-in">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"/></svg>
                            </div>
                        </div>
                    </div>
                </div>

                <hr class="border-gray-100">

                <!-- Document 2 -->
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h4 class="text-sm font-bold text-brand">Driver's License</h4>
                            <p class="text-[10px] text-brand-muted uppercase tracking-widest mt-0.5">Expires: 24 Oct 2028</p>
                        </div>
                        <div class="flex gap-2">
                            <button class="w-8 h-8 flex items-center justify-center rounded-full bg-green-50 text-green-600 hover:bg-green-100 transition"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg></button>
                            <button class="w-8 h-8 flex items-center justify-center rounded-full bg-red-50 text-red-600 hover:bg-red-100 transition"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="aspect-video bg-surface border border-gray-200 rounded-lg flex items-center justify-center relative group overflow-hidden">
                            <img src="https://via.placeholder.com/600x400/eeeeee/aaaaaa?text=License+Front" class="w-full h-full object-cover">
                            <div class="absolute inset-0 bg-brand/80 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity backdrop-blur-sm cursor-zoom-in">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"/></svg>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Action Footer -->
            <div class="mt-8 pt-8 border-t border-gray-100 flex items-center justify-between">
                <button class="px-6 py-2.5 text-sm font-bold text-red-600 hover:text-red-700 transition">Reject Application...</button>
                <button class="px-8 py-3 bg-brand text-white font-bold rounded-lg hover:bg-brand-light transition shadow-lg shadow-brand/20">Verify & Approve Driver</button>
            </div>
        </div>
    </div>
</div>

@endsection
