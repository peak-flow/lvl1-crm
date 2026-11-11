# Lesson 4: Waiver and emergency contact

A makerspace needs two things from every member before they pick up a tool: someone to call if they get hurt, and their agreement to the safety waiver. This lesson adds one page that collects both, and locks the ideas board until it is done.

It is also a good lesson for seeing how little code Laravel needs. The whole feature is one migration, one small class, one template, and a nine-line checkpoint.

## What the feature does

- A member opens `/waiver`, enters an emergency contact, ticks "I agree", and saves.
- The site records the moment they agreed.
- Until then, the dashboard shows a reminder and the ideas board sends them to the waiver page.
- Afterwards, the same page lets them change their emergency contact without agreeing again.
- Admins see each member's emergency contact and waiver date on the edit-member page.

The waiver wording in this project is a placeholder. The space's real waiver goes in `resources/views/partials/waiver-text.blade.php`.

## File 1: the migration

Three new columns on `users`:

```php
Schema::table('users', function (Blueprint $table) {
    $table->string('emergency_contact_name')->nullable();
    $table->string('emergency_contact_phone')->nullable();
    $table->timestamp('waiver_signed_at')->nullable();
});
```

`waiver_signed_at` does two jobs with one column. Empty means "has not signed". A date means "signed, and here is when". This is a common trick: store *when* something happened instead of a plain yes or no, and you get the yes or no for free.

The phone number is stored as text, not as a number. Phone numbers have leading zeros, plus signs and brackets, and nobody ever does arithmetic with them.

## File 2: one line of meaning on the model

`app/Models/User.php`:

```php
public function hasSignedWaiver(): bool
{
    return $this->waiver_signed_at !== null;
}
```

- `!==` means "is not exactly equal to", and `null` means "nothing". So this reads: the waiver date is not empty.
- `: bool` says the method answers true or false.

Every other file asks `$member->hasSignedWaiver()` instead of checking the column itself. If the rule ever changes, for example "the waiver must be re-signed every year", it changes here and nowhere else.

A cast in the same file turns the stored date into a date object:

```php
'waiver_signed_at' => 'datetime',
```

That is what lets the template write `$member->waiver_signed_at->format('F j, Y')` and get "November 11, 2026".

## File 3: the form's class

`app/Livewire/Members/WaiverForm.php`. First, `mount()` fills the form with what the member saved before:

```php
public function mount(): void
{
    $member = Auth::user();

    $this->emergencyContactName = (string) $member->emergency_contact_name;
    $this->emergencyContactPhone = (string) $member->emergency_contact_phone;
}
```

`Auth::user()` is the signed-in member. The page never takes a member `id` from the address, so there is no way to open someone else's details.

Then the rules:

```php
$rules = [
    'emergencyContactName' => ['required', 'string', 'max:255'],
    'emergencyContactPhone' => ['required', 'string', 'regex:/^[0-9+().\- ]{7,30}$/'],
];

if (! $member->hasSignedWaiver()) {
    $rules['agreesToWaiver'] = ['accepted'];
}
```

- `accepted` is a built-in rule for checkboxes: it passes only when the box is ticked.
- The rules are an ordinary array, so the code can add to it. The "must agree" rule is only added for a member who has not signed yet.
- `regex:` checks the text against a pattern. This one says: 7 to 30 characters, each one a digit, a space, or one of `+ ( ) . -`. It is deliberately loose. It stops "call my mum" without rejecting real numbers written in different styles.

And the save:

```php
$member->emergency_contact_name = $validated['emergencyContactName'];
$member->emergency_contact_phone = $validated['emergencyContactPhone'];

if (! $member->hasSignedWaiver()) {
    $member->waiver_signed_at = now();
}

$member->save();
```

- `now()` is the current date and time.
- The `if` protects the original date. Changing a phone number next year must not make it look like the waiver was signed next year.

## File 4: the template

`resources/views/livewire/members/waiver-form.blade.php` has two things you have not seen.

Pulling in another template:

```blade
@include('partials.waiver-text')
```

The waiver wording lives in its own small file. Whoever updates the wording does not have to touch the form.

Showing one thing or another:

