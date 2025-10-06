<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Event;
use App\Models\Category;
use App\Models\Booking;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CategoriesFeatureTest extends TestCase
{
    use RefreshDatabase;

    /** 
     * Core Functional Criterion A — Categories appear on Create Event form
     */
    public function test_categories_appear_on_create_event_form()
    {
        $organiser = User::factory()->create(['role' => 'organiser']);
        Category::factory()->count(3)->create();

        $response = $this->actingAs($organiser)->get('/events/create');

        $response->assertOk();
        $response->assertSee('Categories');
        Category::all()->each(fn ($cat) => $response->assertSee($cat->name));
    }

    /** 
     * Core Functional Criterion B — Organiser can assign categories to new event
     */
    public function test_organiser_can_assign_categories_to_new_event()
    {
        $organiser = User::factory()->create(['role' => 'organiser']);
        $categories = Category::factory()->count(2)->create();

        $payload = [
            'title' => 'Music Festival',
            'description' => 'A great lineup of bands',
            'starts_at' => now()->addDays(3),
            'location' => 'Gold Coast',
            'capacity' => 150,
            'categories' => $categories->pluck('id')->toArray(),
        ];

        $response = $this->actingAs($organiser)->post('/events', $payload);
        $response->assertRedirect();

        $this->assertDatabaseHas('events', ['title' => 'Music Festival']);
        $event = Event::first();
        $this->assertCount(2, $event->categories);
    }

    /** 
     * Core Functional Criterion C — Assigned categories appear on event details page
     */
    public function test_assigned_categories_appear_on_event_details_page()
    {
        $organiser = User::factory()->create(['role' => 'organiser']);
        $category = Category::factory()->create(['name' => 'Music']);
        $event = Event::factory()->create([
            'organiser_id' => $organiser->id,
            'starts_at' => now()->addDays(2)
        ]);
        $event->categories()->attach($category);

        $response = $this->get("/events/{$event->id}");
        $response->assertOk()->assertSee('Music');
    }

    /** 
     * Core Functional Criterion D — Filtering by category shows only matching events
     */
    public function test_filtering_by_category_shows_only_matching_events()
    {
        $organiser = User::factory()->create(['role' => 'organiser']);
        $catMusic = Category::factory()->create(['name' => 'Music']);
        $catArt = Category::factory()->create(['name' => 'Art']);

        $event1 = Event::factory()->create([
            'title' => 'Jazz Night',
            'organiser_id' => $organiser->id,
            'starts_at' => now()->addDays(5)
        ]);
        $event1->categories()->attach($catMusic);

        $event2 = Event::factory()->create([
            'title' => 'Gallery Opening',
            'organiser_id' => $organiser->id,
            'starts_at' => now()->addDays(5)
        ]);
        $event2->categories()->attach($catArt);

        $response = $this->get("/events?category_id={$catMusic->id}");
        $response->assertOk();
        $response->assertSeeText('Jazz Night', false);
        $response->assertDontSeeText('Gallery Opening', false);
    }

    /** 
     * Core Functional Criterion E — AJAX category filter returns correct HTML
     */
    public function test_ajax_category_filter_returns_correct_html()
    {
        $organiser = User::factory()->create(['role' => 'organiser']);
        $catMusic = Category::factory()->create(['name' => 'Music']);
        $catTech = Category::factory()->create(['name' => 'Tech']);

        $eventMusic = Event::factory()->create([
            'title' => 'Live Jazz',
            'organiser_id' => $organiser->id,
            'starts_at' => now()->addDays(2)
        ]);
        $eventMusic->categories()->attach($catMusic);

        $eventTech = Event::factory()->create([
            'title' => 'AI Conference',
            'organiser_id' => $organiser->id,
            'starts_at' => now()->addDays(3)
        ]);
        $eventTech->categories()->attach($catTech);

        $response = $this->getJson("/events/filter?categories[]={$catMusic->id}");

        $response->assertOk()
                 ->assertJsonStructure(['html', 'pagination']);

        $json = $response->json();
        $this->assertStringContainsString('Live Jazz', $json['html']);
        $this->assertStringNotContainsString('AI Conference', $json['html']);
    }

    /** 
     * Student-Designed Excellence Marker — Category Popularity Analytics visible on dashboard
     */
    public function test_category_popularity_analytics_is_displayed_on_dashboard()
    {
        $organiser = User::factory()->create(['role' => 'organiser']);
        $cat = Category::factory()->create(['name' => 'Tech']);
        $event = Event::factory()->create([
            'organiser_id' => $organiser->id,
            'capacity' => 10,
            'starts_at' => now()->addDays(1)
        ]);
        $event->categories()->attach($cat);

        // Booking to change occupancy
        $attendee = User::factory()->create(['role' => 'attendee']);
        Booking::create(['user_id' => $attendee->id, 'event_id' => $event->id]);

        // Your real route is /organiser/{id}
        $response = $this->actingAs($organiser)->get("/organiser/{$organiser->id}");

        $response->assertOk()
                 ->assertSee('Category Popularity')
                 ->assertSee('Average Occupancy')
                 ->assertSee('Tech');
    }
}
