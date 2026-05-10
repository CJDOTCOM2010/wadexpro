<?php

namespace App\Modules\Logistics\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Logistics\Models\ChatMessage;
use App\Modules\Logistics\Models\RideRequest;
use App\Modules\Logistics\Services\ChatService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function __construct(
        private ChatService $chatService
    ) {}

    /**
     * Fetch chat history for an active ride.
     */
    public function history(Request $request, string $rideId): JsonResponse
    {
        $ride = RideRequest::where('uuid', $rideId)->firstOrFail();
        
        // Ensure user is participant
        if ($request->user()->id !== $ride->user_id && $request->user()->id !== $ride->driver_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $conversation = $this->chatService->getOrCreateRideConversation($ride);
        
        $messages = ChatMessage::with('sender:id,name,avatar_url')
            ->where('conversation_id', $conversation->id)
            ->orderBy('created_at', 'ASC')
            ->paginate(50);

        return response()->json([
            'data' => [
                'conversation_id' => $conversation->id,
                'messages'        => $messages->items(),
                'pagination'      => [
                    'current_page' => $messages->currentPage(),
                    'last_page'    => $messages->lastPage(),
                ]
            ]
        ]);
    }

    /**
     * Send a new chat message.
     */
    public function send(Request $request, string $rideId): JsonResponse
    {
        $validated = $request->validate([
            'content'      => 'required|string|max:1000',
            'message_type' => 'nullable|string|in:text,image,location',
        ]);

        $ride = RideRequest::where('uuid', $rideId)->firstOrFail();
        
        if ($request->user()->id !== $ride->user_id && $request->user()->id !== $ride->driver_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $conversation = $this->chatService->getOrCreateRideConversation($ride);

        $message = ChatMessage::create([
            'conversation_id' => $conversation->id,
            'sender_id'       => $request->user()->id,
            'content'         => $validated['content'],
            'message_type'    => $validated['message_type'] ?? 'text',
        ]);

        // Trigger Real-time Broadcast (Mocked for now, pending Event class)
        // event(new \App\Modules\Logistics\Events\NewChatMessage($message));

        return response()->json([
            'message' => 'Message sent successfully.',
            'data'    => $message->load('sender:id,name,avatar_url'),
        ]);
    }
}
