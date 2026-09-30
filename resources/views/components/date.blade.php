@props(['value'])
{{-- Stored in UTC, shown in the display timezone and the active locale. --}}
<time datetime="{{ $value->toIso8601String() }}" {{ $attributes }}>{{ $value->copy()->timezone(config('app.display_timezone'))->translatedFormat(__('site.date_format')) }}</time>
