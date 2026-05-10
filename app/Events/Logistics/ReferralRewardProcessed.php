<?php

namespace App\Events\Logistics;

use App\Modules\Payments\Models\Referral;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ReferralRewardProcessed implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * The referral model instance.
     */
    public $referral;

    /**
     * Create a new event instance.
     */
    public function __construct(Referral $referral)
    {
        $this->referral = $referral;
    }

    /**
     * Get the channels the event should broadcast on.
     * Referrer and Referred user should receive notifications on their private channels.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('user.' . $this->referral->inviter_id),
            new PrivateChannel('user.' . $this->referral->referee_id),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'referral.rewarded';
    }
}
