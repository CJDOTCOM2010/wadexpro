<?php

namespace App\Modules\Logistics\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Logistics\Models\ChatConversation;
use App\Modules\Logistics\Models\ChatMessage;
use App\Modules\Logistics\Models\ChatParticipant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CustomerSupportChatController extends Controller
{
    /**
     * Start or get an active support chat for the customer.
     */
    public function getActive(Request $request): JsonResponse
    {
        $user = $request->user();

        // Find active/waiting support chat for user
        $conversation = ChatConversation::where('type', 'support_chat')
            ->whereIn('status', ['active', 'waiting'])
            ->whereHas('participants', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->with(['messages' => function ($q) {
                $q->orderBy('created_at', 'ASC')->with('sender:id,name,avatar_url');
            }])
            ->first();

        // If no active chat, create one
        if (!$conversation) {
            $conversation = ChatConversation::create([
                'type'   => 'support_chat',
                'status' => 'waiting',
            ]);

            ChatParticipant::create([
                'conversation_id' => $conversation->id,
                'user_id'         => $user->id,
            ]);

            // Welcome message
            ChatMessage::create([
                'conversation_id' => $conversation->id,
                'sender_id'       => null, // System
                'content'         => 'Welcome to WADEXPRO Support! How can we help you today?',
                'message_type'    => 'system',
            ]);

            $conversation->load('messages.sender');
        }

        return response()->json([
            'message' => 'Support chat retrieved.',
            'data'    => $conversation,
        ]);
    }

    /**
     * Send message to support.
     */
    public function send(Request $request): JsonResponse
    {
        $request->validate(['content' => 'required|string']);
        $user = $request->user();

        $conversation = ChatConversation::where('type', 'support_chat')
            ->whereIn('status', ['active', 'waiting'])
            ->whereHas('participants', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->firstOrFail();

        $message = ChatMessage::create([
            'conversation_id' => $conversation->id,
            'sender_id'       => $user->id,
            'content'         => $request->content,
            'message_type'    => 'text',
        ]);

        $conversation->update(['updated_at' => now(), 'status' => 'waiting']);

        return response()->json([
            'message' => 'Message sent.',
            'data'    => $message->load('sender:id,name,avatar_url'),
        ]);
    }
}
