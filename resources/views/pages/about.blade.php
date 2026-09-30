@php($social = collect(config('site.social'))->filter(fn (array $link) => filled($link['url'] ?? null)))
<x-layouts.site :title="__('site.about.title')">
    <article class="flow">
        <header class="article-head">
            <h1>{{ __('site.about.title') }}</h1>
        </header>
        <div class="block-text">
            @foreach (__('site.about.body') as $paragraph)
                <p>{{ $paragraph }}</p>
            @endforeach
        </div>

        @if ($social->isNotEmpty() || filled(config('site.email')))
            <section class="section">
                <h2>{{ __('site.about.contact') }}</h2>
                <ul class="chisel-list">
                    @if (filled(config('site.email')))
                        <li><span>{{ __('site.about.email') }}: <a href="mailto:{{ config('site.email') }}">{{ config('site.email') }}</a></span></li>
                    @endif
                    @foreach ($social as $link)
                        <li><a href="{{ $link['url'] }}" rel="me noopener" target="_blank">{{ $link['label'] }}</a></li>
                    @endforeach
                </ul>
            </section>
        @endif
    </article>
</x-layouts.site>
