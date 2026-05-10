<?php

namespace App\Modules\Notifications\Controllers;

use App\Core\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class NotificationController extends Controller
{
    use ApiResponse;

    /**
     * Get user's notifications.
     */
    public function index(Request $request)
    {
        $notifications = $request->user()->notifications()->paginate(15);
        return $this->paginated($notifications, 'Notifications retrieved.');
    }

    /**
     * Mark a specific notification as read.
     */
    public function markAsRead(Request $request, string $id)
    {
        $notification = $request->user()->notifications()->findOrFail($id);
        $notification->markAsRead();
        
        return $this->success(null, 'Notification marked as read.');
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead(Request $request)
    {
        $request->user()->unreadNotifications->markAsRead();
        return $this->success(null, 'All notifications marked as read.');
    }
}
