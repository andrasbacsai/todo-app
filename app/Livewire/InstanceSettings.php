<?php

namespace App\Livewire;

use App\Settings\InstanceSettings as SettingsInstanceSettings;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;

class InstanceSettings extends Component
{
    #[Title('Instance Settings')]
    public bool $isCloud;

    public bool $isRegistrationEnabled;

    public bool $isPaymentEnabled;

    #[Validate(['nullable', 'string'], ['stripe_key.string' => 'Stripe Key must be a string'])]
    public ?string $stripeKey = null;

    #[Validate(['nullable', 'string'], ['stripe_secret.string' => 'Stripe Secret must be a string'])]
    public ?string $stripeSecret = null;

    #[Validate(['nullable', 'string'], ['stripe_webhook_secret.string' => 'Stripe Webhook Secret must be a string'])]
    public ?string $stripeWebhookSecret = null;

    #[Validate(['nullable', 'array'], ['subscriptions.array' => 'Subscriptions must be an array'])]
    public array $subscriptions = [];

    #[Validate(['nullable', 'string'], ['subscription_name.string' => 'Subscription Name must be a string'])]
    public ?string $subscriptionName = null;

    #[Validate(['nullable', 'string'], ['subscription_price_id.string' => 'Subscription Price ID must be a string'])]
    public ?string $subscriptionPriceId = null;

    #[Validate(['nullable', 'boolean'], ['is_one_time_payment.boolean' => 'Is One Time Payment must be a boolean'])]
    public ?bool $isOneTimePayment = false;

    public array $editedSubscriptions = [];

    public function mount(SettingsInstanceSettings $instanceSettings)
    {
        $this->isCloud = $instanceSettings->isCloud();
        $this->isPaymentEnabled = $instanceSettings->is_payment_enabled;
        $this->isRegistrationEnabled = $instanceSettings->is_registration_enabled;
        $this->stripeKey = $instanceSettings->stripe_key;
        $this->stripeSecret = $instanceSettings->stripe_secret;
        $this->stripeWebhookSecret = $instanceSettings->stripe_webhook_secret;
        $this->subscriptions = $instanceSettings->subscriptions;
        $this->editedSubscriptions = $this->getEditedSubscriptions();
    }

    private function getEditedSubscriptions()
    {
        return collect($this->subscriptions)->mapWithKeys(function ($subscription) {
            return [$subscription['name'] => [
                'name' => $subscription['name'],
                'price_id' => $subscription['price_id'],
                'is_one_time_payment' => $subscription['is_one_time_payment'] ?? false,
            ]];
        })->toArray();
    }

    public function updated(SettingsInstanceSettings $instanceSettings)
    {
        $instanceSettings->is_payment_enabled = $this->isPaymentEnabled;
        $instanceSettings->is_registration_enabled = $this->isRegistrationEnabled;
        $instanceSettings->save();
    }

    public function saveStripeKeys(SettingsInstanceSettings $instanceSettings)
    {
        $instanceSettings->stripe_key = $this->stripeKey;
        $instanceSettings->stripe_secret = $this->stripeSecret;
        $instanceSettings->stripe_webhook_secret = $this->stripeWebhookSecret;
        $instanceSettings->save();
    }

    public function addSubscription(SettingsInstanceSettings $instanceSettings)
    {
        $nameAlreadyExists = collect($instanceSettings->subscriptions)->contains('name', $this->subscriptionName);
        if ($nameAlreadyExists) {
            $this->addError('subscriptionName', 'Subscription with this name already exists');

            return;
        }
        $priceIdAlreadyExists = collect($instanceSettings->subscriptions)->contains('price_id', $this->subscriptionPriceId);
        if ($priceIdAlreadyExists) {
            $this->addError('subscriptionPriceId', 'Subscription with this price ID already exists');

            return;
        }
        $instanceSettings->subscriptions[] = [
            'name' => $this->subscriptionName,
            'price_id' => $this->subscriptionPriceId,
            'is_one_time_payment' => $this->isOneTimePayment,
        ];
        $instanceSettings->save();
        $this->subscriptions = $instanceSettings->subscriptions;
        $this->editedSubscriptions = $this->getEditedSubscriptions();
        $this->dispatch('dialog-close');
    }

    public function deleteSubscription(SettingsInstanceSettings $instanceSettings, $name)
    {
        $instanceSettings->subscriptions = collect($instanceSettings->subscriptions)->filter(function ($subscription) use ($name) {
            return $subscription['name'] !== $name;
        })->toArray();
        $instanceSettings->save();
        $this->subscriptions = $instanceSettings->subscriptions;
        $this->editedSubscriptions = $this->getEditedSubscriptions();
    }

    public function updateSubscription(SettingsInstanceSettings $instanceSettings, $originalName)
    {
        $newData = $this->editedSubscriptions[$originalName];

        if ($newData['name'] !== $originalName) {
            $nameExists = collect($instanceSettings->subscriptions)
                ->where('name', $newData['name'])
                ->isNotEmpty();

            if ($nameExists) {
                $this->addError("editedSubscriptions.{$originalName}.name", 'This name already exists');

                return;
            }
        }

        $instanceSettings->subscriptions = collect($instanceSettings->subscriptions)
            ->map(function ($subscription) use ($originalName, $newData) {
                if ($subscription['name'] === $originalName) {
                    return [
                        'name' => $newData['name'],
                        'price_id' => $newData['price_id'],
                        'is_one_time_payment' => $newData['is_one_time_payment'],
                    ];
                }

                return $subscription;
            })->toArray();

        $instanceSettings->save();
        $this->subscriptions = $instanceSettings->subscriptions;

        if ($newData['name'] !== $originalName) {
            $this->editedSubscriptions[$newData['name']] = $this->editedSubscriptions[$originalName];
            unset($this->editedSubscriptions[$originalName]);
        }
    }

    public function render()
    {
        return view('livewire.instance-settings');
    }
}
