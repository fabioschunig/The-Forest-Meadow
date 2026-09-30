<?php

namespace App\Http\Controllers;

use App\Models\Note;
use Illuminate\View\View;

class NoteController extends Controller
{
    public function index(): View
    {
        return view('notes.index', [
            'notes' => Note::published()->with('project')->latest('published_at')->paginate(10),
        ]);
    }

    public function show(string $slug): View
    {
        return view('notes.show', [
            'note' => Note::published()->with('project')->where('slug', $slug)->firstOrFail(),
        ]);
    }
}
