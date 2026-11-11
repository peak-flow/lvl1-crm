<x-layouts::app title="Page idea: Events">
    @include('page-ideas.partials.example-notice')

    <h1 class="mt-6 text-3xl font-semibold">Events</h1>
    <p class="mt-2 max-w-prose text-stone-700">What is happening at the space. Anyone can come to events marked "Everyone".</p>

    <div class="mt-6 flex flex-wrap gap-2 text-sm">
        <button type="button" class="button-primary" aria-pressed="true">All events</button>
        <button type="button" class="button-secondary" aria-pressed="false">Open to everyone</button>
        <button type="button" class="button-secondary" aria-pressed="false">Members only</button>
    </div>

    <section class="mt-8 rounded border border-stone-300 bg-white p-4">
        <h2 class="text-lg font-semibold">{{ $examples['everyWeek']['title'] }}</h2>
        <p class="font-medium">{{ $examples['everyWeek']['when'] }}</p>
        <p class="mt-1 text-stone-700">{{ $examples['everyWeek']['body'] }}</p>
    </section>

    @foreach ($examples['months'] as $monthName => $monthEvents)
        <section class="mt-10">
            <h2 class="border-b border-stone-300 pb-2 text-xl font-semibold">{{ $monthName }}</h2>

            <ul>
                @foreach ($monthEvents as $event)
                    <li class="flex gap-4 border-b border-stone-200 py-5">
                        <div class="w-14 shrink-0 text-center">
                            <div class="text-sm text-stone-600">{{ $event['weekday'] }}</div>
                            <div class="text-3xl font-semibold leading-none">{{ $event['day'] }}</div>
                        </div>

                        <div class="min-w-0 flex-1">
                            <h3 class="text-lg font-semibold">{{ $event['title'] }}</h3>
                            <p class="text-sm">
                                {{ $event['time'] }}
                                <span class="ml-2 rounded px-1.5 py-0.5 text-xs {{ $event['audience'] === 'Everyone' ? 'bg-green-100 text-green-900' : 'bg-stone-200 text-stone-800' }}">
                                    {{ $event['audience'] === 'Everyone' ? 'Open to everyone' : 'Members only' }}
                                </span>
                            </p>
                            <p class="mt-2 text-stone-700">{{ $event['body'] }}</p>

                            <div class="mt-3 flex flex-wrap items-center gap-3 text-sm">
                                @if ($event['youAreComing'])
                                    <span class="font-medium text-green-800">You are coming.</span>
                                    <button type="button" class="underline">Cancel</button>
                                @else
                                    <button type="button" class="button-secondary">I am coming</button>
                                @endif
                                <span class="text-stone-600">{{ $event['coming'] }} people are coming</span>
                            </div>
                        </div>
                    </li>
                @endforeach
            </ul>
        </section>
    @endforeach

    <section class="mt-10 rounded border border-stone-200 bg-white p-4">
        <h2 class="text-lg font-semibold">Want to run an event?</h2>
        <p class="mt-1 text-stone-700">Any member can suggest one. An admin checks the calendar and confirms it.</p>
        <button type="button" class="button-secondary mt-3">Suggest an event</button>
    </section>
</x-layouts::app>
