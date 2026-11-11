<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Style idea: Safety signs</title>
    @fonts(['barlow', 'barlow-condensed'])
    <style>
        /*
            The safety signs already on the shop walls. Each panel is a sign, and its header colour
            means what it means on a real sign: green is safe, red is do not, yellow needs care, blue is information.
        */
        :root {
            --sign-white: #ffffff;
            --sign-black: #111111;
            --safe-green: #00703c;
            --danger-red: #c8102e;
            --caution-yellow: #ffd100;
            --notice-blue: #0057b8;
            --wall: #e6e8ea;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            background: var(--wall);
            color: var(--sign-black);
            font-family: "Barlow", "Helvetica Neue", Arial, sans-serif;
            font-size: 1.0625rem;
            font-weight: 500;
            line-height: 1.45;
        }

        h1, h2, h3, p, ul, dl, dd { margin: 0; }
        ul { padding: 0; list-style: none; }

        .wall { max-width: 70rem; margin: 0 auto; padding: 24px 16px 80px; }

        .sign {
            background: var(--sign-white);
            border: 4px solid var(--sign-black);
            border-radius: 14px;
            overflow: hidden;
        }
        /* Sign headers are set in capitals because that is how the real signs are lettered. */
        .sign-header {
            padding: 6px 18px 8px;
            font-family: "Barlow Condensed", "Arial Narrow", sans-serif;
            font-size: 2.25rem;
            line-height: 1;
            font-weight: 700;
            letter-spacing: 0.03em;
            text-transform: uppercase;
            text-align: center;
            color: var(--sign-white);
            border-bottom: 4px solid var(--sign-black);
        }
        .sign-body { padding: 16px 18px 18px; }
        .sign-body > * + * { margin-top: 12px; }
        .sign-body h3 {
            font-family: "Barlow Condensed", "Arial Narrow", sans-serif;
            font-size: 1.5rem;
            line-height: 1.1;
            font-weight: 600;
        }
        .sign-body li + li { margin-top: 12px; padding-top: 12px; border-top: 2px solid var(--sign-black); }
        .sign-body .small { font-size: 0.9375rem; color: #3d4044; }
        .sign-body .figure {
            font-family: "Barlow Condensed", "Arial Narrow", sans-serif;
            font-size: 3rem;
            line-height: 1;
            font-weight: 700;
        }

        .sign-safe .sign-header { background: var(--safe-green); }
        .sign-danger .sign-header { background: var(--danger-red); }
        .sign-caution .sign-header { background: var(--caution-yellow); color: var(--sign-black); }
        .sign-notice .sign-header { background: var(--notice-blue); }
        .sign-id .sign-header { background: var(--sign-black); text-align: left; font-size: 3rem; text-transform: none; letter-spacing: 0; }

        /* First row: who you are, then the two signs that answer "what may I touch today?" */
        .top { display: grid; gap: 20px; }
        .answer { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-top: 20px; align-items: start; }
        .answer .sign-header { font-size: 2.75rem; padding: 10px 18px 12px; }
        .answer h3 { font-size: 1.875rem; }

        .rest {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(19rem, 1fr));
            gap: 20px;
            margin-top: 20px;
            align-items: start;
        }

        .votes {
            display: inline-block;
            min-width: 2.5rem;
            font-family: "Barlow Condensed", "Arial Narrow", sans-serif;
            font-size: 1.5rem;
            font-weight: 700;
            line-height: 1;
        }

        .button {
            display: inline-block;
            padding: 8px 18px;
            border: 3px solid var(--sign-black);
            border-radius: 8px;
            background: var(--sign-black);
            color: var(--sign-white);
            font: inherit;
            font-weight: 600;
            cursor: pointer;
        }
        .button:focus-visible, a:focus-visible { outline: 4px solid var(--notice-blue); outline-offset: 3px; }

        @media (max-width: 46rem) {
            .answer { grid-template-columns: 1fr; }
            .sign-id .sign-header { font-size: 2.25rem; }
        }
    </style>
