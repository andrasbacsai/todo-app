<div class="min-h-screen flex items-center justify-center">
    <div class="w-full max-w-md p-6">
        <x-typography.h1 class="text-center">
            Jata
        </x-typography.h1>
        <x-form wire:submit="login" class="pt-4 space-y-4">
            <x-form.input wire:model="email" type="email" label="Email" />
            <x-form.input wire:model="password" type="password" label="Password" />
            <x-button type="submit" class="w-full">Login</x-button>
            @if ($isRegistrationEnabled)
                <div>
                    <a href="{{ route('register') }}"><x-button variant="ghost" class="w-full">Register</x-button></a>
                </div>
            @endif
        </x-form>
    </div>
</div>
