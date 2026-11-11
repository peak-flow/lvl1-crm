<x-layouts::app title="Dashboard">
    <h1 class="text-2xl font-semibold">Welcome, {{ auth()->user()->name }}</h1>
    <p class="mt-2 text-stone-700">You are signed in as {{ auth()->user()->email }}.</p>

    @if (session('status'))
        <p role="status" class="mt-4 rounded border border-green-300 bg-green-50 px-3 py-2 text-sm text-green-900">
            {{ session('status') }}
        </p>
    @endif

    @if (! auth()->user()->hasSignedWaiver())
        <div class="mt-6 rounded border border-amber-300 bg-amber-50 p-4">
            <p class="font-medium">One more step before you use the space</p>
            <p class="mt-1 text-sm text-stone-700">Add an emergency contact and sign the waiver.</p>
            <a href="{{ route('waiver.edit') }}" class="button-primary mt-3">Do it now</a>
        </div>
    @endif

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

        @if (auth()->user()->hasSignedWaiver())
            <dt class="font-medium">Emergency contact</dt>
            <dd>
                {{ auth()->user()->emergency_contact_name }}, {{ auth()->user()->emergency_contact_phone }}
                <a href="{{ route('waiver.edit') }}" class="ml-1 underline">Change</a>
            </dd>

            <dt class="font-medium">Waiver</dt>
            <dd>Signed {{ auth()->user()->waiver_signed_at->format('F j, Y') }}</dd>
        @endif
    </dl>

    <div class="mt-6">
        <a href="{{ route('ideas.index') }}" class="button-primary">Ideas for the space</a>
    </div>
</x-layouts::app>
