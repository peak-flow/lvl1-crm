# Architecture

## Stack

| Piece | Choice | Why |
| --- | --- | --- |
| Framework | Laravel 13 | Chosen by Dave (PHP developer) |
| Interactive pages | Livewire 4, class-based components | Members learn one language (PHP); class + template are separate files, which is easier to teach than single-file components |
| Templates and styles | Blade, Tailwind 4 | Laravel defaults |
| Database | SQLite | Zero setup for members running it locally |
| Tests | Pest | Reads close to plain English |

No starter kit was used. Sign-up and login are hand-written so a lesson can explain every line.

## Layout of the code

| Path | Holds |
| --- | --- |
| `routes/web.php` | Every address the site answers |
| `app/Livewire/Auth/` | `Register` and `Login` components |
| `app/Livewire/Ideas/IdeaBoard.php` | Ideas board: post, list, upvote |
| `app/Models/` | `User` (members) and `Idea` |
| `app/Http/Controllers/LogoutController.php` | Sign-out |
| `app/Http/Controllers/LessonController.php` | Lists and renders lessons |
| `resources/views/layouts/app.blade.php` | Shared page shell and top bar |
| `resources/views/livewire/auth/` | Sign-up and sign-in forms |
| `resources/views/learn/` | Lesson list and lesson page |
| `docs/lessons/NN-slug.md` | One markdown lesson per feature |
| `tests/Feature/` | Feature tests |

## Lessons

- A lesson is a markdown file in `docs/lessons/`, named `NN-slug.md`. The number sets the order.
- The first line must be `# Title`. `LessonController` throws if it is missing.
- The same file is readable on GitHub and rendered at `/learn/NN-slug` with `Str::markdown`.
- Only slugs that match an existing file are served; anything else is a 404.
- Lesson HTML is printed unescaped because lessons are written by maintainers and live in the repository. If members can ever submit lessons through the site, that must change.

## Branches

- `main` is the newest working version.
- Each feature is built on a branch named `lesson-NN-short-name`, merged into `main`, and then kept as the snapshot of the project at the end of that lesson.
- Snapshot branches are not updated afterwards. A fix made later lands on `main` only, so an old lesson branch can show code that has since changed.

## Authentication

- Members are rows in the `users` table (`App\Models\User`).
- Passwords are hashed by the model's `hashed` cast.
- `guest` middleware guards `/register` and `/login`; `auth` guards `/dashboard` and `/logout`.
- Not built yet: password reset, email verification, login rate limiting.

## Ideas board

- Members only: `/ideas` sits in the `auth` route group.
- `ideas` holds one row per idea and belongs to the member who posted it.
- `idea_votes` holds one row per member per idea, with a unique index on the pair, so a double vote is impossible at the database level.
- Vote counts are never stored; they are counted from `idea_votes` when the list is built.
- Voting is a toggle: pressing again removes the vote.
- Not built yet: moderation, editing or deleting ideas, paging for long lists.

## Language tracks

Laravel only for now. If a Python track is added later, the plan is one language-neutral spec per feature that each track implements.
