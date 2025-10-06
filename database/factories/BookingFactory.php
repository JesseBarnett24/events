<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Event;
use Illuminate\Database\Eloquent\Factories\Factory;

class BookingFactory extends Factory
{
    //create bookings from seeder
    //return void
    public function definition()
    {
        return [
            'user_id' => User::factory()->state(['role' => 'attendee']),
            'event_id' => Event::factory(),
        ];
    }
}
