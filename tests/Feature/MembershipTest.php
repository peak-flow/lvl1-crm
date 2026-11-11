<?php

use App\Enums\MembershipStatus;
use App\Livewire\Auth\Register;
use App\Models\MembershipPlan;
use App\Models\User;
use Database\Seeders\MembershipPlanSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

it('starts a new sign-up as an active member with no plan and no admin powers', function () {
    Livewire::test(Register::class)
        ->set('name', 'Ada Lovelace')
        ->set('email', 'ada@example.com')
        ->set('password', 'correct-horse-battery')
        ->set('password_confirmation', 'correct-horse-battery')
        ->call('register');

    $member = User::where('email', 'ada@example.com')->first();

    expect($member->membership_status)->toBe(MembershipStatus::Active);
    expect($member->membership_plan_id)->toBeNull();
    expect($member->is_admin)->toBeFalse();
});

it('shows a member their status and plan on the dashboard', function () {
    $plan = MembershipPlan::factory()->create(['name' => 'Regular', 'monthly_price_in_cents' => 5000]);
    $member = User::factory()->create();
    $member->membershipPlan()->associate($plan)->save();

    $this->actingAs($member)
        ->get('/dashboard')
        ->assertOk()
        ->assertSeeInOrder(['Status', 'Active', 'Plan', 'Regular, $50.00 a month']);
});

it('tells a member with no plan that they have none yet', function () {
    $this->actingAs(User::factory()->create())->get('/dashboard')->assertSee('No plan yet');
});

it('seeds the example plans once, however many times the seeder runs', function () {
    $this->seed(MembershipPlanSeeder::class);
    $this->seed(MembershipPlanSeeder::class);

    expect(MembershipPlan::orderBy('name')->pluck('name')->all())->toBe(['Family', 'Regular', 'Student']);
});

it('makes a member an admin from the command line', function () {
    $member = User::factory()->create(['email' => 'ada@example.com']);

    $this->artisan('members:make-admin', ['email' => 'ada@example.com'])->assertSuccessful();

    expect($member->refresh()->is_admin)->toBeTrue();
});

it('fails clearly when the email belongs to nobody', function () {
    $this->artisan('members:make-admin', ['email' => 'nobody@example.com'])
        ->expectsOutputToContain('No member has the email address')
        ->assertFailed();
});
