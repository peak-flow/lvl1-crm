# Lesson 2: The ideas board

Members can now suggest improvements for the space and upvote the ones they want most. The most upvoted ideas rise to the top.

Lesson 1 used a table that Laravel gave us for free. In this lesson you will see how to add your own tables, connect them to each other, and show a list of records on a page.

## What the feature does

- A signed-in member opens `/ideas`.
- They post an idea: a short title and, if they want, some details.
- Every idea has an upvote button. Each member gets one vote per idea.
- Pressing the button again takes the vote back.
- Ideas are listed with the most votes first.

## The data

Two new tables hold everything.

| Table | One row is | Columns that matter |
| --- | --- | --- |
| `ideas` | One idea | `user_id` (who posted it), `title`, `description` |
| `idea_votes` | One member's vote on one idea | `idea_id`, `user_id` |

Notice that an idea does not store a vote count. The count is worked out by counting rows in `idea_votes`. That way the number can never disagree with the votes that exist.

## File 1: the migration

A **migration** is a PHP file that describes a change to the database. Running `php artisan migrate` applies every migration that has not been applied yet. This is how everyone on the project ends up with the same tables.

`database/migrations/2026_11_11_205233_create_ideas_table.php`:

```php
Schema::create('ideas', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->string('title');
    $table->text('description')->nullable();
    $table->timestamps();
});
```

- `$table->id()` adds a numbered `id` column. Every row gets the next number.
- `$table->foreignId('user_id')->constrained()` adds a column that must hold the `id` of a real row in `users`. This is a **foreign key**: a link from one table to another that the database enforces.
- `->cascadeOnDelete()` means "if that member is deleted, delete their ideas too".
- `$table->string('title')` is short text. `$table->text('description')` is long text.
- `->nullable()` allows the column to be empty.
- `$table->timestamps()` adds `created_at` and `updated_at`, which Laravel fills in for you.

The votes table has one extra line worth reading:

```php
$table->unique(['idea_id', 'user_id']);
```

This tells the database to refuse two rows with the same idea and the same member. Even if our PHP code had a bug, a member could never vote twice.

## File 2: the model

A **model** is a class that stands for one table. `app/Models/Idea.php` stands for `ideas`.

```php
#[Fillable(['user_id', 'title', 'description'])]
class Idea extends Model
{
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function voters(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'idea_votes')->withTimestamps();
    }
}
```

The two methods are **relationships**. They teach Laravel how the tables connect, so you can move between them without writing database queries by hand.

- `user()` says an idea **belongs to** one member. Laravel finds them through the `user_id` column. Afterwards, `$idea->user->name` gives the name of whoever posted it.
- `voters()` says an idea **belongs to many** members, and that the links are kept in the `idea_votes` table. `$idea->voters` gives every member who upvoted it.

The first kind is called one-to-many: one member has many ideas. The second is many-to-many: one idea has many voters, and one member votes on many ideas. A many-to-many link always needs a table in the middle.

PHP you just met:

- `#[Fillable([...])]` is an **attribute**: a label attached to the class. This one lists the columns that may be filled in when creating an idea.
- `: BelongsTo` after a method name is its **return type**. It says what kind of thing the method hands back.

## File 3: the component class

`app/Livewire/Ideas/IdeaBoard.php` has three methods.

### Posting an idea

```php
public function submitIdea(): void
{
    $validated = $this->validate([
        'title' => ['required', 'string', 'max:120'],
        'description' => ['nullable', 'string', 'max:1000'],
    ]);

    Idea::create([
        'user_id' => Auth::id(),
        'title' => $validated['title'],
        'description' => $validated['description'] === '' ? null : $validated['description'],
    ]);

    $this->reset('title', 'description');
}
```

- `Auth::id()` is the `id` of the signed-in member. The idea's owner comes from the server, never from the form, so nobody can post as someone else.
- `$this->reset('title', 'description')` empties the form, ready for the next idea.

PHP you just met:

- `$validated['title']` reads one entry out of an array by its key.
- `condition ? this : that` is a short if/else that produces a value. Here: if the details were left empty, store nothing (`null`); otherwise store what was typed.
- `===` checks that two values are exactly equal.

### Voting

```php
public function toggleVote(int $ideaId): void
{
    $idea = Idea::findOrFail($ideaId);

    $idea->voters()->toggle(Auth::id());
}
```

