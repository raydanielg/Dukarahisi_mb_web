<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ClassRoom;
use App\Models\Level;
use App\Models\Note;
use App\Models\OrderItem;
use App\Models\Subject;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    /**
     * List all active levels.
     */
    public function levels(Request $request)
    {
        $levels = Level::where('is_active', true)
            ->orderBy('order')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $levels,
        ]);
    }

    /**
     * List classes for a level.
     */
    public function classes(Request $request, Level $level)
    {
        $classes = $level->classRooms()
            ->where('is_active', true)
            ->orderBy('order')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $classes,
        ]);
    }

    /**
     * List subjects for a class.
     */
    public function subjects(Request $request, ClassRoom $classRoom)
    {
        $subjects = $classRoom->subjects()
            ->where('is_active', true)
            ->orderBy('order')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $subjects,
        ]);
    }

    /**
     * List notes for a subject.
     */
    public function notes(Request $request, Subject $subject)
    {
        $notes = $subject->notes()
            ->where('status', 'published')
            ->where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function (Note $note) {
                return [
                    'id' => $note->id,
                    'title' => $note->title,
                    'slug' => $note->slug,
                    'description' => $note->description,
                    'price' => $note->final_price,
                    'is_free' => $note->is_free,
                    'cover_image' => $note->cover_image_url,
                    'downloads_count' => $note->downloads_count,
                    'average_rating' => $note->reviews()->avg('rating') ?? 0,
                    'reviews_count' => $note->reviews()->count(),
                ];
            });

        return response()->json([
            'status' => 'success',
            'data' => $notes,
        ]);
    }

    /**
     * Show single note details.
     */
    public function note(Request $request, Note $note)
    {
        if ($note->status !== 'published' || !$note->is_active) {
            return response()->json([
                'status' => 'error',
                'message' => 'Note not available.',
            ], 404);
        }

        $user = $request->user();
        $hasPurchased = false;

        if ($user) {
            $hasPurchased = OrderItem::whereHas('order', function ($q) use ($user) {
                $q->where('user_id', $user->id)->where('status', 'paid');
            })->where('note_id', $note->id)->exists() || $note->is_free;
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'id' => $note->id,
                'title' => $note->title,
                'slug' => $note->slug,
                'description' => $note->description,
                'price' => $note->final_price,
                'is_free' => $note->is_free,
                'cover_image' => $note->cover_image_url,
                'file_url' => $hasPurchased ? $note->file_url : null,
                'has_purchased' => $hasPurchased,
                'average_rating' => $note->reviews()->avg('rating') ?? 0,
                'reviews_count' => $note->reviews()->count(),
                'subject' => $note->subject->name,
                'class' => $note->subject->classRoom->name,
                'level' => $note->subject->classRoom->level->name,
            ],
        ]);
    }
}
