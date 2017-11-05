<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\User::create([
            'name' => 'user_zcl',
            'email' => 'qq@qq.com',
            'phone' => '13246305541',
            'created_ip' => '2130706433',
            'password' => bcrypt('123456')
        ]);
    }
}
