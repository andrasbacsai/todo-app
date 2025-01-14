<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class Init extends Command
{
    protected $signature = 'app:init {--production}';

    protected $description = 'Initialize the application';

    public function handle()
    {
        $this->info('Initializing application...');

        if ($this->option('production')) {
            if (config('database.default') == 'sqlite') {
                $this->createDatabase();
            }

            return;
        }
        $this->createLogFile();
        $this->createDatabase();
        $this->migrate();
        Artisan::call('optimize:clear');

    }

    private function createLogFile()
    {
        $this->info('Creating laravel.log file...');
        if (! file_exists(storage_path('logs/laravel.log'))) {
            touch(storage_path('logs/laravel.log'));
        }
    }

    private function createDatabase()
    {
        $dbPath = config('database.connections.sqlite.database');
        $this->info("Database path: {$dbPath}");
        $dbDir = dirname($dbPath);

        if (! file_exists($dbDir)) {
            mkdir($dbDir, 0755, true);
            $this->info("Created database directory: {$dbDir}");
        }

        if (! file_exists($dbPath)) {
            touch($dbPath);
            chmod($dbPath, 0755);
            $this->info("Created database file: {$dbPath}");
        }
    }

    private function migrate()
    {
        $this->info('Migrating database...');
        Artisan::call('migrate');
    }
}
