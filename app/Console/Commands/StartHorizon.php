<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class StartHorizon extends Command
{
    protected $signature = 'start:horizon';

    protected $description = 'Start Horizon';

    public function handle()
    {
        if (config('constants.horizon.is_horizon_enabled')) {
            $this->info('Horizon is enabled on this server.');
            $this->call('horizon');
            exit(0);
        } else {
            exit(0);
        }
    }
}
