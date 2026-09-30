<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\View\View;

class ProjectController extends Controller
{
    public function index(): View
    {
        return view('projects.index', [
            'projects' => Project::published()->orderBy('sort_order')->get(),
        ]);
    }

    public function show(string $slug): View
    {
        $project = Project::published()->where('slug', $slug)->firstOrFail();

        return view('projects.show', [
            'project' => $project,
            'devlog' => $project->notes()->published()->latest('published_at')->get(),
        ]);
    }
}
