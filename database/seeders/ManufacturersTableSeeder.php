<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ManufacturersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('manufacturers')->insert([
            'name' => 'Apple',
            'updated_at' => DB::raw('CURRENT_TIMESTAMP')
        ]);        
        DB::table('manufacturers')->insert([
            'name' => 'Samsung',
            'updated_at' => DB::raw('CURRENT_TIMESTAMP')
        ]);
        DB::table('manufacturers')->insert([
            'name' => 'Nokia',
            'updated_at' => DB::raw('CURRENT_TIMESTAMP')
        ]);
    }
}
