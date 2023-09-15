<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StoreAddress extends Seeder
{

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\StoreAddress::create([
            'name' => 'Okanemo Store',
            'full_address' => "Jalan Karang Tengah Raya, Ruko Bona Indah Plaza Blok A2/A1, Lt.3",
            'province_id' => 6,
            'city_id' => 153,
            'district_id' => 2103,
            'postal_code' => 12440,
            'default' => true,
        ]);
    }
}
