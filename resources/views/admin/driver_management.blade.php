@extends('admin.layout')
@section('title', 'Driver Registry')
@section('content')

<div class="mb-8 flex items-center justify-between">
    <div>
        <h2 class="text-2xl font-black text-brand tracking-tight">Driver Registry</h2>
        <p class="text-brand-muted font-medium mt-1">Manage all driver accounts, status, and compliance tracking.</p>
    </div>
    <div class="flex gap-4">
        <button class="px-6 py-3 bg-surface text-brand font-bold rounded-lg border border-gray-100 hover:bg-gray-100 transition flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            Export List
        </button>
        <button class="px-6 py-3 bg-brand text-white font-bold rounded-lg hover:bg-brand-light transition flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Onboard Driver
        </button>
    </div>
</div>

<!-- Stats Overview -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    <div class="bg-white p-6 rounded-lg border border-gray-100 shadow-sm relative overflow-hidden group">
        <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-brand/5 rounded-full group-hover:scale-150 transition-transform duration-500"></div>
        <p class="text-[10px] font-black text-brand-muted uppercase tracking-widest relative z-10">Total Enrolled</p>
        <p class="text-3xl font-black text-brand tracking-tight mt-2 relative z-10">1,204</p>
    </div>
    <div class="bg-white p-6 rounded-lg border border-gray-100 shadow-sm relative overflow-hidden group">
        <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-green-500/5 rounded-full group-hover:scale-150 transition-transform duration-500"></div>
        <p class="text-[10px] font-black text-brand-muted uppercase tracking-widest relative z-10">Active & Verified</p>
        <p class="text-3xl font-black text-green-600 tracking-tight mt-2 relative z-10">982</p>
    </div>
    <div class="bg-white p-6 rounded-lg border border-gray-100 shadow-sm relative overflow-hidden group">
        <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-amber-500/5 rounded-full group-hover:scale-150 transition-transform duration-500"></div>
        <p class="text-[10px] font-black text-brand-muted uppercase tracking-widest relative z-10">Pending KYC</p>
        <p class="text-3xl font-black text-amber-500 tracking-tight mt-2 relative z-10">45</p>
    </div>
    <div class="bg-white p-6 rounded-lg border border-gray-100 shadow-sm relative overflow-hidden group">
        <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-red-500/5 rounded-full group-hover:scale-150 transition-transform duration-500"></div>
        <p class="text-[10px] font-black text-brand-muted uppercase tracking-widest relative z-10">Suspended / Blocked</p>
        <p class="text-3xl font-black text-red-500 tracking-tight mt-2 relative z-10">17</p>
    </div>
</div>

