<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            'name' => "admin",
            'email' => "admin@admin.com",
            'password' => Hash::make('1111111111'),
            'permissions'=> '{"platform.systems.roles":true,"platform.systems.users":true,"platform.systems.attachment":true,"platform.index":true}'
        ]);
    }
}
