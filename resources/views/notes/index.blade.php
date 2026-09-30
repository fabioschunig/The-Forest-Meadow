<x-layouts.site :title="__('site.notes.title')">
    <div class="page">
        <header class="page-head">
            <h1>{{ __('site.notes.title') }}</h1>
            <p>{{ __('site.notes.intro') }}</p>
        </header>

        @if ($notes->isEmpty())
            <p class="empty">{{ __('site.notes.empty') }}</p>
        @else
            <div class="entry-list">
                @foreach ($notes as $note)
                    <article class="entry-item">
                        <div class="when meta">
                            <x-date :value="$note->published_at" />
                            @if ($note->project?->isPublished())
                                <a class="plaque" href="{{ localized_route('projects.show', ['slug' => $note->project->slug]) }}">{{ $note->project->title }}</a>
                            @endif
                        </div>
                        <h3><a href="{{ localized_route('notes.show', ['slug' => $note->slug]) }}">{{ $note->title }}</a></h3>
                        @if (filled($note->excerpt))
                            <p>{{ $note->excerpt }}</p>
                        @endif
                    </article>
                @endforeach
            </div>

            @if ($notes->hasPages())
                <nav class="pager" aria-label="{{ __('site.notes.title') }}">
                    @if ($notes->previousPageUrl())
                        <a href="{{ $notes->previousPageUrl() }}" rel="prev">← {{ __('site.notes.newer') }}</a>
                    @else
                        <span></span>
                    @endif
                    @if ($notes->nextPageUrl())
                        <a href="{{ $notes->nextPageUrl() }}" rel="next">{{ __('site.notes.older') }} →</a>
                    @endif
                </nav>
            @endif
        @endif
    </div>
</x-layouts.site>
