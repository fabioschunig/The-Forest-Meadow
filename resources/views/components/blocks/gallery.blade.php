@props(['data'])
@if (filled($data['images'] ?? null))
    <figure class="block-figure is-wide">
        <div class="gallery">
            @foreach ($data['images'] as $image)
                <img src="{{ upload_url($image) }}" alt="" loading="lazy" decoding="async">
            @endforeach
        </div>
        @if (filled($data['caption'] ?? null))
            <figcaption>{{ $data['caption'] }}</figcaption>
        @endif
    </figure>
@endif
