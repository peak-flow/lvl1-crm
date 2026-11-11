<x-layouts::app title="Page ideas">
    <h1 class="text-2xl font-semibold">Page ideas</h1>
    <p class="mt-2 max-w-prose text-stone-700">
        Pages the site does not have yet. Each one is a mock-up with made-up content, so we can agree on what
        belongs on the page before anyone builds it.
    </p>
    <p class="mt-2 max-w-prose text-stone-700">
        They use the site's current plain look on purpose. For how things could look, see
        <a href="{{ route('style-ideas.index') }}" class="underline">Style ideas</a>.
    </p>

    <ul class="mt-8 space-y-4">
        @foreach ($pages as $pageSlug => $page)
            <li class="rounded border border-stone-200 bg-white p-4">
                <h2 class="text-lg font-semibold">
                    <a href="{{ route('page-ideas.show', $pageSlug) }}" class="underline">{{ $page['name'] }}</a>
                </h2>
                <p class="mt-1 text-stone-700">{{ $page['description'] }}</p>
            </li>
        @endforeach
    </ul>
</x-layouts::app>
