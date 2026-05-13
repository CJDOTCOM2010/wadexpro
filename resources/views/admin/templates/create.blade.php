@extends('admin.layout')
@section('title', 'Create Notification Template')
@section('content')

<div class="max-w-4xl mx-auto">
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-black text-brand tracking-tight">New Notification Template</h2>
            <p class="text-brand-muted font-medium mt-1 text-sm">Design dynamic communication content with placeholder variables.</p>
        </div>
        <a href="{{ route('orchestrator.templates.index') }}" class="text-brand-muted hover:text-brand font-bold text-sm flex items-center gap-2 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Back to Templates
        </a>
    </div>

    @if($errors->any())
    <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm">
        <ul class="list-disc pl-5 font-medium">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif
    
    @if(session('error'))
    <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm font-bold">
        {{ session('error') }}
    </div>
    @endif

    <div class="bg-white rounded-lg border border-gray-100 shadow-sm p-6 lg:p-8" x-data="{ channel: 'email', eventName: '' }">
        <form action="{{ route('orchestrator.templates.store') }}" method="POST">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Channel Selection -->
                <div>
                    <label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-2">Delivery Channel <span class="text-red-500">*</span></label>
                    <select name="channel" x-model="channel" class="w-full border border-gray-200 rounded-lg px-4 py-3 text-sm font-bold focus:ring-2 focus:ring-accent/20 outline-none transition">
                        <option value="email">📧 Email</option>
                        <option value="sms">💬 SMS</option>
                        <option value="whatsapp">📱 WhatsApp</option>
                        <option value="push">🔔 Push Notification</option>
                    </select>
                </div>

                <!-- Event Selection -->
                <div>
                    <label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-2">System Event Trigger <span class="text-red-500">*</span></label>
                    <select name="event_name" x-model="eventName" class="w-full border border-gray-200 rounded-lg px-4 py-3 text-sm font-bold focus:ring-2 focus:ring-accent/20 outline-none transition">
                        <option value="">-- Select Event --</option>
                        @foreach($events as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Custom Event Name (only shows if custom is selected) -->
            <div x-show="eventName === 'custom'" x-cloak class="mb-6">
                <label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-2">Custom Event Identifier <span class="text-red-500">*</span></label>
                <input type="text" name="custom_event_name" placeholder="e.g. notify_holiday_greeting" 
                    class="w-full border border-gray-200 rounded-lg px-4 py-3 text-sm font-medium focus:ring-2 focus:ring-accent/20 outline-none transition">
                <p class="text-[11px] text-gray-400 mt-1">Use lowercase letters and underscores only.</p>
            </div>

            <!-- Subject (only shows for email) -->
            <div x-show="channel === 'email'" x-cloak class="mb-6">
                <label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-2">Email Subject Line <span class="text-red-500">*</span></label>
                <input type="text" name="subject" value="{{ old('subject') }}" placeholder="WADEXPRO: Update on your account" 
                    class="w-full border border-gray-200 rounded-lg px-4 py-3 text-sm font-medium focus:ring-2 focus:ring-accent/20 outline-none transition">
            </div>

            <!-- Content Area -->
            <div class="mb-6">
                <div class="flex items-center justify-between mb-2">
                    <label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest">Message Content <span class="text-red-500">*</span></label>
                    <button type="button" @click="$refs.vars.classList.toggle('hidden')" class="text-[10px] font-black text-accent uppercase tracking-widest hover:underline">
                        View Variables
                    </button>
                </div>
                
                <div x-ref="vars" class="hidden mb-3 p-4 bg-surface rounded-lg border border-gray-100">
                    <p class="text-xs font-bold text-brand mb-2">Available Placeholders:</p>
                    <div class="flex flex-wrap gap-2">
                        <code class="text-[10px] px-2 py-1 bg-white border border-gray-200 rounded text-brand-muted select-all">{user_name}</code>
                        <code class="text-[10px] px-2 py-1 bg-white border border-gray-200 rounded text-brand-muted select-all">{amount}</code>
                        <code class="text-[10px] px-2 py-1 bg-white border border-gray-200 rounded text-brand-muted select-all">{ride_id}</code>
                        <code class="text-[10px] px-2 py-1 bg-white border border-gray-200 rounded text-brand-muted select-all">{driver_name}</code>
                        <code class="text-[10px] px-2 py-1 bg-white border border-gray-200 rounded text-brand-muted select-all">{otp_code}</code>
                        <code class="text-[10px] px-2 py-1 bg-white border border-gray-200 rounded text-brand-muted select-all">{date}</code>
                    </div>
                </div>

                <textarea name="content" rows="8" placeholder="Hello {user_name},&#10;&#10;Your ride {ride_id} is on the way!" 
                    class="w-full border border-gray-200 rounded-lg px-4 py-3 text-sm font-medium focus:ring-2 focus:ring-accent/20 outline-none transition">{{ old('content') }}</textarea>
            </div>

            <div class="mb-8">
                <label class="flex items-center gap-3 cursor-pointer group">
                    <input type="checkbox" name="is_active" value="1" checked class="w-5 h-5 rounded text-accent focus:ring-accent/30">
                    <div>
                        <span class="block text-sm font-bold text-brand group-hover:text-accent transition">Activate Template</span>
                        <span class="block text-xs text-brand-muted mt-0.5">Template will be used immediately when the event triggers.</span>
                    </div>
                </label>
            </div>

            <div class="flex justify-end pt-6 border-t border-gray-50">
                <button type="submit" class="px-8 py-3.5 bg-brand text-white font-black rounded-lg hover:bg-brand-light transition shadow-lg text-sm uppercase tracking-widest">
                    Create Template
                </button>
            </div>
        </form>
    </div>
</div>

@endsection
