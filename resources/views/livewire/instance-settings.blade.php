<div>
    <div class="space-y-0.5">
        <h2 class="text-2xl font-bold tracking-tight">Instance Settings</h2>
        <p class="text-muted-foreground">
            Manage your instance settings. Only root users can access this page.
        </p>
    </div>
    <div class="flex flex-col space-y-2 pt-4">
        <x-card>
            <x-card.header>
                <div class="flex justify-between">
                    <div>
                        <x-card.title>
                            Registration
                        </x-card.title>
                        <x-card.description>
                            Enable or disable registration for your instance.
                        </x-card.description>
                    </div>
                    <div>
                        <x-switch class="dark:has-[:checked]:bg-warning rounded-lg" name="is_registration_enabled"
                            data-label="Registration" wire:model.live="isRegistrationEnabled" />
                    </div>
                </div>
            </x-card.header>
        </x-card>

        @if ($isCloud)
            <x-card>
                <x-card.header>
                    <div class="flex justify-between">
                        <div>
                            <x-card.title>
                                Payments
                            </x-card.title>
                            <x-card.description>
                                Enable or disable payments for your instance.
                            </x-card.description>
                        </div>
                        <div class="flex items-center space-x-2">
                            @if ($isPaymentEnabled)
                                <x-sheet>
                                    <x-sheet.trigger>
                                        <div class="flex items-center space-x-2 pr-8 cursor-pointer">
                                            <span
                                                class="text-muted-foreground font-bold text-xs hover:text-foreground">Configure</span>
                                        </div>
                                    </x-sheet.trigger>
                                    <x-sheet.content class="!max-w-[1000px]">
                                        <x-sheet.header>
                                            <x-sheet.title>Stripe Keys</x-sheet.title>
                                            <x-sheet.description>
                                                <x-form wire:submit="saveStripeKeys" class="space-y-4">
                                                    <x-form.input wire:model="stripeKey" type="password"
                                                        label="Stripe Key" />
                                                    <x-form.input wire:model="stripeSecret" type="password"
                                                        label="Stripe Secret" />
                                                    <x-form.input wire:model="stripeWebhookSecret" type="password"
                                                        label="Stripe Webhook Secret" />
                                                    <x-button type="submit" class="w-full">Save</x-button>
                                                </x-form>
                                            </x-sheet.description>

                                            <x-sheet.description class="pt-4">
                                                <x-dialog>
                                                    <div class="flex space-x-2 items-center">
                                                        <x-sheet.title>Subscriptions</x-sheet.title>
                                                        <x-dialog.trigger size="sm">
                                                            <x-lucide-plus class="mr-2 size-4" />Add
                                                        </x-dialog.trigger>
                                                    </div>
                                                    <x-dialog.content class="sm:max-w-[425px]">
                                                        <x-form wire:submit="addSubscription"
                                                            class="flex flex-col space-y-2 text-foreground">
                                                            <x-dialog.header>
                                                                <x-dialog.title>
                                                                    Add Subscription
                                                                </x-dialog.title>
                                                                <x-dialog.description>
                                                                    Add a new subscription to your instance.
                                                                </x-dialog.description>
                                                            </x-dialog.header>
                                                            <div class="flex flex-col space-y-2 py-2">
                                                                <x-form.input wire:model="subscriptionName"
                                                                    type="text" label="Subscription Name" />
                                                                <x-form.input wire:model="subscriptionPriceId"
                                                                    type="text" label="Subscription Price ID" />
                                                                <div class="flex items-center space-x-2 py-2">
                                                                    <div class="flex items-center space-x-2">
                                                                        <x-switch id="isOneTimePayment"
                                                                            class="dark:has-[:checked]:bg-warning rounded-lg"
                                                                            wire:model="isOneTimePayment" />
                                                                        <x-label htmlFor="isOneTimePayment"
                                                                            class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">
                                                                            Is One Time Payment
                                                                        </x-label>
                                                                    </div>

                                                                </div>
                                                            </div>
                                                            <x-dialog.footer>
                                                                <x-button type="submit" variant="default">Save
                                                                </x-button>
                                                                <x-dialog.close>Cancel</x-dialog.close>
                                                            </x-dialog.footer>
                                                        </x-form>
                                                    </x-dialog.content>
                                                </x-dialog>
                                                @forelse ($subscriptions as $subscription)
                                                    <x-card.header class="pb-3 px-0 pt-0"
                                                        wire:key="subscription-{{ $subscription['name'] }}">
                                                        <x-card.description>
                                                            <x-form class="flex space-x-2 space-y-0">
                                                                <div class="flex w-full items-center space-x-2">
                                                                    <x-input
                                                                        wire:model="editedSubscriptions.{{ $subscription['name'] }}.name"
                                                                        type="text" placeholder="Name" />
                                                                    <x-input
                                                                        wire:model="editedSubscriptions.{{ $subscription['name'] }}.price_id"
                                                                        type="password" placeholder="Price ID" />
                                                                    <x-label htmlFor="isOneTimePayment">
                                                                        Once
                                                                    </x-label>
                                                                    <x-switch id="isOneTimePayment"
                                                                        class="dark:has-[:checked]:bg-warning rounded-lg"
                                                                        wire:model="editedSubscriptions.{{ $subscription['name'] }}.is_one_time_payment" />
                                                                </div>
                                                                <x-button
                                                                    wire:click="updateSubscription('{{ $subscription['name'] }}')">Update</x-button>
                                                                <x-button variant="destructive"
                                                                    wire:click="deleteSubscription('{{ $subscription['name'] }}')">Delete</x-button>
                                                            </x-form>

                                                        </x-card.description>

                                                    </x-card.header>
                                                @empty
                                                    <div> No subscriptions found </div>
                                                @endforelse
                                            </x-sheet.description>
                                        </x-sheet.header>
                                    </x-sheet.content>
                                </x-sheet>
                            @endif
                            <x-switch class="dark:has-[:checked]:bg-warning rounded-lg" name="is_payment_enabled"
                                data-label="Payments" wire:model.live="isPaymentEnabled" />
                        </div>
                    </div>
                </x-card.header>
            </x-card>
        @endif
    </div>
</div>
