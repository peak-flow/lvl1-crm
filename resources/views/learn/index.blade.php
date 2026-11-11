<x-layouts::app title="Learn">
    <h1 class="text-2xl font-semibold">Learn how this site is built</h1>
    <p class="mt-2 max-w-prose text-stone-700">
        One lesson per feature. Each explains what the feature does, the code behind it,
        and the PHP and Laravel ideas it uses.
    </p>

    <ol class="mt-6 space-y-2">
        @foreach ($lessons as $lesson)
            <li>
                <a href="{{ route('learn.show', $lesson['slug']) }}" class="underline">{{ $lesson['title'] }}</a>
            </li>
        @endforeach
    </ol>
</x-layouts::app>
