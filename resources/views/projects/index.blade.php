<x-layouts.site :title="__('site.projects.title')">
    <div class="page">
        <header class="page-head">
            <h1>{{ __('site.projects.title') }}</h1>
            <p>{{ __('site.projects.intro') }}</p>
        </header>

        @if ($projects->isEmpty())
            <p class="empty">{{ __('site.projects.empty') }}</p>
        @else
            <div class="cards">
                @foreach ($projects as $project)
                    <x-project-card :project="$project" />
                @endforeach
            </div>
        @endif
    </div>
</x-layouts.site>