</head>
<body>
    @include('style-ideas.partials.switcher')

    <main class="wall">
        <div class="top">
            <section class="sign sign-id">
                <h1 class="sign-header">{{ $exampleSpace['member']['name'] }}</h1>
                <div class="sign-body">
                    <p>
                        {{ $exampleSpace['member']['plan'] }} member since {{ $exampleSpace['member']['memberSince'] }}.
                        {{ $exampleSpace['member']['status'] }}.
                    </p>
                </div>
            </section>
        </div>

        <div class="answer">
            <section class="sign sign-safe">
                <h2 class="sign-header">Cleared to use</h2>
                <div class="sign-body">
                    <ul>
                        @foreach ($exampleSpace['toolSignOffs'] as $toolSignOff)
                            @if ($toolSignOff['signedOff'])
                                <li>
                                    <h3>{{ $toolSignOff['tool'] }}</h3>
                                    <p class="small">{{ $toolSignOff['detail'] }}</p>
                                </li>
                            @endif
                        @endforeach
                    </ul>
                </div>
            </section>

            <section class="sign sign-danger">
                <h2 class="sign-header">Do not use</h2>
                <div class="sign-body">
                    <ul>
                        @foreach ($exampleSpace['toolsOutOfOrder'] as $brokenTool)
                            <li>
                                <h3>{{ $brokenTool['tool'] }}</h3>
                                <p>Out of order. {{ $brokenTool['note'] }}</p>
                            </li>
                        @endforeach
                        @foreach ($exampleSpace['toolSignOffs'] as $toolSignOff)
                            @unless ($toolSignOff['signedOff'])
                                <li>
                                    <h3>{{ $toolSignOff['tool'] }}</h3>
                                    <p>You are not signed off. {{ $toolSignOff['detail'] }}.</p>
                                </li>
                            @endunless
                        @endforeach
                    </ul>
                </div>
            </section>
        </div>

        <div class="rest">
            <section class="sign sign-caution">
                <h2 class="sign-header">Coming up</h2>
                <div class="sign-body">
                    <ul>
                        <li>
                            <h3>Dues, {{ $exampleSpace['dues']['amount'] }}</h3>
                            <p>Next payment {{ $exampleSpace['dues']['nextPayment'] }}, from {{ strtolower($exampleSpace['dues']['paymentMethod']) }}.</p>
                            <p class="small">{{ $exampleSpace['dues']['lastPayment'] }}.</p>
                        </li>
                        <li>
                            <h3>Storage, {{ $exampleSpace['storage']['location'] }}</h3>
                            <p>{{ $exampleSpace['storage']['renews'] }}.</p>
                        </li>
                    </ul>
                </div>
            </section>

            <section class="sign sign-notice">
                <h2 class="sign-header">Your bookings</h2>
                <div class="sign-body">
                    <ul>
                        @foreach ($exampleSpace['bookings'] as $booking)
                            <li>
                                <h3>{{ $booking['tool'] }}</h3>
                                <p>{{ $booking['when'] }}</p>
                            </li>
                        @endforeach
                    </ul>
                    <button type="button" class="button">Book a tool</button>
                </div>
            </section>

            <section class="sign sign-notice">
                <h2 class="sign-header">Classes</h2>
                <div class="sign-body">
                    <ul>
                        @foreach ($exampleSpace['classes'] as $class)
                            <li>
                                <h3>{{ $class['title'] }}</h3>
                                <p>{{ $class['when'] }}, {{ $class['price'] }}</p>
                                <p class="small">{{ $class['booked'] ? 'You are booked.' : $class['spotsLeft'].' spots left.' }}</p>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </section>

            <section class="sign sign-notice">
                <h2 class="sign-header">Notice</h2>
                <div class="sign-body">
                    <ul>
                        @foreach ($exampleSpace['announcements'] as $announcement)
                            <li>
                                <h3>{{ $announcement['title'] }}</h3>
                                <p>{{ $announcement['body'] }}</p>
                                <p class="small">{{ $announcement['when'] }}</p>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </section>

            <section class="sign sign-safe">
                <h2 class="sign-header">Door access</h2>
                <div class="sign-body">
                    <h3>{{ $exampleSpace['doorAccess']['fob'] }} is working</h3>
                    <ul>
                        @foreach ($exampleSpace['doorAccess']['entries'] as $entry)
                            <li>{{ $entry['when'] }}<br><span class="small">{{ $entry['door'] }}</span></li>
                        @endforeach
                    </ul>
                </div>
            </section>

            <section class="sign sign-notice">
                <h2 class="sign-header">Ideas</h2>
                <div class="sign-body">
                    <ul>
                        @foreach ($exampleSpace['ideas'] as $idea)
                            <li><span class="votes">{{ $idea['votes'] }}</span>{{ $idea['title'] }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="button">Post an idea</button>
                </div>
            </section>
        </div>
    </main>
</body>
</html>
