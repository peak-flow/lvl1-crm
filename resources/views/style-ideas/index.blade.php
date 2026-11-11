<x-layouts::app title="Style ideas">
    <h1 class="text-2xl font-semibold">Style ideas</h1>
    <p class="mt-2 max-w-prose text-stone-700">
        The site works, but it looks plain. These are mock-ups of what a member's home page could look like
        once every feature is built: tool sign-offs, bookings, dues, door access and classes.
    </p>
    <p class="mt-2 max-w-prose text-stone-700">
        All three show the same made-up member, so the only difference is the design. Nothing on them is real
        and nothing is saved.
    </p>

    <ul class="mt-8 space-y-4">
        @foreach ($styles as $styleSlug => $style)
            <li class="rounded border border-stone-200 bg-white p-4">
                <h2 class="text-lg font-semibold">
                    <a href="{{ route('style-ideas.show', $styleSlug) }}" class="underline">{{ $style['name'] }}</a>
                </h2>
                <p class="mt-1 text-stone-700">{{ $style['description'] }}</p>
            </li>
        @endforeach
    </ul>
</x-layouts::app>
