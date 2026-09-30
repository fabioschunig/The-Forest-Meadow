@props(['data'])
@php($ratio = in_array($data['aspect_ratio'] ?? null, ['16:9', '4:3', '1:1'], true) ? str_replace(':', ' / ', $data['aspect_ratio']) : '16 / 9')
@if (filled($data['url'] ?? null) && str_starts_with($data['url'], 'https://'))
    <figure class="block-figure is-wide">
        {{-- The iframe is created on click (resources/js/game-embed.js), so the build isn't downloaded for nothing. --}}
        <div class="embed" style="--ratio: {{ $ratio }}" data-game-src="{{ $data['url'] }}" data-game-title="{{ $data['caption'] ?? '' }}">
            <div class="game-gate">
                <button class="btn btn-sun" type="button">{{ __('site.blocks.play') }}</button>
                <p class="meta">{{ __('site.blocks.play_hint') }}</p>
            </div>
        </div>
        @if (filled($data['caption'] ?? null))
            <figcaption>{{ $data['caption'] }}</figcaption>
        @endif
    </figure>
@endif
