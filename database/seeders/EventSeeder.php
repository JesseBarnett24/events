<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Event;
use App\Models\User;
use App\Models\Category;

class EventSeeder extends Seeder
{
    public function run()
    {
        $organisers = User::where('role', 'organiser')->get();

        Event::factory(10)->create([
            'organiser_id' => $organisers->random()->id,
        ])->each(function ($event) {
            $event->categories()->attach(
                Category::inRandomOrder()->take(rand(1,3))->pluck('id')
            );
        });
    }
}
