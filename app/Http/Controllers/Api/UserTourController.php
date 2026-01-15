<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserTourController extends Controller
{
    /**
     * Mark a tour as completed for the authenticated user.
     */
    public function complete(Request $request)
    {
        $request->validate([
            'tour_id' => 'required|string',
        ]);

        $user = Auth::user();
        $completedTours = $user->tours_completed ?? [];

        // If not already in the array, add it
        if (!in_array($request->tour_id, $completedTours)) {
            $completedTours[] = $request->tour_id;
            $user->tours_completed = $completedTours;
            $user->save();
        }

        return response()->json(['success' => true, 'completed_tours' => $completedTours]);
    }
}
