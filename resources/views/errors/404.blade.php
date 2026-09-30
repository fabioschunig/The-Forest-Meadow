<x-layouts.site :title="__('site.errors.404_title')">
    <div class="page error-page">
        <p class="eyebrow">404</p>
        <h1>{{ __('site.errors.404_title') }}</h1>
        <p>{{ __('site.errors.404_body') }}</p>
        <p><a class="btn btn-ghost" href="{{ localized_route('home') }}">{{ __('site.errors.back_home') }}</a></p>
    </div>
</x-layouts.site>