```blade
@if ($member->hasSignedWaiver())
    <p class="mt-3 text-sm text-stone-700">
        You agreed to this waiver on {{ $member->waiver_signed_at->format('F j, Y') }}.
    </p>
@else
    <label class="mt-3 flex items-start gap-2 text-sm">
        <input wire:model="agreesToWaiver" type="checkbox" class="mt-1">
        <span>I have read the waiver and I agree to it.</span>
    </label>
    @error('agreesToWaiver') <p class="form-error">{{ $message }}</p> @enderror
@endif
```

A member who has signed sees the date. A member who has not sees the checkbox.

## File 5: the checkpoint

Lessons 1 and 3 used middleware that Laravel provides. This is the first one we wrote ourselves. `app/Http/Middleware/EnsureWaiverIsSigned.php`:

```php
public function handle(Request $request, Closure $next): Response
{
    if (! $request->user()->hasSignedWaiver()) {
        return redirect()
            ->route('waiver.edit')
            ->with('status', 'Please add an emergency contact and sign the waiver first.');
    }

    return $next($request);
}
```

Every middleware has this shape. It receives the request and decides between two things: turn the person away, or pass the request along with `$next($request)`.

`->with('status', ...)` attaches a one-time message that the next page can show. This is called a **flash message**.

The middleware gets a short name in `bootstrap/app.php`:

```php
$middleware->alias(['waiver.signed' => EnsureWaiverIsSigned::class]);
```

And then protecting a page takes one extra piece on its route, in `routes/web.php`:

```php
Route::livewire('/waiver', WaiverForm::class)->name('waiver.edit');
Route::livewire('/ideas', IdeaBoard::class)->name('ideas.index')->middleware('waiver.signed');
```

When tool sign-offs or bookings are built, they get the same `->middleware('waiver.signed')` and are protected too. The waiver page itself must not have it, or nobody could ever reach the page to sign.

### One Livewire detail

The start-here guide explained that pressing a button sends a background request. Livewire re-checks middleware on those requests, but only the ones it knows about. One line in `app/Providers/AppServiceProvider.php` adds ours to the list:

```php
Livewire::addPersistentMiddleware([EnsureWaiverIsSigned::class]);
```

Without it, the check would run when the ideas page loads but not when the upvote button is pressed.

## The tests

```sh
php artisan test tests/Feature/Livewire/Members
```

This one moves the clock forward to prove the signing date is protected:

```php
it('lets a member who already signed change their contact without agreeing again', function () {
    $member = User::factory()->create();
    $originalSignedAt = $member->waiver_signed_at;

    $this->travel(3)->days();

    Livewire::actingAs($member)
        ->test(WaiverForm::class)
        ->assertSet('emergencyContactName', $member->emergency_contact_name)
        ->set('emergencyContactName', 'Mary Somerville')
        ->call('save')
        ->assertHasNoErrors();

    $member->refresh();

    expect($member->emergency_contact_name)->toBe('Mary Somerville');
    expect($member->waiver_signed_at->equalTo($originalSignedAt))->toBeTrue();
});
```

`$this->travel(3)->days()` makes the test believe three days have passed. Tests about time would otherwise be impossible to write.

## Count what it took

| Piece | Lines of our own code, roughly |
| --- | --- |
| Migration | 5 |
| `hasSignedWaiver()` and the cast | 5 |
| Form class | 40 |
| Middleware | 9 |
| Route, alias, Livewire line | 4 |

The rest is HTML. Storing the data, checking the input, showing errors next to the right field, remembering who is signed in, and redirecting with a message all came from the framework.

## Try it yourself

1. **Add a field.** Add "How are they related to you?" to the emergency contact. You need a migration, a property, a rule, an input, and a line in `save()`.
2. **Protect another page.** Put `->middleware('waiver.signed')` on the dashboard route. What happens when a new member signs up? Why is that a problem, and which page must never have this middleware?
3. **Show it in the list.** On the admin Members page, add a column that says whether each member has signed.
4. **Expire the waiver.** Change `hasSignedWaiver()` so a waiver older than one year no longer counts. Hint: `$this->waiver_signed_at->gt(now()->subYear())`. Write the test first, using `travel`. This one is a stretch; ask for help in the Discord.

## Words from this lesson

| Word | Meaning |
| --- | --- |
| `null` | Nothing; an empty value |
| Timestamp | A stored date and time |
| Flash message | A message kept for exactly one page load |
| `@include` | Pulls one template into another |
| Regex | A pattern that text must match |
| Alias | A short name for a class |
| Persistent middleware | Middleware Livewire re-checks on every button press |
