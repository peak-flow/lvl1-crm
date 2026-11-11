# Makerspace member site: task list

Each feature ships with a lesson in `docs/lessons/` and is built one at a time.

## Done

- [x] Project scaffold: Laravel 13, Livewire 4, Pest, SQLite, Tailwind
- [x] Feature 1: member sign-up, login, logout, dashboard placeholder
- [x] Lesson 1: `docs/lessons/01-member-sign-up-and-login.md`
- [x] `/learn` pages that render the lesson markdown
- [x] Feature 2: ideas board with upvotes, members only (Rob's suggestion)
- [x] Lesson 2: `docs/lessons/02-ideas-board.md`

## Next (order not yet agreed)

- [ ] Liability waiver and emergency contact at sign-up
- [ ] Membership plans and statuses (active, paused, lapsed, banned)
- [ ] Roles: member, board, admin
- [ ] Admin member list: search, filter, notes, CSV export
- [ ] Recurring dues (Stripe or PayPal: not chosen)
- [ ] Member self-service profile
- [ ] Tool training sign-offs
- [ ] Ideas board: moderation (hide or delete), once roles exist
- [ ] Ideas board: walk-up kiosk use by non-members (needs a decision on fair voting without login)
- [ ] Door access and tool interlocks (depends on the space's hardware)

## Open questions

- Is "LVL1 Members" the right site name? It was taken from the folder name.
- What handles dues and door access at the space today?
- Password reset and email verification need a mail service: which one?
