<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\ClassRoom;
use App\Models\LessonPlan;
use App\Models\Level;
use App\Models\Logbook;
use App\Models\Note;
use App\Models\SchemeOfWork;
use App\Models\Subject;
use App\Models\Syllabus;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LandingController extends Controller
{
    protected const MATERIAL_TYPES = [
        'notes' => [
            'title' => 'Subject Notes',
            'singular' => 'Subject Note',
            'model' => Note::class,
            'icon' => 'M19 2l-5 4.5v11l5-4.5V2M6.5 5C4.55 5 2.45 5.4 1 6.5v14.66c0 .25.25.5.5.5.1 0 .15-.07.25-.07 1.35-.65 3.3-1.09 4.75-1.09 1.95 0 4.05.4 5.5 1.5 1.35-.85 3.8-1.5 5.5-1.5 1.65 0 3.35.31 4.75 1.06.1.05.15.03.25.03.25 0 .5-.25.5-.5V6.5c-.6-.45-1.25-.75-2-1V19c-1.1-.35-2.3-.5-3.5-.5-1.7 0-4.15.65-5.5 1.5V6.5C10.55 5.4 8.45 5 6.5 5z',
            'color' => 'from-rose-500 to-pink-500',
        ],
        'books' => [
            'title' => 'Books',
            'singular' => 'Book',
            'model' => Book::class,
            'icon' => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253',
            'color' => 'from-indigo-500 to-blue-600',
        ],
        'lesson-plans' => [
            'title' => 'Lesson Plans',
            'singular' => 'Lesson Plan',
            'model' => LessonPlan::class,
            'icon' => 'M19 3h-1V1h-2v2H8V1H6v2H5a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2V5a2 2 0 00-2-2m0 16H5V10h14v9M7 12h5v5H7v-5z',
            'color' => 'from-blue-500 to-cyan-500',
        ],
        'syllabus' => [
            'title' => 'Syllabus',
            'singular' => 'Syllabus',
            'model' => Syllabus::class,
            'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
            'color' => 'from-teal-500 to-emerald-600',
        ],
        'schemes' => [
            'title' => 'Schemes',
            'singular' => 'Scheme of Work',
            'model' => SchemeOfWork::class,
            'icon' => 'M6 2a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8l-6-6H6m0 2h7v5h5v11H6V4m2 8v2h8v-2H8m0 4v2h5v-2H8z',
            'color' => 'from-emerald-500 to-teal-500',
        ],
        'logbooks' => [
            'title' => 'Logbooks',
            'singular' => 'Logbook',
            'model' => Logbook::class,
            'icon' => 'M17 4v6l-2-2-2 2V4H9v16h10V4h-2M7 7V5h2V4a2 2 0 0 1 2-2h12c1.05 0 2 .95 2 2v16c0 1.05-.95 2-2 2H7c-1.05 0-2-.95-2-2v-1H3v-2h2v-4H3v-2h2V7H3m2-2v2h2V5H5m0 14h2v-2H5v2m0-6h2v-2H5v2z',
            'color' => 'from-violet-500 to-purple-500',
        ],
        'exams' => [
            'title' => 'Exams',
            'singular' => 'Exam',
            'model' => Note::class,
            'icon' => 'M19 3h-4.18C14.25 1.44 12.53.64 11 1.2c-.86.3-1.5.96-1.82 1.8H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V5a2 2 0 0 0-2-2m-7 0a1 1 0 0 1 1 1 1 1 0 0 1-1 1 1 1 0 0 1-1-1 1 1 0 0 1 1-1M7 7h10V5h2v14H5V5h2v2m10 4H7V9h10v2m-2 4H7v-2h8v2z',
            'color' => 'from-amber-500 to-orange-500',
        ],
    ];

    public function index(Request $request): View
    {
        $levels = Level::where('is_active', true)
            ->with(['classRooms' => function ($query) {
                $query->where('is_active', true)
                    ->orderBy('order')
                    ->with(['subjects' => function ($query) {
                        $query->where('is_active', true)
                            ->orderBy('order')
                            ->with(['topics' => function ($query) {
                                $query->where('is_active', true)->orderBy('order');
                            }]);
                    }]);
            }])
            ->orderBy('order')
            ->get();

        $materialCounts = [];
        foreach (self::MATERIAL_TYPES as $key => $config) {
            $model = $config['model'];
            $materialCounts[$key] = [
                'title' => $config['title'],
                'singular' => $config['singular'],
                'icon' => $config['icon'],
                'color' => $config['color'],
                'count' => $model::where('is_active', true)->count(),
                'latest' => $model::where('is_active', true)
                    ->latest()
                    ->limit(3)
                    ->get(),
            ];
        }

        $featuredMaterials = Note::where('is_active', true)
            ->where('status', 'published')
            ->with(['subject.classRoom.level'])
            ->latest()
            ->limit(8)
            ->get();

        $subjectCount = Subject::where('is_active', true)->count();
        $classCount = ClassRoom::where('is_active', true)->count();
        $levelCount = $levels->count();
        $totalMaterialCount = array_sum(array_column($materialCounts, 'count'));

        return view('landing', compact(
            'levels',
            'materialCounts',
            'featuredMaterials',
            'subjectCount',
            'classCount',
            'levelCount',
            'totalMaterialCount'
        ));
    }
}
