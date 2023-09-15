<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BannerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $banner= array(
            'title' => "banner 1",
            'slug' => "banner1",
            'description' => "<p>Praesent dapibus, neque id cursus ucibus, tortor neque egestas augue, magna eros eu erat. Aliquam erat volutpat. Nam dui mi, tincidunt quis, accumsan porttitor, facilisis luctus, metus.</p>",
            'photo' => "banner/0e2c1d97a56e6498274b19a6ccc517bd646db890439d823833f9e0381b662d44.jpeg",
            'status' => "active",
        );
        \App\Models\Banner::create($banner);
    }
}
