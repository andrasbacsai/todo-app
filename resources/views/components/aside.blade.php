<aside class="w-full">
    <nav class="flex flex-wrap space-x-2  items-center justify-center">
        <a wire:navigate.hover href="{{ route('dashboard') }}"><x-dropdown-menu.item><x-lucide-alarm-clock-check
                    class="size-4 mr-2" />Today
            </x-dropdown-menu.item></a>
        @if (auth()->user()->paid())
            <a wire:navigate.hover href="{{ route('dump') }}"><x-dropdown-menu.item><x-lucide-list-todo
                        class="size-4 mr-2" />
                    Task Dump</x-dropdown-menu.item></a>
        @else
            <a wire:navigate.hover href="{{ route('dump') }}"><x-dropdown-menu.item>
                    <x-lucide-layers class="size-4 mr-2" />
                    Task Dump</x-dropdown-menu.item></a>
        @endif
    </nav>
</aside>
