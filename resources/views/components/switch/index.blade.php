<label role="switch"
    {{ $attributes->twMerge('inline-flex h-5 w-9 shrink-0 cursor-pointer items-center rounded-full border-2 border-transparent shadow-sm transition-colors focus-within:outline-none focus-within:ring-2 focus-within:ring-ring focus-within:ring-offset-2 focus-within:ring-offset-background disabled:cursor-not-allowed disabled:opacity-50 has-[:checked]:bg-primary dark:bg-foreground/50 bg-foreground') }}>
    <input type="checkbox" class="peer sr-only" {{ $attributes->except('class') }} />
    <x-switch.thumb />
</label>
