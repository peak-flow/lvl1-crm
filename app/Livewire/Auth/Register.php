<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Join')]
class Register extends Component
{
    public string $name = '';

    public string $email = '';

    public string $password = '';

    public string $password_confirmation = '';

    public function register(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'confirmed', Password::min(8)],
        ]);

        // The User model hashes the password before saving it (see its casts() method).
        $member = User::create($validated);

        Auth::login($member);

        // A fresh session id stops an attacker reusing one planted before sign-up.
        session()->regenerate();

        $this->redirectRoute('dashboard');
    }

    public function render(): View
    {
        return view('livewire.auth.register');
    }
}
