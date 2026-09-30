@props(['overlay' => false])
@php
    $prefix = locale_prefix();
    $links = [
        'projects.index' => __('site.nav.projects'),
        'notes.index' => __('site.nav.notes'),
        'about' => __('site.nav.about'),
    ];
@endphp
<header @class(['site-header', 'is-overlay' => $overlay])>
    <a class="brand" href="{{ localized_route('home') }}">{{ config('app.name') }}</a>

    <nav class="site-nav" aria-label="{{ __('site.nav.label') }}">
        <ul>
            @foreach ($links as $route => $label)
                @php($section = str_replace('.index', '', $route))
                <li>
                    <a href="{{ localized_route($route) }}" @if (request()->routeIs("{$prefix}.{$section}*")) aria-current="page" @endif>{{ $label }}</a>
                </li>
            @endforeach
            <li>
                <span class="lang" role="group" aria-label="{{ __('site.nav.language') }}">
                    @foreach (config('app.locales') as $otherPrefix => $locale)
                        @if ($otherPrefix === $prefix)
                            <span aria-current="true">{{ strtoupper($otherPrefix) }}</span>
                        @else
                            <a href="{{ switch_locale_url($otherPrefix) }}" hreflang="{{ str_replace('_', '-', $locale) }}" lang="{{ str_replace('_', '-', $locale) }}" title="{{ trans('site.nav.switch_to', [], $locale) }}">{{ strtoupper($otherPrefix) }}</a>
                        @endif
                    @endforeach
                </span>
            </li>
        </ul>
    </nav>
</header>
