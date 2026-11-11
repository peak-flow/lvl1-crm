<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class StyleIdeaController extends Controller
{
    /**
     * The design directions on offer, keyed by the slug used in the address.
     */
    private const STYLES = [
        'pegboard' => [
            'name' => 'Pegboard',
            'description' => 'The shop tool wall. Your tools hang on the board: solid if you are signed off, an empty outline if you are not.',
        ],
        'cutting-mat' => [
            'name' => 'Cutting mat',
            'description' => 'A green cutting mat with its measuring grid. Everything is a slip of paper laid on the mat.',
        ],
        'safety-signs' => [
            'name' => 'Safety signs',
            'description' => 'The signs already on the shop walls. Green means go ahead, red means do not touch, yellow needs your attention.',
        ],
    ];

    public function index(): View
    {
        return view('style-ideas.index', ['styles' => self::STYLES]);
    }

    public function show(string $styleSlug): View
    {
        abort_unless(isset(self::STYLES[$styleSlug]), 404);

        return view('style-ideas.'.$styleSlug, [
            'styles' => self::STYLES,
            'currentStyleSlug' => $styleSlug,
            'exampleSpace' => $this->exampleSpace(),
        ]);
    }

    /**
     * Made-up data for the mock-ups. None of it comes from the database and
     * none of it describes a real person. Every style page shows this same
     * data, so the only difference between them is the design.
     *
     * @return array<string, mixed>
     */
    private function exampleSpace(): array
    {
        return [
            'member' => [
                'name' => 'Jordan Rivera',
                'plan' => 'Regular',
                'status' => 'Active',
                'memberSince' => 'March 2024',
            ],
            'toolSignOffs' => [
                ['tool' => 'Laser cutter', 'signedOff' => true, 'detail' => 'Signed off by Sam K., April 2024'],
                ['tool' => 'Table saw', 'signedOff' => true, 'detail' => 'Signed off by Priya N., May 2024'],
                ['tool' => '3D printers', 'signedOff' => true, 'detail' => 'Signed off by Sam K., March 2024'],
                ['tool' => 'CNC router', 'signedOff' => false, 'detail' => 'You are booked on the class on November 18'],
                ['tool' => 'MIG welder', 'signedOff' => false, 'detail' => 'Next class is December 3'],
                ['tool' => 'Metal lathe', 'signedOff' => false, 'detail' => 'No class scheduled yet'],
            ],
            'toolsOutOfOrder' => [
                ['tool' => 'Bandsaw', 'note' => 'Blade snapped. New one arrives Thursday.'],
                ['tool' => '3D printer 2', 'note' => 'Nozzle clogged. Printers 1 and 3 are fine.'],
            ],
            'bookings' => [
                ['tool' => 'Laser cutter', 'when' => 'Thursday, 6:00 to 8:00 PM'],
                ['tool' => 'Table saw', 'when' => 'Saturday, 10:00 AM to noon'],
            ],
            'dues' => [
                'amount' => '$50.00',
                'nextPayment' => 'December 1',
                'lastPayment' => 'Paid November 1',
                'paymentMethod' => 'Card ending 4242',
            ],
            'doorAccess' => [
                'fob' => 'Fob 0418',
                'entries' => [
                    ['when' => 'Today, 6:42 PM', 'door' => 'Front door'],
                    ['when' => 'Saturday, 9:58 AM', 'door' => 'Front door'],
                    ['when' => 'Last Thursday, 7:15 PM', 'door' => 'Wood shop'],
                ],
            ],
            'classes' => [
                ['title' => 'CNC router sign-off', 'when' => 'November 18, 6:30 PM', 'spotsLeft' => 2, 'price' => '$25', 'booked' => true],
                ['title' => 'MIG welding basics', 'when' => 'December 3, 6:30 PM', 'spotsLeft' => 4, 'price' => '$40', 'booked' => false],
                ['title' => 'Intro to soldering', 'when' => 'December 10, 7:00 PM', 'spotsLeft' => 8, 'price' => 'Free', 'booked' => false],
            ],
            'announcements' => [
                ['title' => 'Shop clean-up day', 'body' => 'Saturday at 10. Pizza for everyone who shows up.', 'when' => '2 days ago'],
                ['title' => 'New dust collector is in', 'body' => 'Turn it on at the blast gate before you use the table saw.', 'when' => 'Last week'],
            ],
            'ideas' => [
                ['title' => 'Buy a second laser cutter', 'votes' => 14],
                ['title' => 'More shelves in the wood shop', 'votes' => 9],
                ['title' => 'Leave the lights on a timer', 'votes' => 6],
            ],
            'storage' => [
                'location' => 'Shelf B-12',
                'renews' => 'Renews January 1',
            ],
        ];
    }
}
