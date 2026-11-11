<?php

it('lists the lessons', function () {
    $this->get('/learn')
        ->assertOk()
        ->assertSeeInOrder([
            'Lesson 1: Member sign-up and login',
            'Lesson 2: The ideas board',
            'Lesson 3: Membership plans, statuses, and admins',
        ]);
});

it('shows a lesson as a web page', function () {
    $this->get('/learn/01-member-sign-up-and-login')
        ->assertOk()
        ->assertSee('<h1>Lesson 1: Member sign-up and login</h1>', false);
});

it('returns 404 for a lesson that does not exist', function (string $lessonSlug) {
    $this->get('/learn/'.$lessonSlug)->assertNotFound();
})->with([
    'unknown lesson' => ['99-not-written-yet'],
    'a file outside the lessons folder' => ['..%2F..%2F.env'],
]);
