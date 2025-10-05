<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('products')->insert([
            'name' => 'iPhone 6',
            'price' => 600,
            'manufacturer_id' => 1,
            'image'=>'product_images/wizard.jpg',
            'updated_at' => DB::raw('CURRENT_TIMESTAMP'),
            ]);
            DB::table('products')->insert([
            'name' => 'Note 4',
            'price' => 567,
            'manufacturer_id' => 2,
            'image'=>'product_images/wizard.jpg',
            'updated_at' => DB::raw('CURRENT_TIMESTAMP'),
            ]);
    }
}
