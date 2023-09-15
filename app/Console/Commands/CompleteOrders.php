<?php

namespace App\Console\Commands;

use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CompleteOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:complete';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically complete orders if not completed within 3x24 hours';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $orders = Order::where('status', 'shipping')
            ->where('updated_at', '<=', Carbon::now()->subDays(3))
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
            try {
            $order->status = 'completed';
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

        $this->info('Orders completed successfully.');
    }
}
