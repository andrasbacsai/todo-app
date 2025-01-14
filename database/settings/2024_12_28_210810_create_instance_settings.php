<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('instance.is_registration_enabled', true);
        $this->migrator->add('instance.is_payment_enabled', false);

        $this->migrator->add('instance.stripe_key', null, true);
        $this->migrator->add('instance.stripe_secret', null, true);
        $this->migrator->add('instance.stripe_webhook_secret', null, true);

        $this->migrator->add('instance.subscriptions', []);
        $this->migrator->add('instance.one_time_payments', []);
    }
};
