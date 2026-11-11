<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>{{ $title ?? config('app.name') }}</title>

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        @livewireStyles
    </head>
    <body class="min-h-screen bg-stone-50 text-stone-900">
        <header class="border-b border-stone-200 bg-white">
            <nav class="mx-auto flex max-w-3xl flex-wrap items-center gap-x-6 gap-y-2 px-4 py-3 text-sm">
                <a href="{{ route('home') }}" class="font-semibold">{{ config('app.name') }}</a>
                <a href="{{ route('learn.index') }}">Learn</a>

                <div class="ml-auto flex items-center gap-4">
                    @auth
                        <a href="{{ route('dashboard') }}">Dashboard</a>
                        <a href="{{ route('ideas.index') }}">Ideas</a>
                        @can('manage-members')
                            <a href="{{ route('admin.members.index') }}">Members</a>
                        @endcan
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="underline">Sign out</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}">Sign in</a>
                        <a href="{{ route('register') }}">Join</a>
                    @endauth
                </div>
            </nav>
        </header>

        <main class="mx-auto max-w-3xl px-4 py-10">
            {{ $slot }}
        </main>

        @livewireScripts
    </body>
</html>
