# Makerspace member site: task list

Each feature ships with a lesson in `docs/lessons/` and is built one at a time.

## Done

- [x] Project scaffold: Laravel 13, Livewire 4, Pest, SQLite, Tailwind
- [x] Feature 1: member sign-up, login, logout, dashboard placeholder
- [x] Lesson 1: `docs/lessons/01-member-sign-up-and-login.md`
- [x] `/learn` pages that render the lesson markdown
- [x] Feature 2: ideas board with upvotes, members only (Rob's suggestion)
- [x] Lesson 2: `docs/lessons/02-ideas-board.md`
- [x] One branch per lesson (`lesson-NN-short-name`)
- [x] Feature 3: membership plans, statuses, basic admin flag, admin pages to set plan and status
- [x] Lesson 3: `docs/lessons/03-membership-plans.md`
- [x] Start-here guide: `docs/lessons/00-how-laravel-and-livewire-fit-together.md`
- [x] Feature 4: waiver and emergency contact page; ideas board locked until signed
- [x] Lesson 4: `docs/lessons/04-waiver-and-emergency-contact.md`
- [x] Style ideas section: Pegboard, Cutting mat, Safety signs (static mock-ups, no database, no lesson)

## Next (order not yet agreed)

- [ ] Replace the placeholder waiver wording with the space's real waiver (`resources/views/partials/waiver-text.blade.php`)
- [ ] Record which version of the waiver a member agreed to, and ask again when the wording changes
- [ ] Replace the example plans and prices with the space's real ones
- [ ] Make statuses do something (for example, stop a banned member signing in)
- [ ] Admin pages to add, edit and retire plans
- [ ] Roles beyond the single admin flag: board, admin
- [ ] Admin member list: search, filter, notes, CSV export (a basic list exists)
- [ ] Let an admin make or remove other admins from the site
- [ ] Recurring dues (Stripe or PayPal: not chosen)
- [ ] Member self-service profile
- [ ] Tool training sign-offs
- [ ] Ideas board: moderation (hide or delete), once roles exist
- [ ] Ideas board: walk-up kiosk use by non-members (needs a decision on fair voting without login)
- [ ] Door access and tool interlocks (depends on the space's hardware)

## Open questions

- Which style idea (if any) should the real site adopt?
- Is "LVL1 Members" the right site name? It was taken from the folder name.
- What handles dues and door access at the space today?
- Password reset and email verification need a mail service: which one?
