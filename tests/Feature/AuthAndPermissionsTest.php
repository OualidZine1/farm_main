<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthAndPermissionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/')->assertRedirect('/login');
    }

    public function test_login_page_is_available(): void
    {
        $this->get('/login')->assertOk();
    }

    public function test_password_reset_request_page_is_available(): void
    {
        $this->get('/forgot-password')->assertOk();
    }

    public function test_manager_cannot_create_categories(): void
    {
        $manager = User::factory()->create(['role' => 'manager']);

        $this->actingAs($manager)
            ->post(route('categories.store'), ['name' => 'Seeds'])
            ->assertForbidden();
    }

    public function test_administrator_can_create_categories(): void
    {
        $admin = User::factory()->create(['role' => 'administrator']);

        $this->actingAs($admin)
            ->post(route('categories.store'), ['name' => 'Seeds'])
            ->assertRedirect(route('categories.index'));

        $this->assertDatabaseHas(Category::class, ['name' => 'Seeds']);
    }

    public function test_administrator_can_create_manager(): void
    {
        $admin = User::factory()->create(['role' => 'administrator']);

        $this->actingAs($admin)
            ->post(route('profile.manager.create'), [
                'manager_firstname' => 'John',
                'manager_lastname' => 'Doe',
                'manager_email' => 'john.doe@example.com',
                'manager_cin' => 'AB123456',
                'manager_password' => 'Password123',
                'manager_password_confirmation' => 'Password123',
            ])
            ->assertRedirect(route('profile.show'));

        $this->assertDatabaseHas(User::class, [
            'firstname' => 'John',
            'lastname' => 'Doe',
            'email' => 'john.doe@example.com',
            'cin' => 'AB123456',
            'role' => 'manager',
        ]);
    }
}
