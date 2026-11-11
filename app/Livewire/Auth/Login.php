<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Sign in')]
class Login extends Component
{
    public string $email = '';

    public string $password = '';

    public bool $remember = false;

    public function login(): void
    {
        $this->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        $credentials = ['email' => $this->email, 'password' => $this->password];

        if (! Auth::attempt($credentials, $this->remember)) {
            // One message for both a wrong email and a wrong password, so the
            // form never reveals which emails belong to members.
            throw ValidationException::withMessages([
                'email' => 'That email and password do not match a member account.',
            ]);
        }

        session()->regenerate();

        $this->redirectIntended(route('dashboard'));
    }

    public function render(): View
    {
        return view('livewire.auth.login');
    }
}
