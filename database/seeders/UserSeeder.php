<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Event;
use App\Models\Category;

class UserSeeder extends Seeder
{
    // Seed the database with sample organisers, attendees, and test accounts
    // @return void
    public function run()
    {
        // Create 5 generic organisers
        User::factory()->count(5)->state(['role' => 'organiser'])->create();

        // Create 200 attendees for testing and demo purposes
        User::factory()->count(200)->state(['role' => 'attendee'])->create();

        // Create a specific test organiser account for login and testing
        $testOrganiser = User::factory()->create([
            'name' => 'Test Organiser',
            'email' => 'organiser@example.com',
            'password' => bcrypt('password123'),
            'role' => 'organiser',
        ]);

        // Generate 5 events owned by the test organiser
        $events = Event::factory(5)->create([
            'organiser_id' => $testOrganiser->id,
        ]);

        // Randomly assign 1–3 categories to each created event
        $events->each(function ($event) {
            $event->categories()->attach(
                Category::inRandomOrder()->take(rand(1, 3))->pluck('id')
            );
        });

        // Create a specific test attendee account for login and booking tests
        User::factory()->create([
            'name' => 'Test Attendee',
            'email' => 'attendee@example.com',
            'password' => bcrypt('password123'),
            'role' => 'attendee',
        ]);
    }
}
