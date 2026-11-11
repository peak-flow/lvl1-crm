# Start here: How Laravel and Livewire fit together

The numbered lessons each explain one feature. This guide is the map: where things live in the project, and what happens between pressing a button and seeing the page change. Read it once now and come back whenever a lesson mentions a folder you cannot place.

## The project folder

A Laravel project has a lot of folders, and you will work in only a few of them. The ones marked with a star are where nearly all of our code goes.

```text
lvl1-crm/
├── app/                     ★ Our PHP code
│   ├── Console/Commands/      Our own terminal commands
│   ├── Enums/                 Fixed lists of choices, such as membership statuses
│   ├── Http/Controllers/      Classes that answer a plain web request
│   ├── Livewire/            ★ Livewire component classes (the brains of a page)
│   ├── Models/              ★ One class per database table
│   └── Providers/             Code that runs when the site starts up
├── bootstrap/                 Starts Laravel. Leave it alone.
├── config/                    Settings, one file per topic
├── database/
│   ├── factories/             Recipes for fake records, used by tests
│   ├── migrations/          ★ Files that create and change tables
│   ├── seeders/               Files that fill tables with starting data
│   └── database.sqlite        The database itself (on your machine only)
├── docs/lessons/            ★ The lessons, including this one
├── node_modules/              Downloaded JavaScript tools. Never edit.
├── public/                    The only folder a browser can reach directly
├── resources/
│   ├── css/app.css            Styles
│   └── views/               ★ Blade templates (the HTML)
│       ├── layouts/           The shell every page sits inside
│       └── livewire/        ★ The template for each Livewire component
├── routes/
│   └── web.php              ★ Every address the site answers
├── storage/                   Logs and temporary files
├── tests/Feature/           ★ Tests
├── vendor/                    Downloaded PHP packages. Never edit.
├── .env                       Your personal settings and secrets. Never shared.
├── artisan                    The `php artisan` command-line tool
└── composer.json              The list of PHP packages the project needs
```

Three rules of thumb:

- If it is about **what the site can do**, it is in `app/`.
- If it is about **what the site looks like**, it is in `resources/views/`.
- If it is about **what the site remembers**, it is in `database/`.

## A Livewire component is two files

Every interactive page in this project is a Livewire component, and a component is always a pair:

| File | Job | Example |
| --- | --- | --- |
| A PHP class in `app/Livewire/` | Holds the data and the actions | `app/Livewire/Ideas/IdeaBoard.php` |
| A Blade template in `resources/views/livewire/` | Draws the HTML | `resources/views/livewire/ideas/idea-board.blade.php` |

The names match: `Ideas/IdeaBoard.php` goes with `ideas/idea-board.blade.php`. The class ends with a `render()` method that points at its template.

Inside the class there are two kinds of thing:

- **Public properties**, such as `public string $title = '';`. These are the component's data. The template can read them.
- **Public methods**, such as `submitIdea()`. These are the component's actions. The template can call them.

## How a page gets to your screen

Take the ideas board. You type `/ideas` into the browser.

1. **Route.** Laravel looks in `routes/web.php` and finds `Route::livewire('/ideas', IdeaBoard::class)`.
2. **Middleware.** The route sits in the `auth` group, so Laravel first checks you are signed in. If not, you are sent to the sign-in page and the journey ends here.
3. **Component.** Laravel creates an `IdeaBoard` object. If the class has a `mount()` method, it runs now, once.
4. **Render.** `render()` runs. It fetches the ideas from the database through the `Idea` model and hands them to the template.
5. **Template.** Blade turns the template into plain HTML.
6. **Layout.** That HTML is dropped into `resources/views/layouts/app.blade.php`, which adds the top bar and the page shell.
7. **Response.** The finished page is sent to your browser.

So far this is how any Laravel page works. The Livewire part is what happens next.

## How a click becomes a change

You type an idea and press **Post idea**. The page does not reload, yet the new idea appears. Here is what happened.

