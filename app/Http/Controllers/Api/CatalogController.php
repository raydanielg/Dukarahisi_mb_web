<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\ClassRoom;
use App\Models\LessonPlan;
use App\Models\Level;
use App\Models\Logbook;
use App\Models\Note;
use App\Models\OrderItem;
use App\Models\SchemeOfWork;
use App\Models\Subject;
use App\Models\Syllabus;
use App\Models\Topic;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    /**
     * List all active levels.
     */
    public function levels(Request $request)
    {
        $materialType = $request->query('material_type', 'notes');
        
        $levels = Level::where('is_active', true)
            ->orderBy('order')
            ->get()
            ->map(function (Level $level) {
                return [
                    'id' => $level->id,
                    'name' => $level->name,
                    'description' => $level->description,
                    'icon' => $level->icon
                        ? (str_starts_with($level->icon, 'public/')
                            ? asset(str_replace('public/', 'storage/', $level->icon))
                            : $level->icon)
                        : 'assets/icons/level.png',
                    'order' => $level->order,
                    'is_active' => $level->is_active,
                ];
            });

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
        $materialType = $request->query('material_type', 'notes');
        
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
        $materialType = $request->query('material_type', 'notes');
        
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
     * List topics for a subject.
     */
    public function topics(Request $request, $subjectId)
    {
        $materialType = $request->query('material_type', 'notes');
        $subject = Subject::findOrFail($subjectId);
        
        $topics = Topic::where('subject_id', $subjectId)
            ->where('is_active', true)
            ->orderBy('order')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $topics,
        ]);
    }

    /**
     * List materials for a topic based on material type.
     */
    public function materials(Request $request, $topicId)
    {
        $materialType = $request->query('material_type', 'notes');
        $topic = Topic::findOrFail($topicId);
        
        $materials = [];
        $user = $request->user();

        switch ($materialType) {
            case 'notes':
                $materials = $this->getNotes($topic, $user);
                break;
            case 'books':
                $materials = $this->getBooks($topic, $user);
                break;
            case 'lesson-plans':
                $materials = $this->getLessonPlans($topic, $user);
                break;
            case 'syllabus':
                $materials = $this->getSyllabus($topic, $user);
                break;
            case 'scheme-of-work':
                $materials = $this->getSchemeOfWork($topic, $user);
                break;
            case 'logbooks':
                $materials = $this->getLogbooks($topic, $user);
                break;
            default:
                $materials = $this->getNotes($topic, $user);
        }

        return response()->json([
            'status' => 'success',
            'data' => $materials,
        ]);
    }

    /**
     * Get notes for a topic.
     */
    private function getNotes(Topic $topic, $user)
    {
        $notes = Note::where('topic_id', $topic->id)
            ->where('status', 'published')
            ->where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function (Note $note) use ($user) {
                $hasPurchased = $this->checkPurchase($note, $user);
                return [
                    'id' => $note->id,
                    'title' => $note->title,
                    'description' => $note->description,
                    'price' => $note->final_price,
                    'is_free' => $note->is_free,
                    'cover_image' => $note->cover_image_url,
                    'file_url' => $hasPurchased ? url("/api/catalog/materials/notes/{$note->id}/download") : null,
                    'file_type' => 'pdf',
                    'has_purchased' => $hasPurchased,
                    'material_type' => 'notes',
                ];
            });

        return $notes;
    }

    /**
     * Get books for a topic.
     */
    private function getBooks(Topic $topic, $user)
    {
        $books = Book::where('topic_id', $topic->id)
            ->where('is_active', true)
            ->orderBy('order')
            ->get()
            ->map(function (Book $book) {
                return [
                    'id' => $book->id,
                    'title' => $book->title,
                    'description' => $book->description,
                    'price' => $book->final_price,
                    'is_free' => $book->is_free,
                    'cover_image' => null,
                    'file_url' => $book->file_path ? url("/api/catalog/materials/books/{$book->id}/download") : null,
                    'file_type' => 'pdf',
                    'has_purchased' => true,
                    'material_type' => 'books',
                ];
            });

        return $books;
    }

    /**
     * Get lesson plans for a topic.
     */
    private function getLessonPlans(Topic $topic, $user)
    {
        $lessonPlans = LessonPlan::where('topic_id', $topic->id)
            ->where('is_active', true)
            ->orderBy('order')
            ->get()
            ->map(function (LessonPlan $lessonPlan) {
                return [
                    'id' => $lessonPlan->id,
                    'title' => $lessonPlan->title,
                    'description' => $lessonPlan->description,
                    'price' => $lessonPlan->final_price,
                    'is_free' => $lessonPlan->is_free,
                    'cover_image' => null,
                    'file_url' => $lessonPlan->file_path ? url("/api/catalog/materials/lesson-plans/{$lessonPlan->id}/download") : null,
                    'file_type' => 'pdf',
                    'has_purchased' => true,
                    'material_type' => 'lesson-plans',
                ];
            });

        return $lessonPlans;
    }

    /**
     * Get syllabus for a topic.
     */
    private function getSyllabus(Topic $topic, $user)
    {
        $syllabus = Syllabus::where('topic_id', $topic->id)
            ->where('is_active', true)
            ->orderBy('order')
            ->get()
            ->map(function (Syllabus $syllabus) {
                return [
                    'id' => $syllabus->id,
                    'title' => $syllabus->title,
                    'description' => $syllabus->description,
                    'price' => $syllabus->final_price,
                    'is_free' => $syllabus->is_free,
                    'cover_image' => null,
                    'file_url' => $syllabus->file_path ? url("/api/catalog/materials/syllabus/{$syllabus->id}/download") : null,
                    'file_type' => 'pdf',
                    'has_purchased' => true,
                    'material_type' => 'syllabus',
                ];
            });

        return $syllabus;
    }

    /**
     * Get scheme of work for a topic.
     */
    private function getSchemeOfWork(Topic $topic, $user)
    {
        $schemes = SchemeOfWork::where('topic_id', $topic->id)
            ->where('is_active', true)
            ->orderBy('order')
            ->get()
            ->map(function (SchemeOfWork $scheme) {
                return [
                    'id' => $scheme->id,
                    'title' => $scheme->title,
                    'description' => $scheme->description,
                    'price' => $scheme->final_price,
                    'is_free' => $scheme->is_free,
                    'cover_image' => null,
                    'file_url' => $scheme->file_path ? url("/api/catalog/materials/scheme-of-work/{$scheme->id}/download") : null,
                    'file_type' => 'pdf',
                    'has_purchased' => true,
                    'material_type' => 'scheme-of-work',
                ];
            });

        return $schemes;
    }

    /**
     * Get logbooks for a topic.
     */
    private function getLogbooks(Topic $topic, $user)
    {
        $logbooks = Logbook::where('topic_id', $topic->id)
            ->where('is_active', true)
            ->orderBy('order')
            ->get()
            ->map(function (Logbook $logbook) {
                return [
                    'id' => $logbook->id,
                    'title' => $logbook->title,
                    'description' => $logbook->description,
                    'price' => $logbook->final_price,
                    'is_free' => $logbook->is_free,
                    'cover_image' => null,
                    'file_url' => $logbook->file_path ? url("/api/catalog/materials/logbooks/{$logbook->id}/download") : null,
                    'file_type' => 'pdf',
                    'has_purchased' => true,
                    'material_type' => 'logbooks',
                ];
            });

        return $logbooks;
    }

    /**
     * Check if user has purchased a note.
     */
    private function checkPurchase(Note $note, $user)
    {
        if (!$user) {
            return $note->is_free;
        }

        return OrderItem::whereHas('order', function ($q) use ($user) {
            $q->where('user_id', $user->id)->where('status', 'paid');
        })->where('note_id', $note->id)->exists() || $note->is_free;
    }

    /**
     * Download a material file securely through the API.
     */
    public function downloadMaterial(Request $request, string $type, int $id)
    {
        $models = [
            'notes' => Note::class,
            'books' => Book::class,
            'lesson-plans' => LessonPlan::class,
            'syllabus' => Syllabus::class,
            'scheme-of-work' => SchemeOfWork::class,
            'logbooks' => Logbook::class,
        ];

        if (!isset($models[$type])) {
            return response()->json(['status' => 'error', 'message' => 'Invalid material type.'], 404);
        }

        $modelClass = $models[$type];
        $item = $modelClass::findOrFail($id);

        // Access check for paid notes
        if ($type === 'notes') {
            $user = $request->user();
            $hasAccess = $this->checkPurchase($item, $user);
            if (!$hasAccess) {
                return response()->json(['status' => 'error', 'message' => 'Purchase required.'], 403);
            }
        }

        if (!$item->file_path) {
            return response()->json(['status' => 'error', 'message' => 'File not found.'], 404);
        }

        $path = str_replace('public/', '', $item->file_path);
        $altPath = ltrim($item->file_path, '/');

        $resolvedPath = null;
        if (\Storage::disk('public')->exists($path)) {
            $resolvedPath = $path;
        } elseif (\Storage::disk('public')->exists($altPath)) {
            $resolvedPath = $altPath;
        }

        if (!$resolvedPath) {
            return response()->json([
                'status' => 'error',
                'message' => 'File not found on disk.',
                'debug' => [
                    'file_path' => $item->file_path,
                    'resolved_path' => $path,
                    'alt_path' => $altPath,
                    'storage_root' => storage_path('app/public'),
                ],
            ], 404);
        }

        return \Storage::disk('public')->download($resolvedPath, $item->title . '.pdf');
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
        $hasPurchased = $this->checkPurchase($note, $user);

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