<!-- Driver Data Table -->
<div class="bg-white rounded-lg border border-gray-100 shadow-sm overflow-hidden">
    <!-- Toolbar -->
    <div class="p-6 border-b border-gray-50 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="relative w-full md:w-96">
            <input type="text" placeholder="Search by name, phone, or license..." class="w-full bg-surface border border-gray-100 rounded-lg pl-10 pr-4 py-2.5 text-sm font-medium outline-none focus:ring-2 focus:ring-brand/20 transition-all">
            <svg class="w-4 h-4 text-gray-400 absolute left-4 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
        </div>
        <div class="flex items-center gap-2">
            <select class="bg-white border border-gray-100 rounded-lg px-4 py-2.5 text-sm font-bold text-brand outline-none focus:ring-2 focus:ring-brand/20 cursor-pointer">
                <option value="all">All Statuses</option>
                <option value="active">Active</option>
                <option value="pending">Pending KYC</option>
                <option value="suspended">Suspended</option>
            </select>
            <select class="bg-white border border-gray-100 rounded-lg px-4 py-2.5 text-sm font-bold text-brand outline-none focus:ring-2 focus:ring-brand/20 cursor-pointer">
                <option value="all">All Vehicle Types</option>
                <option value="sedan">Sedan</option>
                <option value="suv">SUV</option>
                <option value="motorcycle">Motorcycle</option>
            </select>
        </div>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-surface/30 border-b border-gray-50">
                    <th class="px-6 py-4 text-[10px] font-black text-brand-muted uppercase tracking-widest whitespace-nowrap">Driver Info</th>
                    <th class="px-6 py-4 text-[10px] font-black text-brand-muted uppercase tracking-widest whitespace-nowrap">Contact</th>
                    <th class="px-6 py-4 text-[10px] font-black text-brand-muted uppercase tracking-widest whitespace-nowrap">Assigned Vehicle</th>
                    <th class="px-6 py-4 text-[10px] font-black text-brand-muted uppercase tracking-widest whitespace-nowrap">Rating</th>
                    <th class="px-6 py-4 text-[10px] font-black text-brand-muted uppercase tracking-widest whitespace-nowrap">Status</th>
                    <th class="px-6 py-4 text-[10px] font-black text-brand-muted uppercase tracking-widest text-right whitespace-nowrap">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                <!-- Driver Row 1 -->
                <tr class="hover:bg-surface/20 transition-colors group">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-full bg-brand/10 flex items-center justify-center shrink-0 border border-brand/20 overflow-hidden">
                                <img src="https://i.pravatar.cc/150?u=1" alt="Avatar" class="w-full h-full object-cover">
                            </div>
                            <div>
                                <p class="text-sm font-bold text-brand">Kwame Mensah</p>
                                <p class="text-[10px] text-brand-muted uppercase tracking-widest mt-0.5 font-mono">DRV-0982</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <p class="text-xs font-bold text-brand">+233 24 123 4567</p>
                        <p class="text-[10px] text-brand-muted mt-0.5 truncate w-32">kwame.m@example.com</p>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                            <div>
                                <p class="text-xs font-bold text-brand">Toyota Corolla (2018)</p>
                                <p class="text-[10px] font-mono text-brand-muted mt-0.5">GW-4562-21</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-1">
                            <svg class="w-4 h-4 text-accent" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            <span class="text-xs font-bold text-brand">4.9</span>
                            <span class="text-[10px] text-brand-muted ml-1">(124 trips)</span>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-2">
                            <div class="w-1.5 h-1.5 rounded-full bg-green-500 shadow-[0_0_5px_rgba(34,197,94,0.5)]"></div>
                            <span class="text-[10px] font-black uppercase text-green-600 tracking-widest">Active</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <button class="px-3 py-1.5 bg-surface text-[10px] font-black text-brand uppercase tracking-widest rounded border border-gray-100 hover:bg-gray-100 transition">View Profile</button>
                    </td>
                </tr>

                <!-- Driver Row 2 -->
                <tr class="hover:bg-surface/20 transition-colors group">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-full bg-amber-50 flex items-center justify-center shrink-0 border border-amber-200 text-amber-500 font-bold">
                                OA
                            </div>
                            <div>
                                <p class="text-sm font-bold text-brand">Osei Appiah</p>
                                <p class="text-[10px] text-brand-muted uppercase tracking-widest mt-0.5 font-mono">DRV-1024</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <p class="text-xs font-bold text-brand">+233 20 987 6543</p>
                        <p class="text-[10px] text-brand-muted mt-0.5 truncate w-32">osei@example.com</p>
                    </td>
                    <td class="px-6 py-4">
                        <p class="text-xs font-bold text-gray-400 italic">Unassigned</p>
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-[10px] text-brand-muted italic">No ratings yet</span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-2">
                            <div class="w-1.5 h-1.5 rounded-full bg-amber-500"></div>
                            <span class="text-[10px] font-black uppercase text-amber-600 tracking-widest">Pending KYC</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <button class="px-3 py-1.5 bg-amber-50 text-[10px] font-black text-amber-600 uppercase tracking-widest rounded border border-amber-100 hover:bg-amber-100 transition">Review Docs</button>
                    </td>
                </tr>
                
                <!-- Driver Row 3 -->
                <tr class="hover:bg-surface/20 transition-colors group opacity-75">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-full bg-brand/10 flex items-center justify-center shrink-0 border border-brand/20 overflow-hidden grayscale">
                                <img src="https://i.pravatar.cc/150?u=3" alt="Avatar" class="w-full h-full object-cover">
                            </div>
                            <div>
                                <p class="text-sm font-bold text-brand">Emmanuel Yeboah</p>
                                <p class="text-[10px] text-brand-muted uppercase tracking-widest mt-0.5 font-mono">DRV-0841</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <p class="text-xs font-bold text-brand">+233 55 111 2233</p>
                        <p class="text-[10px] text-brand-muted mt-0.5 truncate w-32">emmanuel@example.com</p>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-gray-300"></span>
                            <div>
                                <p class="text-xs font-bold text-brand">Nissan Almera (2015)</p>
                                <p class="text-[10px] font-mono text-brand-muted mt-0.5">GR-1122-19</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-1">
                            <svg class="w-4 h-4 text-accent" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            <span class="text-xs font-bold text-brand">3.8</span>
                            <span class="text-[10px] text-brand-muted ml-1">(45 trips)</span>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-2">
                            <div class="w-1.5 h-1.5 rounded-full bg-red-500"></div>
                            <span class="text-[10px] font-black uppercase text-red-600 tracking-widest">Suspended</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <button class="px-3 py-1.5 bg-surface text-[10px] font-black text-brand uppercase tracking-widest rounded border border-gray-100 hover:bg-gray-100 transition">View Case</button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="p-4 border-t border-gray-50 flex items-center justify-between bg-surface/10">
        <p class="text-[10px] font-bold text-brand-muted uppercase tracking-widest">Showing 1 to 10 of 1,204 drivers</p>
        <div class="flex items-center gap-1">
            <button class="w-8 h-8 flex items-center justify-center rounded border border-gray-200 text-gray-400 hover:text-brand hover:border-brand transition"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg></button>
            <button class="w-8 h-8 flex items-center justify-center rounded bg-brand text-white font-bold text-xs">1</button>
            <button class="w-8 h-8 flex items-center justify-center rounded border border-gray-200 text-brand font-bold text-xs hover:bg-surface transition">2</button>
            <button class="w-8 h-8 flex items-center justify-center rounded border border-gray-200 text-brand font-bold text-xs hover:bg-surface transition">3</button>
            <span class="text-gray-400 px-2">...</span>
            <button class="w-8 h-8 flex items-center justify-center rounded border border-gray-200 text-gray-400 hover:text-brand hover:border-brand transition"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></button>
        </div>
    </div>
</div>

@endsection
