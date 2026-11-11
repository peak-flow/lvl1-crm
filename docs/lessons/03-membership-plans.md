# Lesson 3: Membership plans, statuses, and admins

Until now every account was the same. In this lesson members get a **plan** (what they pay for) and a **status** (whether they are in good standing), and a few members become **admins** who can change both.

You will learn how to add columns to a table that already exists, how to limit a value to a fixed list of choices, how to fill a table with starting data, how to restrict a page to certain people, and how to write your own terminal command.

## What the feature does

- Every member has a status: Active, Paused, Lapsed, or Banned. New sign-ups start as Active.
- Every member can be on one plan, or on none yet.
- The dashboard shows a member their own status and plan.
- Admins get a **Members** page listing everyone, and can edit any member's plan and status.
- The first admin is created from the terminal.

One thing to know: a status is only a label for now. A banned member can still sign in. Later features, such as door access, will act on it.

## Try it first

```sh
php artisan migrate --seed
php artisan members:make-admin you@example.com
```

Use the email you signed up with. Refresh the site and a **Members** link appears in the top bar.

## The data

| Table | What changed |
| --- | --- |
| `membership_plans` | New. One row per plan: `name`, `monthly_price_in_cents`, `description` |
| `users` | Three new columns: `membership_plan_id`, `membership_status`, `is_admin` |

## File 1: a migration that changes a table

In Lesson 2 a migration created tables. This one changes a table that already has people in it.

`database/migrations/2026_11_11_205921_add_membership_columns_to_users_table.php`:

```php
Schema::table('users', function (Blueprint $table) {
    $table->foreignId('membership_plan_id')->nullable()->constrained()->nullOnDelete();
    $table->string('membership_status')->default('active');
    $table->boolean('is_admin')->default(false);
});
```

- `Schema::table` (not `Schema::create`) means "change an existing table".
- `->default('active')` fills the column for every row that already exists, and for new rows that do not say otherwise. Without a default, the database would not know what to put in the rows of members who signed up before today.
- `->nullable()` on the plan lets a member have no plan.
- `->nullOnDelete()` means "if the plan is deleted, empty this column" instead of deleting the member.
- `boolean` is a column that holds only true or false.

The other migration creates `membership_plans`. Look at how it stores the price:

```php
$table->unsignedInteger('monthly_price_in_cents');
```

Money is stored as a whole number of cents: `5000` means $50.00. Computers store decimal numbers like `0.1` slightly wrong, and those tiny errors add up to missing pennies. Whole numbers are always exact.

## File 2: an enum for the status

A status must be one of four words. If the code used plain text, a typo like `'actve'` would be saved without complaint. An **enum** is a type whose only possible values are the ones you list.

`app/Enums/MembershipStatus.php`:

```php
enum MembershipStatus: string
{
    case Active = 'active';
    case Paused = 'paused';
    case Lapsed = 'lapsed';
    case Banned = 'banned';

    public function label(): string
    {
        return ucfirst($this->value);
    }
}
```

- Each `case` is one allowed value. `MembershipStatus::Paused` is how the code refers to it.
- `= 'paused'` is the text stored in the database for that case.
- `label()` gives the word shown to people. `ucfirst` makes the first letter a capital.

The `User` model is told to use it:

```php
'membership_status' => MembershipStatus::class,
```

That line sits in `casts()`, next to the password line from Lesson 1. A **cast** converts a column on the way in and out of the database. So `$member->membership_status` is a `MembershipStatus`, not loose text, and `$member->membership_status->label()` prints "Paused".

## File 3: the models

`app/Models/MembershipPlan.php` is new:

```php
public function members(): HasMany
{
    return $this->hasMany(User::class);
}

public function monthlyPriceForDisplay(): string
{
    return '$'.number_format($this->monthly_price_in_cents / 100, 2).' a month';
}
```

`app/Models/User.php` gained the other side of the relationship:

```php
public function membershipPlan(): BelongsTo
{
    return $this->belongsTo(MembershipPlan::class);
}
```

- A plan **has many** members; a member **belongs to** one plan. These are the two ends of the same one-to-many link from Lesson 2.
- `monthlyPriceForDisplay()` turns `5000` into `$50.00 a month`. The `.` joins pieces of text together.

`User.php` also sets what a brand-new member starts with:

```php
protected $attributes = [
    'membership_status' => MembershipStatus::Active->value,
    'is_admin' => false,
];
```

### Why `is_admin` is not in the Fillable list

Look at the top of `User.php`:

```php
#[Fillable(['name', 'email', 'password'])]
```

`User::create([...])` only accepts the columns in that list. Everything else is ignored. This protects against **mass assignment**: someone adding an extra `is_admin=1` field to the sign-up form and being made an admin by accident. Because `is_admin` is not in the list, the only way to set it is on purpose, one line at a time:

```php
$member->is_admin = true;
$member->save();
```

## File 4: the seeder

A **seeder** fills tables with starting data. `database/seeders/MembershipPlanSeeder.php`:

```php
foreach (self::EXAMPLE_PLANS as $examplePlan) {
    MembershipPlan::firstOrCreate(['name' => $examplePlan['name']], $examplePlan);
}
```

