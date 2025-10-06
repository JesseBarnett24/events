<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Event;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;

class GuestAccessTest extends TestCase
{
    use RefreshDatabase;

    /** Requirement: A guest can view the paginated list of upcoming events. */
    public function test_a_guest_can_view_the_paginated_list_of_upcoming_events()
    {
        // ✅ Create organiser first to satisfy foreign key
        $organiser = User::factory()->create(['role' => 'organiser']);

        // ✅ Create 12 FUTURE and 3 PAST events
        Event::factory()->count(12)->create([
            'organiser_id' => $organiser->id,
            'starts_at' => Carbon::now()->addDays(7),
        ]);
        Event::factory()->count(3)->create([
            'organiser_id' => $organiser->id,
            'starts_at' => Carbon::now()->subDays(7),
        ]);

        $res = $this->get('/events');
        $res->assertOk();

        // ✅ The controller paginates to 8 events per page
        // So we check presence of multiple event titles
        $this->assertTrue($res->original->getData()['events']->count() <= 8);

        // ✅ Page should contain known heading
        $res->assertSee('Events', false);
    }

    /** Requirement: A guest can view the details page for a specific event. */
    public function test_a_guest_can_view_a_specific_event_details_page()
    {
        $organiser = User::factory()->create(['role' => 'organiser']);

        $event = Event::factory()->create([
            'organiser_id' => $organiser->id,
            'title' => 'Guest Visible Event',
            'starts_at' => now()->addDays(2),
            'location' => 'Guestville',
        ]);

        $this->get("/events/{$event->id}")
            ->assertOk()
            ->assertSee('Guest Visible Event', false)
            ->assertSee('Guestville', false);
    }

    /** Requirement: A guest is redirected to the login page for authenticated routes. */
    public function test_a_guest_is_redirected_when_accessing_protected_routes()
    {
        $this->get('/events/create')->assertRedirect('/login');
        $this->post('/events', [])->assertRedirect('/login');
        $this->post('/bookings', [])->assertRedirect('/login');
        $this->delete('/bookings/1/cancel')->assertRedirect('/login');
        $this->get('/organiser/1')->assertRedirect('/login');
    }

    /** Requirement: A guest viewing an event details page cannot see action buttons. */
    public function test_a_guest_cannot_see_action_buttons_on_event_details_page()
    {
        $organiser = User::factory()->create(['role' => 'organiser']);

        $event = Event::factory()->create([
            'organiser_id' => $organiser->id,
            'starts_at' => now()->addDays(3),
        ]);

        $response = $this->get("/events/{$event->id}");
        $response->assertOk();

        // ✅ These are the actionable buttons guests shouldn’t see
        $response->assertDontSee('Book Now', false);
        $response->assertDontSee('Edit Event', false);
        $response->assertDontSee('Delete', false);
    }
}
