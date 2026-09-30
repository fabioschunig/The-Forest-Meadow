@props(['data'])
@use('App\Support\VideoEmbed')
@php($player = VideoEmbed::url($data['url'] ?? ''))
<figure class="block-figure is-wide">
    @if ($player)
        <div class="embed">
            <iframe src="{{ $player }}" title="{{ $data['caption'] ?? __('site.blocks.watch_video') }}" loading="lazy" allow="fullscreen; picture-in-picture" allowfullscreen referrerpolicy="strict-origin-when-cross-origin"></iframe>
        </div>
    @elseif (filled($data['url'] ?? null))
        <a href="{{ $data['url'] }}" rel="noopener" target="_blank">{{ __('site.blocks.watch_video') }}</a>
    @endif
    @if (filled($data['caption'] ?? null))
        <figcaption>{{ $data['caption'] }}</figcaption>
    @endif
</figure>
