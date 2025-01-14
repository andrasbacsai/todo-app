<aside class="-mx-4 lg:w-1/5">
    <nav class="flex flex-wrap space-x-2 lg:flex-col lg:space-x-0 lg:space-y-1">
        <a wire:navigate wire:current.strict="font-bold"
            href="{{ route('dashboard') }}"><x-dropdown-menu.item><x-lucide-alarm-clock-check
                    class="size-4 mr-2" />Today</x-dropdown-menu.item></a>
        @if (auth()->user()->paid())
            <a wire:navigate wire:current.exact="font-bold"
                href="{{ route('dump') }}"><x-dropdown-menu.item><x-lucide-list-todo class="size-4 mr-2" />
                    Dump</x-dropdown-menu.item></a>
        @else
            <a wire:navigate wire:current.exact="font-bold" href="{{ route('dump') }}"><x-dropdown-menu.item>
                    <x-lucide-layers class="size-4 mr-2" />
                    Dump</x-dropdown-menu.item></a>
        @endif
    </nav>
</aside>
