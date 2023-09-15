<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = array(
            array(
                'name' => 'Admin',
                'email' => 'admin@gmail.com',
                'email_verified_at' => Carbon::now(),
                'password' => Hash::make('1111'),
                'role' => 'admin',
                'status' => 'active'
            ),
            array(
                'name' => 'User',
                'email' => 'user@gmail.com',
                'email_verified_at' => Carbon::now(),
                'password' => Hash::make('1111'),
                'role' => 'user',
                'status' => 'active'
            ),
        );

        DB::table('users')->insert($users);
    }
}
