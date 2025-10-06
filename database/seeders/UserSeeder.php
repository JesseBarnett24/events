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
        \App\Models\User::factory()->create([
            'name' => 'Test Organiser',
            'email' => 'organiser@example.com',
            'password' => bcrypt('password123'),
            'role' => 'organiser',
        ]);
        // Create Attendees
        User::factory()->count(8)->state(['role' => 'attendee'])->create();
        \App\Models\User::factory()->create([
            'name' => 'Test Attendee',
            'email' => 'attendee@example.com',
            'password' => bcrypt('password123'),
            'role' => 'attendee',
        ]);
        
    }
}
