<?php

use App\Livewire\Auth\Register;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;

uses(RefreshDatabase::class);

it('shows the sign-up page to guests', function () {
    $this->get('/register')->assertOk()->assertSee('Join the space');
});

it('creates a member, signs them in, and sends them to the dashboard', function () {
    Livewire::test(Register::class)
        ->set('name', 'Ada Lovelace')
        ->set('email', 'ada@example.com')
        ->set('password', 'correct-horse-battery')
        ->set('password_confirmation', 'correct-horse-battery')
        ->call('register')
        ->assertHasNoErrors()
        ->assertRedirect(route('dashboard'));

    $member = User::where('email', 'ada@example.com')->first();

    expect($member)->not->toBeNull();
    expect($member->password)->not->toBe('correct-horse-battery');
    expect(Hash::check('correct-horse-battery', $member->password))->toBeTrue();
    $this->assertAuthenticatedAs($member);
});

it('rejects an email that already belongs to a member', function () {
    User::factory()->create(['email' => 'ada@example.com']);

    Livewire::test(Register::class)
        ->set('name', 'Another Ada')
        ->set('email', 'ada@example.com')
        ->set('password', 'correct-horse-battery')
        ->set('password_confirmation', 'correct-horse-battery')
        ->call('register')
        ->assertHasErrors(['email']);

    expect(User::count())->toBe(1);
});

it('rejects a short password and a mismatched confirmation', function (string $password, string $confirmation) {
    Livewire::test(Register::class)
        ->set('name', 'Ada Lovelace')
        ->set('email', 'ada@example.com')
        ->set('password', $password)
        ->set('password_confirmation', $confirmation)
        ->call('register')
        ->assertHasErrors(['password']);

    expect(User::count())->toBe(0);
    $this->assertGuest();
})->with([
    'too short' => ['short', 'short'],
    'confirmation differs' => ['correct-horse-battery', 'something-else-entirely'],
]);
