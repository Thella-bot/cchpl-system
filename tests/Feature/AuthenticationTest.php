<?php

namespace Tests\Feature;

use App\Models\MembershipCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_page_is_reachable_by_guests(): void
    {
        $this->get(route('register'))->assertOk();
    }

    public function test_login_page_is_reachable_by_guests(): void
    {
        $this->get(route('login'))->assertOk();
    }

    public function test_a_guest_can_register_a_new_account(): void
    {
        $response = $this->post(route('register'), [
            'name' => 'Thabo Mokhele',
            'email' => 'thabo@example.test',
            'password' => 'SecurePass123',
            'password_confirmation' => 'SecurePass123',
        ]);

        $response->assertRedirect(route('member.dashboard'));
        $this->assertDatabaseHas('users', ['email' => 'thabo@example.test']);
        $this->assertTrue(Hash::check('SecurePass123', User::where('email', 'thabo@example.test')->first()->password));
    }

    public function test_registration_requires_a_strong_unique_password(): void
    {
        User::factory()->create(['email' => 'taken@example.test']);

        $this->post(route('register'), [
            'name' => 'Dup User',
            'email' => 'taken@example.test',
            'password' => 'short',
            'password_confirmation' => 'short',
        ])
            ->assertSessionHasErrors(['email', 'password']);

        $this->assertDatabaseCount('users', 1);
    }

    public function test_an_existing_member_can_log_in(): void
    {
        $user = User::factory()->create([
            'email' => 'member@example.test',
            'password' => Hash::make('CorrectPass123'),
        ]);

        $this->post(route('login'), [
            'email' => 'member@example.test',
            'password' => 'CorrectPass123',
        ])
            ->assertRedirect(route('member.dashboard'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_login_fails_with_invalid_credentials(): void
    {
        User::factory()->create([
            'email' => 'member@example.test',
            'password' => Hash::make('CorrectPass123'),
        ]);

        $this->post(route('login'), [
            'email' => 'member@example.test',
            'password' => 'WrongPass123',
        ])
            ->assertRedirect()
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_a_member_can_log_out(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('logout'))
            ->assertRedirect('/');

        $this->assertGuest();
    }

    public function test_unverified_members_cannot_access_membership_application(): void
    {
        $user = User::factory()->unverified()->create();

        $this->actingAs($user)
            ->get(route('membership.apply'))
            ->assertRedirect(route('verification.notice'));
    }

    public function test_verified_members_can_reach_the_membership_application(): void
    {
        $user = User::factory()->create();
        MembershipCategory::factory()->create();

        $this->actingAs($user)
            ->get(route('membership.apply'))
            ->assertOk();
    }
}
