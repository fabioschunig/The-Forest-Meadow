@props(['data'])
@php($layout = in_array($data['layout'] ?? null, ['wide', 'full'], true) ? $data['layout'] : null)
@if (filled($data['image'] ?? null))
    <figure @class(['block-figure', "is-{$layout}" => $layout])>
        <img src="{{ upload_url($data['image']) }}" alt="{{ $data['alt'] ?? '' }}" loading="lazy" decoding="async">
        @if (filled($data['caption'] ?? null))
            <figcaption>{{ $data['caption'] }}</figcaption>
        @endif
    </figure>
@endif
