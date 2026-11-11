<div class="mx-auto max-w-md">
    <h1 class="text-2xl font-semibold">Sign in</h1>

    <form wire:submit="login" class="mt-6 space-y-4">
        <div>
            <label for="email" class="form-label">Email</label>
            <input wire:model="email" id="email" type="email" autocomplete="email" class="form-input">
            @error('email') <p class="form-error">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="password" class="form-label">Password</label>
            <input wire:model="password" id="password" type="password" autocomplete="current-password" class="form-input">
            @error('password') <p class="form-error">{{ $message }}</p> @enderror
        </div>

        <label class="flex items-center gap-2 text-sm">
            <input wire:model="remember" type="checkbox">
            Keep me signed in
        </label>

        <button type="submit" class="button-primary">Sign in</button>
    </form>

    <p class="mt-4 text-sm text-stone-600">
        Not a member yet? <a href="{{ route('register') }}" class="underline">Join</a>
    </p>
</div>
