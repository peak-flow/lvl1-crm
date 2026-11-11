<?php

namespace App\Livewire\Ideas;

use App\Models\Idea;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Ideas')]
class IdeaBoard extends Component
{
    public string $title = '';

    public string $description = '';

    public function submitIdea(): void
    {
        $validated = $this->validate([
            'title' => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        Idea::create([
            'user_id' => Auth::id(),
            'title' => $validated['title'],
            'description' => $validated['description'] === '' ? null : $validated['description'],
        ]);

        $this->reset('title', 'description');
    }

    public function toggleVote(int $ideaId): void
    {
        $idea = Idea::findOrFail($ideaId);

        // toggle() adds the member's vote if it is missing and removes it if it is there.
        $idea->voters()->toggle(Auth::id());
    }

    public function render(): View
    {
        $ideas = Idea::query()
            ->with('user')
            ->withCount('voters')
            ->withExists(['voters as voted_by_current_member' => function (Builder $query) {
                $query->where('users.id', Auth::id());
            }])
            ->orderByDesc('voters_count')
            ->latest()
            ->get();

        return view('livewire.ideas.idea-board', ['ideas' => $ideas]);
    }
}
