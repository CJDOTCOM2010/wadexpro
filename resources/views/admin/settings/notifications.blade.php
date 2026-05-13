@extends('admin.layout')
@section('title', 'Communication & Notification Channels')
@section('content')
<div class="max-w-6xl mx-auto">
    <div class="mb-10">
        <h2 class="text-3xl font-black text-brand tracking-tighter">Communication Infrastructure</h2>
        <p class="text-brand-muted font-medium mt-1">Configure Email, SMS, WhatsApp, Push Notifications, and In-Call channels without touching code.</p>
    </div>

    @if(session('success'))<div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-lg font-bold text-sm">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg font-bold text-sm">{{ session('error') }}</div>@endif

    {{-- Channel Toggle Bar --}}
    <form action="{{ route('orchestrator.settings.notifications.update') }}" method="POST">
    @csrf
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-10">
        @foreach(['email'=>'Email','sms'=>'SMS','whatsapp'=>'WhatsApp','push'=>'Push','incall'=>'In-Call'] as $ch=>$label)
        <label class="bg-white border border-gray-100 rounded-lg p-4 shadow-sm flex items-center gap-3 cursor-pointer hover:shadow-md transition group">
            <input type="checkbox" name="channel_{{ $ch }}_enabled" value="1" {{ ($channels[$ch] ?? false) ? 'checked' : '' }}
                class="w-5 h-5 rounded text-accent focus:ring-accent/30">
            <span class="font-black text-sm text-brand group-hover:text-accent transition">{{ $label }}</span>
        </label>
        @endforeach
    </div>

    {{-- Tabs --}}
    <div x-data="{ tab: 'email' }" class="mb-6">
        <div class="flex flex-wrap gap-2 mb-8 border-b border-gray-100 pb-4">
            @foreach(['email'=>'📧 Email','sms'=>'💬 SMS','whatsapp'=>'📱 WhatsApp','push'=>'🔔 Push','incall'=>'📞 In-Call','events'=>'⚡ Events'] as $t=>$lbl)
            <button type="button" @click="tab='{{ $t }}'" :class="tab==='{{ $t }}' ? 'bg-brand text-white' : 'bg-white text-brand border border-gray-100'"
                class="px-5 py-2.5 rounded-lg font-black text-xs uppercase tracking-wider transition hover:shadow-md">{{ $lbl }}</button>
            @endforeach
        </div>

        {{-- ═══ EMAIL TAB ═══ --}}
        <div x-show="tab==='email'" x-cloak>
            <div class="bg-white rounded-lg border border-gray-100 shadow-sm p-6 mb-6">
                <h3 class="text-lg font-black text-brand mb-6 flex items-center gap-2">📧 Email Gateway Configuration</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-2">Provider</label>
                        <select name="email_provider" class="w-full border border-gray-200 rounded-lg px-4 py-3 text-sm font-bold focus:ring-2 focus:ring-accent/20 outline-none">
                            @foreach(['smtp'=>'SMTP','mailgun'=>'Mailgun','sendgrid'=>'SendGrid','ses'=>'Amazon SES','postmark'=>'Postmark'] as $v=>$l)
                            <option value="{{ $v }}" {{ ($settings['email_provider'] ?? '') == $v ? 'selected' : '' }}>{{ $l }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-2">From Address</label>
                        <input type="email" name="email_from_address" value="{{ $settings['email_from_address'] ?? '' }}" placeholder="noreply@wadexpro.com"
                            class="w-full border border-gray-200 rounded-lg px-4 py-3 text-sm font-medium focus:ring-2 focus:ring-accent/20 outline-none">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-2">From Name</label>
                        <input type="text" name="email_from_name" value="{{ $settings['email_from_name'] ?? '' }}" placeholder="WADEXPRO"
                            class="w-full border border-gray-200 rounded-lg px-4 py-3 text-sm font-medium focus:ring-2 focus:ring-accent/20 outline-none">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-2">SMTP Encryption</label>
                        <select name="smtp_encryption" class="w-full border border-gray-200 rounded-lg px-4 py-3 text-sm font-bold focus:ring-2 focus:ring-accent/20 outline-none">
                            @foreach(['tls'=>'TLS','ssl'=>'SSL',''=>'None'] as $v=>$l)
                            <option value="{{ $v }}" {{ ($settings['smtp_encryption'] ?? 'tls') == $v ? 'selected' : '' }}>{{ $l }}</option>
                            @endforeach
                        </select>
                    </div>
                    @foreach(['smtp_host'=>'SMTP Host','smtp_port'=>'SMTP Port','smtp_username'=>'SMTP Username'] as $k=>$l)
                    <div>
                        <label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-2">{{ $l }}</label>
                        <input type="text" name="{{ $k }}" value="{{ $settings[$k] ?? '' }}" placeholder="{{ $l }}"
                            class="w-full border border-gray-200 rounded-lg px-4 py-3 text-sm font-medium focus:ring-2 focus:ring-accent/20 outline-none">
                    </div>
                    @endforeach
                    <div>
                        <label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-2">SMTP Password 🔒</label>
                        <input type="password" name="smtp_password" value="{{ $settings['smtp_password'] ?? '' }}" placeholder="••••••••"
                            class="w-full border border-gray-200 rounded-lg px-4 py-3 text-sm font-medium focus:ring-2 focus:ring-accent/20 outline-none bg-amber-50/30">
                    </div>
                    @foreach(['mailgun_domain'=>'Mailgun Domain','mailgun_secret'=>'Mailgun Secret 🔒','sendgrid_api_key'=>'SendGrid API Key 🔒','ses_key'=>'SES Key 🔒','ses_secret'=>'SES Secret 🔒','ses_region'=>'SES Region','postmark_token'=>'Postmark Token 🔒'] as $k=>$l)
                    <div>
                        <label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-2">{{ $l }}</label>
                        <input type="{{ str_contains($l, '🔒') ? 'password' : 'text' }}" name="{{ $k }}" value="{{ $settings[$k] ?? '' }}" placeholder="{{ str_replace(' 🔒', '', $l) }}"
                            class="w-full border border-gray-200 rounded-lg px-4 py-3 text-sm font-medium focus:ring-2 focus:ring-accent/20 outline-none {{ str_contains($l, '🔒') ? 'bg-amber-50/30' : '' }}">
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- ═══ SMS TAB ═══ --}}
        <div x-show="tab==='sms'" x-cloak>
            <div class="bg-white rounded-lg border border-gray-100 shadow-sm p-6 mb-6">
                <h3 class="text-lg font-black text-brand mb-6">💬 SMS Gateway Configuration</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-2">Provider</label>
                        <select name="sms_provider" class="w-full border border-gray-200 rounded-lg px-4 py-3 text-sm font-bold focus:ring-2 focus:ring-accent/20 outline-none">
                            @foreach(['twilio'=>'Twilio','vonage'=>'Vonage (Nexmo)','arkesel'=>'Arkesel','termii'=>'Termii','africas_talking'=>"Africa's Talking"] as $v=>$l)
                            <option value="{{ $v }}" {{ ($settings['sms_provider'] ?? '') == $v ? 'selected' : '' }}>{{ $l }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-2">Sender ID</label>
                        <input type="text" name="sms_sender_id" value="{{ $settings['sms_sender_id'] ?? '' }}" placeholder="WADEXPRO"
                            class="w-full border border-gray-200 rounded-lg px-4 py-3 text-sm font-medium focus:ring-2 focus:ring-accent/20 outline-none">
                    </div>
                    @foreach(['twilio_account_sid'=>'Twilio Account SID','twilio_auth_token'=>'Twilio Auth Token 🔒','twilio_from'=>'Twilio From Number','vonage_api_key'=>'Vonage API Key','vonage_api_secret'=>'Vonage API Secret 🔒','arkesel_api_key'=>'Arkesel API Key 🔒','termii_api_key'=>'Termii API Key 🔒','termii_sender_id'=>'Termii Sender ID','africas_talking_username'=>"AT Username",'africas_talking_api_key'=>"AT API Key 🔒"] as $k=>$l)
                    <div>
                        <label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-2">{{ $l }}</label>
                        <input type="{{ str_contains($l, '🔒') ? 'password' : 'text' }}" name="{{ $k }}" value="{{ $settings[$k] ?? '' }}" placeholder="{{ str_replace(' 🔒', '', $l) }}"
                            class="w-full border border-gray-200 rounded-lg px-4 py-3 text-sm font-medium focus:ring-2 focus:ring-accent/20 outline-none {{ str_contains($l, '🔒') ? 'bg-amber-50/30' : '' }}">
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- ═══ WHATSAPP TAB ═══ --}}
        <div x-show="tab==='whatsapp'" x-cloak>
            <div class="bg-white rounded-lg border border-gray-100 shadow-sm p-6 mb-6">
                <h3 class="text-lg font-black text-brand mb-6">📱 WhatsApp Business Configuration</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-2">Provider</label>
                        <select name="whatsapp_provider" class="w-full border border-gray-200 rounded-lg px-4 py-3 text-sm font-bold focus:ring-2 focus:ring-accent/20 outline-none">
                            @foreach(['meta'=>'Meta (Official API)','twilio'=>'Twilio WhatsApp','360dialog'=>'360dialog'] as $v=>$l)
                            <option value="{{ $v }}" {{ ($settings['whatsapp_provider'] ?? '') == $v ? 'selected' : '' }}>{{ $l }}</option>
                            @endforeach
                        </select>
                    </div>
                    @foreach(['whatsapp_phone_number_id'=>'Phone Number ID','whatsapp_access_token'=>'Access Token 🔒','whatsapp_business_account_id'=>'Business Account ID','whatsapp_webhook_verify_token'=>'Webhook Verify Token','whatsapp_twilio_account_sid'=>'Twilio Account SID','whatsapp_twilio_auth_token'=>'Twilio Auth Token 🔒','whatsapp_twilio_number'=>'Twilio WhatsApp Number','whatsapp_360dialog_api_key'=>'360dialog API Key 🔒'] as $k=>$l)
                    <div>
                        <label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-2">{{ $l }}</label>
                        <input type="{{ str_contains($l, '🔒') ? 'password' : 'text' }}" name="{{ $k }}" value="{{ $settings[$k] ?? '' }}" placeholder="{{ str_replace(' 🔒', '', $l) }}"
                            class="w-full border border-gray-200 rounded-lg px-4 py-3 text-sm font-medium focus:ring-2 focus:ring-accent/20 outline-none {{ str_contains($l, '🔒') ? 'bg-amber-50/30' : '' }}">
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- ═══ PUSH TAB ═══ --}}
        <div x-show="tab==='push'" x-cloak>
            <div class="bg-white rounded-lg border border-gray-100 shadow-sm p-6 mb-6">
                <h3 class="text-lg font-black text-brand mb-6">🔔 Push Notification Configuration</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-2">Provider</label>
                        <select name="push_provider" class="w-full border border-gray-200 rounded-lg px-4 py-3 text-sm font-bold focus:ring-2 focus:ring-accent/20 outline-none">
                            @foreach(['fcm'=>'Firebase Cloud Messaging','apns'=>'Apple Push (APNs)','onesignal'=>'OneSignal'] as $v=>$l)
                            <option value="{{ $v }}" {{ ($settings['push_provider'] ?? '') == $v ? 'selected' : '' }}>{{ $l }}</option>
                            @endforeach
                        </select>
                    </div>
                    @foreach(['fcm_server_key'=>'FCM Server Key 🔒','fcm_project_id'=>'FCM Project ID','apns_key_id'=>'APNs Key ID','apns_team_id'=>'APNs Team ID','apns_p8_private_key'=>'APNs P8 Key 🔒','apns_bundle_id'=>'APNs Bundle ID','onesignal_app_id'=>'OneSignal App ID','onesignal_api_key'=>'OneSignal API Key 🔒'] as $k=>$l)
                    <div>
                        <label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-2">{{ $l }}</label>
                        <input type="{{ str_contains($l, '🔒') ? 'password' : 'text' }}" name="{{ $k }}" value="{{ $settings[$k] ?? '' }}" placeholder="{{ str_replace(' 🔒', '', $l) }}"
                            class="w-full border border-gray-200 rounded-lg px-4 py-3 text-sm font-medium focus:ring-2 focus:ring-accent/20 outline-none {{ str_contains($l, '🔒') ? 'bg-amber-50/30' : '' }}">
                    </div>
                    @endforeach
                    <div class="md:col-span-2">
                        <label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-2">FCM Service Account JSON 🔒</label>
                        <textarea name="fcm_service_account_json" rows="4" placeholder='{"type":"service_account",...}'
                            class="w-full border border-gray-200 rounded-lg px-4 py-3 text-sm font-mono focus:ring-2 focus:ring-accent/20 outline-none bg-amber-50/30">{{ $settings['fcm_service_account_json'] ?? '' }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        {{-- ═══ IN-CALL TAB ═══ --}}
        <div x-show="tab==='incall'" x-cloak>
            <div class="bg-white rounded-lg border border-gray-100 shadow-sm p-6 mb-6">
                <h3 class="text-lg font-black text-brand mb-6">📞 In-App Voice/Video Call Configuration</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-2">Provider</label>
                        <select name="incall_provider" class="w-full border border-gray-200 rounded-lg px-4 py-3 text-sm font-bold focus:ring-2 focus:ring-accent/20 outline-none">
                            @foreach(['agora'=>'Agora','twilio'=>'Twilio Voice','vonage_video'=>'Vonage Video'] as $v=>$l)
                            <option value="{{ $v }}" {{ ($settings['incall_provider'] ?? '') == $v ? 'selected' : '' }}>{{ $l }}</option>
                            @endforeach
                        </select>
                    </div>
                    @foreach(['agora_app_id'=>'Agora App ID','agora_app_certificate'=>'Agora Certificate 🔒','vonage_video_api_key'=>'Vonage Video API Key','vonage_video_api_secret'=>'Vonage Video Secret 🔒','incall_twilio_account_sid'=>'Twilio Account SID','incall_twilio_auth_token'=>'Twilio Auth Token 🔒','incall_twilio_twiml_app_sid'=>'TwiML App SID'] as $k=>$l)
                    <div>
                        <label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-2">{{ $l }}</label>
                        <input type="{{ str_contains($l, '🔒') ? 'password' : 'text' }}" name="{{ $k }}" value="{{ $settings[$k] ?? '' }}" placeholder="{{ str_replace(' 🔒', '', $l) }}"
                            class="w-full border border-gray-200 rounded-lg px-4 py-3 text-sm font-medium focus:ring-2 focus:ring-accent/20 outline-none {{ str_contains($l, '🔒') ? 'bg-amber-50/30' : '' }}">
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- ═══ EVENTS TAB ═══ --}}
        <div x-show="tab==='events'" x-cloak>
        </div>

        {{-- Save Button (for all tabs except events) --}}
        <div x-show="tab!=='events'" class="mt-8">
            <button type="submit" class="px-8 py-3.5 bg-brand text-white font-black rounded-lg hover:bg-brand-light transition shadow-lg hover:shadow-xl text-sm uppercase tracking-widest">
                💾 Synchronize Communication Channels
            </button>
        </div>
    </div>
    </form>

    {{-- Events Form (separate) --}}
    <div x-data="{ tab: 'events' }" x-show="false"><!-- dummy to scope --></div>
    <form action="{{ route('orchestrator.settings.notifications.events') }}" method="POST" id="eventsForm" class="hidden">
        @csrf
        <div class="bg-white rounded-lg border border-gray-100 shadow-sm p-6">
            <h3 class="text-lg font-black text-brand mb-6">⚡ Notification Event Triggers</h3>
            <p class="text-sm text-brand-muted mb-6">Enable or disable notifications for specific platform events.</p>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach(['notify_ride_booked'=>'Ride Booked','notify_ride_assigned'=>'Driver Assigned','notify_ride_completed'=>'Ride Completed','notify_ride_cancelled'=>'Ride Cancelled','notify_payment_received'=>'Payment Received','notify_payout_approved'=>'Payout Approved','notify_driver_approved'=>'Driver Approved','notify_driver_rejected'=>'Driver Rejected','notify_driver_suspended'=>'Driver Suspended','notify_otp_login'=>'OTP Login','notify_support_ticket_reply'=>'Support Reply','notify_promo_applied'=>'Promo Applied','notify_wallet_credited'=>'Wallet Credited','notify_wallet_debited'=>'Wallet Debited','notify_kyc_submitted'=>'KYC Submitted'] as $ek=>$el)
                <label class="flex items-center gap-3 bg-surface/30 rounded-lg p-3 hover:bg-surface/60 transition cursor-pointer">
                    <input type="checkbox" name="events[{{ $ek }}]" value="true" {{ ($events[$ek] ?? 'false') === 'true' ? 'checked' : '' }}
                        class="w-4 h-4 rounded text-accent focus:ring-accent/30">
                    <span class="text-sm font-bold text-brand">{{ $el }}</span>
                </label>
                @endforeach
            </div>
            <div class="mt-6">
                <button type="submit" class="px-8 py-3.5 bg-accent text-white font-black rounded-lg hover:bg-accent-light transition shadow-lg text-sm uppercase tracking-widest">
                    ⚡ Save Event Triggers
                </button>
            </div>
        </div>
    </form>

    {{-- Test Channel Panel --}}
    <div class="mt-10 bg-white rounded-lg border border-gray-100 shadow-sm p-6">
        <h3 class="text-lg font-black text-brand mb-4">🧪 Channel Test Console</h3>
        <p class="text-sm text-brand-muted mb-6">Send a test message through any configured channel to verify connectivity.</p>
        <form action="{{ route('orchestrator.settings.notifications.test') }}" method="POST" class="flex flex-wrap items-end gap-4">
            @csrf
            <div>
                <label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-2">Channel</label>
                <select name="channel" class="border border-gray-200 rounded-lg px-4 py-3 text-sm font-bold focus:ring-2 focus:ring-accent/20 outline-none">
                    <option value="email">Email</option>
                    <option value="sms">SMS</option>
                    <option value="whatsapp">WhatsApp</option>
                    <option value="push">Push (Validate Only)</option>
                </select>
            </div>
            <div class="flex-1 min-w-[200px]">
                <label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-2">Target (Email or Phone)</label>
                <input type="text" name="target" placeholder="user@example.com or +233XXXXXXXXX"
                    class="w-full border border-gray-200 rounded-lg px-4 py-3 text-sm font-medium focus:ring-2 focus:ring-accent/20 outline-none">
            </div>
            <button type="submit" class="px-6 py-3 bg-emerald-600 text-white font-black rounded-lg hover:bg-emerald-700 transition text-sm uppercase tracking-widest">
                🚀 Send Test
            </button>
        </form>
    </div>
</div>

{{-- Alpine.js tab-events sync script --}}
<script>
document.addEventListener('alpine:init', () => {
    // Show events form when events tab is active
    const eventsForm = document.getElementById('eventsForm');
    if (eventsForm) {
        const observer = new MutationObserver(() => {
            // Use Alpine's reactivity through x-effect instead
        });
    }
});
// Simple tab-events form toggle
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[\\@click]').forEach(btn => {
        btn.addEventListener('click', () => {
            const ef = document.getElementById('eventsForm');
            if (ef) {
                setTimeout(() => {
                    ef.classList.toggle('hidden', !btn.textContent.includes('Events'));
                }, 50);
            }
        });
    });
});
</script>
@endsection
