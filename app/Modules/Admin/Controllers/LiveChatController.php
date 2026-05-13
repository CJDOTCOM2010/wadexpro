<?php

namespace App\Modules\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Modules\Logistics\Models\ChatConversation;
use App\Modules\Logistics\Models\ChatMessage;
use App\Modules\Logistics\Models\ChatParticipant;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class LiveChatController extends Controller
{
    /**
     * Admin Live Chat Dashboard — shows all active support conversations.
     */
    public function index()
    {
        $conversations = ChatConversation::where('type', 'support_chat')
            ->with(['participants.user', 'latestMessage.sender'])
            ->orderByDesc('updated_at')
            ->paginate(30);

        $stats = [
            'active'   => ChatConversation::where('type', 'support_chat')->where('status', 'active')->count(),
            'waiting'  => ChatConversation::where('type', 'support_chat')->where('status', 'waiting')->count(),
            'closed'   => ChatConversation::where('type', 'support_chat')->where('status', 'closed')->count(),
        ];

        return view('admin.live_chat', compact('conversations', 'stats'));
    }

    /**
     * View a single conversation thread.
     */
    public function show(string $id)
    {
        $conversation = ChatConversation::with([
            'participants.user',
            'messages.sender',
        ])->findOrFail($id);

        $messages = ChatMessage::with('sender:id,name,avatar_url,user_type')
            ->where('conversation_id', $id)
            ->orderBy('created_at', 'ASC')
            ->get();

        // Mark as read by current agent
        ChatParticipant::updateOrCreate(
            ['conversation_id' => $id, 'user_id' => auth('admin')->id()],
            ['last_read_at' => now()]
        );

        // Get all active conversations for sidebar
        $conversations = ChatConversation::where('type', 'support_chat')
            ->with(['participants.user', 'latestMessage.sender'])
            ->orderByDesc('updated_at')
            ->get();

        return view('admin.live_chat_thread', compact('conversation', 'messages', 'conversations'));
    }

    /**
     * Send a reply as admin agent.
     */
    public function reply(Request $request, string $id)
    {
        $request->validate(['message' => 'required|string|max:2000']);

        $conversation = ChatConversation::findOrFail($id);

        // Ensure agent is a participant
        ChatParticipant::firstOrCreate([
            'conversation_id' => $id,
            'user_id'         => auth('admin')->id(),
        ]);

        $message = ChatMessage::create([
            'conversation_id' => $id,
            'sender_id'       => auth('admin')->id(),
            'content'         => $request->message,
            'message_type'    => 'text',
        ]);

        // Update conversation status and timestamp
        $conversation->update([
            'status'     => 'active',
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Message sent.');
    }

    /**
     * Close a conversation.
     */
    public function close(string $id)
    {
        $conversation = ChatConversation::findOrFail($id);
        $conversation->update(['status' => 'closed']);

        // Send system message
        ChatMessage::create([
            'conversation_id' => $id,
            'sender_id'       => auth('admin')->id(),
            'content'         => 'This conversation has been closed by support. Thank you for reaching out.',
            'message_type'    => 'system',
        ]);

        return redirect()->route('orchestrator.livechat')
            ->with('success', 'Conversation closed.');
    }

    /**
     * Reopen a conversation.
     */
    public function reopen(string $id)
    {
        ChatConversation::findOrFail($id)->update(['status' => 'active']);
        return back()->with('success', 'Conversation reopened.');
    }
}
