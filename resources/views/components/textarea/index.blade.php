@props(['loading' => false, 'target' => null])
<div class="relative">
    <textarea
        {{ $attributes->merge(['class' => 'flex min-h-[60px] w-full rounded-md border border-input bg-transparent  dark:bg-input-background px-3 py-2 text-sm shadow-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50 read-only:cursor-not-allowed read-only:opacity-50 ']) }}></textarea>
    @if ($loading && $target)
        <div class="absolute bottom-2 right-2 flex items-center">
            <div class="text-muted-foreground" wire:loading.remove wire:target="{{ $target }}">
                <x-lucide-check class="size-4" />
            </div>
            <div class="text-muted-foreground" wire:loading wire:target="{{ $target }}">
                <x-lucide-loader-2 class="size-4 animate-spin" />
            </div>
        </div>
    @endif
</div>
