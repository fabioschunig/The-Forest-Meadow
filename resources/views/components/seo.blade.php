@props(['title', 'description', 'model' => null, 'image' => null, 'type' => 'website', 'publishedAt' => null, 'noindex' => false])
@php
    $prefix = locale_prefix();
    $alternates = page_alternates($model);
    // An /en page showing untranslated (Portuguese) content points search engines to the /pt original.
    $contentPrefix = isset($alternates[$prefix]) ? $prefix : default_locale_prefix();
    $canonical = $alternates[$contentPrefix];
    $locales = config('app.locales');
@endphp
<meta name="description" content="{{ $description }}">
@if ($noindex)
    <meta name="robots" content="noindex">
@else
    <link rel="canonical" href="{{ $canonical }}">
    @foreach ($alternates as $altPrefix => $url)
        <link rel="alternate" hreflang="{{ str_replace('_', '-', $locales[$altPrefix]) }}" href="{{ $url }}">
    @endforeach
    <link rel="alternate" hreflang="x-default" href="{{ $alternates[default_locale_prefix()] }}">
@endif
<link rel="alternate" type="application/rss+xml" title="{{ config('app.name') }} · {{ __('site.notes.title') }}" href="{{ route("{$prefix}.feeds.{$prefix}") }}">

<meta property="og:site_name" content="{{ config('app.name') }}">
<meta property="og:title" content="{{ $title }}">
<meta property="og:description" content="{{ $description }}">
<meta property="og:url" content="{{ $canonical }}">
<meta property="og:type" content="{{ $type }}">
@if ($image)
    <meta property="og:image" content="{{ $image }}">
@else
    <meta property="og:image" content="{{ asset('images/og-default.jpg') }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
@endif
{{-- The language of the content shown, which differs from the URL on untranslated /en pages. --}}
<meta property="og:locale" content="{{ trans('site.og_locale', [], $locales[$contentPrefix]) }}">
@foreach (array_keys($alternates) as $altPrefix)
    @continue($altPrefix === $contentPrefix)
    <meta property="og:locale:alternate" content="{{ trans('site.og_locale', [], $locales[$altPrefix]) }}">
@endforeach
@if ($publishedAt)
    <meta property="article:published_time" content="{{ $publishedAt->toIso8601String() }}">
@endif
<meta name="twitter:card" content="summary_large_image">
