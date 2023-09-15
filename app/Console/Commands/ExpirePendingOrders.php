<?php

namespace App\Console\Commands;

use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ExpirePendingOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:expire-pending';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Expire pending orders that are not confirmed within 24 hours';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $orders = Order::where('status', 'Pending')
            ->where('created_at', '<=', Carbon::now()->subDay())
            ->get();

        $insertedId = \DB::connection('logging_db')->table('schedule_logs')->insertGetId([
            'jobs_id' => \Helper::generateSchedulerId(),
            'brands' => \Helper::getSetting()->brand_name,
            'job_type' => 'clear expired carts',
            'job_group' => 'command',
            'data' => json_encode($orders),
            'fetched' => now(),
            'completed' => now(),
            'status' => 'completed',
        ]);

        \DB::connection('logging_db')->table('schedule_logs')->where('id', $insertedId)->update([
            'completed' => now(),
        ]);

        foreach ($orders as $order) {
            try{
                $order->status = 'expired';
                $order->save();
            } catch (\Exception $e) {

                $insertedId = \DB::connection('logging_db')->table('schedule_logs')->insertGetId([
                    'jobs_id' => \Helper::generateSchedulerId(),
                    'brands' => \Helper::getSetting()->brand_name,
                    'job_type' => 'clear expired carts',
                    'job_group' => 'command',
                    'data' => json_encode($orders),
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

        $this->info('Expired ' . count($orders) . ' pending orders.');
    }
}
