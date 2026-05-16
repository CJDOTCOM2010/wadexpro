@extends('admin.layout')
@section('title', 'Notification Templates')
@section('content')

@php
$channelIcons = ['email' => 'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z', 'sms' => 'M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z', 'whatsapp' => 'M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z', 'push' => 'M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9'];
$channelLabels = ['email' => 'Email', 'sms' => 'SMS', 'whatsapp' => 'WhatsApp', 'push' => 'Push'];
$channelColors = ['email' => 'bg-blue-50 text-blue-700', 'sms' => 'bg-emerald-50 text-emerald-700', 'whatsapp' => 'bg-green-50 text-green-700', 'push' => 'bg-purple-50 text-purple-700'];
$events = [
    'notify_ride_booked' => 'Ride Booked',
    'notify_ride_assigned' => 'Driver Assigned',
    'notify_ride_completed' => 'Ride Completed',
    'notify_ride_cancelled' => 'Ride Cancelled',
    'notify_payment_received' => 'Payment Received',
    'notify_payout_approved' => 'Payout Approved',
    'notify_driver_approved' => 'Driver Approved',
    'notify_driver_rejected' => 'Driver Rejected',
    'notify_driver_suspended' => 'Driver Suspended',
    'notify_otp_login' => 'OTP Login',
    'notify_support_ticket_reply' => 'Support Ticket Reply',
    'notify_promo_applied' => 'Promo Code Applied',
    'notify_wallet_credited' => 'Wallet Credited',
    'notify_wallet_debited' => 'Wallet Debited',
    'notify_kyc_submitted' => 'KYC Submitted',
    'custom' => 'Custom Event...',
];
@endphp

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

