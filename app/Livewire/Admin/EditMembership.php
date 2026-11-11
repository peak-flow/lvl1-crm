<?php

namespace App\Livewire\Admin;

use App\Enums\MembershipStatus;
use App\Models\MembershipPlan;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Edit membership')]
class EditMembership extends Component
{
    public User $member;

    public string $membershipPlanId = '';

    public string $membershipStatus = '';

    public function mount(User $member): void
    {
        $this->member = $member;
        $this->membershipPlanId = (string) $member->membership_plan_id;
        $this->membershipStatus = $member->membership_status->value;
    }

    public function save(): void
    {
        // The route already checks this, but a second check here means the
        // method stays safe even if someone finds another way to call it.
        Gate::authorize('manage-members');

        $validated = $this->validate([
            'membershipPlanId' => ['nullable', 'exists:membership_plans,id'],
            'membershipStatus' => ['required', Rule::enum(MembershipStatus::class)],
        ]);

        $this->member->membership_plan_id = $validated['membershipPlanId'] === '' ? null : (int) $validated['membershipPlanId'];
        $this->member->membership_status = MembershipStatus::from($validated['membershipStatus']);
        $this->member->save();

        session()->flash('status', "{$this->member->name}'s membership was updated.");

        $this->redirectRoute('admin.members.index');
    }

    public function render(): View
    {
        return view('livewire.admin.edit-membership', [
            'membershipPlans' => MembershipPlan::orderBy('name')->get(),
            'membershipStatuses' => MembershipStatus::cases(),
        ]);
    }
}
