<?php

namespace App\Console;

use App\Console\Commands\ClearExpiredCarts;
use App\Console\Commands\CompleteOrders;
use App\Console\Commands\ExpireCoupons;
use App\Console\Commands\ExpirePendingOrders;
use App\Console\Commands\RejectExpiredRefunds;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        CompleteOrders::class,
        ClearExpiredCarts::class,
        ExpireCoupons::class,
        ExpirePendingOrders::class,
        RejectExpiredRefunds::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('orders:complete')->everyMinute();
        $schedule->command('orders:expire-pending')->everyMinute();
        $schedule->command('coupons:expire')->everyMinute();
        $schedule->command('carts:clear-expired')->everyMinute();
        $schedule->command('refunds:reject-expired')->hourly();

    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
