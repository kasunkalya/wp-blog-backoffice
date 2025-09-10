<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Console\Scheduling\Schedule;
use App\Console\Commands\WpSyncCommand;

class ConsoleServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->commands([
            WpSyncCommand::class,
        ]);
    }

    public function boot(Schedule $schedule): void
    {    
        $schedule->command('wp:sync-all')->hourly();
    }
}
