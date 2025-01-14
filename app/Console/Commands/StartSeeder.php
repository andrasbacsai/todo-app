<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class StartSeeder extends Command
{
    protected $signature = 'start:seeder {--production}';

    protected $description = 'Start Seeder';

    public function handle()
    {
        if (config('constants.seeder.is_seeder_enabled')) {
            $this->info('Seeder is enabled on this server.');
            if ($this->option('production')) {
                $this->call('db:seed', ['--class' => 'ProductionSeeder', '--force' => true]);
            } else {
                $this->call('db:seed', ['--force' => true]);
            }
            exit(0);
        } else {
            exit(0);
        }
    }
}
