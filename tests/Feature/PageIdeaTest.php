<?php

it('lists the page ideas without needing to sign in', function () {
    $this->get('/page-ideas')->assertOk()->assertSeeInOrder(['Events', 'Classes and courses']);
});

it('shows the events mock-up', function () {
    $this->get('/page-ideas/events')
        ->assertOk()
        ->assertSee('This is a mock-up')
        ->assertSeeInOrder(['November', 'Shop clean-up day', 'December', 'Monthly members meeting']);
});

it('shows the classes mock-up, including a full class', function () {
    $this->get('/page-ideas/classes')
        ->assertOk()
        ->assertSee('This is a mock-up')
        ->assertSee('Unlocks the CNC router')
        ->assertSeeInOrder(['Sign-off classes', 'This class is full.', 'Skills classes', 'Courses', 'Build a side table']);
});

it('returns 404 for a page idea that does not exist', function () {
    $this->get('/page-ideas/forum')->assertNotFound();
});
