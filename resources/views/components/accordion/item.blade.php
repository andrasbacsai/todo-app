@props(['value'])

<div x-accordion:item {{ $attributes->twMerge('border-b') }} x-data="{ item: '{{ $value }}' }" :data-state="__getDataState(item)">
    {{ $slot }}
</div>
