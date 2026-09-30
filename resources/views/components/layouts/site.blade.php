@props(['title' => null, 'description' => null, 'overlayHeader' => false])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ? $title.' · '.config('app.name') : config('app.name') }}</title>
    <meta name="description" content="{{ $description ?? __('site.tagline') }}">
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
