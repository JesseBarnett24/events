<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Event;
use App\Models\Booking;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AttendeeActionsTest extends TestCase
{
    use RefreshDatabase;

    /** Requirement: A user can successfully register as an Attendee. */
    public function test_a_user_can_successfully_register_as_an_attendee()
    {
        $payload = [
            'name' => 'Alice Attendee',
            'email' => 'alice@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'terms' => 'on', // Breeze uses 'terms' not 'agree'
        ];
        
        

        $res = $this->post('/register', $payload);
        $res->assertRedirect(); // should redirect to home or dashboard

        // Some Laravel installs don’t log users in automatically after register
        $this->assertDatabaseHas('users', [
            'email' => 'alice@example.com',
            'role'  => 'attendee',
        ]);

        $user = User::where('email', 'alice@example.com')->first();
        $this->assertNotNull($user);
    }

    /** Requirement: A registered Attendee can log in and log out. */
    public function test_a_registered_attendee_can_log_in_and_log_out()
    {
        $user = User::factory()->create([
            'role' => 'attendee',
            'password' => bcrypt('password123')
        ]);

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'password123'
        ])->assertRedirect();

        $this->assertAuthenticatedAs($user);

        $this->post('/logout')->assertRedirect();
        $this->assertGuest();
    }

    /** Requirement: A logged-in Attendee can book an available, upcoming event. */
    public function test_a_logged_in_attendee_can_book_an_available_upcoming_event()
    {
        $attendee = User::factory()->create(['role' => 'attendee']);
        $organiser = User::factory()->create(['role' => 'organiser']);
        $event = Event::factory()->create([
            'organiser_id' => $organiser->id,
            'capacity' => 10,
            'starts_at' => now()->addDays(1),
        ]);

        $this->actingAs($attendee)
             ->post('/bookings', ['event_id' => $event->id])
             ->assertRedirect();

        $this->assertDatabaseHas('bookings', [
            'user_id' => $attendee->id,
            'event_id' => $event->id,
        ]);
    }

    /** Requirement: After booking, the event is on their "My Bookings" page. */
    public function test_after_booking_an_attendee_can_see_the_event_on_their_bookings_page()
    {
        $attendee = User::factory()->create(['role' => 'attendee']);
        $organiser = User::factory()->create(['role' => 'organiser']);
        $event = Event::factory()->create([
            'organiser_id' => $organiser->id,
            'starts_at' => now()->addDays(2),
        ]);

        Booking::create(['user_id' => $attendee->id, 'event_id' => $event->id]);

        $this->actingAs($attendee)
             ->get('/bookings/mine')
             ->assertOk()
             ->assertSee($event->title);
    }

    /** Requirement: An Attendee cannot book the same event more than once. */
    public function test_an_attendee_cannot_book_the_same_event_more_than_once()
    {
        $attendee = User::factory()->create(['role' => 'attendee']);
        $organiser = User::factory()->create(['role' => 'organiser']);
        $event = Event::factory()->create([
            'organiser_id' => $organiser->id,
            'capacity' => 5,
            'starts_at' => now()->addDays(1),
        ]);

        $this->actingAs($attendee)->post('/bookings', ['event_id' => $event->id]);
        $this->actingAs($attendee)->post('/bookings', ['event_id' => $event->id]);

        $this->assertEquals(1, Booking::where('user_id', $attendee->id)
                                      ->where('event_id', $event->id)
                                      ->count());
    }

    /** Requirement: An Attendee cannot book a full event (manual capacity check). */
    public function test_an_attendee_cannot_book_a_full_event()
    {
        $organiser = User::factory()->create(['role' => 'organiser']);
        $attendee = User::factory()->create(['role' => 'attendee']);
        $other = User::factory()->create(['role' => 'attendee']);
        $event = Event::factory()->create([
            'organiser_id' => $organiser->id,
            'capacity' => 1,
            'starts_at' => now()->addDays(1),
        ]);

        Booking::create(['user_id' => $other->id, 'event_id' => $event->id]);

        $this->actingAs($attendee)
             ->post('/bookings', ['event_id' => $event->id])
             ->assertRedirect()
             ->assertSessionHas('error');
    }

    /** Requirement: An Attendee cannot see "Edit" or "Delete" buttons on any event page. */
    public function test_an_attendee_cannot_see_edit_or_delete_buttons_on_any_event_page()
    {
        $attendee = User::factory()->create(['role' => 'attendee']);
        $organiser = User::factory()->create(['role' => 'organiser']);
        $event = Event::factory()->create([
            'organiser_id' => $organiser->id,
            'starts_at' => now()->addDays(2),
        ]);

        $this->actingAs($attendee)
             ->get("/events/{$event->id}")
             ->assertOk()
             ->assertDontSee('Edit Event')
             ->assertDontSee('Delete');
    }
}
