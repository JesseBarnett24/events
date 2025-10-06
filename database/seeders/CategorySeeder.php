<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    // Seed the database with a predefined list of event categories
    // @return void
    public function run()
    {
        $categories = ['Music', 'Tech', 'Food', 'Sports', 'Art'];

        // Create a record for each category name in the list
        foreach ($categories as $cat) {
            Category::create(['name' => $cat]);
        }
    }
}
