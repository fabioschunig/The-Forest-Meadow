<?php

namespace App\Http\Controllers;

use App\Models\Note;
use App\Models\Project;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        $projects = Project::published()->orderBy('sort_order')->get();

        return view('pages.home', [
            // The pedestal holds one project: the first featured one in portfolio order.
            'featured' => $projects->firstWhere('is_featured', true),
            'projects' => $projects,
            'latestNotes' => Note::published()->with('project')->latest('published_at')->take(3)->get(),
        ]);
    }
}
