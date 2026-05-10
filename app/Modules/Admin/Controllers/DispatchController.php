<?php

namespace App\Modules\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Logistics\Models\RideRequest;
use Illuminate\Http\Request;

class DispatchController extends Controller
{
    /**
     * Retrieve all non-completed rides for the operations grid.
     */
    public function activeRides(Request $request)
    {
        // For the admin dashboard, we fetch any ride that isn't cancelled or completed.
        // We load the customer relationship securely to display the name natively.
        $brides = RideRequest::with('customer:id,first_name,last_name,email')
                    ->whereNotIn('status', ['cancelled', 'completed'])
                    ->orderBy('created_at', 'desc')
                    ->get();

        return response()->json([
            'status' => 'success',
            'data'   => $brides
        ], 200);
    }
}