<div x-data="templateManager()" class="max-w-6xl mx-auto">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-black text-brand tracking-tight">Notification Templates</h2>
            <p class="text-sm text-brand-muted font-medium mt-0.5">Manage dynamic content for Email, SMS, WhatsApp, and Push channels.</p>
        </div>
        <button @click="openCreate()" class="px-5 py-2.5 bg-brand text-white rounded-lg text-xs font-bold hover:bg-brand-light transition-colors flex items-center gap-2 shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            New Template
        </button>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 mb-6">
        <div class="bg-white border border-gray-100 rounded-xl p-4">
            <p class="text-lg font-black text-brand">{{ $templates->count() }}</p>
            <p class="text-[10px] font-bold text-brand-muted uppercase tracking-wider">Total Templates</p>
        </div>
        @foreach(['email' => 'text-blue-600', 'sms' => 'text-emerald-600', 'whatsapp' => 'text-green-600', 'push' => 'text-purple-600'] as $ch => $color)
        <div class="bg-white border border-gray-100 rounded-xl p-4">
            <p class="text-lg font-black text-brand {{ $color }}">{{ $templates->where('channel', $ch)->count() }}</p>
            <p class="text-[10px] font-bold text-brand-muted uppercase tracking-wider">{{ ucfirst($ch) }}</p>
        </div>
        @endforeach
    </div>

    {{-- Table --}}
    <div class="bg-white border border-gray-100 rounded-xl overflow-hidden">
        <div class="divide-y divide-gray-50">
            @forelse($templates as $template)
            <div class="px-5 py-4 hover:bg-surface/20 transition-colors">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-lg flex items-center justify-center shrink-0 {{ $channelColors[$template->channel] ?? 'bg-gray-50 text-gray-600' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $channelIcons[$template->channel] ?? '' }}"/></svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-bold text-brand truncate">{{ $template->event_name }}</p>
                        <p class="text-[11px] text-brand-muted font-mono mt-0.5">{{ $template->content ? Str::limit($template->content, 60) : 'No content' }}</p>
                    </div>
                    <div class="flex items-center gap-3 shrink-0">
                        <span class="px-2 py-0.5 text-[9px] font-bold rounded {{ $channelColors[$template->channel] ?? '' }} uppercase">{{ $channelLabels[$template->channel] ?? $template->channel }}</span>
                        <form action="{{ route('orchestrator.templates.toggle', $template->id) }}" method="POST" class="inline">
                            @csrf @method('PATCH')
                            <button type="submit" class="flex items-center gap-1.5 px-2 py-1 rounded-lg text-[10px] font-bold transition-colors {{ $template->is_active ? 'bg-green-50 text-green-700' : 'bg-gray-50 text-gray-500' }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $template->is_active ? 'bg-green-500' : 'bg-gray-400' }}"></span>
                                {{ $template->is_active ? 'Active' : 'Inactive' }}
                            </button>
                        </form>
                        <button @click="openEdit('{{ $template->id }}', '{{ $template->event_name }}', '{{ $template->channel }}', '{{ addslashes($template->subject ?? '') }}', '{{ addslashes($template->content) }}', '{{ $template->is_active ? '1' : '0' }}')" class="w-7 h-7 rounded-lg flex items-center justify-center text-gray-400 hover:text-brand hover:bg-surface transition-colors" title="Edit">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                        </button>
                        <button @click="confirmDelete('{{ $template->id }}', '{{ $template->event_name }}')" class="w-7 h-7 rounded-lg flex items-center justify-center text-gray-300 hover:text-red-500 hover:bg-red-50 transition-colors" title="Delete">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </div>
                </div>
            </div>
            @empty
            <div class="flex flex-col items-center justify-center py-16 text-brand-muted">
                <svg class="w-14 h-14 mb-3 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                <p class="text-sm font-bold">No templates configured</p>
                <p class="text-xs mt-1">Create your first notification template to get started.</p>
            </div>
            @endforelse
        </div>
    </div>

    {{-- Create/Edit Modal --}}
    <div x-show="showForm" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-brand/50 backdrop-blur-sm" @click="showForm = false"></div>
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-xl relative z-10 max-h-[90vh] overflow-y-auto" @click.outside="showForm = false">
            <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between sticky top-0 bg-white">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 bg-accent/10 rounded-lg flex items-center justify-center text-accent">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-brand" x-text="form.id ? 'Edit Template' : 'New Template'"></h3>
                        <p class="text-xs text-brand-muted" x-text="form.id ? 'Update notification content.' : 'Create a new notification template.'"></p>
                    </div>
                </div>
                <button @click="showForm = false" class="w-7 h-7 bg-surface rounded-lg flex items-center justify-center text-brand-muted hover:text-brand transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form :action="form.action" method="POST" class="p-6 space-y-4">
                @csrf
                <input type="hidden" name="_method" x-model="form.method">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-[10px] font-bold text-brand-muted uppercase tracking-wider mb-1.5 block">Channel <span class="text-red-500">*</span></label>
                        <select name="channel" x-model="form.channel" :disabled="!!form.id" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-medium outline-none focus:ring-2 focus:ring-accent/20 transition-shadow">
                            <option value="email">Email</option>
                            <option value="sms">SMS</option>
                            <option value="whatsapp">WhatsApp</option>
                            <option value="push">Push Notification</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-[10px] font-bold text-brand-muted uppercase tracking-wider mb-1.5 block">Event <span class="text-red-500">*</span></label>
                        <select name="event_name" x-model="form.event_name" :disabled="!!form.id" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-medium outline-none focus:ring-2 focus:ring-accent/20 transition-shadow">
                            <option value="">Select Event</option>
                            @foreach($events as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div x-show="form.event_name === 'custom' && !form.id">
                    <label class="text-[10px] font-bold text-brand-muted uppercase tracking-wider mb-1.5 block">Custom Event Name</label>
                    <input type="text" name="custom_event_name" placeholder="e.g. notify_holiday_greeting" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-medium outline-none focus:ring-2 focus:ring-accent/20 transition-shadow">
                </div>
                <div x-show="form.channel === 'email'">
                    <label class="text-[10px] font-bold text-brand-muted uppercase tracking-wider mb-1.5 block">Subject Line</label>
                    <input type="text" name="subject" x-model="form.subject" placeholder="WADEXPRO: Update on your account" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-medium outline-none focus:ring-2 focus:ring-accent/20 transition-shadow">
                </div>
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="text-[10px] font-bold text-brand-muted uppercase tracking-wider">Content <span class="text-red-500">*</span></label>
                        <button type="button" @click="showVars = !showVars" class="text-[10px] font-bold text-accent hover:underline">Placeholders</button>
                    </div>
                    <div x-show="showVars" class="mb-3 p-3 bg-surface rounded-lg border border-gray-100">
                        <p class="text-[10px] font-bold text-brand mb-2">Available variables:</p>
                        <div class="flex flex-wrap gap-1.5">
                            @foreach(['user_name', 'amount', 'ride_id', 'driver_name', 'otp_code', 'date', 'time', 'reference'] as $var)
                            <code class="text-[9px] px-1.5 py-0.5 bg-white border border-gray-200 rounded text-brand-muted select-all cursor-pointer" @click="insertVar('{{ $var }}')">{ {{ $var }} }</code>
                            @endforeach
                        </div>
                    </div>
                    <textarea name="content" x-model="form.content" rows="6" placeholder="Hello {user_name},&#10;&#10;Your ride {ride_id} is on the way!" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-medium font-mono outline-none focus:ring-2 focus:ring-accent/20 transition-shadow resize-none"></textarea>
                </div>
                <label class="flex items-center gap-3 p-3.5 bg-surface rounded-lg cursor-pointer hover:bg-accent/5 transition-colors">
                    <input type="checkbox" name="is_active" value="1" x-model="form.is_active" class="w-4 h-4 rounded border-gray-300 text-accent focus:ring-accent/30">
                    <div>
                        <span class="text-sm font-bold text-brand">Active</span>
                        <p class="text-[10px] text-brand-muted">Template will be used when the event triggers.</p>
                    </div>
                </label>
                <div class="flex justify-end gap-2 pt-2 border-t border-gray-100">
                    <button type="button" @click="showForm = false" class="px-4 py-2 text-xs font-bold text-brand-muted hover:text-brand transition-colors">Cancel</button>
                    <button type="submit" class="px-5 py-2 bg-brand text-white rounded-lg text-xs font-bold hover:bg-brand-light transition-colors" x-text="form.id ? 'Update Template' : 'Create Template'"></button>
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
                    <h3 class="text-lg font-bold text-brand text-center mb-2">Delete Template?</h3>
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
function templateManager() {
    return {
        showForm: false, showVars: false,
        form: { id: null, action: '', method: 'POST', event_name: '', channel: 'email', subject: '', content: '', is_active: true },
        deleteStep: 0, deleteId: '', deleteLabel: '', deleteConfirm: '',
        openCreate() {
            this.form = { id: null, action: '{{ route('orchestrator.templates.store') }}', method: 'POST', event_name: '', channel: 'email', subject: '', content: '', is_active: true };
            this.showForm = true; this.showVars = false;
        },
        openEdit(id, eventName, channel, subject, content, isActive) {
            this.form = { id, action: '/orchestrator/settings/templates/' + id, method: 'PUT', event_name: eventName, channel, subject, content, is_active: isActive === '1' };
            this.showForm = true; this.showVars = false;
        },
        insertVar(name) {
            this.form.content += '{' + name + '}';
        },
        confirmDelete(id, label) { this.deleteId = id; this.deleteLabel = label; this.deleteStep = 1; this.deleteConfirm = ''; },
        closeDelete() { this.deleteStep = 0; this.deleteConfirm = ''; },
        executeDelete() {
            if (this.deleteConfirm !== 'DELETE') return;
            const f = document.createElement('form'); f.method = 'POST'; f.action = '/orchestrator/settings/templates/' + this.deleteId;
            const c = document.createElement('input'); c.type = 'hidden'; c.name = '_token'; c.value = '{{ csrf_token() }}'; f.appendChild(c);
            const m = document.createElement('input'); m.type = 'hidden'; m.name = '_method'; m.value = 'DELETE'; f.appendChild(m);
            document.body.appendChild(f); f.submit();
        }
    };
}
</script>
<style>[x-cloak] { display: none !important; }</style>
@endsection