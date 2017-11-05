<?php

use Illuminate\Database\Seeder;

class UserAdminsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\UserAdmin::create([
            'name' => 'admin_zcl',
            'email' => 'admin@qq.com',
            'phone' => '13246305540',
            'password' => bcrypt('123456')
        ]);
    }
}
