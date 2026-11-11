<?php

use App\Livewire\Auth\Login;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

it('signs a member in with the right password', function () {
    $member = User::factory()->create(['password' => 'correct-horse-battery']);

    Livewire::test(Login::class)
        ->set('email', $member->email)
        ->set('password', 'correct-horse-battery')
        ->call('login')
        ->assertHasNoErrors()
        ->assertRedirect(route('dashboard'));

    $this->assertAuthenticatedAs($member);
});

it('refuses a wrong password', function () {
    $member = User::factory()->create(['password' => 'correct-horse-battery']);

    Livewire::test(Login::class)
        ->set('email', $member->email)
        ->set('password', 'wrong-password')
        ->call('login')
        ->assertHasErrors(['email']);

    $this->assertGuest();
});

it('sends guests to the sign-in page when they open the dashboard', function () {
    $this->get('/dashboard')->assertRedirect(route('login'));
});

it('shows the dashboard to a signed-in member', function () {
    $member = User::factory()->create(['name' => 'Ada Lovelace']);

    $this->actingAs($member)->get('/dashboard')->assertOk()->assertSee('Welcome, Ada Lovelace');
});

it('sends a signed-in member away from the sign-in page', function () {
    $this->actingAs(User::factory()->create())->get('/login')->assertRedirect(route('dashboard'));
});

it('signs a member out', function () {
    $this->actingAs(User::factory()->create())->post('/logout')->assertRedirect(route('home'));

    $this->assertGuest();
});
