<x-layouts::app title="Dashboard">
    <h1 class="text-2xl font-semibold">Welcome, {{ auth()->user()->name }}</h1>
    <p class="mt-2 text-stone-700">You are signed in as {{ auth()->user()->email }}.</p>
    <p class="mt-2 text-stone-700">More member features will appear here as they are built.</p>
</x-layouts::app>
