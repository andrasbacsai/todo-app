<?php

namespace App\View\Components;

use App\Settings\InstanceSettings;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Navigation extends Component
{
    public bool $isPaymentEnabled;

    public bool $isRegistrationEnabled;

    public bool $isRootUser;

    public bool $isCloudEnabled;

    public function __construct(InstanceSettings $instanceSettings, public array $breadcrumbs = [])
    {
        $this->isRootUser = auth()->user()->is_root_user;
        $this->isPaymentEnabled = $instanceSettings->is_payment_enabled;
        $this->isRegistrationEnabled = $instanceSettings->is_registration_enabled;
        $this->isCloudEnabled = $instanceSettings->isCloud();
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.navigation');
    }
}
