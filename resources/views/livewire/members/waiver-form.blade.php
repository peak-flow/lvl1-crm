<div class="mx-auto max-w-xl">
    <h1 class="text-2xl font-semibold">Waiver and emergency contact</h1>

    @if (session('status'))
        <p role="status" class="mt-4 rounded border border-amber-300 bg-amber-50 px-3 py-2 text-sm text-amber-900">
            {{ session('status') }}
        </p>
    @endif

    <form wire:submit="save" class="mt-6 space-y-6">
        <fieldset class="space-y-4">
            <legend class="text-lg font-semibold">Emergency contact</legend>
            <p class="text-sm text-stone-600">Who should we call if you are hurt at the space? Only admins can see this.</p>

            <div>
                <label for="emergencyContactName" class="form-label">Their name</label>
                <input wire:model="emergencyContactName" id="emergencyContactName" type="text" autocomplete="off" class="form-input">
                @error('emergencyContactName') <p class="form-error">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="emergencyContactPhone" class="form-label">Their phone number</label>
                <input wire:model="emergencyContactPhone" id="emergencyContactPhone" type="tel" autocomplete="off" class="form-input">
                @error('emergencyContactPhone') <p class="form-error">{{ $message }}</p> @enderror
            </div>
        </fieldset>

        <section>
            <h2 class="text-lg font-semibold">Waiver</h2>

            <div class="mt-2 space-y-3 rounded border border-stone-200 bg-white p-4 text-sm leading-6">
                @include('partials.waiver-text')
            </div>

            @if ($member->hasSignedWaiver())
                <p class="mt-3 text-sm text-stone-700">
                    You agreed to this waiver on {{ $member->waiver_signed_at->format('F j, Y') }}.
                </p>
            @else
                <label class="mt-3 flex items-start gap-2 text-sm">
                    <input wire:model="agreesToWaiver" type="checkbox" class="mt-1">
                    <span>I have read the waiver and I agree to it.</span>
                </label>
                @error('agreesToWaiver') <p class="form-error">{{ $message }}</p> @enderror
            @endif
        </section>

        <button type="submit" class="button-primary">
            {{ $member->hasSignedWaiver() ? 'Save emergency contact' : 'Agree and save' }}
        </button>
    </form>
</div>
