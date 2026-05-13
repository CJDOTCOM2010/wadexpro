<?php

namespace App\Modules\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Support\Models\SupportTicket;
use App\Modules\Support\Models\TicketReply;
use App\Models\User;
use Illuminate\Http\Request;

class SupportTicketController extends Controller
{
    /**
     * Ticket Inbox — filterable list of all tickets.
     */
    public function index(Request $request)
    {
        $query = SupportTicket::with(['user', 'assignedTo'])
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('subject', 'like', "%{$search}%")
                  ->orWhere('ticket_number', 'like', "%{$search}%")
                  ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$search}%"));
            });
        }

        // Queue-based filter
        if ($request->get('queue') === 'unassigned') {
            $query->whereNull('assigned_to');
        } elseif ($request->get('queue') === 'mine') {
            $query->where('assigned_to', auth('admin')->id());
        }

        $tickets = $query->paginate(20)->withQueryString();

        $counts = [
            'unassigned' => SupportTicket::whereNull('assigned_to')->where('status', '!=', 'closed')->count(),
            'mine'       => SupportTicket::where('assigned_to', auth('admin')->id())->where('status', '!=', 'closed')->count(),
            'open'       => SupportTicket::whereIn('status', ['open', 'in_progress'])->count(),
        ];

        // For "Compose New" user search
        $users = User::whereIn('user_type', ['customer', 'driver'])->limit(50)->get(['id', 'name', 'phone', 'user_type']);

        return view('admin.support_tickets', compact('tickets', 'counts', 'users'));
    }

    /**
     * Store a new ticket from admin.
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id'  => 'required|exists:users,id',
            'subject'  => 'required|string|max:255',
            'category' => 'required|string',
            'priority' => 'required|in:low,medium,high,urgent',
            'message'  => 'required|string',
        ]);

        $user = User::findOrFail($request->user_id);

        $ticket = SupportTicket::create([
            'user_id'     => $user->id,
            'user_type'   => $user->user_type,
            'subject'     => $request->subject,
            'category'    => $request->category,
            'priority'    => $request->priority,
            'status'      => 'open',
            'assigned_to' => auth('admin')->id(),
        ]);

        // Create initial reply (the message)
        $ticket->replies()->create([
            'sender_id'   => auth('admin')->id(),
            'sender_type' => 'admin',
            'message'     => $request->message,
            'is_internal' => false,
        ]);

        return redirect()->route('orchestrator.support.ticket.show', $ticket->id)
            ->with('success', 'Ticket created successfully.');
    }

    /**
     * Show a single ticket with full thread.
     */
    public function show($id)
    {
        $ticket = SupportTicket::with([
            'user',
            'order',
            'assignedTo',
            'replies.sender',
        ])->findOrFail($id);

        $agents = User::where('user_type', 'admin')->get(['id', 'name']);

        return view('admin.support_ticket_detail', compact('ticket', 'agents'));
    }

    /**
     * Post a reply to a ticket thread.
     */
    public function reply(Request $request, $id)
    {
        $request->validate([
            'message'     => 'required|string',
            'is_internal' => 'boolean',
        ]);

        $ticket = SupportTicket::findOrFail($id);

        $ticket->replies()->create([
            'sender_id'   => auth('admin')->id(),
            'sender_type' => 'admin',
            'message'     => $request->message,
            'is_internal' => $request->boolean('is_internal', false),
        ]);

        // Log first response time
        if (!$ticket->first_response_at) {
            $ticket->update(['first_response_at' => now(), 'status' => 'in_progress']);
        }

        return back()->with('success', 'Reply sent successfully.');
    }

    /**
     * Assign ticket to a staff member.
     */
    public function assign(Request $request, $id)
    {
        $request->validate(['assigned_to' => 'required|uuid']);

        $ticket = SupportTicket::findOrFail($id);
        $ticket->update(['assigned_to' => $request->assigned_to]);

        return back()->with('success', 'Ticket assigned.');
    }

    /**
     * Resolve a ticket.
     */
    public function resolve($id)
    {
        $ticket = SupportTicket::findOrFail($id);
        $ticket->update([
            'status'      => 'resolved',
            'resolved_by' => auth('admin')->id(),
            'resolved_at' => now(),
        ]);

        return back()->with('success', 'Ticket marked as resolved.');
    }

    /**
     * Close a ticket.
     */
    public function close($id)
    {
        $ticket = SupportTicket::findOrFail($id);
        $ticket->update([
            'status'    => 'closed',
            'closed_at' => now(),
        ]);

        return redirect()->route('orchestrator.support.tickets')
            ->with('success', 'Ticket closed.');
    }
}
