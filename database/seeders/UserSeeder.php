<?php

namespace Database\Seeders;

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
            [
                'id' => '1',
                'name' => 'Alex',
                'email' => 'a@mail.com',
                'email_verified_at' => null,
                'password' => Hash::make('123456789'),
                'created_at' => '2023/03/26'
            ],
            [
                'id' => '2',
                'name' => 'Eli',
                'email' => 'e@mail.com',
                'email_verified_at' => null,
                'password' => Hash::make('123456789'),
                'created_at' => '2023/03/26'
            ]
        ]);
    }
}
