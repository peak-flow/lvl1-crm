<x-layouts::app>
    <h1 class="text-3xl font-semibold">{{ config('app.name') }}</h1>
    <p class="mt-3 max-w-prose text-stone-700">
        The member site for our makerspace, built by members one feature at a time.
        Every feature comes with a lesson that explains how it works.
    </p>

    <div class="mt-6 flex flex-wrap gap-3">
        @guest
            <a href="{{ route('register') }}" class="button-primary">Join</a>
            <a href="{{ route('login') }}" class="button-secondary">Sign in</a>
        @endguest
        <a href="{{ route('learn.index') }}" class="button-secondary">Read the lessons</a>
    </div>
</x-layouts::app>
