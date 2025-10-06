<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CategoryFactory extends Factory
{
    //create Categories from seeder
    //return void
    public function definition()
    {
        return [
            'name' => $this->faker->unique()->word(),
        ];
    }
}
