<div class="pb-4 flex items-center gap-2  mb-4">
    <a wire:navigate href="{{ route('dashboard') }}" class="flex items-center gap-2 text-md">
        <x-logo size="8" /></a>
    <div class="flex-1"></div>
    <x-aside />
    <div class="flex-1">
    </div>
    <x-dropdown-menu class="inline ">
        <div class="flex items-center gap-2 cursor-pointer" x-dropdown-menu:button>
            <x-avatar class="size-8">
                {{-- <x-avatar.image src="https://github.com/andrasbacsai.png" /> --}}
                <x-avatar.fallback>{{ auth()->user()->name[0] }}</x-avatar.fallback>
            </x-avatar>
        </div>

        <x-dropdown-menu.content class="min-w-[12rem]">
            <div class="flex flex-col gap-0">
                <x-dropdown-menu.label class="pb-0">{{ auth()->user()->name }}
                    @if (auth()->user()->is_root_user)
                        <span class="text-xs text-muted-foreground">(root)</span>
                    @endif
                </x-dropdown-menu.label>
                <x-dropdown-menu.label
                    class="text-xs text-muted-foreground">{{ auth()->user()->email }}</x-dropdown-menu.label>
            </div>
            <x-dropdown-menu.separator />
            <a wire:navigate href="{{ route('dashboard') }}"><x-dropdown-menu.item><x-lucide-house
                        class="size-4 mr-2" />Dashboard</x-dropdown-menu.item></a>
            @if ($isPaymentEnabled && $isCloudEnabled)
                <a wire:navigate href="{{ route('billing') }}"><x-dropdown-menu.item><x-lucide-credit-card
                            class="size-4 mr-2" />Billing</x-dropdown-menu.item></a>
            @endif

            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button class="w-full"><x-dropdown-menu.item><x-lucide-log-out
                            class="size-4 mr-2" />Logout</x-dropdown-menu.item>
                </button>
            </form>

            @if ($isRootUser)
                <x-dropdown-menu.separator />

                <a wire:navigate href="{{ route('instance-settings') }}"><x-dropdown-menu.item><x-lucide-cog
                            class="size-4 mr-2" />Instance Settings</x-dropdown-menu.item></a>
            @endif
        </x-dropdown-menu.content>
    </x-dropdown-menu>
</div>