- `Idea::findOrFail($ideaId)` loads the idea with that `id`, or shows a "not found" page if there is none.
- `toggle` adds a row to `idea_votes` if the member has not voted, and removes it if they have. One line covers both "upvote" and "take it back".
- `int $ideaId` means the method expects a whole number.

### Building the list

```php
$ideas = Idea::query()
    ->with('user')
    ->withCount('voters')
    ->withExists(['voters as voted_by_current_member' => function (Builder $query) {
        $query->where('users.id', Auth::id());
    }])
    ->orderByDesc('voters_count')
    ->latest()
    ->get();
```

Read it as a sentence: get the ideas, with their author, with a count of voters, with a yes-or-no for whether I voted, most votes first, newest first among ties.

- `->with('user')` loads every idea's author in one go. Without it, the page would ask the database once per idea, which gets slow as the list grows.
- `->withCount('voters')` adds a `voters_count` number to each idea.
- `->withExists([...])` adds a true-or-false named `voted_by_current_member`.
- `->get()` runs the query and returns the results.

Each `->` hands its result to the next one. This style is called **method chaining**.

## File 4: the template

`resources/views/livewire/ideas/idea-board.blade.php` shows the form and then loops over the ideas.

```blade
@forelse ($ideas as $idea)
    <li wire:key="idea-{{ $idea->id }}" class="flex gap-4 rounded border border-stone-200 bg-white p-4">
        <button
            type="button"
            wire:click="toggleVote({{ $idea->id }})"
```

and further down:

```blade
@empty
    <li class="text-stone-600">No ideas yet. Post the first one.</li>
@endforelse
```

- `@forelse ... @empty ... @endforelse` repeats the first part once per idea, and shows the `@empty` part when there are none.
- `wire:click="toggleVote({{ $idea->id }})"` calls the `toggleVote` method with this idea's `id` when the button is clicked.
- `wire:key` gives each list item a unique name, so Livewire can tell the items apart when the order changes.
- `{{ $idea->created_at->diffForHumans() }}` prints a time such as "5 minutes ago".

## File 5: the route

One new line in `routes/web.php`, inside the group that requires sign-in:

```php
Route::livewire('/ideas', IdeaBoard::class)->name('ideas.index');
```

Because it sits in the `auth` group from Lesson 1, a visitor who is not signed in is sent to the sign-in page.

## The tests

```sh
php artisan test tests/Feature/Livewire/Ideas
```

One of them:

```php
it('counts one vote per member and lets them take it back', function () {
    $member = User::factory()->create();
    $idea = Idea::factory()->create();

    $board = Livewire::actingAs($member)->test(IdeaBoard::class);

    $board->call('toggleVote', $idea->id);
    expect($idea->voters()->count())->toBe(1);

    $board->call('toggleVote', $idea->id);
    expect($idea->voters()->count())->toBe(0);
});
```

`Idea::factory()->create()` makes a pretend idea. A **factory** is a recipe for fake records, defined in `database/factories/IdeaFactory.php`, so tests do not have to spell out every column.

## Try it yourself

1. **Change the order.** In `IdeaBoard.php`, remove `->orderByDesc('voters_count')`. What order do the ideas appear in now? Which test fails?
2. **Tighten a rule.** Make the title at least 5 characters long by adding `'min:5'` to its rules. Add a case to the test that checks titles.
3. **Show more.** In the template, print the number of ideas at the top of the list. Hint: `$ideas->count()`.
4. **Let authors delete.** Add a "Delete" button that only the member who posted an idea can see and use. You will need a new method, a check that `Auth::id()` matches `$idea->user_id`, and a test proving another member cannot delete it. This one is a stretch; ask for help in the Discord.

## Words from this lesson

| Word | Meaning |
| --- | --- |
| Migration | A file that describes a change to the database's tables |
| Foreign key | A column that must point at a real row in another table |
| Relationship | A method on a model that says how its table connects to another |
| One-to-many | One row here links to many rows there, such as a member and their ideas |
| Many-to-many | Rows on both sides link to many on the other, joined by a middle table |
| Method chaining | Calling one method after another on the result of the last |
| Factory | A recipe that makes fake records for tests |
| Attribute | A `#[...]` label attached to a class or method |