1. **You type.** The input has `wire:model="title"`. Livewire's JavaScript, already running in the page, remembers what you typed. Nothing is sent yet.
2. **You submit.** The form has `wire:submit="submitIdea"`. Livewire sends a small background request to the server. It carries three things: a saved copy of the component's properties, what you typed, and the name of the method to call.
3. **The server rebuilds the component.** PHP does not keep anything running between requests. So Livewire creates a fresh `IdeaBoard` object and restores its properties from the saved copy. Then it puts what you typed into `$title` and `$description`.
4. **The method runs.** `submitIdea()` validates the input, saves the idea, and empties the form with `reset`.
5. **Render again.** `render()` runs again, exactly as in step 4 of the first journey. The list now includes the new idea.
6. **The HTML goes back.** The server sends the new HTML and a new saved copy of the properties.
7. **The page is patched.** Livewire compares the new HTML with what is on screen and changes only the parts that differ. Your scroll position and everything else stay put.

That loop, **send, rebuild, run, render, patch**, is all Livewire does. Every button in this project goes through it.

### The same thing as a picture

```text
   BROWSER                                   SERVER
   -------                                   ------
   type in a field
   (wire:model remembers it)

   press the button
   (wire:submit / wire:click)
          |
          |   properties + what you typed + method name
          +-------------------------------------------->  rebuild the component
                                                          fill in what you typed
                                                          run the method
                                                          render() the template
          <--------------------------------------------+
          |   new HTML + new properties
          v
   patch only what changed
```

## Things that follow from this

Once the loop makes sense, several rules stop being arbitrary.

- **Public properties are the component's memory.** A property survives from one click to the next because it travels to the browser and back. An ordinary variable inside a method is gone when the method ends.
- **`render()` runs on every interaction.** That is why the ideas list is fetched there: it is always fresh.
- **`mount()` runs only once.** Use it to set things up when the page first loads, as the edit-membership page does.
- **Never put a secret in a public property.** It is sent to the browser, where anyone can read it.
- **Never trust what comes back.** A person can tamper with the request. This is why every action validates its input, and why the admin save method checks permission again even though the page is already protected.
- **A validation failure stops the method.** When `$this->validate()` fails, nothing after it runs, and the template's `@error` blocks show the messages.

## The `wire:` words you will meet

| In the template | What it does |
| --- | --- |
| `wire:model="title"` | Ties an input to the `$title` property |
| `wire:submit="submitIdea"` | Calls a method when the form is submitted |
| `wire:click="toggleVote(5)"` | Calls a method when the element is clicked, passing `5` |
| `wire:key="idea-5"` | Names an item in a list so Livewire can track it |

## Where to look when something breaks

| What you see | Where to look |
| --- | --- |
| "404 Not Found" | `routes/web.php`: is the address there? |
| "403 Forbidden" | Middleware or a gate is refusing you (Lesson 3) |
| Sent to the sign-in page | The route is in the `auth` group and you are signed out |
| An error page with a file name and line number | That file and line. Read the top line of the message first. |
| The page loads but looks unstyled | Is `composer run dev` still running? |
| "no such table" | Run `php artisan migrate` |
| Nothing happens when you click | Check the method name in `wire:click` matches the method in the class |

The full error log is in `storage/logs/laravel.log`.

## Learning resources

The official manuals. Both are well written and worth reading directly:

- [Laravel documentation](https://laravel.com/docs)
  - [Directory structure](https://laravel.com/docs/structure)
  - [Routing](https://laravel.com/docs/routing)
  - [Blade templates](https://laravel.com/docs/blade)
  - [Eloquent, the model layer](https://laravel.com/docs/eloquent)
  - [Validation](https://laravel.com/docs/validation)
  - [Migrations](https://laravel.com/docs/migrations)
- [Livewire documentation](https://livewire.laravel.com/docs)
  - [Components](https://livewire.laravel.com/docs/components)
  - [Properties](https://livewire.laravel.com/docs/properties)
  - [Actions](https://livewire.laravel.com/docs/actions)
  - [How Livewire rebuilds a component (hydration)](https://livewire.laravel.com/docs/hydration)

Courses and tutorials:

- [Laracasts](https://laracasts.com): video courses on PHP, Laravel and Livewire. Many of the beginner series are free.
- [Laravel Daily](https://laraveldaily.com): short, practical tutorials and courses.
- [Laravel Learn](https://laravel.com/learn): Laravel's own beginner courses.
- [PHP manual](https://www.php.net/manual/en/langref.php): the reference for the PHP language itself.

When you are ready, go on to Lesson 1: Member sign-up and login.
