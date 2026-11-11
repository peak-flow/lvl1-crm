# Lesson 1: Member sign-up and login

This is the first feature of the makerspace member site. By the end of this lesson you will understand how a person creates an account, signs in, and signs out, and you will be able to read every file involved.

You do not need to know PHP yet. The lesson explains each new piece of the language the first time it shows up.

## What the feature does

| Page | Address | Who can open it |
| --- | --- | --- |
| Join | `/register` | Only people who are not signed in |
| Sign in | `/login` | Only people who are not signed in |
| Dashboard | `/dashboard` | Only signed-in members |
| Sign out | a button in the top bar | Only signed-in members |

## Run it on your machine

You need PHP 8.3 or newer, Composer, and Node.js.

```sh
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate
composer run dev
```

Open <http://localhost:8000>, click **Join**, and make an account. You are now a member of your own local copy.

## The three words to know

- **Laravel** is a PHP framework: a large set of ready-made code for the things every website needs, such as reading web requests, talking to a database, and keeping people signed in.
- **Blade** is Laravel's template language. A Blade file is HTML with a few extras, such as `{{ $name }}` to print a value.
- **Livewire** lets a PHP class and a Blade template work together as one live piece of a page. When you press a button, Livewire calls a method on the PHP class and updates the page, with no JavaScript written by us.

## The journey of a sign-up

1. Your browser asks for `/register`.
2. `routes/web.php` matches that address to the `Register` component.
3. The component draws the form from its Blade template.
4. You fill in the form and press **Create account**.
5. Livewire sends what you typed to the `register()` method.
6. The method checks the input, saves a new row in the `users` table, signs you in, and sends you to `/dashboard`.

The rest of this lesson walks through those files in that order.

## File 1: the routes

`routes/web.php` is the list of every address the site answers.

```php
Route::middleware('guest')->group(function () {
    Route::livewire('/register', Register::class)->name('register');
    Route::livewire('/login', Login::class)->name('login');
});

Route::middleware('auth')->group(function () {
    Route::view('/dashboard', 'dashboard')->name('dashboard');
    Route::post('/logout', LogoutController::class)->name('logout');
});
```

What to notice:

- `Route::livewire('/register', Register::class)` means "when someone visits `/register`, show the `Register` component".
- `->name('register')` gives the route a nickname. Other code says `route('register')` instead of typing the address, so the address can change in one place.
- **Middleware** is a checkpoint a request passes through before it reaches the page. `guest` turns away people who are already signed in. `auth` turns away people who are not, and sends them to the sign-in page.

PHP you just met:

- `Route::livewire(...)` uses `::` to call a method that belongs to a class itself.
- `->name(...)` uses `->` to call a method on an object you already have. Here it is the route that `Route::livewire` handed back.
- `function () { ... }` is a function with no name, passed as a value. Laravel runs it to collect the routes inside the group.
- `Register::class` is the full name of the class, written safely so a typo becomes an error.

## File 2: the component class

`app/Livewire/Auth/Register.php` holds the data and the behaviour of the sign-up form.

```php
class Register extends Component
{
    public string $name = '';

    public string $email = '';

    public string $password = '';

    public string $password_confirmation = '';
```

PHP you just met:

- A **class** is a blueprint that bundles data with the functions that work on it.
- `extends Component` means "start from Livewire's `Component` class and add to it".
- `public string $name = '';` is a **property**: a named piece of data. Variables in PHP always start with `$`. `string` says it holds text, and `''` is the empty text it starts with.
- `public` means code outside the class may read it. Livewire needs that so the form can fill it in.

Now the method that runs when the form is submitted:

```php
public function register(): void
{
    $validated = $this->validate([
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
        'password' => ['required', 'string', 'confirmed', Password::min(8)],
    ]);

    $member = User::create($validated);

    Auth::login($member);

    session()->regenerate();

    $this->redirectRoute('dashboard');
}
```

Line by line:

- `$this->validate([...])` checks what the person typed against a list of rules. If any rule fails, the method stops right there and the form shows the error. If all pass, it returns the checked values.
- `unique:users,email` looks in the `users` table and fails if the email is already there.
- `confirmed` requires the password to match the field named `password_confirmation`.
- `User::create($validated)` saves a new row in the `users` table and gives back a `User` object for it.
- `Auth::login($member)` signs that member in. Laravel remembers them using a **session**: a small record on the server, matched to a cookie in the browser.
- `session()->regenerate()` issues a fresh session id, which blocks a known attack where someone plants an id before you sign in.
- `$this->redirectRoute('dashboard')` sends the browser to the dashboard.

