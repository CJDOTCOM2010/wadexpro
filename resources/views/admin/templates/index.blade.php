@extends('admin.layout')
@section('title', 'Notification Templates')
@section('content')

<div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
        <h2 class="text-2xl font-black text-brand tracking-tight">Notification Templates</h2>
        <p class="text-brand-muted font-medium mt-1 text-sm">Manage dynamic content for Email, SMS, WhatsApp, and Push channels.</p>
    </div>
    <a href="{{ route('orchestrator.templates.create') }}" class="px-6 py-3 bg-brand text-white font-bold rounded-lg hover:bg-brand-light transition flex items-center gap-2 whitespace-nowrap self-start">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        New Template
    </a>
</div>

@if(session('success'))
<div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-lg font-bold text-sm">
    {{ session('success') }}
</div>
@endif

@if(session('error'))
<div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg font-bold text-sm">
    {{ session('error') }}
</div>
@endif

<div class="bg-white rounded-lg border border-gray-100 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-surface/30 border-b border-gray-100">
                    <th class="px-6 py-4 text-[11px] font-black text-brand-muted uppercase tracking-widest">Event Trigger</th>
                    <th class="px-6 py-4 text-[11px] font-black text-brand-muted uppercase tracking-widest">Channel</th>
                    <th class="px-6 py-4 text-[11px] font-black text-brand-muted uppercase tracking-widest">Status</th>
                    <th class="px-6 py-4 text-[11px] font-black text-brand-muted uppercase tracking-widest text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($templates as $template)
                <tr class="hover:bg-surface/30 transition group">
                    <td class="px-6 py-4">
                        <span class="font-bold text-brand">{{ $template->event_name }}</span>
                    </td>
                    <td class="px-6 py-4">
                        @if($template->channel == 'email')
                            <span class="px-2.5 py-1 bg-blue-50 text-blue-600 rounded-md text-xs font-bold uppercase">📧 Email</span>
                        @elseif($template->channel == 'sms')
                            <span class="px-2.5 py-1 bg-emerald-50 text-emerald-600 rounded-md text-xs font-bold uppercase">💬 SMS</span>
                        @elseif($template->channel == 'whatsapp')
                            <span class="px-2.5 py-1 bg-green-50 text-green-600 rounded-md text-xs font-bold uppercase">📱 WhatsApp</span>
                        @else
                            <span class="px-2.5 py-1 bg-purple-50 text-purple-600 rounded-md text-xs font-bold uppercase">🔔 Push</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <form action="{{ route('orchestrator.templates.toggle', $template->id) }}" method="POST">
                            @csrf @method('PATCH')
                            <button type="submit" class="flex items-center gap-2 px-3 py-1.5 rounded-lg border transition {{ $template->is_active ? 'border-green-200 bg-green-50 text-green-700 hover:bg-green-100' : 'border-gray-200 bg-gray-50 text-gray-500 hover:bg-gray-100' }}">
                                <div class="w-2 h-2 rounded-full {{ $template->is_active ? 'bg-green-500' : 'bg-gray-400' }}"></div>
                                <span class="text-xs font-black uppercase tracking-wider">{{ $template->is_active ? 'Active' : 'Inactive' }}</span>
                            </button>
                        </form>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-3">
                            <a href="{{ route('orchestrator.templates.edit', $template->id) }}" class="p-2 text-gray-400 hover:text-accent transition rounded-lg hover:bg-accent/10" title="Edit Template">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                            </a>
                            <form action="{{ route('orchestrator.templates.destroy', $template->id) }}" method="POST" onsubmit="return confirm('Delete this template permanently?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-2 text-gray-400 hover:text-red-500 transition rounded-lg hover:bg-red-50" title="Delete Template">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                        <div class="w-16 h-16 bg-surface rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                        </div>
                        <p class="font-bold text-brand">No templates configured</p>
                        <p class="text-sm text-brand-muted mt-1">Create your first notification template to get started.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
