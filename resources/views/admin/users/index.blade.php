<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="space-y-1">
                <span class="inline-flex w-fit rounded-full border border-accent/30 bg-accent-light px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.32em] text-brand-primary">Admin · Users</span>
                <h1 class="font-display text-3xl text-brand-ink sm:text-4xl">Users</h1>
                <p class="text-sm text-brand-muted">Create and manage client and freelancer accounts.</p>
            </div>
            <a href="{{ route('admin.users.create') }}" class="btn-primary">Create User</a>
        </div>
    </x-slot>

    @if (session('success'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-medium text-emerald-700">{{ session('success') }}</div>
    @endif

    <div class="rounded-3xl border border-white/70 bg-white/90 shadow-panel overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-warm-300/40">
                    <th class="px-5 py-4 text-left text-[11px] font-semibold uppercase tracking-[0.28em] text-brand-muted">User</th>
                    <th class="px-5 py-4 text-left text-[11px] font-semibold uppercase tracking-[0.28em] text-brand-muted">Role</th>
                    <th class="px-5 py-4 text-left text-[11px] font-semibold uppercase tracking-[0.28em] text-brand-muted">Subscription</th>
                    <th class="px-5 py-4 text-left text-[11px] font-semibold uppercase tracking-[0.28em] text-brand-muted">Checklist</th>
                    <th class="px-5 py-4 text-right text-[11px] font-semibold uppercase tracking-[0.28em] text-brand-muted">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-warm-200/50">
                @forelse ($users as $user)
                    <tr>
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">
                                @if ($user->profileImageUrl())
                                    <img src="{{ $user->profileImageUrl() }}" alt="{{ $user->name }}" class="h-11 w-11 rounded-2xl object-cover">
                                @else
                                    <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-accent-light font-display text-sm font-semibold text-brand-primary">{{ $user->avatarInitials() }}</div>
                                @endif
                                <div>
                                    <p class="font-semibold text-brand-ink">{{ $user->name }}</p>
                                    <p class="text-xs text-brand-muted">{{ $user->email }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-4 text-brand-muted">{{ $user->role->label() }}</td>
                        <td class="px-5 py-4">
                            @if ($user->role->value === 'client')
                                @if ($user->hasActiveSubscription())
                                    <span class="inline-flex rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-semibold text-emerald-700">Subscribed</span>
                                @else
                                    <span class="inline-flex rounded-full bg-warm-200 px-2.5 py-0.5 text-xs font-semibold text-warm-700">Not Subscribed</span>
                                @endif
                            @else
                                <span class="text-xs text-brand-muted">N/A</span>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-brand-muted">
                            {{ $user->role->value === 'client' ? $user->checklistItems()->count() . ' item(s)' : 'N/A' }}
                        </td>
                        <td class="px-5 py-4 text-right">
                            @if ($user->role->value === 'client')
                                <a href="{{ route('admin.checklists.show', $user) }}" class="rounded-xl border border-warm-300/50 bg-warm-100 px-3 py-1.5 text-xs font-semibold text-brand-ink transition hover:border-brand-primary/40">Manage Checklist</a>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-5 py-16 text-center text-sm text-brand-muted">No users found.</td></tr>
                @endforelse
            </tbody>
        </table>

        @if ($users->hasPages())
            <div class="border-t border-warm-300/40 px-5 py-4">{{ $users->links() }}</div>
        @endif
    </div>
</x-app-layout>