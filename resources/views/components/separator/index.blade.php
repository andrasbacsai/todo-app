@props([
    'orientation' => 'horizontal',
    'decorative' => true,
])
<div @if (!$decorative) aria-orientation="{{ $orientation }}"
        role="separator" @endif
    {{ $attributes->class([
            'shrink-0 bg-border',
            'h-px w-full' => $orientation == 'horizontal',
            'h-full w-px' => $orientation == 'vertical',
        ])->merge([]) }}>
</div>
