@php($untranslated = ! $project->hasTranslation('title', app()->getLocale()))
<x-layouts.site :title="$project->title" :description="$project->summary">
    <article class="flow" @if ($untranslated) lang="pt-BR" @endif>
        <header class="article-head">
            <x-project-plaques :project="$project" />
            <h1>{{ $project->title }}</h1>
            @if (filled($project->summary))
                <p class="summary">{{ $project->summary }}</p>
            @endif
            <x-untranslated-notice :model="$project" />

            @if ($project->started_on || $project->released_on || filled($project->links))
                <dl class="facts">
                    @if ($project->started_on)
                        <div><dt>{{ __('site.projects.started_on') }}</dt><dd><x-date :value="$project->started_on" /></dd></div>
                    @endif
                    @if ($project->released_on)
                        <div><dt>{{ __('site.projects.released_on') }}</dt><dd><x-date :value="$project->released_on" /></dd></div>
                    @endif
                    @if (filled($project->links))
                        <div>
                            <dt>{{ __('site.projects.links') }}</dt>
                            <dd>
                                @foreach ($project->links as $link)
                                    <a href="{{ $link['url'] }}" rel="noopener" target="_blank">{{ $link['label'] }}</a>@unless ($loop->last) · @endunless
                                @endforeach
                            </dd>
                        </div>
                    @endif
                </dl>
            @endif
        </header>

        @if ($project->cover_image)
            <x-cover class="article-cover is-wide" :path="$project->cover_image" :alt="$project->title" />
        @endif

        <x-content-blocks :blocks="$project->content" />

        <x-ruin seed="41" class="is-wide" />

        <section class="section" aria-labelledby="devlog-title">
            <h2 id="devlog-title">{{ __('site.projects.devlog') }}</h2>
            @if ($devlog->isEmpty())
                <p class="empty">{{ __('site.projects.devlog_empty') }}</p>
            @else
                <x-devlog :notes="$devlog" />
            @endif
        </section>
    </article>
</x-layouts.site>
