<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ActivityController extends Controller
{
    /**
     * Get all activities for authenticated user
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $activities = $user->activities()
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $activities,
        ]);
    }

    /**
     * Create a new activity for a user
     */
    public static function createActivity($userId, $type, $title, $description, $icon = null, $data = null)
    {
        return Activity::create([
            'user_id' => $userId,
            'type' => $type,
            'title' => $title,
            'description' => $description,
            'icon' => $icon,
            'data' => $data,
        ]);
    }
}
