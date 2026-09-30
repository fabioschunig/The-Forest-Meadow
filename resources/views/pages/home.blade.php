<x-layouts.site overlay-header>
    <x-hero-scene />

    <div class="page">
        <section class="page-head">
            <h2>{{ __('site.home.intro_title') }}</h2>
            <p>{{ __('site.home.intro') }}</p>
        </section>

        @if ($featured)
            <section class="section" aria-labelledby="featured-title">
                <h2 id="featured-title" class="eyebrow">{{ __('site.home.featured') }}</h2>
                <x-altar :project="$featured" />
            </section>
        @endif

        @if ($latestNotes->isNotEmpty())
            <x-ruin seed="23" />
            <section class="section" aria-labelledby="latest-title">
                <div class="section-head">
                    <h2 id="latest-title">{{ __('site.home.latest') }}</h2>
                    <a href="{{ localized_route('notes.index') }}">{{ __('site.home.all_notes') }}</a>
                </div>
                <x-devlog :notes="$latestNotes" />
            </section>
        @endif

        @if ($projects->isNotEmpty())
            <x-ruin seed="37" />
            <section class="section" aria-labelledby="projects-title">
                <div class="section-head">
                    <h2 id="projects-title">{{ __('site.home.projects') }}</h2>
                    <a href="{{ localized_route('projects.index') }}">{{ __('site.home.all_projects') }}</a>
                </div>
                <div class="cards">
                    @foreach ($projects as $project)
                        <x-project-card :project="$project" />
                    @endforeach
                </div>
            </section>
        @endif
    </div>
</x-layouts.site>
