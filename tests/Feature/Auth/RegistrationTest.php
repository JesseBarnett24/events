<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    /** Requirement: Registration screen renders */
    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');
        $response->assertStatus(200);
    }

    /** Requirement: Users must agree to Privacy Policy and Terms before creating account */
    public function test_new_users_can_register(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'terms' => 'on', // ✅ correct checkbox name for Breeze/Jetstream
        ]);

        $response->assertRedirect(); // Should redirect to dashboard/home
        $this->assertAuthenticated(); // User should be logged in automatically

        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
        ]);
    }

    /** Requirement: User cannot register without agreeing to Privacy Policy */
    public function test_user_cannot_register_without_agreeing_to_privacy_policy(): void
    {
        $response = $this->from('/register')->post('/register', [
            'name' => 'No Consent User',
            'email' => 'noagree@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            // 🚫 no 'terms' key included
        ]);

        $response->assertRedirect('/register');
        $response->assertSessionHasErrors('terms'); // ✅ updated key name

        $this->assertGuest();
        $this->assertDatabaseMissing('users', ['email' => 'noagree@example.com']);
    }
}
