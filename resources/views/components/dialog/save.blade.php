@props([
    'variant' => 'outline',
])

@php
    $wireAttributes = $attributes->whereStartsWith('wire:')->getAttributes();
    $hasWireAction = !empty($wireAttributes);
    $clickHandler = $hasWireAction
        ? "if (!clicked) { clicked = true; \$wire.\$call(\$el.getAttribute('wire:click')).then(() => { __dialogOpen = false; clicked = false; }); }"
        : '__dialogOpen = false';
@endphp

<x-button {{ $attributes->merge(['variant' => $variant]) }} x-data="{ clicked: false }"
    x-on:click="{{ $hasWireAction ? '.prevent' : '' }}" x-on:wire:loading.attr="disabled" x-on:click="{{ $clickHandler }}">
    @if ($slot->isEmpty())
        {{ __('Close') }}
    @else
        {{ $slot }}
    @endif
</x-button>
