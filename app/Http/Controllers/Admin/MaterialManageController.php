<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\ClassRoom;
use App\Models\LessonNote;
use App\Models\LessonPlan;
use App\Models\Level;
use App\Models\Logbook;
use App\Models\Note;
use App\Models\SchemeOfWork;
use App\Models\Subject;
use App\Models\Syllabus;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MaterialManageController extends Controller
{
    protected const TYPES = [
        'notes' => [
            'model' => Note::class,
            'title' => 'Notes',
            'singular' => 'Note',
            'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
        ],
        'books' => [
            'model' => Book::class,
            'title' => 'Books',
            'singular' => 'Book',
            'icon' => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253',
        ],
        'lesson-notes' => [
            'model' => LessonNote::class,
            'title' => 'Lesson Notes',
            'singular' => 'Lesson Note',
            'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4',
        ],
        'lesson-plans' => [
            'model' => LessonPlan::class,
            'title' => 'Lesson Plans',
            'singular' => 'Lesson Plan',
            'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
        ],
        'syllabus' => [
            'model' => Syllabus::class,
            'title' => 'Syllabus',
            'singular' => 'Syllabus',
            'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
        ],
        'schemes' => [
            'model' => SchemeOfWork::class,
            'title' => 'Scheme of Work',
            'singular' => 'Scheme of Work',
            'icon' => 'M4 6h16M4 10h16M4 14h16M4 18h16',
        ],
        'logbooks' => [
            'model' => Logbook::class,
            'title' => 'Logbooks',
            'singular' => 'Logbook',
            'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
        ],
    ];

    protected function getConfig(string $type): array
    {
        if (!isset(self::TYPES[$type])) {
            abort(404);
        }
        return self::TYPES[$type];
    }

    protected function getModel(string $type)
    {
        return $this->getConfig($type)['model'];
    }

    public function index(string $type, Request $request)
    {
        $config = $this->getConfig($type);
        $model = $config['model'];

        $levels = Level::orderBy('order')->get();
        $classRooms = ClassRoom::with('level')->orderBy('order')->get();
        $subjects = Subject::with('classRoom.level')->orderBy('order')->get();

        $query = $model::with('subject.classRoom.level')->orderBy('order');

        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }

        if ($request->filled('class_room_id')) {
            $query->whereHas('subject', function ($q) use ($request) {
                $q->where('class_room_id', $request->class_room_id);
            });
        }

        if ($request->filled('level_id')) {
            $query->whereHas('subject.classRoom', function ($q) use ($request) {
                $q->where('level_id', $request->level_id);
            });
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $items = $query->get();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'items' => $items]);
        }

        return view('admin.materials.index', compact('items', 'levels', 'classRooms', 'subjects', 'type', 'config'));
    }

    public function store(string $type, Request $request)
    {
        $config = $this->getConfig($type);
        $model = $config['model'];

        $validated = $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'file_path' => 'nullable|string|max:500',
            'order' => 'required|integer|min:0',
        ]);

        $data = [
            'subject_id' => $validated['subject_id'],
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'order' => $validated['order'],
            'is_active' => true,
        ];

        if ($type === 'notes') {
            $data['slug'] = Str::slug($validated['title']) . '-' . time();
            $data['price'] = 0;
            $data['is_free'] = true;
            $data['status'] = 'published';
        }

        if (isset($validated['file_path'])) {
            $data['file_path'] = $validated['file_path'];
        }

        $item = $model::create($data);
        $item->load('subject.classRoom.level');

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $config['singular'] . ' added successfully.',
                'item' => $item,
            ]);
        }

        return redirect()->route("admin.materials.$type")->with('status', $config['singular'] . ' added successfully.');
    }

    public function update(string $type, int $id, Request $request)
    {
        $config = $this->getConfig($type);
        $model = $config['model'];
        $item = $model::findOrFail($id);

        $validated = $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'file_path' => 'nullable|string|max:500',
            'order' => 'required|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $data = [
            'subject_id' => $validated['subject_id'],
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'order' => $validated['order'],
            'is_active' => $validated['is_active'] ?? true,
        ];

        if (isset($validated['file_path'])) {
            $data['file_path'] = $validated['file_path'];
        }

        $item->update($data);
        $item->load('subject.classRoom.level');

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $config['singular'] . ' updated successfully.',
                'item' => $item,
            ]);
        }

        return redirect()->route("admin.materials.$type")->with('status', $config['singular'] . ' updated successfully.');
    }

    public function destroy(string $type, int $id)
    {
        $config = $this->getConfig($type);
        $model = $config['model'];
        $item = $model::findOrFail($id);
        $item->delete();

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $config['singular'] . ' deleted successfully.'
            ]);
        }

        return redirect()->route("admin.materials.$type")->with('status', $config['singular'] . ' deleted successfully.');
    }
}
