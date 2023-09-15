<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class SettingTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $theme = array(
            'primary_color' => '#FFFFFF',
            'secondary_color' => '#FFFFFF',
            'heading_color' => '#FFFFFF',
            'text_color' => '#FFFFFF',
            'primary_button_color' => '#FFFFFF',
            'secondary_button_color' => '#FFFFFF'
        );
        $setting = array(
            'brand_name' => "ASIMA",
            'description' => "Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum. sed ut perspiciatis unde sunt in culpa qui officia deserunt mollit anim id est laborum. sed ut perspiciatis unde omnis iste natus error sit voluptatem Excepteu sunt in culpa qui officia deserunt mollit anim id est laborum. sed ut perspiciatis Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum. sed ut perspi deserunt mollit anim id est laborum. sed ut perspi.",
            'short_des' => "Praesent dapibus, neque id cursus ucibus, tortor neque egestas augue, magna eros eu erat. Aliquam erat volutpat. Nam dui mi, tincidunt quis, accumsan porttitor, facilisis luctus, metus.",
            'photo' => "assets/b1d8f65bc159d6782d6115d99842b2cc9ade49f4165189962b2957afef2f0957.jpeg",
            'brand_logo' => "assets/eeee5e36d8908a57734131029473c5856db2de196dbb080fa6560df8f23ddb05.png",
            'address' => "NO. 342 - London Oxford Street, 012 United Kingdom",
            'email' => "eshop@gmail.com",
            'phone' => "+060 (800) 801-582",
            'socmed_facebook' => 'asimastaging',
            'socmed_instagram' => 'asimastaging',
            'socmed_wa' => '81280047477',
            'theme' => $theme,
        );
        \App\Models\Settings::create($setting);
    }
}
