<div class="mx-auto max-w-md">
    <p class="text-sm"><a href="{{ route('admin.members.index') }}" class="underline">All members</a></p>

    <h1 class="mt-4 text-2xl font-semibold">{{ $member->name }}</h1>
    <p class="mt-1 text-sm text-stone-600">{{ $member->email }}</p>

    <dl class="mt-4 grid grid-cols-[auto_1fr] gap-x-6 gap-y-1 text-sm text-stone-700">
        <dt class="font-medium">Emergency contact</dt>
        <dd>
            @if ($member->emergency_contact_name)
                {{ $member->emergency_contact_name }}, {{ $member->emergency_contact_phone }}
            @else
                Not given yet
            @endif
        </dd>

        <dt class="font-medium">Waiver</dt>
        <dd>
            @if ($member->hasSignedWaiver())
                Signed {{ $member->waiver_signed_at->format('F j, Y') }}
            @else
                Not signed
            @endif
        </dd>
    </dl>

    <form wire:submit="save" class="mt-6 space-y-4">
        <div>
            <label for="membershipPlanId" class="form-label">Plan</label>
            <select wire:model="membershipPlanId" id="membershipPlanId" class="form-input">
                <option value="">No plan yet</option>
                @foreach ($membershipPlans as $membershipPlan)
                    <option value="{{ $membershipPlan->id }}">
                        {{ $membershipPlan->name }}, {{ $membershipPlan->monthlyPriceForDisplay() }}
                    </option>
                @endforeach
            </select>
            @error('membershipPlanId') <p class="form-error">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="membershipStatus" class="form-label">Status</label>
            <select wire:model="membershipStatus" id="membershipStatus" class="form-input">
                @foreach ($membershipStatuses as $membershipStatusOption)
                    <option value="{{ $membershipStatusOption->value }}">{{ $membershipStatusOption->label() }}</option>
                @endforeach
            </select>
            @error('membershipStatus') <p class="form-error">{{ $message }}</p> @enderror
        </div>

        <button type="submit" class="button-primary">Save</button>
    </form>
</div>
