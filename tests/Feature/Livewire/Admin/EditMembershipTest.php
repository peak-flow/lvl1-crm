<?php

use App\Enums\MembershipStatus;
use App\Livewire\Admin\EditMembership;
use App\Models\MembershipPlan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

it('refuses members who are not admins', function () {
    $member = User::factory()->create();

    $this->actingAs(User::factory()->create())->get("/admin/members/{$member->id}")->assertForbidden();
});

it('shows the member\'s current plan and status in the form', function () {
    $plan = MembershipPlan::factory()->create();
    $member = User::factory()->create(['membership_status' => MembershipStatus::Lapsed]);
    $member->membershipPlan()->associate($plan)->save();

    Livewire::actingAs(User::factory()->admin()->create())
        ->test(EditMembership::class, ['member' => $member])
        ->assertSet('membershipPlanId', (string) $plan->id)
        ->assertSet('membershipStatus', 'lapsed');
});

it('lets an admin change a member\'s plan and status', function () {
    $plan = MembershipPlan::factory()->create();
    $member = User::factory()->create();

    Livewire::actingAs(User::factory()->admin()->create())
        ->test(EditMembership::class, ['member' => $member])
        ->set('membershipPlanId', (string) $plan->id)
        ->set('membershipStatus', 'paused')
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('admin.members.index'));

    $member->refresh();

    expect($member->membership_plan_id)->toBe($plan->id);
    expect($member->membership_status)->toBe(MembershipStatus::Paused);
});

it('lets an admin take a member off their plan', function () {
    $member = User::factory()->create();
    $member->membershipPlan()->associate(MembershipPlan::factory()->create())->save();

    Livewire::actingAs(User::factory()->admin()->create())
        ->test(EditMembership::class, ['member' => $member])
        ->set('membershipPlanId', '')
        ->call('save')
        ->assertHasNoErrors();

    expect($member->refresh()->membership_plan_id)->toBeNull();
});

it('rejects a status or plan that does not exist', function () {
    $member = User::factory()->create();

    Livewire::actingAs(User::factory()->admin()->create())
        ->test(EditMembership::class, ['member' => $member])
        ->set('membershipPlanId', '999')
        ->set('membershipStatus', 'emperor')
        ->call('save')
        ->assertHasErrors(['membershipPlanId', 'membershipStatus']);

    expect($member->refresh()->membership_status)->toBe(MembershipStatus::Active);
});

it('stops a non-admin from saving even if they reach the form', function () {
    $member = User::factory()->create();

    Livewire::actingAs(User::factory()->create())
        ->test(EditMembership::class, ['member' => $member])
        ->set('membershipStatus', 'banned')
        ->call('save')
        ->assertForbidden();

    expect($member->refresh()->membership_status)->toBe(MembershipStatus::Active);
});
