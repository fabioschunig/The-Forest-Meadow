@props(['blocks' => []])
{{-- Renders the blocks written in the admin panel; unknown block types are skipped. --}}
@foreach ($blocks ?? [] as $block)
    @if (view()->exists('components.blocks.'.($block['type'] ?? '')))
        <x-dynamic-component :component="'blocks.'.$block['type']" :data="$block['data'] ?? []" />
    @endif
@endforeach
