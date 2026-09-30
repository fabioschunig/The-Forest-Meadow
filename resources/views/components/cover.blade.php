@props(['path' => null, 'alt' => ''])
<div {{ $attributes->class(['cover', 'is-placeholder' => blank($path)]) }}>
    @if (filled($path))
        <img src="{{ upload_url($path) }}" alt="{{ $alt }}" loading="lazy" decoding="async">
    @endif
</div>
