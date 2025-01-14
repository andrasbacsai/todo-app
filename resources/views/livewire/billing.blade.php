<div>
    @php
        $access = auth()->user()->paid();
    @endphp

    @if ($access)
        <x-typography.p>You have an active {{ $access['type'] }}: <span
                class="font-bold">{{ $access['name'] }}</span></x-typography.p>
        @if ($access['type'] === 'subscription' && $access['name'] != 'iddqd')
            <a href="{{ route('subscription-billing-portal') }}"><x-button variant="secondary">Manage
                    Subscription</x-button></a>
        @endif
    @else
        <x-typography.p class="mb-4">You do not have an active subscription. Please subscribe to
            continue.</x-typography.p>
        <div class="flex gap-2">
            @forelse ($subscriptions as $subscription)
                @if ($subscription['is_one_time_payment'])
                    <x-button variant="outline" class="w-full"
                        wire:click="subscribeToSubscription('{{ data_get($subscription, 'name') }}')">Pay Once
                        {{ data_get($subscription, 'name') }}</x-button>
                @else
                    <x-button variant="outline" class="w-full"
                        wire:click="subscribeToSubscription('{{ data_get($subscription, 'name') }}')">Subscribe to
                        {{ data_get($subscription, 'name') }}</x-button>
                @endif
            @empty
                <x-typography.p>No subscriptions found. Contact support.</x-typography.p>
            @endforelse
        </div>
    @endif
</div>
