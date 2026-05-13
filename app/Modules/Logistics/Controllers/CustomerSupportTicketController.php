<?php

namespace App\Modules\Logistics\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Support\Models\SupportTicket;
use Illuminate\Http\Request;

class CustomerSupportTicketController extends Controller
{
    /**
     * Get all support tickets for the authenticated customer.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $tickets = SupportTicket::where('user_id', $user->id)
            ->with(['replies' => function ($q) {
                $q->orderBy('created_at', 'ASC');
            }])
            ->orderBy('created_at', 'DESC')
            ->get();

        return response()->json([
            'message' => 'Support tickets retrieved.',
            'data'    => $tickets
        ]);
    }

    /**
     * Create a new support ticket.
     */
    public function store(Request $request)
    {
        $request->validate([
            'subject'  => 'required|string|max:255',
            'category' => 'required|string',
            'priority' => 'required|in:low,medium,high,urgent',
            'message'  => 'required|string',
        ]);

        $user = $request->user();

        $ticket = SupportTicket::create([
            'user_id'     => $user->id,
            'user_type'   => $user->user_type,
            'subject'     => $request->subject,
            'category'    => $request->category,
            'priority'    => $request->priority,
            'status'      => 'open',
        ]);

        // Create initial message
        $ticket->replies()->create([
            'sender_id'   => $user->id,
            'sender_type' => $user->user_type,
            'message'     => $request->message,
            'is_internal' => false,
        ]);

        $ticket->load('replies');

        return response()->json([
            'message' => 'Support ticket created successfully.',
            'data'    => $ticket
        ]);
    }

    /**
     * Reply to an existing support ticket.
     */
    public function reply(Request $request, $id)
    {
        $request->validate([
            'message' => 'required|string',
        ]);

        $user = $request->user();
        $ticket = SupportTicket::where('user_id', $user->id)->findOrFail($id);

        $reply = $ticket->replies()->create([
            'sender_id'   => $user->id,
            'sender_type' => $user->user_type,
            'message'     => $request->message,
            'is_internal' => false,
        ]);

        // Reopen ticket if it was closed
        if ($ticket->status === 'closed') {
            $ticket->update(['status' => 'open', 'updated_at' => now()]);
        } else {
            $ticket->touch();
        }

        return response()->json([
            'message' => 'Reply sent successfully.',
            'data'    => $reply
        ]);
    }
}
