<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ShippingFee;

class ShippingFeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        ShippingFee::create([
            'amount' => 10000,
            'amount_type' => 'fixed',
            'courir' => json_encode([
                "dse" => false,
                "ide" => false,
                "idl" => false,
                "jet" => false,
                "jne" => true,
                "jnt" => true,
                "jtl" => false,
                "ncs" => false,
                "pos" => true,
                "rex" => false,
                "rpx" => true,
                "sap" => false,
                "lion" => false,
                "slis" => false,
                "star" => false,
                "tiki" => true,
                "first" => false,
                "indah" => false,
                "ninja" => false,
                "pandu" => true,
                "pahala" => false,
                "wahana" => true,
                "sentral" => true,
                "sicepat" => true,
                "anteraja" => true
            ]),
        ]);
    }

}
