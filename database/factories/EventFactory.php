<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class EventFactory extends Factory
{
    public function definition()
    {
        return [
            'title' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph(),
            'starts_at' => $this->faker->dateTimeBetween('+1 day', '+3 months'),
            'location' => $this->faker->city(),
            'capacity' => $this->faker->numberBetween(20, 300),
            'organiser_id' => User::factory()->state(['role' => 'organiser']),
        ];
    }
}
