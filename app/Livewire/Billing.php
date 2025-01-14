<?php

namespace App\Livewire;

use App\Settings\InstanceSettings;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Title;
use Livewire\Component;

class Billing extends Component
{
    #[Title('Billing')]

    #[Locked]
    public $subscriptions;

    public function mount(InstanceSettings $instanceSettings)
    {
        if (InstanceSettings::isSelfHosted()) {
            return redirect()->route('dashboard');
        }

        $this->subscriptions = $instanceSettings->subscriptions;
    }

    public function subscribeToSubscription($name)
    {
        $subscription = collect($this->subscriptions)->firstWhere('name', $name);
        session()->put('price_id', $subscription['price_id']);

        if ($subscription['is_one_time_payment']) {
            return redirect()->route('one-time-payment-checkout');
        }

        return redirect()->route('subscription-checkout');
    }

    public function render()
    {
        return view('livewire.billing');
    }
}
