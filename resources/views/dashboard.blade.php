<x-layouts::app title="Dashboard">
    <h1 class="text-2xl font-semibold">Welcome, {{ auth()->user()->name }}</h1>
    <p class="mt-2 text-stone-700">You are signed in as {{ auth()->user()->email }}.</p>

    <h2 class="mt-8 text-lg font-semibold">Your membership</h2>
    <dl class="mt-2 grid max-w-md grid-cols-[auto_1fr] gap-x-6 gap-y-1 text-stone-700">
        <dt class="font-medium">Status</dt>
        <dd>{{ auth()->user()->membership_status->label() }}</dd>

        <dt class="font-medium">Plan</dt>
        <dd>
            @if (auth()->user()->membershipPlan)
                {{ auth()->user()->membershipPlan->name }}, {{ auth()->user()->membershipPlan->monthlyPriceForDisplay() }}
            @else
                No plan yet
            @endif
        </dd>
    </dl>

    <div class="mt-6">
        <a href="{{ route('ideas.index') }}" class="button-primary">Ideas for the space</a>
    </div>
</x-layouts::app>
