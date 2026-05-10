<?php

namespace App\Modules\Logistics\Services;

use App\Modules\Logistics\Models\ChatConversation;
use App\Modules\Logistics\Models\ChatParticipant;
use App\Modules\Logistics\Models\RideRequest;
use Illuminate\Support\Facades\DB;

class ChatService
{
    /**
     * Get or create a conversation for a specific ride.
     */
    public function getOrCreateRideConversation(RideRequest $ride): ChatConversation
    {
        return DB::transaction(function () use ($ride) {
            // 1. Find or Create Conversation
            $conversation = ChatConversation::firstOrCreate(
                ['order_id' => $ride->uuid],
                [
                    'type'   => 'order_chat',
                    'status' => 'active',
                ]
            );

            // 2. Ensure Participants are enrolled (Passenger & Driver)
            $participants = [
                $ride->user_id, // Passenger
                $ride->driver_id, // Driver (if assigned)
            ];

            foreach ($participants as $userId) {
                if ($userId) {
                    ChatParticipant::firstOrCreate([
                        'conversation_id' => $conversation->id,
                        'user_id'         => $userId,
                    ]);
                }
            }

            return $conversation;
        });
    }

    /**
     * Mark all messages as read for a participant.
     */
    public function markAsRead(string $conversationId, int $userId): void
    {
        ChatParticipant::where('conversation_id', $conversationId)
            ->where('user_id', $userId)
            ->update(['last_read_at' => now()]);
    }
}
