@props([
    'type' => 'text',
    'label' => 'Label',
    'descriptionTrailing' => '',
    'copy' => true,
])

<x-form.item>
    @if ($type !== 'search')
        <x-form.label>
            {{ $label }}
        </x-form.label>
    @endif

    <x-input :copy="$copy" type="{{ $type }}" x-form:control {{ $attributes }} />

    @if ($descriptionTrailing)
        <x-form.description>
            {{ $descriptionTrailing }}
        </x-form.description>
    @endif

    <x-form.message
        name="{{ $attributes->has('wire:model') ? $attributes->get('wire:model') : $attributes->get('name') }}" />
</x-form.item>
