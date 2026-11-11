# LVL1 Members

A member site for our makerspace, built by members one feature at a time.

Every feature comes with a lesson that explains what it does, the code behind it, and the PHP and Laravel ideas it uses. You can use the site, learn from it, or help build the next piece.

## What works today

- Sign up, sign in, and sign out
- An ideas board where members suggest improvements for the space and upvote them
- Membership plans and statuses, with admins who can set them for any member
- A lessons section at `/learn`

What comes next is in [README-todo.md](README-todo.md).

## Lessons

You do not need to know PHP to start. Each lesson explains new ideas the first time they appear.

| # | Lesson | Feature |
| --- | --- | --- |
| 1 | [Member sign-up and login](docs/lessons/01-member-sign-up-and-login.md) | Accounts, sign-in, sign-out |
| 2 | [The ideas board](docs/lessons/02-ideas-board.md) | Posting ideas, upvoting, your own database tables |
| 3 | [Membership plans, statuses, and admins](docs/lessons/03-membership-plans.md) | Plans, statuses, admin-only pages, your own terminal command |

The same lessons are shown inside the running site at `/learn`.

### See the code as it was at each lesson

Every lesson has its own Git branch. A branch holds the whole project exactly as it was when that lesson's feature was finished, so you can step through the site growing one feature at a time.

| Branch | The project after |
| --- | --- |
| `lesson-01-sign-up-login` | Lesson 1 |
| `lesson-02-ideas-board` | Lesson 2 |
| `lesson-03-membership-plans` | Lesson 3 |
| `main` | The newest lesson, plus any fixes since |

To jump to a lesson, run these from the `lvl1-crm` project folder:

```sh
git switch lesson-01-sign-up-login
composer install
php artisan migrate
```

`git switch` swaps the files in your folder for that branch's version. The other two commands bring the PHP packages and the database in line with it. From Lesson 3 onwards, also run `php artisan db:seed` to add the example membership plans.

Useful commands while you explore:

| Command | What it does |
| --- | --- |
| `git branch -a` | Lists every branch |
| `git switch main` | Returns to the newest version |
| `git diff lesson-01-sign-up-login lesson-02-ideas-board` | Shows everything Lesson 2 added |
| `git status` | Shows which branch you are on and what you have changed |

If Git refuses to switch because you have changed files, either save your work with `git stash` or undo it with `git restore .` first.

## Run it on your machine

There are two steps: install the tools once, then download and start the project. It takes about 15 minutes.

The tools you need are:

- **Git**, to download the code and share your changes
- **PHP 8.3 or newer** and **Composer**, to run the site and fetch its PHP packages
- **Node.js 22 or newer**, to build the styles

### Step 1: install the tools

Pick your system.

#### Mac

1. Install [Laravel Herd](https://herd.laravel.com/). Download it, open the file, drag Herd into Applications, and start it. The free version is all you need. It gives you PHP, Composer, and Node.js.
2. Install Git. Open the **Terminal** app and run:

   ```sh
   xcode-select --install
   ```

   If it says the tools are already installed, you are done.

#### Windows

1. Install [Laravel Herd](https://herd.laravel.com/). Download the Windows installer and run it. The free version is all you need. It gives you PHP, Composer, and Node.js.
2. Install [Git for Windows](https://git-scm.com/download/win). The default choice on every installer screen is fine.
3. Use **PowerShell** for the commands in the rest of this guide.

#### Linux

Herd is not available for Linux yet, so the tools are installed separately.

1. Install PHP and Composer with Laravel's installer:

   ```sh
   /bin/bash -c "$(curl -fsSL https://php.new/install/linux)"
   ```

2. Install Git with your package manager:

   ```sh
   sudo apt install git      # Ubuntu, Debian, Mint
   sudo dnf install git      # Fedora
   ```

3. Install Node.js 22 or newer by following the instructions at [nodejs.org/en/download](https://nodejs.org/en/download). The version in your package manager is often too old.

#### Check that it worked

Close your terminal and open a new one, so it picks up the tools you just installed. Then run:

```sh
git --version
php --version
composer --version
node --version
```

Each command should print a version number. If one says "command not found", that tool is not installed yet.

### Step 2: download and start the project

Run these one at a time, in a folder where you keep your projects.

```sh
git clone https://github.com/peak-flow/lvl1-crm.git
cd lvl1-crm
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
composer run dev
```

What each line does:

| Command | What it does |
| --- | --- |
| `git clone ...` | Downloads the project into a new `lvl1-crm` folder |
| `cd lvl1-crm` | Moves into that folder |
| `composer install` | Fetches the PHP packages the site uses |
| `npm install` | Fetches the tools that build the styles |
| `cp .env.example .env` | Creates your personal settings file |
| `php artisan key:generate` | Writes a secret key into that settings file |
| `php artisan migrate --seed` | Creates the database tables and adds the example membership plans |
| `composer run dev` | Starts the site |

Open <http://localhost:8000> and click **Join**. The database is a single SQLite file on your machine, so there is nothing else to install and nothing you can break.

To see the admin pages, make yourself an admin. Open a second terminal in the `lvl1-crm` folder and run this with the email you signed up with:

```sh
php artisan members:make-admin you@example.com
```

Leave the first terminal window open while you work. To stop the site, press `Ctrl` + `C` in it. To start it again later, run `composer run dev` from the `lvl1-crm` folder.

### Step 3: make a change

1. Open the `lvl1-crm` folder in a code editor. [Visual Studio Code](https://code.visualstudio.com) is free and works on every system.
2. Open `resources/views/home.blade.php` and change some of the text.
3. Refresh the page in your browser. Your change is there.

Now read [Lesson 1](docs/lessons/01-member-sign-up-and-login.md) to see how sign-up and login work.

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

New features follow two rules: the feature and its lesson ship together, and each feature is built on its own branch named `lesson-NN-short-name`. The branch is kept after it is merged into `main`, so it stays as that lesson's snapshot. A lesson is a markdown file in `docs/lessons/`, named like `02-short-name.md`, and its first line is its title.

Questions are welcome in the makerspace Discord.
