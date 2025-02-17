@props([
    'align' => 'center',
    'side' => 'bottom',
    'sideOffset' => 4,
])
@php
    $alignment = $side . ['center' => '', 'end' => '-end', 'start' => '-start'][$align];
@endphp

<div x-hover-card:content x-transition.delay.200ms x-cloak
    x-anchor.{{ $alignment }}.offset.{{ $sideOffset }}="document.getElementById($id('alpine-hover-card-trigger'))"
    @mouseenter="__open = true" @mouseleave="__close()"
    {{ $attributes->twMerge('w-64 rounded-md border bg-popover p-4 text-popover-foreground shadow-md outline-none') }}>
    {{ $slot }}
</div>
