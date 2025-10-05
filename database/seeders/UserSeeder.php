<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Create Organisers
        User::factory()->count(2)->state(['role' => 'organiser'])->create();
        // Create Attendees
        User::factory()->count(8)->state(['role' => 'attendee'])->create();
    }
}
