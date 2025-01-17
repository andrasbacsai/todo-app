@props([
    'variant' => null,
    'size' => null,
    'type' => 'button',
])

@inject('button', 'App\Services\ButtonCvaService')

@php
    $wireTarget = $attributes->get('wire:target');
@endphp

<button type="{{ $type }}" {{ $attributes->twMerge($button::new()(['variant' => $variant, 'size' => $size])) }}>
    <div class="inline-flex items-center gap-x-2">
        {{ $slot }}
        <x-lucide-refresh-cw id="loading-icon" wire:loading wire:target="{{ $wireTarget }}"
            class="ml-2 size-4 animate-spin" />
    </div>
</button>
