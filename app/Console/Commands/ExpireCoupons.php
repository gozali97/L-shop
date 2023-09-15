<?php

namespace App\Console\Commands;

use App\Models\Coupon;
use Carbon\Carbon;
use Illuminate\Console\Command;
use PHPUnit\TextUI\Help;

class ExpireCoupons extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'coupons:expire';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Expire coupons that have reached the end date';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $coupons = Coupon::where('end_date', '<=', Carbon::now())->get();

        $insertedId = \DB::connection('logging_db')->table('schedule_logs')->insertGetId([
            'jobs_id' => \Helper::generateSchedulerId(),
            'brands' => \Helper::getSetting()->brand_name,
            'job_type' => 'clear expired carts',
            'job_group' => 'command',
            'data' => json_encode($coupons),
            'fetched' => now(),
            'completed' => now(),
            'status' => 'completed',
        ]);

        \DB::connection('logging_db')->table('schedule_logs')->where('id', $insertedId)->update([
            'completed' => now(),
        ]);

        foreach ($coupons as $coupon) {
            try{
                $coupon->status = 'expired';
                $coupon->save();
            } catch (\Exception $e) {
                $insertedId = \DB::connection('logging_db')->table('schedule_logs')->insertGetId([
                    'jobs_id' => \Helper::generateSchedulerId(),
                    'brands' => \Helper::getSetting()->brand_name,
                    'job_type' => 'clear expired carts',
                    'job_group' => 'command',
                    'data' => json_encode($coupons),
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

        $this->info('Expired ' . count($coupons) . ' coupons.');
    }


}
