<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\View\View;

class LessonController extends Controller
{
    public function index(): View
    {
        return view('learn.index', ['lessons' => $this->lessons()]);
    }

    public function show(string $lessonSlug): View
    {
        $lessons = $this->lessons();

        // Only slugs that match a real lesson file are accepted, so the URL can
        // never be used to read some other file on the server.
        abort_unless(isset($lessons[$lessonSlug]), 404);

        $lesson = $lessons[$lessonSlug];

        return view('learn.show', [
            'lesson' => $lesson,
            'lessonHtml' => Str::markdown(File::get($lesson['path'])),
        ]);
    }

    /**
     * Every lesson in docs/lessons, in file-name order, keyed by slug.
     *
     * @return array<string, array{slug: string, title: string, path: string}>
     */
    private function lessons(): array
    {
        $lessons = [];

        foreach (File::glob(base_path('docs/lessons/*.md')) as $lessonPath) {
            $lessonSlug = basename($lessonPath, '.md');

            $lessons[$lessonSlug] = [
                'slug' => $lessonSlug,
                'title' => $this->lessonTitle($lessonPath),
                'path' => $lessonPath,
            ];
        }

        ksort($lessons);

        return $lessons;
    }

    /**
     * A lesson's title is its first markdown heading ("# Like this").
     */
    private function lessonTitle(string $lessonPath): string
    {
        $firstLine = Str::before(File::get($lessonPath), "\n");

        if (! Str::startsWith($firstLine, '# ')) {
            throw new \RuntimeException("Lesson file [{$lessonPath}] must start with a '# Title' line.");
        }

        return trim(Str::after($firstLine, '# '));
    }
}
