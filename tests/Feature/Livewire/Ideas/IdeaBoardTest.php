<?php

use App\Livewire\Ideas\IdeaBoard;
use App\Models\Idea;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

it('sends guests to the sign-in page', function () {
    $this->get('/ideas')->assertRedirect(route('login'));
});

it('shows the board to a signed-in member', function () {
    Idea::factory()->create(['title' => 'Buy a second laser cutter']);

    $this->actingAs(User::factory()->create())
        ->get('/ideas')
        ->assertOk()
        ->assertSee('Buy a second laser cutter');
});

it('lets a member post an idea and clears the form', function () {
    $member = User::factory()->create();

    Livewire::actingAs($member)
        ->test(IdeaBoard::class)
        ->set('title', 'More shelves in the wood shop')
        ->set('description', 'The offcuts pile is taking over.')
        ->call('submitIdea')
        ->assertHasNoErrors()
        ->assertSet('title', '')
        ->assertSet('description', '')
        ->assertSee('More shelves in the wood shop');

    $idea = Idea::first();

    expect($idea->user_id)->toBe($member->id);
    expect($idea->description)->toBe('The offcuts pile is taking over.');
});

it('stores no description when the details are left empty', function () {
    Livewire::actingAs(User::factory()->create())
        ->test(IdeaBoard::class)
        ->set('title', 'Fix the squeaky door')
        ->call('submitIdea')
        ->assertHasNoErrors();

    expect(Idea::first()->description)->toBeNull();
});

it('rejects an idea with no title or a title that is too long', function (string $title) {
    Livewire::actingAs(User::factory()->create())
        ->test(IdeaBoard::class)
        ->set('title', $title)
        ->call('submitIdea')
        ->assertHasErrors(['title']);

    expect(Idea::count())->toBe(0);
})->with([
    'empty' => [''],
    'too long' => [str_repeat('a', 121)],
]);

it('counts one vote per member and lets them take it back', function () {
    $member = User::factory()->create();
    $idea = Idea::factory()->create();

    $board = Livewire::actingAs($member)->test(IdeaBoard::class);

    $board->call('toggleVote', $idea->id);
    expect($idea->voters()->count())->toBe(1);

    $board->call('toggleVote', $idea->id);
    expect($idea->voters()->count())->toBe(0);
});

it('adds votes from different members together', function () {
    $idea = Idea::factory()->create();

    Livewire::actingAs(User::factory()->create())->test(IdeaBoard::class)->call('toggleVote', $idea->id);
    Livewire::actingAs(User::factory()->create())->test(IdeaBoard::class)->call('toggleVote', $idea->id);

    expect($idea->voters()->count())->toBe(2);
});

it('lists the most upvoted idea first', function () {
    $quietIdea = Idea::factory()->create(['title' => 'Quiet idea']);
    $popularIdea = Idea::factory()->create(['title' => 'Popular idea']);
    $popularIdea->voters()->attach(User::factory()->count(2)->create());

    Livewire::actingAs(User::factory()->create())
        ->test(IdeaBoard::class)
        ->assertSeeInOrder(['Popular idea', 'Quiet idea']);
});
