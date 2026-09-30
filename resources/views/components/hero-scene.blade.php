<section class="hero" aria-label="{{ __('site.tagline') }}">
    <canvas id="scene" aria-hidden="true"></canvas>
    <div class="hero-copy">
        <h1>{{ config('app.name') }}</h1>
        <p>{{ __('site.tagline') }}</p>
    </div>
    <button class="motion-toggle" type="button" aria-pressed="false" data-pause="{{ __('site.motion.pause') }}" data-resume="{{ __('site.motion.resume') }}">
        <span class="dot" aria-hidden="true"></span><span class="label">{{ __('site.motion.pause') }}</span>
    </button>
</section>
