@props([
    'variant' => 'outline',
])
<x-button :$variant x-on:click="__dialogOpen = true" {{ $attributes->twMerge() }}>
    {{ $slot }}
</x-button>
