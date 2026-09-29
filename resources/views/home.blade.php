<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }}</title>
</head>
<body>
    {{-- Placeholder until the visual identity phase. --}}
    <h1>{{ config('app.name') }}</h1>
    <p>{{ __('home.placeholder') }}</p>
</body>
</html>
