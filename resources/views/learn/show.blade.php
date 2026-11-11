<x-layouts::app :title="$lesson['title']">
    <p class="text-sm"><a href="{{ route('learn.index') }}" class="underline">All lessons</a></p>

    {{-- Lesson markdown is written by us and lives in the repository, so its HTML is trusted. --}}
    <article class="lesson mt-4">{!! $lessonHtml !!}</article>
</x-layouts::app>
