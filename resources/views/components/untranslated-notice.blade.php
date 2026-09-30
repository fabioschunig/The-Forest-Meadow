@props(['model'])
@if (! $model->hasTranslation('title', app()->getLocale()))
    <p class="notice" role="note">{{ __('site.untranslated') }}</p>
@endif
