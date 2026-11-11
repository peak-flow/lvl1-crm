# LVL1 Members

A member site for our makerspace, built by members one feature at a time.

Every feature comes with a lesson that explains what it does, the code behind it, and the PHP and Laravel ideas it uses. You can use the site, learn from it, or help build the next piece.

## What works today

- Sign up, sign in, and sign out
- A lessons section at `/learn`

What comes next is in [README-todo.md](README-todo.md).

## Lessons

You do not need to know PHP to start. Each lesson explains new ideas the first time they appear.

| # | Lesson | Feature |
| --- | --- | --- |
| 1 | [Member sign-up and login](docs/lessons/01-member-sign-up-and-login.md) | Accounts, sign-in, sign-out |

The same lessons are shown inside the running site at `/learn`.

## Run it on your machine

You need PHP 8.3 or newer, [Composer](https://getcomposer.org), and [Node.js](https://nodejs.org).

```sh
git clone https://github.com/peak-flow/lvl1-crm.git
cd lvl1-crm
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate
composer run dev
```

Open <http://localhost:8000> and click **Join**. The database is a single SQLite file on your machine, so there is nothing else to install and nothing you can break.

## Run the tests

```sh
php artisan test
```

## How it is built

- [Laravel 13](https://laravel.com) for the framework
- [Livewire 4](https://livewire.laravel.com) for interactive pages, so everything stays in PHP
- [Tailwind CSS](https://tailwindcss.com) for styling
- SQLite for the database
- [Pest](https://pestphp.com) for tests

More detail is in [readme-architecture.md](readme-architecture.md).

## Helping out

1. Read the lesson for the feature you want to touch.
2. Try the "Try it yourself" exercises at the end of it.
3. Pick something from [README-todo.md](README-todo.md), or open an issue with your idea.
4. Make your change on a branch, run the tests, and open a pull request.

New features follow one rule: the feature and its lesson ship together. A lesson is a markdown file in `docs/lessons/`, named like `02-short-name.md`, and its first line is its title.

Questions are welcome in the makerspace Discord.
