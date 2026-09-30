@php($social = collect(config('site.social'))->filter(fn (array $link) => filled($link['url'] ?? null)))
<footer class="site-footer">
    <x-ruin seed="71" />
    <div class="inner">
        <a class="brand" href="{{ localized_route('home') }}">{{ config('app.name') }}</a>
        @if ($social->isNotEmpty() || filled(config('site.email')))
            <ul>
                @foreach ($social as $link)
                    <li><a href="{{ $link['url'] }}" rel="me noopener" target="_blank">{{ $link['label'] }}</a></li>
                @endforeach
                @if (filled(config('site.email')))
                    <li><a href="mailto:{{ config('site.email') }}">{{ config('site.email') }}</a></li>
                @endif
            </ul>
        @endif
    </div>
</footer>