PHP you just met:

- `$this` means "the object this code is running inside", here the form component.
- `[...]` is an **array**, a list of values. `'name' => [...]` is an array entry with a label, called a key.
- `: void` after the method name says the method returns nothing.

### Where does the password get scrambled?

Passwords must never be stored as typed. Look at `app/Models/User.php`:

```php
protected function casts(): array
{
    return [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
}
```

`'password' => 'hashed'` tells Laravel to **hash** the password every time one is saved. A hash is a one-way scramble: the site can check whether a typed password matches it, but nobody can turn the hash back into the password, not even us.

## File 3: the form template

`resources/views/livewire/auth/register.blade.php` is the HTML for the form.

```blade
<form wire:submit="register" class="mt-6 space-y-4">
    <div>
        <label for="name" class="form-label">Name</label>
        <input wire:model="name" id="name" type="text" autocomplete="name" class="form-input">
        @error('name') <p class="form-error">{{ $message }}</p> @enderror
    </div>
```

- `wire:submit="register"` means "when this form is submitted, call the `register()` method on the class".
- `wire:model="name"` ties this input to the `$name` property. Whatever is typed here is what `register()` sees.
- `@error('name') ... @enderror` only shows its contents when the `name` rule failed, and `{{ $message }}` prints the reason.
- `{{ ... }}` prints a value safely. Anything a person typed is shown as text and can never run as code.

## Signing in

`app/Livewire/Auth/Login.php` follows the same shape. The interesting part:

```php
$credentials = ['email' => $this->email, 'password' => $this->password];

if (! Auth::attempt($credentials, $this->remember)) {
    throw ValidationException::withMessages([
        'email' => 'That email and password do not match a member account.',
    ]);
}

session()->regenerate();

$this->redirectIntended(route('dashboard'));
```

- `Auth::attempt(...)` finds the member with that email, hashes the typed password the same way, and compares. It answers `true` or `false`.
- `!` means "not", so the block runs when the attempt failed.
- `throw` stops the method and reports a problem. Here the problem is shown under the email field.
- The message is the same whether the email or the password was wrong. That is on purpose: a stranger cannot use the form to find out which emails belong to members.
- `redirectIntended` sends you to the page you were trying to reach before you were asked to sign in, or to the dashboard if there was none.

## Signing out

`app/Http/Controllers/LogoutController.php` is a **controller**: a plain class that handles one web request.

```php
public function __invoke(Request $request): RedirectResponse
{
    Auth::logout();

    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()->route('home');
}
```

`__invoke` is a special method name that lets the whole class act as a single action. The sign-out button is a small form that sends a `POST` request, because a request that changes something should never be a plain link.

## The tests

Tests are code that checks the feature still works. They live in `tests/Feature/Livewire/Auth/`. Run them with:

```sh
php artisan test tests/Feature/Livewire/Auth
```

One of them, in plain reading order:

```php
it('refuses a wrong password', function () {
    $member = User::factory()->create(['password' => 'correct-horse-battery']);

    Livewire::test(Login::class)
        ->set('email', $member->email)
        ->set('password', 'wrong-password')
        ->call('login')
        ->assertHasErrors(['email']);

    $this->assertGuest();
});
```

It makes a pretend member, fills the sign-in form with the wrong password, submits it, and checks that an error appeared and nobody was signed in. The tests use a throwaway database, so they never touch your real data.

## Try it yourself

1. **Change a message.** In `Login.php`, reword the "do not match" message. Run the tests. Do they still pass? Why?
2. **Tighten a rule.** In `Register.php`, change `Password::min(8)` to `Password::min(12)`. Try to sign up with a 10-character password.
3. **Break something on purpose.** Remove `'confirmed'` from the password rules and run the tests. Read the failure message, then put the rule back.
4. **Add a field.** Add an optional "pronouns" field to the sign-up form. You will need a new column in the `users` table (a migration), a new property, a new rule, a new input, and a new entry in the `#[Fillable(...)]` list at the top of `app/Models/User.php`. This one is a stretch; ask for help in the Discord.

## Words from this lesson

| Word | Meaning |
| --- | --- |
| Route | An address the site answers, and what answers it |
| Middleware | A checkpoint a request passes before reaching the page |
| Component | A PHP class plus a template that act as one live part of a page |
| Property | A named piece of data on a class |
| Method | A function that belongs to a class |
| Validation | Checking input against rules before using it |
| Model | A class that represents one database table, such as `User` for `users` |
| Hash | A one-way scramble used to store passwords |
| Session | The server's record that keeps you signed in |
| Controller | A class that handles a web request |
