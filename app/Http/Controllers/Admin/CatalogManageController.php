<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassRoom;
use App\Models\Level;
use App\Models\SubLevel;
use App\Models\Subject;
use App\Models\Topic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class CatalogManageController extends Controller
{
    protected function subLevelsTableExists(): bool
    {
        try {
            return Schema::hasTable('sub_levels');
        } catch (\Exception $e) {
            return false;
        }
    }

    // Levels
    public function levelsIndex(Request $request)
    {
        $withSubLevels = $this->subLevelsTableExists();

        if ($request->ajax() || $request->wantsJson()) {
            $query = $withSubLevels ? Level::with('subLevels') : Level::query();
            $query->orderBy('order');

            if ($request->filled('search')) {
                $search = $request->input('search');
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            }

            $levels = $query->get();
            return response()->json(['success' => true, 'levels' => $levels]);
        }

        $levels = ($withSubLevels ? Level::with('subLevels') : Level::query())->orderBy('order')->get();
        return view('admin.catalog.levels', compact('levels', 'withSubLevels'));
    }

    public function levelsStore(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'icon' => 'nullable|image|mimes:png,jpg,jpeg,svg,webp|max:2048',
            'order' => 'required|integer|min:0',
        ]);

        $data = [
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'order' => $validated['order'],
            'is_active' => true,
        ];

        if ($request->hasFile('icon')) {
            $data['icon'] = $this->storeIconFile($request->file('icon'), 'level');
        }

        $level = Level::create($data);
        if ($this->subLevelsTableExists()) {
            $level->load('subLevels');
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Level added successfully.',
                'level' => $level
            ]);
        }

        return redirect()->route('admin.catalog.levels')->with('status', 'Level added successfully.');
    }

    public function levelsUpdate(Request $request, Level $level)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'icon' => 'nullable|image|mimes:png,jpg,jpeg,svg,webp|max:2048',
            'order' => 'required|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $data = [
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'order' => $validated['order'],
            'is_active' => $validated['is_active'] ?? true,
        ];

        if ($request->hasFile('icon')) {
            $this->deleteIconFile($level);
            $data['icon'] = $this->storeIconFile($request->file('icon'), 'level');
        } elseif ($request->boolean('remove_icon')) {
            $this->deleteIconFile($level);
            $data['icon'] = null;
        }

        $level->update($data);
        if ($this->subLevelsTableExists()) {
            $level->load('subLevels');
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Level updated successfully.',
                'level' => $level
            ]);
        }

        return redirect()->route('admin.catalog.levels')->with('status', 'Level updated successfully.');
    }

    protected function storeIconFile($file, string $prefix): string
    {
        $filename = $prefix . '_' . time() . '_' . str_replace(' ', '_', $file->getClientOriginalName());
        return $file->storeAs('public/icons', $filename);
    }

    protected function deleteIconFile($level): void
    {
        if ($level->icon && \Storage::exists($level->icon)) {
            \Storage::delete($level->icon);
        }
    }

    public function levelsDestroy(Level $level)
    {
        $this->deleteIconFile($level);
        $level->delete();

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Level deleted successfully.'
            ]);
        }

        return redirect()->route('admin.catalog.levels')->with('status', 'Level deleted successfully.');
    }

    // Sub-Levels
    public function subLevelsStore(Request $request)
    {
        $validated = $request->validate([
            'level_id' => 'required|exists:levels,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'order' => 'required|integer|min:0',
        ]);

        $subLevel = SubLevel::create([
            'level_id' => $validated['level_id'],
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'order' => $validated['order'],
            'is_active' => true,
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Sub-level added successfully.',
                'subLevel' => $subLevel
            ]);
        }

        return redirect()->route('admin.catalog.levels')->with('status', 'Sub-level added successfully.');
    }

    public function subLevelsUpdate(Request $request, SubLevel $subLevel)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'order' => 'required|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $subLevel->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'order' => $validated['order'],
            'is_active' => $validated['is_active'] ?? true,
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Sub-level updated successfully.',
                'subLevel' => $subLevel
            ]);
        }

        return redirect()->route('admin.catalog.levels')->with('status', 'Sub-level updated successfully.');
    }

    public function subLevelsDestroy(SubLevel $subLevel)
    {
        $subLevel->delete();

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Sub-level deleted successfully.'
            ]);
        }

        return redirect()->route('admin.catalog.levels')->with('status', 'Sub-level deleted successfully.');
    }

    // Classes
    public function classesIndex(Request $request)
    {
        $withSubLevels = $this->subLevelsTableExists();
        $levels = ($withSubLevels ? Level::with('subLevels') : Level::query())->orderBy('order')->get();
        $query = $withSubLevels ? ClassRoom::with(['level', 'subLevel']) : ClassRoom::with(['level']);
        $query->orderBy('order');

        if ($request->filled('level_id')) {
            $query->where('level_id', $request->level_id);
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $classes = $query->get();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'classes' => $classes]);
        }

        return view('admin.catalog.classes', compact('classes', 'levels', 'withSubLevels'));
    }

    public function classesStore(Request $request)
    {
        $validated = $request->validate([
            'level_id' => 'required|exists:levels,id',
            'sub_level_id' => 'nullable|exists:sub_levels,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'order' => 'required|integer|min:0',
        ]);

        $class = ClassRoom::create([
            'level_id' => $validated['level_id'],
            'sub_level_id' => $validated['sub_level_id'] ?? null,
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'order' => $validated['order'],
            'is_active' => true,
        ]);

        $class->load(['level', 'subLevel']);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Class added successfully.',
                'class' => $class
            ]);
        }

        return redirect()->route('admin.catalog.classes')->with('status', 'Class added successfully.');
    }

    public function classesUpdate(Request $request, ClassRoom $classRoom)
    {
        $validated = $request->validate([
            'level_id' => 'required|exists:levels,id',
            'sub_level_id' => 'nullable|exists:sub_levels,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'order' => 'required|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $classRoom->update([
            'level_id' => $validated['level_id'],
            'sub_level_id' => $validated['sub_level_id'] ?? null,
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'order' => $validated['order'],
            'is_active' => $validated['is_active'] ?? true,
        ]);

        $classRoom->load(['level', 'subLevel']);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Class updated successfully.',
                'class' => $classRoom
            ]);
        }

        return redirect()->route('admin.catalog.classes')->with('status', 'Class updated successfully.');
    }

    public function classesDestroy(ClassRoom $classRoom)
    {
        $classRoom->delete();

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Class deleted successfully.'
            ]);
        }

        return redirect()->route('admin.catalog.classes')->with('status', 'Class deleted successfully.');
    }

    // Subjects
    public function subjectsIndex(Request $request)
    {
        $classRooms = ClassRoom::with('level')->orderBy('order')->get();
        $query = Subject::with('classRoom.level')->orderBy('order');

        if ($request->filled('class_room_id')) {
            $query->where('class_room_id', $request->class_room_id);
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $subjects = $query->get();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'subjects' => $subjects]);
        }

        return view('admin.catalog.subjects', compact('subjects', 'classRooms'));
    }

    public function subjectsStore(Request $request)
    {
        $validated = $request->validate([
            'class_room_id' => 'required|exists:class_rooms,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'icon' => 'nullable|string|max:255',
            'order' => 'required|integer|min:0',
        ]);

        $subject = Subject::create([
            'class_room_id' => $validated['class_room_id'],
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'icon' => $validated['icon'] ?? null,
            'order' => $validated['order'],
            'is_active' => true,
        ]);

        $subject->load('classRoom.level');

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Subject added successfully.',
                'subject' => $subject
            ]);
        }

        return redirect()->route('admin.catalog.subjects')->with('status', 'Subject added successfully.');
    }

    public function subjectsUpdate(Request $request, Subject $subject)
    {
        $validated = $request->validate([
            'class_room_id' => 'required|exists:class_rooms,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'icon' => 'nullable|string|max:255',
            'order' => 'required|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $subject->update([
            'class_room_id' => $validated['class_room_id'],
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'icon' => $validated['icon'] ?? null,
            'order' => $validated['order'],
            'is_active' => $validated['is_active'] ?? true,
        ]);

        $subject->load('classRoom.level');

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Subject updated successfully.',
                'subject' => $subject
            ]);
        }

        return redirect()->route('admin.catalog.subjects')->with('status', 'Subject updated successfully.');
    }

    public function subjectsDestroy(Subject $subject)
    {
        $subject->delete();

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Subject deleted successfully.'
            ]);
        }

        return redirect()->route('admin.catalog.subjects')->with('status', 'Subject deleted successfully.');
    }

    // Topics
    public function topicsIndex(Request $request)
    {
        $levels = Level::orderBy('order')->get();
        $classRooms = ClassRoom::with('level')->orderBy('order')->get();
        $subjects = Subject::with('classRoom.level')->orderBy('order')->get();

        $query = Topic::with('subject.classRoom.level')->orderBy('order');

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
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $topics = $query->get();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'topics' => $topics]);
        }

        return view('admin.catalog.topics', compact('topics', 'levels', 'classRooms', 'subjects'));
    }

    public function topicsStore(Request $request)
    {
        $validated = $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'order' => 'required|integer|min:0',
        ]);

        $topic = Topic::create([
            'subject_id' => $validated['subject_id'],
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'order' => $validated['order'],
            'is_active' => true,
        ]);

        $topic->load('subject.classRoom.level');

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Topic added successfully.',
                'topic' => $topic
            ]);
        }

        return redirect()->route('admin.catalog.topics')->with('status', 'Topic added successfully.');
    }

    public function topicsUpdate(Request $request, Topic $topic)
    {
        $validated = $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'order' => 'required|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $topic->update([
            'subject_id' => $validated['subject_id'],
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'order' => $validated['order'],
            'is_active' => $validated['is_active'] ?? true,
        ]);

        $topic->load('subject.classRoom.level');

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Topic updated successfully.',
                'topic' => $topic
            ]);
        }

        return redirect()->route('admin.catalog.topics')->with('status', 'Topic updated successfully.');
    }

    public function topicsDestroy(Topic $topic)
    {
        $topic->delete();

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Topic deleted successfully.'
            ]);
        }

        return redirect()->route('admin.catalog.topics')->with('status', 'Topic deleted successfully.');
    }
}
