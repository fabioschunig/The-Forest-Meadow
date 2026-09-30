@props(['data'])
@use('Filament\Forms\Components\RichEditor\RichContentRenderer')
{{-- toHtml() sanitizes the admin's HTML with Symfony's HtmlSanitizer. --}}
<div class="block-text">{!! RichContentRenderer::make($data['body'] ?? '')->toHtml() !!}</div>