- `foreach` runs the block once for each item in the list.
- `firstOrCreate` looks for a plan with that name and only creates it if none exists. Running the seeder twice does not make duplicates.
- The plans and prices in this file are examples. Change them to the space's real ones.

`php artisan migrate --seed` runs the migrations and then the seeders.

## File 5: who is allowed

**Authentication** asks "who are you?". That was Lesson 1. **Authorization** asks "are you allowed to do this?". That is new.

A **gate** is a named yes-or-no question about the signed-in member. It is defined once, in `app/Providers/AppServiceProvider.php`:

```php
Gate::define('manage-members', function (User $member) {
    return $member->is_admin;
});
```

Then it is used in three places.

In `routes/web.php`, to guard the pages:

```php
Route::middleware('can:manage-members')->group(function () {
    Route::livewire('/admin/members', MemberList::class)->name('admin.members.index');
    Route::livewire('/admin/members/{member}', EditMembership::class)->name('admin.members.edit');
});
```

In the layout, to hide the link from people who cannot use it:

```blade
@can('manage-members')
    <a href="{{ route('admin.members.index') }}">Members</a>
@endcan
```

And inside the method that saves changes:

```php
Gate::authorize('manage-members');
```

Hiding a link is a courtesy, not security. Anyone can type an address by hand. The route check and the check inside `save()` are what actually stop them. If the rule for "who is an admin" ever changes, it changes in one place: the gate.

## File 6: the edit page

`app/Livewire/Admin/EditMembership.php` edits one member. Its address is `/admin/members/{member}`, where `{member}` is the member's `id`.

```php
public function mount(User $member): void
{
    $this->member = $member;
    $this->membershipPlanId = (string) $member->membership_plan_id;
    $this->membershipStatus = $member->membership_status->value;
}
```

- `mount` runs once, when the page first loads. It copies the member's current plan and status into the form.
- Laravel sees `User $member` and the `{member}` in the address, and loads that member from the database for you. This is called **route model binding**. An `id` that does not exist gives a "not found" page.
- `(string)` converts a value to text, because form fields hold text.

The rules in `save()`:

```php
$validated = $this->validate([
    'membershipPlanId' => ['nullable', 'exists:membership_plans,id'],
    'membershipStatus' => ['required', Rule::enum(MembershipStatus::class)],
]);
```

- `exists:membership_plans,id` requires the chosen plan to be a real row.
- `Rule::enum(...)` requires the status to be one of the enum's cases.

The form offers only valid choices, but the rules are still needed. A form in a browser can be edited by anyone who knows how, so the server never trusts it.

## File 7: your own terminal command

`app/Console/Commands/MakeMemberAdmin.php` adds a command to `php artisan`:

```php
#[Signature('members:make-admin {email : The email address the member signed up with}')]
#[Description('Give a member admin powers')]
class MakeMemberAdmin extends Command
{
    public function handle(): int
    {
        $member = User::where('email', $this->argument('email'))->first();

        if ($member === null) {
            $this->error("No member has the email address [{$this->argument('email')}].");

            return self::FAILURE;
        }

        $member->is_admin = true;
        $member->save();

        $this->info("{$member->name} is now an admin.");

        return self::SUCCESS;
    }
}
```

- The signature gives the command its name and says it takes one **argument**, `email`.
- `handle()` is what runs.
- `User::where('email', ...)->first()` finds the first member with that email, or gives `null` (nothing) if there is none.
- `"{$member->name} is now an admin."` shows that double-quoted text can have values dropped into it with `{ }`.
- Returning `FAILURE` tells the terminal the command did not work.

Why a terminal command? Someone has to be the first admin, and there is no admin yet to appoint them. Only a person with access to the server can run commands, which is exactly the person who should hold that power.

## The tests

```sh
php artisan test tests/Feature/Livewire/Admin tests/Feature/MembershipTest.php
```

One of them checks the rule that matters most:

```php
it('refuses members who are not admins', function () {
    $this->actingAs(User::factory()->create())->get('/admin/members')->assertForbidden();
});
```

Tests for "who may not do this" are as important as tests for "this works".

## Try it yourself

1. **Add a status.** Add `case Pending = 'pending';` to the enum. Open the edit page. Where did the new choice appear without you touching the template? Why?
2. **Change a price.** Edit the Student price in the seeder and run `php artisan db:seed`. Did the price change? Read the comment about `firstOrCreate` and explain what happened.
3. **Count members.** On the Members page, show how many members are on each status. Hint: `$members->where('membership_status', MembershipStatus::Active)->count()`.
4. **Undo an admin.** Write a `members:remove-admin` command, with a test. This one is a stretch; ask for help in the Discord.

## Words from this lesson

| Word | Meaning |
| --- | --- |
| Enum | A type whose only possible values are the ones you list |
| Cast | A rule that converts a column on its way in and out of the database |
| Default | The value a column gets when none is given |
| Seeder | A file that fills tables with starting data |
| Mass assignment | Filling many columns at once from a list, which is why the list of allowed columns matters |
| Authorization | Deciding whether someone is allowed to do something |
| Gate | A named yes-or-no rule about the signed-in member |
| Route model binding | Laravel loading a record for you from the `id` in the address |
| Argument | A value typed after a terminal command's name |
