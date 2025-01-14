<div class="min-h-screen flex items-center justify-center">
    <div class="w-full max-w-md p-6">
        <x-typography.h1 class="text-center">
            Register
        </x-typography.h1>

        <x-form wire:submit="register" class="pt-4 space-y-4">
            <x-form.input wire:model="email" type="email" label="Email" />
            <x-form.input wire:model="password" type="password" label="Password" />
            <x-form.input wire:model="password_confirmation" type="password" label="Confirm Password" />
            <x-button type="submit" class="w-full">Register</x-button>

            @if ($isRootUser)
                <x-typography.muted>You are about to create the first user.<br><br> This user will have full access to
                    the
                    system
                    (root user).</x-typography.muted>
            @else
                <div>
                    <a href="{{ route('login') }}"><x-button variant="ghost" class="w-full">Login
                            instead</x-button></a>
                </div>
            @endif
        </x-form>
    </div>
</div>
