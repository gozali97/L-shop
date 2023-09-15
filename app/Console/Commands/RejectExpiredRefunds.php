<?php

namespace App\Console\Commands;

use App\Models\Refund;
use Carbon\Carbon;
use Illuminate\Console\Command;

class RejectExpiredRefunds extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'refunds:reject-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reject expired refunds if no response from admin';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $expiredRefunds = Refund::query()
            ->where('status', 'pending')
            ->where('created_at', '<=', Carbon::now()->subHours(48))
            ->get();

        $insertedId = \DB::connection('logging_db')->table('schedule_logs')->insertGetId([
            'jobs_id' => \Helper::generateSchedulerId(),
            'brands' => \Helper::getSetting()->brand_name,
            'job_type' => 'clear expired carts',
            'job_group' => 'command',
            'data' => json_encode($expiredRefunds),
            'fetched' => now(),
            'completed' => now(),
            'status' => 'completed',
        ]);

        \DB::connection('logging_db')->table('schedule_logs')->where('id', $insertedId)->update([
            'completed' => now(),
        ]);

        foreach ($expiredRefunds as $refund) {
            try{
            $refund->status = 'rejected';
            $refund->save();
            } catch (\Exception $e) {

                $insertedId = \DB::connection('logging_db')->table('schedule_logs')->insertGetId([
                    'jobs_id' => \Helper::generateSchedulerId(),
                    'brands' => \Helper::getSetting()->brand_name,
                    'job_type' => 'clear expired carts',
                    'job_group' => 'command',
                    'data' => json_encode($expiredRefunds),
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

        $this->info('Rejected ' . count($expiredRefunds) . ' expired refunds.');
    }
}
