<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class StartMigration extends Command
{
    protected $signature = 'start:migration';

    protected $description = 'Start Migration';

    public function handle()
    {
        if (config('constants.migration.is_migration_enabled')) {
            $this->info('Migration is enabled on this server.');
            $this->call('migrate', ['--force' => true]);
            exit(0);
        } else {
            exit(0);
        }
    }
}
