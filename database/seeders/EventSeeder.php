<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Event;
use App\Models\User;
use App\Models\Category;

class EventSeeder extends Seeder
{
    // Seed the database with events and assign them to organisers with categories
    // @return void
    public function run()
    {
        $organisers = User::where('role', 'organiser')->get();

        // Create 40 events distributed randomly among the organisers
        $events = Event::factory(40)->make()->each(function ($event) use ($organisers) {
            $event->organiser_id = $organisers->random()->id;
            $event->save();

            // Attach between 1 and 3 random categories to each event
            $event->categories()->attach(
                Category::inRandomOrder()->take(rand(1, 3))->pluck('id')
            );
        });
    }
}
