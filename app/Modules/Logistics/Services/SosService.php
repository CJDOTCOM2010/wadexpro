<?php

namespace App\Modules\Logistics\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SosService
{
    /**
     * Trigger an SOS emergency event.
     *
     * @return array{id: string, status: string, message: string}
     */
    public function trigger(string $userId, float $lat, float $lng, ?string $rideRequestId = null): array
    {
        $id = (string) Str::uuid();

        // 1. Record the event
        DB::table('sos_events')->insert([
            'id'              => $id,
            'user_id'         => $userId,
            'ride_request_id' => $rideRequestId,
            'lat'             => $lat,
            'lng'             => $lng,
            'status'          => 'triggered',
            'created_at'      => now(),
            'updated_at'      => now(),
        ]);

        // 2. Fetch user data for broadcasting
        $user = DB::table('users')->where('id', $userId)->first(['name', 'phone']);

        $eventData = [
            'id'         => $id,
            'user_id'    => $userId,
            'user_name'  => $user->name ?? 'Unknown User',
            'user_phone' => $user->phone ?? 'Unknown Phone',
            'lat'        => $lat,
            'lng'        => $lng,
            'status'     => 'triggered',
            'created_at' => now()->toIso8601String(),
        ];

        // 3. Dispatch Real-time Broadcast
        event(new \App\Events\Logistics\SosAlertTriggered($eventData));

        return [
            'id'      => $id,
            'status'  => 'triggered',
            'message' => 'Emergency alert dispatched. Help is on the way.',
        ];
    }

    /**
     * Acknowledge an SOS event (admin action).
     */
    public function acknowledge(string $sosId, string $adminUserId): void
    {
        DB::table('sos_events')
            ->where('id', $sosId)
            ->where('status', 'triggered')
            ->update([
                'status'          => 'acknowledged',
                'acknowledged_at' => now(),
                'updated_at'      => now(),
            ]);
    }

    /**
     * Resolve an SOS event.
     */
    public function resolve(string $sosId, string $adminUserId, string $notes, bool $isFalseAlarm = false): void
    {
        DB::table('sos_events')
            ->where('id', $sosId)
            ->whereIn('status', ['triggered', 'acknowledged'])
            ->update([
                'status'      => $isFalseAlarm ? 'false_alarm' : 'resolved',
                'resolved_by' => $adminUserId,
                'resolved_at' => now(),
                'notes'       => $notes,
                'updated_at'  => now(),
            ]);
    }

    /**
     * Get active SOS events for admin dashboard.
     */
    public function getActiveEvents(): \Illuminate\Support\Collection
    {
        return DB::table('sos_events')
            ->whereIn('status', ['triggered', 'acknowledged'])
            ->orderByDesc('created_at')
            ->join('users', 'sos_events.user_id', '=', 'users.id')
            ->select(
                'sos_events.*',
                'users.name as user_name',
                'users.phone as user_phone'
            )
            ->get();
    }
}
