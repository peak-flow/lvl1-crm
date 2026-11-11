<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Style idea: Pegboard</title>
    @fonts(['zilla-slab'])
    <style>
        /* The shop tool wall: hardboard with rows of holes, tools hung on it, enamel plates screwed to it. */
        :root {
            --board: #c99b62;
            --hole: #96703f;
            --plate: #fbf6ea;
            --ink: #2e2118;
            --outline-red: #a8221b;
            --steel: #5e6368;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            color: var(--ink);
            font-family: "Zilla Slab", Rockwell, Georgia, serif;
            font-size: 1.0625rem;
            line-height: 1.5;
            background-color: var(--board);
            background-image: radial-gradient(circle, var(--hole) 0 3px, transparent 3.5px);
            background-size: 28px 28px;
        }

        h1, h2, h3, p, ul, dl, dd { margin: 0; }
        ul { padding: 0; list-style: none; }

        .board { max-width: 68rem; margin: 0 auto; padding: 28px 16px 84px; }

        .nameplate {
            display: inline-block;
            background: var(--plate);
            border: 2px solid var(--ink);
            padding: 14px 22px 16px;
        }
        .nameplate h1 { font-size: 2.5rem; line-height: 1.05; font-weight: 700; }
        .nameplate p { margin-top: 6px; font-weight: 500; }

        .board-heading {
            display: inline-block;
            margin-top: 56px;
            padding: 2px 12px 4px;
            background: var(--ink);
            color: var(--plate);
            font-size: 1.375rem;
            font-weight: 700;
        }
        .board-note {
            display: table;
            margin-top: 10px;
            padding: 4px 12px 6px;
            max-width: 34rem;
            background: var(--plate);
            font-weight: 500;
        }

        /* The tool wall. A tool you may use hangs there, solid. One you may not is only its painted outline. */
        .tool-wall {
            margin-top: 20px;
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(15rem, 1fr));
            gap: 28px;
        }
        .tool {
            position: relative;
            min-height: 8.75rem;
            padding: 30px 18px 16px;
        }
        .tool::before {
            /* the hook it hangs from */
            content: "";
            position: absolute;
            top: -8px;
            left: 50%;
            width: 10px;
            height: 26px;
            margin-left: -5px;
            border-radius: 5px;
            background: var(--steel);
        }
        .tool h3 { font-size: 1.5rem; line-height: 1.15; font-weight: 700; }
        .tool p { margin-top: 8px; font-size: 0.9375rem; line-height: 1.4; }
        .tool-hanging { background: var(--ink); color: var(--plate); }
        .tool-missing {
            border: 3px dashed var(--outline-red);
            color: var(--outline-red);
            background: rgba(251, 246, 234, 0.55);
        }
        .tool-missing::before { display: none; }
        .tool-state { font-weight: 700; }

        .plates {
            margin-top: 20px;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(19rem, 1fr));
            gap: 28px;
            align-items: start;
        }
        .plate { background: var(--plate); border: 2px solid var(--ink); padding: 18px 20px 20px; }
        .plate h2 { font-size: 1.375rem; font-weight: 700; }
        .plate > * + * { margin-top: 12px; }
        .plate li + li { margin-top: 10px; padding-top: 10px; border-top: 1px solid rgba(46, 33, 24, 0.25); }
        .plate strong { font-weight: 700; }
        .plate .quiet { font-size: 0.9375rem; color: #5b4636; }
        .big-figure { font-size: 2.25rem; line-height: 1; font-weight: 700; }

        .plate-broken { border-color: var(--outline-red); border-width: 3px; }
        .plate-broken h2 { color: var(--outline-red); }

        .votes {
            display: inline-block;
            min-width: 2.25rem;
            margin-right: 8px;
            padding: 0 6px;
            background: var(--ink);
            color: var(--plate);
            text-align: center;
            font-weight: 700;
        }

        .button {
            display: inline-block;
            padding: 8px 16px;
            border: 2px solid var(--ink);
            background: var(--ink);
            color: var(--plate);
            font: inherit;
            font-weight: 700;
            cursor: pointer;
        }
        .button-plain { background: transparent; color: var(--ink); }
        .button:focus-visible, a:focus-visible { outline: 3px solid var(--outline-red); outline-offset: 3px; }

        @media (max-width: 30rem) {
            .nameplate h1 { font-size: 2rem; }
        }
    </style>
</head>
<body>
    @include('style-ideas.partials.switcher')

    <main class="board">
        <div class="nameplate">
            <h1>{{ $exampleSpace['member']['name'] }}</h1>
            <p>
                {{ $exampleSpace['member']['plan'] }} member since {{ $exampleSpace['member']['memberSince'] }}.
                {{ $exampleSpace['member']['status'] }}.
            </p>
        </div>

        <div>
            <h2 class="board-heading">Your tools</h2>
            <p class="board-note">A solid tool is one you are signed off to use. An empty outline is one you are not.</p>
        </div>

        <ul class="tool-wall">
            @foreach ($exampleSpace['toolSignOffs'] as $toolSignOff)
                <li class="tool {{ $toolSignOff['signedOff'] ? 'tool-hanging' : 'tool-missing' }}">
                    <h3>{{ $toolSignOff['tool'] }}</h3>
                    <p>
                        @unless ($toolSignOff['signedOff'])
                            <span class="tool-state">Not signed off.</span>
                        @endunless
                        {{ $toolSignOff['detail'] }}
                    </p>
                </li>
            @endforeach
        </ul>

        <h2 class="board-heading">Around the shop</h2>

        <div class="plates">
            <section class="plate plate-broken">
                <h2>Out of order</h2>
                <ul>
                    @foreach ($exampleSpace['toolsOutOfOrder'] as $brokenTool)
                        <li><strong>{{ $brokenTool['tool'] }}.</strong> {{ $brokenTool['note'] }}</li>
                    @endforeach
                </ul>
            </section>

            <section class="plate">
                <h2>Your bookings</h2>
                <ul>
                    @foreach ($exampleSpace['bookings'] as $booking)
                        <li><strong>{{ $booking['tool'] }}</strong><br>{{ $booking['when'] }}</li>
                    @endforeach
                </ul>
                <button type="button" class="button">Book a tool</button>
            </section>

            <section class="plate">
                <h2>Classes</h2>
                <ul>
                    @foreach ($exampleSpace['classes'] as $class)
                        <li>
                            <strong>{{ $class['title'] }}</strong><br>
                            {{ $class['when'] }}, {{ $class['price'] }}<br>
                            <span class="quiet">
                                {{ $class['booked'] ? 'You are booked.' : $class['spotsLeft'].' spots left.' }}
                            </span>
                        </li>
                    @endforeach
                </ul>
                <button type="button" class="button button-plain">See all classes</button>
            </section>

            <section class="plate">
                <h2>Dues</h2>
                <p class="big-figure">{{ $exampleSpace['dues']['amount'] }}</p>
                <p>Next payment {{ $exampleSpace['dues']['nextPayment'] }}, from {{ strtolower($exampleSpace['dues']['paymentMethod']) }}.</p>
                <p class="quiet">{{ $exampleSpace['dues']['lastPayment'] }}.</p>
                <button type="button" class="button button-plain">Change card</button>
            </section>

            <section class="plate">
                <h2>Door access</h2>
                <p><strong>{{ $exampleSpace['doorAccess']['fob'] }}</strong> is working.</p>
                <ul>
                    @foreach ($exampleSpace['doorAccess']['entries'] as $entry)
                        <li>{{ $entry['when'] }}<br><span class="quiet">{{ $entry['door'] }}</span></li>
                    @endforeach
                </ul>
            </section>

            <section class="plate">
                <h2>Announcements</h2>
                <ul>
                    @foreach ($exampleSpace['announcements'] as $announcement)
                        <li>
                            <strong>{{ $announcement['title'] }}</strong><br>
                            {{ $announcement['body'] }}<br>
                            <span class="quiet">{{ $announcement['when'] }}</span>
                        </li>
                    @endforeach
                </ul>
            </section>

            <section class="plate">
                <h2>Ideas for the space</h2>
                <ul>
                    @foreach ($exampleSpace['ideas'] as $idea)
                        <li><span class="votes">{{ $idea['votes'] }}</span>{{ $idea['title'] }}</li>
                    @endforeach
                </ul>
                <button type="button" class="button button-plain">Post an idea</button>
            </section>

            <section class="plate">
                <h2>Your storage</h2>
                <p class="big-figure">{{ $exampleSpace['storage']['location'] }}</p>
                <p class="quiet">{{ $exampleSpace['storage']['renews'] }}.</p>
            </section>
        </div>
    </main>
</body>
</html>
