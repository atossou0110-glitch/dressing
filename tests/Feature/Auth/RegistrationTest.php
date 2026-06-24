<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_users_can_register(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));
        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'is_admin' => true,
        ]);
        $this->assertNotNull(User::query()->where('email', 'test@example.com')->value('email_verified_at'));
    }

    public function test_additional_users_register_without_admin_access_and_are_redirected_home(): void
    {
        User::factory()->admin()->create([
            'email' => 'admin@example.com',
        ]);

        $response = $this->post('/register', [
            'name' => 'Second User',
            'email' => 'second@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('catalog.index', absolute: false));
        $this->assertDatabaseHas('users', [
            'email' => 'second@example.com',
            'is_admin' => false,
        ]);
        $this->assertNull(User::query()->where('email', 'second@example.com')->value('email_verified_at'));
    }
}
