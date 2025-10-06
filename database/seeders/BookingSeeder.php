<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Booking;
use App\Models\User;
use App\Models\Event;

class BookingSeeder extends Seeder
{
    // Seed the database with random bookings linking attendees to events
    // @return void
    public function run()
    {
        $attendees = User::where('role', 'attendee')->get();
        $events = Event::all();

        // Assign each attendee between 1 and 3 random event bookings
        foreach ($attendees as $attendee) {
            $attendeeEvents = $events->random(rand(1, 3));
            foreach ($attendeeEvents as $event) {
                Booking::firstOrCreate([
                    'user_id' => $attendee->id,
                    'event_id' => $event->id,
                ]);
            }
        }
    }
}
