<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Illuminate\View\View;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Members')]
class MemberList extends Component
{
    public function render(): View
    {
        $members = User::query()
            ->with('membershipPlan')
            ->orderBy('name')
            ->get();

        return view('livewire.admin.member-list', ['members' => $members]);
    }
}
