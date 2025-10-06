<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    // Run all database seeders to populate the system with initial data
    // @return void
    public function run()
    {
        $this->call([
            UserSeeder::class,
            CategorySeeder::class,
            EventSeeder::class,
            BookingSeeder::class,
        ]);
    }
}
