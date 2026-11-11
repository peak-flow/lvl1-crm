<x-layouts::app title="Page idea: Classes and courses">
    @include('page-ideas.partials.example-notice')

    <h1 class="mt-6 text-3xl font-semibold">Classes and courses</h1>
    <p class="mt-2 max-w-prose text-stone-700">
        Learn a tool, learn a skill, or build something over a few weeks. Members book with one click;
        everyone else can book and pay as a guest.
    </p>

    <section class="mt-10">
        <h2 class="text-xl font-semibold">Sign-off classes</h2>
        <p class="mt-1 max-w-prose text-stone-700">
            Some tools can hurt you. Pass the class for a tool and you are signed off to use it on your own.
        </p>

        <ul class="mt-4 space-y-4">
            @foreach ($examples['signOffClasses'] as $class)
                <li class="rounded border border-stone-200 bg-white p-4">
                    <div class="flex flex-wrap items-start justify-between gap-x-6 gap-y-2">
                        <div class="min-w-0">
                            <h3 class="text-lg font-semibold">{{ $class['title'] }}</h3>
                            <p>{{ $class['when'] }}, {{ $class['length'] }}</p>
                            <p class="text-sm text-stone-600">Taught by {{ $class['teacher'] }}</p>
                        </div>
                        <p class="text-2xl font-semibold">{{ $class['price'] }}</p>
                    </div>

                    <p class="mt-3 text-sm">
                        <span class="rounded bg-green-100 px-1.5 py-0.5 text-green-900">Unlocks the {{ $class['unlocks'] }}</span>
                        @if ($class['needsFirst'])
                            <span class="ml-2 text-stone-700">You need the {{ $class['needsFirst'] }} first.</span>
                        @endif
                    </p>

                    <div class="mt-4 flex flex-wrap items-center gap-3 text-sm">
                        @if ($class['spotsLeft'] > 0)
                            <button type="button" class="button-primary">Book a spot</button>
                            <span class="text-stone-600">{{ $class['spotsLeft'] }} spots left</span>
                        @else
                            <button type="button" class="button-secondary">Join the waiting list</button>
                            <span class="font-medium text-red-800">This class is full.</span>
                        @endif
                    </div>
                </li>
            @endforeach
        </ul>
    </section>

    <section class="mt-12">
        <h2 class="text-xl font-semibold">Skills classes</h2>
        <p class="mt-1 max-w-prose text-stone-700">One evening or morning. No sign-off, just something new to know.</p>

        <ul class="mt-4 space-y-4">
            @foreach ($examples['skillsClasses'] as $class)
                <li class="rounded border border-stone-200 bg-white p-4">
                    <div class="flex flex-wrap items-start justify-between gap-x-6 gap-y-2">
                        <div class="min-w-0">
                            <h3 class="text-lg font-semibold">{{ $class['title'] }}</h3>
                            <p>{{ $class['when'] }}, {{ $class['length'] }}</p>
                            <p class="text-sm text-stone-600">Taught by {{ $class['teacher'] }}</p>
                        </div>
                        <p class="text-2xl font-semibold">{{ $class['price'] }}</p>
                    </div>
                    <p class="mt-3 text-stone-700">{{ $class['body'] }}</p>
                    <div class="mt-4 flex flex-wrap items-center gap-3 text-sm">
                        <button type="button" class="button-primary">Book a spot</button>
                        <span class="text-stone-600">{{ $class['spotsLeft'] }} spots left</span>
                    </div>
                </li>
            @endforeach
        </ul>
    </section>

    <section class="mt-12">
        <h2 class="text-xl font-semibold">Courses</h2>
        <p class="mt-1 max-w-prose text-stone-700">Several sessions with the same group. You book the whole course.</p>

        <ul class="mt-4 space-y-4">
            @foreach ($examples['courses'] as $course)
                <li class="rounded border border-stone-200 bg-white p-4">
                    <div class="flex flex-wrap items-start justify-between gap-x-6 gap-y-2">
                        <div class="min-w-0">
                            <h3 class="text-lg font-semibold">{{ $course['title'] }}</h3>
                            <p>{{ $course['when'] }}</p>
                            <p class="text-sm text-stone-600">Taught by {{ $course['teacher'] }}</p>
                        </div>
                        <p class="text-xl font-semibold">{{ $course['price'] }}</p>
                    </div>
                    <p class="mt-3 text-stone-700">{{ $course['body'] }}</p>

                    <ol class="mt-3 list-decimal space-y-1 pl-6">
                        @foreach ($course['weeks'] as $weekTopic)
                            <li>{{ $weekTopic }}</li>
                        @endforeach
                    </ol>

                    <div class="mt-4 flex flex-wrap items-center gap-3 text-sm">
                        <button type="button" class="button-primary">Book the course</button>
                        <span class="text-stone-600">{{ $course['spotsLeft'] }} spots left</span>
                    </div>
                </li>
            @endforeach
        </ul>
    </section>

    <section class="mt-12 rounded border border-stone-200 bg-white p-4">
        <h2 class="text-lg font-semibold">Want to teach?</h2>
        <p class="mt-1 text-stone-700">Members who are signed off on a tool can offer to teach its class.</p>
        <button type="button" class="button-secondary mt-3">Offer a class</button>
    </section>
</x-layouts::app>
