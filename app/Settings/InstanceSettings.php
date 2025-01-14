<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class InstanceSettings extends Settings
{
    public bool $is_self_hosted = true;

    public bool $is_registration_enabled = true;

    public bool $is_payment_enabled = false;

    public ?string $stripe_key;

    public ?string $stripe_secret;

    public ?string $stripe_webhook_secret;

    public ?array $subscriptions;

    public ?array $one_time_payments;

    public static function group(): string
    {
        return 'instance';
    }

    public static function isSelfHosted(): bool
    {
        return app(self::class)->is_self_hosted;
    }

    public static function isCloud(): bool
    {
        return ! app(self::class)->is_self_hosted;
    }

    public static function setCloud(bool $value): void
    {
        app(self::class)->is_self_hosted = ! $value;
        app(self::class)->save();
    }
}
