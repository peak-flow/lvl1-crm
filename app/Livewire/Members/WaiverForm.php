<?php

namespace App\Livewire\Members;

use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Waiver and emergency contact')]
class WaiverForm extends Component
{
    public string $emergencyContactName = '';

    public string $emergencyContactPhone = '';

    public bool $agreesToWaiver = false;

    public function mount(): void
    {
        $member = Auth::user();

        $this->emergencyContactName = (string) $member->emergency_contact_name;
        $this->emergencyContactPhone = (string) $member->emergency_contact_phone;
    }

    public function save(): void
    {
        $member = Auth::user();

        $rules = [
            'emergencyContactName' => ['required', 'string', 'max:255'],
            'emergencyContactPhone' => ['required', 'string', 'regex:/^[0-9+().\- ]{7,30}$/'],
        ];

        // A member agrees to the waiver once. After that this page only updates the contact.
        if (! $member->hasSignedWaiver()) {
            $rules['agreesToWaiver'] = ['accepted'];
        }

        $validated = $this->validate($rules, [
            'emergencyContactPhone.regex' => 'Enter a phone number using digits, spaces, and + ( ) - only.',
            'agreesToWaiver.accepted' => 'You need to agree to the waiver to use the space.',
        ]);

        $member->emergency_contact_name = $validated['emergencyContactName'];
        $member->emergency_contact_phone = $validated['emergencyContactPhone'];

        if (! $member->hasSignedWaiver()) {
            $member->waiver_signed_at = now();
        }

        $member->save();

        session()->flash('status', 'Thanks. Your details are saved.');

        $this->redirectRoute('dashboard');
    }

    public function render(): View
    {
        return view('livewire.members.waiver-form', ['member' => Auth::user()]);
    }
}
