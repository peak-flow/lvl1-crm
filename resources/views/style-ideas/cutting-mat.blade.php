<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Style idea: Cutting mat</title>
    @fonts(['jost'])
    <style>
        /* A green self-healing cutting mat. Headings are printed on the mat; everything else is paper laid on it. */
        :root {
            --mat: #1b6654;
            --mat-line: rgba(244, 238, 177, 0.13);
            --mat-line-major: rgba(244, 238, 177, 0.34);
            --mat-print: #f4eeb1;
            --paper: #fdfdfb;
            --ink: #14241f;
            --pencil: #4a5b55;
            --blade-red: #d8402f;
            --unit: 16px;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            color: var(--mat-print);
            font-family: "Jost", "Century Gothic", Futura, sans-serif;
            font-size: 1.0625rem;
            line-height: 1.5;
            background-color: var(--mat);
            background-image:
                linear-gradient(var(--mat-line-major) 1px, transparent 1px),
                linear-gradient(90deg, var(--mat-line-major) 1px, transparent 1px),
                linear-gradient(var(--mat-line) 1px, transparent 1px),
                linear-gradient(90deg, var(--mat-line) 1px, transparent 1px);
            background-size: 80px 80px, 80px 80px, 16px 16px, 16px 16px;
        }

        h1, h2, h3, p, ul, dl, dd { margin: 0; }
        ul { padding: 0; list-style: none; }

        .mat { max-width: 64rem; margin: 0 auto; padding: calc(var(--unit) * 3) var(--unit) calc(var(--unit) * 6); }

        /* The ruler printed along the top edge of the mat. */
        .ruler {
            display: flex;
            height: 32px;
            border-bottom: 2px solid var(--mat-print);
            background-image: repeating-linear-gradient(90deg, var(--mat-print) 0 1px, transparent 1px 16px);
            background-size: 100% 10px;
            background-position: left bottom;
            background-repeat: no-repeat;
            overflow: hidden;
        }
        .ruler span {
            flex: 0 0 80px;
            padding-left: 4px;
            border-left: 2px solid var(--mat-print);
            font-size: 0.8125rem;
            line-height: 1;
            font-weight: 500;
        }

        .member { margin-top: calc(var(--unit) * 3); }
        .member h1 {
            font-size: clamp(2.75rem, 9vw, 5rem);
            line-height: 0.95;
            font-weight: 600;
            letter-spacing: -0.02em;
        }
        .member p { margin-top: var(--unit); font-size: 1.25rem; font-weight: 500; }

        /* Each section: its name printed on the mat at the left, its paper slips to the right. */
        .measure {
            display: grid;
            grid-template-columns: 13rem 1fr;
            gap: var(--unit) calc(var(--unit) * 2);
            margin-top: calc(var(--unit) * 5);
            padding-top: var(--unit);
            border-top: 2px solid var(--mat-print);
        }
        .measure > h2 { font-size: 1.625rem; line-height: 1.15; font-weight: 600; }
        .measure > h2 small { display: block; margin-top: 6px; font-size: 1rem; font-weight: 400; line-height: 1.35; }

        .slips { display: grid; grid-template-columns: repeat(auto-fill, minmax(14rem, 1fr)); gap: var(--unit); align-items: start; }
        .slips-wide { grid-template-columns: 1fr; }

        .slip { background: var(--paper); color: var(--ink); padding: var(--unit); }
        .slip h3 { font-size: 1.1875rem; line-height: 1.2; font-weight: 600; }
        .slip p { margin-top: 6px; }
        .slip .pencil { color: var(--pencil); font-size: 0.9375rem; }
        .slip li + li { margin-top: 10px; padding-top: 10px; border-top: 1px dashed #b9c2be; }
        .slip .figure { font-size: 2.5rem; line-height: 1; font-weight: 600; letter-spacing: -0.01em; }

        /* A slip for a tool you cannot use yet is just a cut line on the mat, with no paper in it. */
        .slip-empty {
            background: transparent;
            color: var(--mat-print);
            border: 2px dashed var(--mat-print);
        }
        .slip-empty .pencil { color: var(--mat-print); }

        .slip-warning { border-left: 8px solid var(--blade-red); }

        .votes { display: inline-block; min-width: 2.5rem; font-weight: 600; }

        .button {
            display: inline-block;
            margin-top: var(--unit);
            padding: 8px 16px;
            border: 2px solid var(--mat-print);
            background: var(--mat-print);
            color: var(--ink);
            font: inherit;
            font-weight: 600;
            cursor: pointer;
        }
        .button:focus-visible, a:focus-visible { outline: 3px solid #fff; outline-offset: 3px; }

        @media (max-width: 44rem) {
            .measure { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    @include('style-ideas.partials.switcher')

    <main class="mat">
        <div class="ruler" aria-hidden="true">
            @foreach (range(0, 60, 5) as $rulerMark)
                <span>{{ $rulerMark }}</span>
            @endforeach
        </div>

        <header class="member">
            <h1>{{ $exampleSpace['member']['name'] }}</h1>
            <p>
                {{ $exampleSpace['member']['plan'] }} member since {{ $exampleSpace['member']['memberSince'] }}.
                {{ $exampleSpace['member']['status'] }}.
            </p>
        </header>

        <section class="measure">
            <h2>Your tools <small>Paper means you are signed off. A cut line means not yet.</small></h2>
            <ul class="slips">
                @foreach ($exampleSpace['toolSignOffs'] as $toolSignOff)
                    <li class="slip {{ $toolSignOff['signedOff'] ? '' : 'slip-empty' }}">
                        <h3>{{ $toolSignOff['tool'] }}</h3>
                        <p class="pencil">{{ $toolSignOff['detail'] }}</p>
                    </li>
                @endforeach
            </ul>
        </section>

        <section class="measure">
            <h2>Out of order</h2>
            <ul class="slips">
                @foreach ($exampleSpace['toolsOutOfOrder'] as $brokenTool)
                    <li class="slip slip-warning">
                        <h3>{{ $brokenTool['tool'] }}</h3>
                        <p>{{ $brokenTool['note'] }}</p>
                    </li>
                @endforeach
            </ul>
        </section>

        <section class="measure">
            <h2>Your bookings</h2>
            <div>
                <ul class="slips">
                    @foreach ($exampleSpace['bookings'] as $booking)
                        <li class="slip">
                            <h3>{{ $booking['tool'] }}</h3>
                            <p>{{ $booking['when'] }}</p>
                        </li>
                    @endforeach
                </ul>
                <button type="button" class="button">Book a tool</button>
            </div>
        </section>

        <section class="measure">
            <h2>Classes</h2>
            <ul class="slips">
                @foreach ($exampleSpace['classes'] as $class)
                    <li class="slip">
                        <h3>{{ $class['title'] }}</h3>
                        <p>{{ $class['when'] }}, {{ $class['price'] }}</p>
                        <p class="pencil">{{ $class['booked'] ? 'You are booked.' : $class['spotsLeft'].' spots left.' }}</p>
                    </li>
                @endforeach
            </ul>
        </section>

        <section class="measure">
            <h2>Dues and door</h2>
            <div class="slips">
                <div class="slip">
                    <h3>Dues</h3>
                    <p class="figure">{{ $exampleSpace['dues']['amount'] }}</p>
                    <p>Next payment {{ $exampleSpace['dues']['nextPayment'] }}, from {{ strtolower($exampleSpace['dues']['paymentMethod']) }}.</p>
                    <p class="pencil">{{ $exampleSpace['dues']['lastPayment'] }}.</p>
                </div>
                <div class="slip">
                    <h3>{{ $exampleSpace['doorAccess']['fob'] }} is working</h3>
                    <ul style="margin-top: 10px;">
                        @foreach ($exampleSpace['doorAccess']['entries'] as $entry)
                            <li>{{ $entry['when'] }}<br><span class="pencil">{{ $entry['door'] }}</span></li>
                        @endforeach
                    </ul>
                </div>
                <div class="slip">
                    <h3>Your storage</h3>
                    <p class="figure">{{ $exampleSpace['storage']['location'] }}</p>
                    <p class="pencil">{{ $exampleSpace['storage']['renews'] }}.</p>
                </div>
            </div>
        </section>

        <section class="measure">
            <h2>Announcements</h2>
            <ul class="slips slips-wide">
                @foreach ($exampleSpace['announcements'] as $announcement)
                    <li class="slip">
                        <h3>{{ $announcement['title'] }}</h3>
                        <p>{{ $announcement['body'] }}</p>
                        <p class="pencil">{{ $announcement['when'] }}</p>
                    </li>
                @endforeach
            </ul>
        </section>

        <section class="measure">
            <h2>Ideas for the space</h2>
            <div>
                <div class="slip">
                    <ul>
                        @foreach ($exampleSpace['ideas'] as $idea)
                            <li><span class="votes">{{ $idea['votes'] }}</span>{{ $idea['title'] }}</li>
                        @endforeach
                    </ul>
                </div>
                <button type="button" class="button">Post an idea</button>
            </div>
        </section>
    </main>
</body>
</html>
