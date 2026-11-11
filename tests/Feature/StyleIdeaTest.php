<?php

it('lists the style ideas without needing to sign in', function () {
    $this->get('/style-ideas')->assertOk()->assertSeeInOrder(['Pegboard', 'Cutting mat', 'Safety signs']);
});

it('shows each style idea with the same example member', function (string $styleSlug) {
    $this->get('/style-ideas/'.$styleSlug)
        ->assertOk()
        ->assertSee('Jordan Rivera')
        ->assertSee('Nothing here is real');
})->with(['pegboard', 'cutting-mat', 'safety-signs']);

it('returns 404 for a style that does not exist', function () {
    $this->get('/style-ideas/neon')->assertNotFound();
});
