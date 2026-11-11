<div>
    <h1 class="text-2xl font-semibold">Members</h1>

    @if (session('status'))
        <p role="status" class="mt-4 rounded border border-green-300 bg-green-50 px-3 py-2 text-sm text-green-900">
            {{ session('status') }}
        </p>
    @endif

    <div class="mt-6 overflow-x-auto rounded border border-stone-200 bg-white">
        <table class="w-full text-left text-sm">
            <thead class="border-b border-stone-200 text-stone-600">
                <tr>
                    <th class="px-3 py-2 font-medium">Name</th>
                    <th class="px-3 py-2 font-medium">Email</th>
                    <th class="px-3 py-2 font-medium">Plan</th>
                    <th class="px-3 py-2 font-medium">Status</th>
                    <th class="px-3 py-2"><span class="sr-only">Actions</span></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($members as $member)
                    <tr wire:key="member-{{ $member->id }}" class="border-b border-stone-100 last:border-0">
                        <td class="px-3 py-2">
                            {{ $member->name }}
                            @if ($member->is_admin)
                                <span class="ml-1 rounded bg-stone-200 px-1.5 py-0.5 text-xs">Admin</span>
                            @endif
                        </td>
                        <td class="px-3 py-2">{{ $member->email }}</td>
                        <td class="px-3 py-2">{{ $member->membershipPlan?->name ?? 'No plan yet' }}</td>
                        <td class="px-3 py-2">{{ $member->membership_status->label() }}</td>
                        <td class="px-3 py-2 text-right">
                            <a href="{{ route('admin.members.edit', $member) }}" class="underline">Edit</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
