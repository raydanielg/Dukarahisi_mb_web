<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Note;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class NoteController extends Controller
{
    /**
     * Display a listing of notes.
     */
    public function index(Request $request)
    {
        $query = Note::with('subject.classRoom.level')
            ->orderBy('created_at', 'desc');

        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $notes = $query->paginate(20);
        return view('admin.notes.index', compact('notes'));
    }

    /**
     * Show the form for creating a new note.
     */
    public function create()
    {
        $subjects = Subject::with('classRoom.level')->where('is_active', true)->get();
        return view('admin.notes.create', compact('subjects'));
    }

    /**
     * Store a newly created note.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'is_free' => 'boolean',
            'status' => 'required|in:draft,published',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'file' => 'required|file|mimes:pdf|max:51200',
        ]);

        $slug = Str::slug($validated['title']);
        $originalSlug = $slug;
        $counter = 1;
        while (Note::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter++;
        }

        $coverImage = null;
        if ($request->hasFile('cover_image')) {
            $coverImage = $request->file('cover_image')->store('notes/covers', 'public');
        }

        $filePath = $request->file('file')->store('notes/files', 'public');

        Note::create([
            'subject_id' => $validated['subject_id'],
            'title' => $validated['title'],
            'slug' => $slug,
            'description' => $validated['description'] ?? null,
            'price' => $validated['is_free'] ? 0 : $validated['price'],
            'is_free' => $validated['is_free'] ?? false,
            'status' => $validated['status'],
            'cover_image' => $coverImage,
            'file_path' => $filePath,
        ]);

        return redirect()->route('admin.notes.index')
            ->with('status', 'Note created successfully.');
    }

    /**
     * Show the form for editing a note.
     */
    public function edit(Note $note)
    {
        $subjects = Subject::with('classRoom.level')->where('is_active', true)->get();
        return view('admin.notes.edit', compact('note', 'subjects'));
    }

    /**
     * Update the specified note.
     */
    public function update(Request $request, Note $note)
    {
        $validated = $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'is_free' => 'boolean',
            'status' => 'required|in:draft,published',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'file' => 'nullable|file|mimes:pdf|max:51200',
        ]);

        if ($request->hasFile('cover_image')) {
            if ($note->cover_image) {
                Storage::disk('public')->delete($note->cover_image);
            }
            $note->cover_image = $request->file('cover_image')->store('notes/covers', 'public');
        }

        if ($request->hasFile('file')) {
            if ($note->file_path) {
                Storage::disk('public')->delete($note->file_path);
            }
            $note->file_path = $request->file('file')->store('notes/files', 'public');
        }

        $note->subject_id = $validated['subject_id'];
        $note->title = $validated['title'];
        $note->description = $validated['description'] ?? null;
        $note->price = $validated['is_free'] ? 0 : $validated['price'];
        $note->is_free = $validated['is_free'] ?? false;
        $note->status = $validated['status'];
        $note->save();

        return redirect()->route('admin.notes.index')
            ->with('status', 'Note updated successfully.');
    }

    /**
     * Remove the specified note.
     */
    public function destroy(Note $note)
    {
        if ($note->cover_image) {
            Storage::disk('public')->delete($note->cover_image);
        }
        if ($note->file_path) {
            Storage::disk('public')->delete($note->file_path);
        }
        $note->delete();

        return redirect()->route('admin.notes.index')
            ->with('status', 'Note deleted successfully.');
    }
}
