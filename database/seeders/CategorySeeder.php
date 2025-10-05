<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run()
    {
        $categories = ['Music', 'Tech', 'Food', 'Sports', 'Art'];
        foreach ($categories as $cat) {
            Category::create(['name' => $cat]);
        }
    }
}
