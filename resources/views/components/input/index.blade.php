@props([
    'type' => 'text',
    'timeout' => 2000,
    'copy' => true,
    'target' => null,
])

<div {{ $attributes->only(['class'])->filter(fn($class) => str_contains($class, 'col-span') || str_contains($class, 'row-span'))->merge(['class' => 'relative w-full']) }}
    x-data="{
        showPassword: false,
        inputType: '{{ $type }}',
        copied: false,
        target: '{{ $target }}'
    }">
    <input :type="showPassword && inputType === 'password' ? 'text' : inputType"
        {{ $attributes->twMerge('flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors file:border-0 dark:bg-input-background file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50 read-only:cursor-not-allowed read-only:opacity-50 text-foreground') }} />
    @if ($target)
        <div class="absolute bottom-2.5 right-2 flex items-center">
            <div class="text-muted-foreground" wire:loading.remove wire:target="{{ $target }}">
                <x-lucide-check class="size-4" />
            </div>
            <div class="text-muted-foreground" wire:loading wire:target="{{ $target }}">
                <x-lucide-loader-2 class="size-4 animate-spin" />
            </div>
        </div>
    @endif
    @if ($type === 'password')
        <button type="button" class="absolute inset-y-0 right-0 flex items-center pr-3" tabindex="-1"
            x-on:click="showPassword = !showPassword">
            <template x-if="!showPassword">
                <x-lucide-eye class="h-4 w-4 text-muted-foreground" />
            </template>
            <template x-if="showPassword">
                <x-lucide-eye-off class="h-4 w-4 text-muted-foreground" />
            </template>
        </button>
    @endif
    @if ($copy === true)
        @if ($type !== 'password' && $type !== 'email')
            <button type="button" class="absolute inset-y-0 right-0 flex items-center pr-3 gap-2" tabindex="-1"
                x-on:click="navigator.clipboard.writeText($el.previousElementSibling.value); copied = true; setTimeout(() => copied = false, {{ $timeout }})">
                <div x-cloak x-show="copied" class="text-xs text-success flex items-center gap-2">Copied!
                    <x-lucide-check class="h-4 w-4 text-success" />
                </div>
                <div x-cloak x-show="!copied"> <x-lucide-copy class="h-4 w-4 text-muted-foreground" /> </div>
            </button>
        @endif
    @endif
</div>
