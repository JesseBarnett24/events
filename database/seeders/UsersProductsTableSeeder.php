<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UsersProductsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users_products')->insert([
            'user_id' => 1,
            'product_id' => 1,
            'quantity' => 10,
            'updated_at' => DB::raw('CURRENT_TIMESTAMP'),
        ]);
        DB::table('users_products')->insert([
            'user_id' => 1,
            'product_id' => 2,
            'quantity' => 20,
            'updated_at' => DB::raw('CURRENT_TIMESTAMP'),
        ]);
            DB::table('users_products')->insert([
            'user_id' => 2,
            'product_id' => 1,
            'quantity' => 10,
            'updated_at' => DB::raw('CURRENT_TIMESTAMP'),
        ]);
    }
}
