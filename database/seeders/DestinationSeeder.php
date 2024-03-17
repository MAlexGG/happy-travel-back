<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DestinationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('destinations')->insert([
            [
                'id' => 1,
                'title' => 'Tortuga Bay',
                'location' => 'Galapagos Islands',
                'image' => 'img/galapagos.jpg',
                'user_id' => 1,
                'created_at' => '2023/03/26'
            ],
            [
                'id' => 2,
                'title' => 'Kamakura',
                'location' => 'Japan',
                'image' => 'img/kamakura.jpg',
                'user_id' => 1,
                'created_at' => '2023/03/26'
            ],
            [
                'id' => 3,
                'title' => 'Tibidabo',
                'location' => 'Barcelona',
                'image' => 'img/tibidabo.jpg',
                'user_id' => 2,
                'created_at' => '2023/03/26'
            ]
        ]);
    }
}
