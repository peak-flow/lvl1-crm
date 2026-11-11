<div class="mx-auto max-w-md">
    <h1 class="text-2xl font-semibold">Join the space</h1>
    <p class="mt-1 text-sm text-stone-600">Create your member account.</p>

    <form wire:submit="register" class="mt-6 space-y-4">
        <div>
            <label for="name" class="form-label">Name</label>
            <input wire:model="name" id="name" type="text" autocomplete="name" class="form-input">
            @error('name') <p class="form-error">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="email" class="form-label">Email</label>
            <input wire:model="email" id="email" type="email" autocomplete="email" class="form-input">
            @error('email') <p class="form-error">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="password" class="form-label">Password</label>
            <input wire:model="password" id="password" type="password" autocomplete="new-password" class="form-input">
            @error('password') <p class="form-error">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="password_confirmation" class="form-label">Confirm password</label>
            <input wire:model="password_confirmation" id="password_confirmation" type="password" autocomplete="new-password" class="form-input">
        </div>

        <button type="submit" class="button-primary">Create account</button>
    </form>

    <p class="mt-4 text-sm text-stone-600">
        Already a member? <a href="{{ route('login') }}" class="underline">Sign in</a>
    </p>
</div>
