@props(['notes'])
{{-- Drawn as a root by resources/js/decor.js; the newest entry's bud is lit. --}}
<div class="devlog">
    @foreach ($notes as $note)
        <article @class(['entry', 'is-newest' => $loop->first])>
            <div class="when"><x-date :value="$note->published_at" /></div>
            <h3><a href="{{ localized_route('notes.show', ['slug' => $note->slug]) }}">{{ $note->title }}</a></h3>
            @if (filled($note->excerpt))
                <p>{{ $note->excerpt }}</p>
            @endif
        </article>
    @endforeach
</div>
