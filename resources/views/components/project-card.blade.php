@props(['project'])
<a class="card" href="{{ localized_route('projects.show', ['slug' => $project->slug]) }}">
    <x-cover :path="$project->cover_image" />
    <div class="card-body">
        <x-project-plaques :project="$project" />
        <h3>{{ $project->title }}</h3>
        @if (filled($project->summary))
            <p>{{ $project->summary }}</p>
        @endif
    </div>
</a>
