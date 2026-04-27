<x-app-layout>
    <x-slot name="header">
        <div class="space-y-1">
            <span class="inline-flex w-fit rounded-full border border-accent/30 bg-accent-light px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.32em] text-brand-primary">Freelancer · Tasks</span>
            <h1 class="font-display text-3xl text-brand-ink sm:text-4xl">Assigned Client Tasks</h1>
            <p class="text-sm text-brand-muted">Track client requests assigned to you and update progress.</p>
        </div>
    </x-slot>

    @if (session('success'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-medium text-emerald-700">{{ session('success') }}</div>
    @endif

    <section class="rounded-3xl border border-white/70 bg-white/90 p-6 shadow-panel">
        <div class="space-y-4">
            @forelse ($tasks as $task)
                <article class="rounded-2xl border border-warm-300/50 p-4">
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <div>
                            <h2 class="font-semibold text-brand-ink">{{ $task->title }}</h2>
                            <p class="text-xs text-brand-muted mt-1">Client: {{ $task->client?->name ?? 'Unknown client' }}</p>
                        </div>
                        <div class="flex gap-2">
                            <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $task->priorityBadge() }}">{{ ucfirst($task->priority) }}</span>
                            <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $task->statusBadge() }}">{{ str_replace('_', ' ', ucfirst($task->status)) }}</span>
                        </div>
                    </div>

                    <p class="mt-3 text-sm text-brand-muted">{{ $task->description ?: 'No description provided.' }}</p>

                    <div class="mt-3 flex flex-wrap items-center gap-3 text-xs text-brand-muted">
                        @if ($task->due_date)
                            <span>Due: {{ $task->due_date->format('M d, Y') }}</span>
                        @endif
                        @if ($task->image_path)
                            <a href="{{ asset('storage/' . $task->image_path) }}" target="_blank" class="font-semibold text-brand-primary hover:underline">View Image</a>
                        @endif
                        @if ($task->voice_note_path)
                            <a href="{{ asset('storage/' . $task->voice_note_path) }}" target="_blank" class="font-semibold text-brand-primary hover:underline">Play Voice Note</a>
                        @endif
                    </div>

                    @if ($task->image_path)
                        <div class="mt-3 w-full max-w-xs overflow-hidden rounded-xl border border-warm-300/50 bg-warm-100">
                            <img src="{{ asset('storage/' . $task->image_path) }}" alt="Task image for {{ $task->title }}" class="h-auto w-full object-cover">
                        </div>
                    @endif

                    <form method="POST" action="{{ route('freelancer.client-tasks.status', $task) }}" class="mt-4 flex flex-wrap items-center gap-2">
                        @csrf
                        @method('PATCH')
                        <select name="status" class="rounded-lg border border-warm-300/50 px-3 py-2 text-sm text-brand-ink">
                            @foreach (['assigned' => 'Assigned', 'in_progress' => 'In Progress', 'completed' => 'Completed'] as $value => $label)
                                <option value="{{ $value }}" {{ $task->status === $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        <button class="rounded-lg bg-brand-primary px-4 py-2 text-sm font-semibold text-white">Update</button>
                    </form>
                </article>
            @empty
                <div class="rounded-2xl border border-dashed border-warm-400/50 bg-warm-200/50 p-10 text-center text-sm text-brand-muted">No tasks have been assigned to you yet.</div>
            @endforelse
        </div>

        <div class="mt-6">{{ $tasks->links() }}</div>
    </section>
</x-app-layout>
