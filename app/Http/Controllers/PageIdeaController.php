<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class PageIdeaController extends Controller
{
    /**
     * Pages the site could have, keyed by the slug used in the address.
     */
    private const PAGES = [
        'events' => [
            'name' => 'Events',
            'description' => 'What is happening at the space: open houses, clean-up days and meetups, with a way to say you are coming.',
        ],
        'classes' => [
            'name' => 'Classes and courses',
            'description' => 'Sign-off classes that unlock a tool, one-evening skills classes, and courses that run over several weeks.',
        ],
    ];

    public function index(): View
    {
        return view('page-ideas.index', ['pages' => self::PAGES]);
    }

    public function show(string $pageSlug): View
    {
        abort_unless(isset(self::PAGES[$pageSlug]), 404);

        return view('page-ideas.'.$pageSlug, [
            'pages' => self::PAGES,
            'currentPageSlug' => $pageSlug,
            'examples' => $pageSlug === 'events' ? $this->exampleEvents() : $this->exampleClasses(),
        ]);
    }

    /**
     * Made-up events for the mock-up. Nothing here comes from the database.
     *
     * @return array<string, mixed>
     */
    private function exampleEvents(): array
    {
        return [
            'everyWeek' => [
                'title' => 'Open house',
                'when' => 'Every Tuesday, 7:00 to 9:00 PM',
                'body' => 'Come and see the space. No booking needed, and a member will show you around.',
            ],
            'months' => [
                'November' => [
                    [
                        'day' => '15', 'weekday' => 'Sat', 'title' => 'Shop clean-up day', 'time' => '10:00 AM to 1:00 PM',
                        'audience' => 'Members', 'body' => 'We tidy the wood shop and sort the offcuts. Pizza for everyone who shows up.',
                        'coming' => 11, 'youAreComing' => true,
                    ],
                    [
                        'day' => '20', 'weekday' => 'Thu', 'title' => 'Show and tell', 'time' => '7:30 PM',
                        'audience' => 'Everyone', 'body' => 'Bring something you made, finished or not, and tell us about it in five minutes.',
                        'coming' => 23, 'youAreComing' => false,
                    ],
                    [
                        'day' => '29', 'weekday' => 'Sat', 'title' => 'Repair cafe', 'time' => '1:00 to 4:00 PM',
                        'audience' => 'Everyone', 'body' => 'Bring a broken lamp, toaster or chair. Volunteers will help you fix it.',
                        'coming' => 8, 'youAreComing' => false,
                    ],
                ],
                'December' => [
                    [
                        'day' => '4', 'weekday' => 'Thu', 'title' => 'Monthly members meeting', 'time' => '7:00 PM',
                        'audience' => 'Members', 'body' => 'Budget update, new equipment vote, and the ideas with the most upvotes.',
                        'coming' => 17, 'youAreComing' => false,
                    ],
                    [
                        'day' => '13', 'weekday' => 'Sat', 'title' => 'Holiday ornament workshop', 'time' => '2:00 to 5:00 PM',
                        'audience' => 'Everyone', 'body' => 'Laser-cut and paint your own ornaments. Good for families; children need an adult with them.',
                        'coming' => 31, 'youAreComing' => false,
                    ],
                ],
            ],
        ];
    }

    /**
     * Made-up classes and courses for the mock-up. Nothing here comes from the database.
     *
     * @return array<string, mixed>
     */
    private function exampleClasses(): array
    {
        return [
            'signOffClasses' => [
                [
                    'title' => 'CNC router sign-off', 'unlocks' => 'CNC router', 'when' => 'Tuesday, November 18, 6:30 PM',
                    'length' => '2 hours', 'teacher' => 'Sam K.', 'price' => '$25', 'spotsLeft' => 2, 'needsFirst' => 'table saw sign-off',
                ],
                [
                    'title' => 'MIG welder sign-off', 'unlocks' => 'MIG welder', 'when' => 'Wednesday, December 3, 6:30 PM',
                    'length' => '3 hours', 'teacher' => 'Priya N.', 'price' => '$40', 'spotsLeft' => 4, 'needsFirst' => null,
                ],
                [
                    'title' => 'Laser cutter sign-off', 'unlocks' => 'laser cutter', 'when' => 'Saturday, December 6, 10:00 AM',
                    'length' => '90 minutes', 'teacher' => 'Sam K.', 'price' => '$20', 'spotsLeft' => 0, 'needsFirst' => null,
                ],
            ],
            'skillsClasses' => [
                [
                    'title' => 'Intro to soldering', 'when' => 'Wednesday, December 10, 7:00 PM',
                    'length' => '2 hours', 'teacher' => 'Marcus T.', 'price' => 'Free', 'spotsLeft' => 8,
                    'body' => 'Build a small blinking badge and take it home. No experience needed.',
                ],
                [
                    'title' => 'Sharpening chisels and planes', 'when' => 'Saturday, December 13, 10:00 AM',
                    'length' => '2 hours', 'teacher' => 'Priya N.', 'price' => '$15', 'spotsLeft' => 5,
                    'body' => 'Bring your own blunt tools or use ours.',
                ],
            ],
            'courses' => [
                [
                    'title' => 'Build a side table', 'when' => 'Four Mondays from January 5, 6:30 to 9:00 PM',
                    'teacher' => 'Priya N.', 'price' => '$180, wood included', 'spotsLeft' => 3,
                    'body' => 'From rough boards to a finished table. You leave signed off on the table saw, jointer and planer.',
                    'weeks' => ['Milling the boards', 'Cutting the joints', 'Glue-up and shaping', 'Sanding and finish'],
                ],
            ],
        ];
    }
}
