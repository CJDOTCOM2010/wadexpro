<?php

namespace App\Modules\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Admin\Models\NotificationTemplate;
use Illuminate\Http\Request;

class NotificationTemplateController extends Controller
{
    /**
     * Common event names for the select dropdown
     */
    protected array $events = [
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
        'custom' => 'Custom Event...'
    ];

    public function index()
    {
        $templates = NotificationTemplate::orderBy('event_name')->get();
        return view('admin.templates.index', compact('templates'));
    }

    public function create()
    {
        $events = $this->events;
        return view('admin.templates.create', compact('events'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'event_name' => 'required|string|max:100',
            'custom_event_name' => 'nullable|string|max:100|required_if:event_name,custom',
            'channel' => 'required|in:email,sms,whatsapp,push',
            'subject' => 'nullable|string|max:255|required_if:channel,email',
            'content' => 'required|string',
            'is_active' => 'boolean',
        ]);

        $eventName = $request->event_name === 'custom' ? $request->custom_event_name : $request->event_name;

        // Check for duplicates
        if (NotificationTemplate::where('event_name', $eventName)->where('channel', $request->channel)->exists()) {
            return back()->withInput()->with('error', "A template for {$eventName} via {$request->channel} already exists.");
        }

        NotificationTemplate::create([
            'event_name' => $eventName,
            'channel' => $request->channel,
            'subject' => $request->subject,
            'content' => $request->content,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('orchestrator.templates.index')->with('success', 'Notification template created successfully.');
    }

    public function edit(string $id)
    {
        $template = NotificationTemplate::findOrFail($id);
        $events = $this->events;
        
        // Add current event to list if not present
        if (!array_key_exists($template->event_name, $events)) {
            $events[$template->event_name] = $template->event_name;
        }

        return view('admin.templates.edit', compact('template', 'events'));
    }

    public function update(Request $request, string $id)
    {
        $template = NotificationTemplate::findOrFail($id);

        $request->validate([
            'subject' => 'nullable|string|max:255|required_if:channel,email',
            'content' => 'required|string',
            'is_active' => 'boolean',
        ]);

        $template->update([
            'subject' => $request->subject,
            'content' => $request->content,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('orchestrator.templates.index')->with('success', 'Notification template updated successfully.');
    }

    public function destroy(string $id)
    {
        $template = NotificationTemplate::findOrFail($id);
        $template->delete();

        return back()->with('success', 'Template removed successfully.');
    }

    public function toggle(string $id)
    {
        $template = NotificationTemplate::findOrFail($id);
        $template->update(['is_active' => !$template->is_active]);

        return back()->with('success', 'Template status toggled.');
    }
}
