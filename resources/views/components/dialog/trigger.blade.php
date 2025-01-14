@props([
    'variant' => 'outline',
    'size' => 'md',
])

<x-button :$variant :$size x-on:click="__dialogOpen = true" {{ $attributes->twMerge() }}>
    {{ $slot }}
</x-button>
