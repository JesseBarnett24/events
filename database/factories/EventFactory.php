<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class EventFactory extends Factory
{
    //create events from seeder
    //return void
    public function definition()
    {
        return [
            'title' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph(),
            'starts_at' => $this->faker->dateTimeBetween('+1 day', '+6 months'),
            'location' => $this->faker->city(),
            'capacity' => $this->faker->numberBetween(10, 200),
            'organiser_id' => null, // Set in seeder
        ];
    }
}
