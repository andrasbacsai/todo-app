<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('instance.is_self_hosted', true);
    }

    public function down(): void
    {
        $this->migrator->delete('instance.is_self_hosted');
    }
};
