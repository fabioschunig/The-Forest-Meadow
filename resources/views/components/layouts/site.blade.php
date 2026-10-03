@props([
    'title' => null,
    'description' => null,
    'overlayHeader' => false,
    // Metadata for search engines and link previews (see components/seo).
    'model' => null,
    'image' => null,
    'type' => 'website',
    'publishedAt' => null,
    'noindex' => false,
])
@php($fullTitle = $title ? $title.' · '.config('app.name') : config('app.name'))
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $fullTitle }}</title>
    <x-seo :title="$fullTitle" :description="$description ?? __('site.tagline')" :model="$model" :image="$image" :type="$type" :published-at="$publishedAt" :noindex="$noindex" />
    <meta name="theme-color" content="#0A1F16">
    @fonts
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <a class="skip-link" href="#content">{{ __('site.skip_to_content') }}</a>
    <x-site-header :overlay="$overlayHeader" />

    <main id="content">
        {{ $slot }}
    </main>

    <x-site-footer />
</body>
</html>
