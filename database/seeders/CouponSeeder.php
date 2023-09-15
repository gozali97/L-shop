<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CouponSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = array(
            array(
                'coupons_name' => 'Test 5 days',
                'code' => 'abc123',
                'type' => 'fixed',
                'value' => '300',
                    'start_date' => Carbon::now(),
                'end_date' => Carbon::now()->addDays(5),
                'status' => 'active'
            ),
            array(
                'coupons_name' => 'Test 7 days',
                'code' => '111111',
                'type' => 'percent',
                'value' => '10',
                'start_date' => Carbon::now(),
                'end_date' => Carbon::now()->addDays(14),
                'status' => 'active'
            ),
        );

        DB::table('coupons')->insert($data);
    }
}
