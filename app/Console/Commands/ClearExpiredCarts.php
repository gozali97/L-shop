<?php

namespace App\Console\Commands;

use App\Models\Cart;
use App\Models\ScheduleLog;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ClearExpiredCarts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'carts:clear-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear expired carts by removing the product items';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $expiredCarts = Cart::query()
            ->where('order_id', null)
            ->where('created_at', '<', Carbon::now()->subDays(60))
            ->get();

            $insertedId = \DB::connection('logging_db')->table('schedule_logs')->insertGetId([
                'jobs_id' => \Helper::generateSchedulerId(),
                'brands' => \Helper::getSetting()->brand_name,
                'job_type' => 'clear expired carts',
                'job_group' => 'command',
                'data' => json_encode($expiredCarts),
                'fetched' => now(),
                'completed' => now(),
                'status' => 'completed',
            ]);

            \DB::connection('logging_db')->table('schedule_logs')->where('id', $insertedId)->update([
                'completed' => now(),
            ]);

            foreach ($expiredCarts as $cart) {
                try {
                    $cart->delete();
                } catch (\Exception $e) {
                    $insertedId = \DB::connection('logging_db')->table('schedule_logs')->insertGetId([
                        'jobs_id' => \Helper::generateSchedulerId(),
                        'brands' => \Helper::getSetting()->brand_name,
                        'job_type' => 'clear expired carts',
                        'job_group' => 'command',
                        'data' => json_encode($expiredCarts),
                        'fetched' => now(),
                        'completed' => now(),
                        'status' => 'failed',
                        'failure_message' => $e->getMessage(),
                    ]);

                    \DB::connection('logging_db')->table('schedule_logs')->where('id', $insertedId)->update([
                        'completed' => now(),
                    ]);
                }
            }


        $this->info('Cleared ' . count($expiredCarts) . ' expired carts.');
    }

}
