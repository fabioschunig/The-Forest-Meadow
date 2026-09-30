@php
    $untranslated = ! $note->hasTranslation('title', app()->getLocale());
    $project = $note->project?->isPublished() ? $note->project : null;
@endphp
<x-layouts.site :title="$note->title" :description="$note->excerpt">
    <article class="flow" @if ($untranslated) lang="pt-BR" @endif>
        <header class="article-head">
            <div class="when meta">
                <x-date :value="$note->published_at" />
                @if ($project)
                    <span>{{ __('site.notes.part_of') }}</span>
                    <a class="plaque" href="{{ localized_route('projects.show', ['slug' => $project->slug]) }}">{{ $project->title }}</a>
                @endif
            </div>
            <h1>{{ $note->title }}</h1>
            @if (filled($note->excerpt))
                <p class="summary">{{ $note->excerpt }}</p>
            @endif
            <x-untranslated-notice :model="$note" />
        </header>

        @if ($note->cover_image)
            <x-cover class="article-cover is-wide" :path="$note->cover_image" :alt="$note->title" />
        @endif

        <x-content-blocks :blocks="$note->content" />

        @if ($project)
            <x-ruin seed="53" class="is-wide" />
            <p><a href="{{ localized_route('projects.show', ['slug' => $project->slug]) }}#devlog-title">← {{ __('site.notes.back_to_devlog', ['project' => $project->title]) }}</a></p>
        @endif
    </article>
</x-layouts.site>
