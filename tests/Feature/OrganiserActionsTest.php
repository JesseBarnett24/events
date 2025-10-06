<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Event;
use App\Models\Category;
use App\Models\Booking;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OrganiserActionsTest extends TestCase
{
    use RefreshDatabase;

    /** Requirement: An Organiser can log in and view their specific dashboard. */
    public function test_an_organiser_can_log_in_and_view_their_specific_dashboard()
    {
        $org = User::factory()->create(['role' => 'organiser', 'password' => bcrypt('password123')]);

        $this->post('/login', ['email' => $org->email, 'password' => 'password123'])->assertRedirect();
        $this->assertAuthenticatedAs($org);

        $this->get("/organiser/{$org->id}")
             ->assertOk()
             ->assertSee('Organiser Dashboard');
    }

    /** Requirement: An Organiser can successfully create an event with valid data. */
    public function test_an_organiser_can_successfully_create_an_event_with_valid_data()
    {
        $org = User::factory()->create(['role' => 'organiser']);
        $cats = Category::factory()->count(3)->create();

        $payload = [
            'title' => 'My New Event',
            'description' => 'Details',
            'starts_at' => now()->addDays(3)->format('Y-m-d\TH:i'),
            'location' => 'Main Hall',
            'capacity' => 50,
            'categories' => $cats->pluck('id')->all(),
        ];

        $res = $this->actingAs($org)->post('/events', $payload);
        $res->assertRedirect();

        $this->assertDatabaseHas('events', [
            'title' => 'My New Event',
            'organiser_id' => $org->id,
        ]);
    }

    /** Requirement: An Organiser gets validation errors for invalid event data. */
    public function test_an_organiser_receives_validation_errors_for_invalid_event_data()
    {
        $org = User::factory()->create(['role' => 'organiser']);

        $payload = [
            'title' => '',                     // required
            'starts_at' => now()->subDay(),    // must be future
            'location' => '',                  // required
            'capacity' => 0,                   // min 1
            'categories' => [],                // at least one
        ];

        $this->actingAs($org)->post('/events', $payload)
            ->assertSessionHasErrors(['title', 'starts_at', 'location', 'capacity', 'categories']);
    }

    /** Requirement: An Organiser can successfully update an event they own. */
    public function test_an_organiser_can_successfully_update_an_event_they_own()
    {
        $org = User::factory()->create(['role' => 'organiser']);
        $event = Event::factory()->create(['organiser_id' => $org->id, 'title' => 'Old Title']);
        $cats = Category::factory()->count(2)->create();

        $res = $this->actingAs($org)->put("/events/{$event->id}", [
            'title' => 'Updated Title',
            'description' => 'New',
            'starts_at' => now()->addDays(5)->format('Y-m-d\TH:i'),
            'location' => 'Room A',
            'capacity' => 120,
            'categories' => $cats->pluck('id')->all(),
        ]);

        $res->assertRedirect();
        $this->assertDatabaseHas('events', ['id' => $event->id, 'title' => 'Updated Title']);
    }

    /** Requirement: An Organiser cannot update an event created by another Organiser. */
    public function test_an_organiser_cannot_update_an_event_created_by_another_organiser()
    {
        $organiser1 = \App\Models\User::factory()->create(['role' => 'organiser']);
        $organiser2 = \App\Models\User::factory()->create(['role' => 'organiser']);

        $event = \App\Models\Event::factory()->create([
            'organiser_id' => $organiser1->id,
            'title' => 'Original Event',
        ]);

        $payload = ['title' => 'Updated by Wrong Organiser'];

        $response = $this->actingAs($organiser2)
                        ->from("/events/{$event->id}/edit")
                        ->put("/events/{$event->id}", $payload);

        // ✅ Expect redirect back
        $response->assertRedirect();

        // ✅ Match your actual flash message
        $response->assertSessionHas('error', 'Unauthorised action.');

        // ✅ Ensure the event title was NOT changed
        $this->assertDatabaseHas('events', [
            'id' => $event->id,
            'title' => 'Original Event',
        ]);
    }

    /** Requirement: An Organiser can delete an event they own with no bookings. */
    public function test_an_organiser_can_delete_an_event_they_own_that_has_no_bookings()
    {
        $org = User::factory()->create(['role' => 'organiser']);
        $event = Event::factory()->create(['organiser_id' => $org->id]);

        $this->actingAs($org)->delete("/events/{$event->id}")
             ->assertRedirect('/events');

        $this->assertDatabaseMissing('events', ['id' => $event->id]);
    }

    /** Requirement: An Organiser cannot delete an event with active bookings. */
    public function test_an_organiser_cannot_delete_an_event_that_has_active_bookings()
    {
        $org = User::factory()->create(['role' => 'organiser']);
        $event = Event::factory()->create(['organiser_id' => $org->id, 'capacity' => 5]);
        $att = User::factory()->create(['role' => 'attendee']);
        Booking::create(['user_id' => $att->id, 'event_id' => $event->id]);

        $this->actingAs($org)->delete("/events/{$event->id}")
             ->assertRedirect()
             ->assertSessionHas('error');

        $this->assertDatabaseHas('events', ['id' => $event->id]);
    }
}
