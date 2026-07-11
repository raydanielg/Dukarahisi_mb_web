<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassRoom;
use App\Models\Level;
use App\Models\Subject;
use Illuminate\Http\Request;

class CatalogManageController extends Controller
{
    // Levels
    public function levelsIndex()
    {
        $levels = Level::orderBy('order')->get();
        return view('admin.catalog.levels', compact('levels'));
    }

    public function levelsStore(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'order' => 'required|integer|min:0',
        ]);

        Level::create($validated);
        return redirect()->route('admin.catalog.levels')->with('status', 'Level added successfully.');
    }

    public function levelsUpdate(Request $request, Level $level)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'order' => 'required|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $level->update([
            'name' => $validated['name'],
            'order' => $validated['order'],
            'is_active' => $validated['is_active'] ?? true,
        ]);

        return redirect()->route('admin.catalog.levels')->with('status', 'Level updated successfully.');
    }

    public function levelsDestroy(Level $level)
    {
        $level->delete();
        return redirect()->route('admin.catalog.levels')->with('status', 'Level deleted successfully.');
    }

    // Classes
    public function classesIndex(Request $request)
    {
        $levels = Level::orderBy('order')->get();
        $query = ClassRoom::with('level')->orderBy('order');

        if ($request->filled('level_id')) {
            $query->where('level_id', $request->level_id);
        }

        $classes = $query->get();
        return view('admin.catalog.classes', compact('classes', 'levels'));
    }

    public function classesStore(Request $request)
    {
        $validated = $request->validate([
            'level_id' => 'required|exists:levels,id',
            'name' => 'required|string|max:255',
            'order' => 'required|integer|min:0',
        ]);

        ClassRoom::create($validated);
        return redirect()->route('admin.catalog.classes')->with('status', 'Class added successfully.');
    }

    public function classesUpdate(Request $request, ClassRoom $classRoom)
    {
        $validated = $request->validate([
            'level_id' => 'required|exists:levels,id',
            'name' => 'required|string|max:255',
            'order' => 'required|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $classRoom->update([
            'level_id' => $validated['level_id'],
            'name' => $validated['name'],
            'order' => $validated['order'],
            'is_active' => $validated['is_active'] ?? true,
        ]);

        return redirect()->route('admin.catalog.classes')->with('status', 'Class updated successfully.');
    }

    public function classesDestroy(ClassRoom $classRoom)
    {
        $classRoom->delete();
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

        $subjects = $query->get();
        return view('admin.catalog.subjects', compact('subjects', 'classRooms'));
    }

    public function subjectsStore(Request $request)
    {
        $validated = $request->validate([
            'class_room_id' => 'required|exists:class_rooms,id',
            'name' => 'required|string|max:255',
            'order' => 'required|integer|min:0',
        ]);

        Subject::create($validated);
        return redirect()->route('admin.catalog.subjects')->with('status', 'Subject added successfully.');
    }

    public function subjectsUpdate(Request $request, Subject $subject)
    {
        $validated = $request->validate([
            'class_room_id' => 'required|exists:class_rooms,id',
            'name' => 'required|string|max:255',
            'order' => 'required|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $subject->update([
            'class_room_id' => $validated['class_room_id'],
            'name' => $validated['name'],
            'order' => $validated['order'],
            'is_active' => $validated['is_active'] ?? true,
        ]);

        return redirect()->route('admin.catalog.subjects')->with('status', 'Subject updated successfully.');
    }

    public function subjectsDestroy(Subject $subject)
    {
        $subject->delete();
        return redirect()->route('admin.catalog.subjects')->with('status', 'Subject deleted successfully.');
    }
}
