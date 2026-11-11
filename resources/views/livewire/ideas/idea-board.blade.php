<div>
    <h1 class="text-2xl font-semibold">Ideas for the space</h1>
    <p class="mt-1 text-sm text-stone-600">
        Suggest an improvement or something the space needs, and upvote the ideas you want most.
    </p>

    <form wire:submit="submitIdea" class="mt-6 space-y-4 rounded border border-stone-200 bg-white p-4">
        <div>
            <label for="title" class="form-label">Your idea</label>
            <input wire:model="title" id="title" type="text" maxlength="120" class="form-input">
            @error('title') <p class="form-error">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="description" class="form-label">Details (optional)</label>
            <textarea wire:model="description" id="description" rows="3" maxlength="1000" class="form-input"></textarea>
            @error('description') <p class="form-error">{{ $message }}</p> @enderror
        </div>

        <button type="submit" class="button-primary">Post idea</button>
    </form>

    <ul class="mt-8 space-y-3">
        @forelse ($ideas as $idea)
            <li wire:key="idea-{{ $idea->id }}" class="flex gap-4 rounded border border-stone-200 bg-white p-4">
                <button
                    type="button"
                    wire:click="toggleVote({{ $idea->id }})"
                    aria-pressed="{{ $idea->voted_by_current_member ? 'true' : 'false' }}"
                    aria-label="{{ $idea->voted_by_current_member ? 'Remove your vote' : 'Upvote this idea' }}"
                    class="{{ $idea->voted_by_current_member ? 'button-primary' : 'button-secondary' }} h-fit shrink-0 text-center"
                >
                    <span class="block text-lg leading-none">{{ $idea->voters_count }}</span>
                    <span class="block text-xs">{{ $idea->voted_by_current_member ? 'Voted' : 'Upvote' }}</span>
                </button>

                <div class="min-w-0">
                    <h2 class="font-semibold break-words">{{ $idea->title }}</h2>
                    @if ($idea->description)
                        <p class="mt-1 break-words text-stone-700">{{ $idea->description }}</p>
                    @endif
                    <p class="mt-2 text-xs text-stone-500">
                        {{ $idea->user->name }}, {{ $idea->created_at->diffForHumans() }}
                    </p>
                </div>
            </li>
        @empty
            <li class="text-stone-600">No ideas yet. Post the first one.</li>
        @endforelse
    </ul>
</div>
