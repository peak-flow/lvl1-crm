<?php

use App\Enums\MembershipStatus;
use App\Models\MembershipPlan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('sends guests to the sign-in page', function () {
    $this->get('/admin/members')->assertRedirect(route('login'));
});

it('refuses members who are not admins', function () {
    $this->actingAs(User::factory()->create())->get('/admin/members')->assertForbidden();
});

it('shows every member with their plan and status to an admin', function () {
    $studentPlan = MembershipPlan::factory()->create(['name' => 'Student']);
    $pausedMember = User::factory()->create(['name' => 'Grace Hopper', 'membership_status' => MembershipStatus::Paused]);
    $pausedMember->membershipPlan()->associate($studentPlan)->save();

    $this->actingAs(User::factory()->admin()->create())
        ->get('/admin/members')
        ->assertOk()
        ->assertSeeInOrder(['Grace Hopper', 'Student', 'Paused']);
});

it('only shows the Members link to admins', function () {
    $this->actingAs(User::factory()->create())->get('/dashboard')->assertDontSee(route('admin.members.index'));
    $this->actingAs(User::factory()->admin()->create())->get('/dashboard')->assertSee(route('admin.members.index'));
});
