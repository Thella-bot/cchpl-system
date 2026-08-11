<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class MemberProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_cannot_access_the_profile_page(): void
    {
        $this->get(route('member.profile'))->assertRedirect(route('login'));
    }

    public function test_member_can_view_their_profile(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('member.profile'))
            ->assertOk()
            ->assertSee($user->name)
            ->assertSee($user->email);
    }

    public function test_member_can_update_their_profile_details(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->put(route('member.profile.update'), [
                'name' => 'Updated Name',
                'phone' => '+26651234567',
                'organization' => 'Lesotho Hospitality Group',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Updated Name',
            'phone' => '+26651234567',
            'organization' => 'Lesotho Hospitality Group',
        ]);
    }

    public function test_profile_update_validates_required_fields(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->put(route('member.profile.update'), [
                'name' => '',
                'phone' => str_repeat('x', 40),
            ])
            ->assertSessionHasErrors(['name', 'phone']);
    }

    public function test_member_can_change_their_password(): void
    {
        $user = User::factory()->create(['password' => Hash::make('current-password')]);

        $this->actingAs($user)
            ->put(route('member.profile.password'), [
                'current_password' => 'current-password',
                'password' => 'NewPass123',
                'password_confirmation' => 'NewPass123',
            ])
            ->assertRedirect();

        $this->assertTrue(Hash::check('NewPass123', $user->fresh()->password));
    }

    public function test_password_change_requires_correct_current_password(): void
    {
        $user = User::factory()->create(['password' => Hash::make('current-password')]);

        $this->actingAs($user)
            ->put(route('member.profile.password'), [
                'current_password' => 'wrong-password',
                'password' => 'NewPass123',
                'password_confirmation' => 'NewPass123',
            ])
            ->assertSessionHasErrors('current_password');

        $this->assertTrue(Hash::check('current-password', $user->fresh()->password));
    }

    public function test_password_must_meet_complexity_rules(): void
    {
        $user = User::factory()->create(['password' => Hash::make('current-password')]);

        $this->actingAs($user)
            ->put(route('member.profile.password'), [
                'current_password' => 'current-password',
                'password' => 'weak',
                'password_confirmation' => 'weak',
            ])
            ->assertSessionHasErrors('password');
    }
}
