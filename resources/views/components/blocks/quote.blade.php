@props(['data'])
@if (filled($data['text'] ?? null))
    <blockquote class="block-quote">
        <p>{{ $data['text'] }}</p>
        @if (filled($data['attribution'] ?? null))
            <cite>{{ $data['attribution'] }}</cite>
        @endif
    </blockquote>
@endif
