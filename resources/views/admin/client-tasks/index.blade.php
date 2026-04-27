<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div class="space-y-1">
                <span class="inline-flex w-fit rounded-full border border-accent/30 bg-accent-light px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.32em] text-brand-primary">Admin · Client Tasks</span>
                <h1 class="font-display text-3xl text-brand-ink sm:text-4xl">Client Task Inbox</h1>
                <p class="text-sm text-brand-muted">Review client requests and assign each task to a freelancer or admin.</p>
            </div>
            <form method="GET" class="flex flex-wrap gap-2">
                <select name="status" class="rounded-xl border border-warm-300/50 px-3 py-2 text-sm text-brand-ink">
                    <option value="">All statuses</option>
                    @foreach (['pending' => 'Pending', 'assigned' => 'Assigned', 'in_progress' => 'In Progress', 'completed' => 'Completed'] as $value => $label)
                        <option value="{{ $value }}" {{ $status === $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                <select name="assignee_type" class="rounded-xl border border-warm-300/50 px-3 py-2 text-sm text-brand-ink">
                    <option value="">All assignees</option>
                    <option value="unassigned" {{ $assigneeType === 'unassigned' ? 'selected' : '' }}>Unassigned</option>
                    <option value="admin" {{ $assigneeType === 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="freelancer" {{ $assigneeType === 'freelancer' ? 'selected' : '' }}>Freelancer</option>
                </select>
                <button class="rounded-xl border border-warm-300/50 bg-warm-100 px-3 py-2 text-sm font-semibold text-brand-ink">Filter</button>
            </form>
        </div>
    </x-slot>

    @if (session('success'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-medium text-emerald-700">{{ session('success') }}</div>
    @endif

    @if (session('error'))
        <div class="rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-sm font-medium text-red-700">{{ session('error') }}</div>
    @endif

    @if ($errors->any())
        <div class="rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-sm text-red-700">
            <ul class="list-disc pl-5 space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <section class="rounded-3xl border border-white/70 bg-white/90 p-6 shadow-panel">
        <div class="space-y-4">
            @forelse ($tasks as $task)
                <article class="rounded-2xl border border-warm-300/50 p-4">
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <div>
                            <h2 class="font-semibold text-brand-ink">{{ $task->title }}</h2>
                            <p class="text-xs text-brand-muted mt-1">Client: {{ $task->client?->name ?? 'Unknown client' }} · Submitted {{ $task->created_at->diffForHumans() }}</p>
                        </div>
                        <div class="flex gap-2">
                            <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $task->priorityBadge() }}">{{ ucfirst($task->priority) }}</span>
                            <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $task->statusBadge() }}">{{ str_replace('_', ' ', ucfirst($task->status)) }}</span>
                        </div>
                    </div>

                    <p class="mt-3 text-sm text-brand-muted">{{ $task->description ?: 'No description provided.' }}</p>

                    <div class="mt-3 flex flex-wrap items-center gap-3 text-xs text-brand-muted">
                        <span>Assigned: {{ $task->assignee?->name ? $task->assignee->name . ' (' . ucfirst((string) $task->assigned_type) . ')' : 'Not assigned yet' }}</span>
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

                    <div class="mt-4 grid gap-3 lg:grid-cols-2">
                        <form method="POST" action="{{ route('admin.client-tasks.assign', $task) }}" class="rounded-xl border border-warm-300/50 p-3 space-y-2">
                            @csrf
                            @method('PATCH')
                            <p class="text-xs font-semibold uppercase tracking-widest text-brand-muted">Assign Task</p>

                            <select name="assigned_type" class="w-full rounded-lg border border-warm-300/50 px-3 py-2 text-sm text-brand-ink">
                                <option value="freelancer">Freelancer</option>
                                <option value="admin">Admin</option>
                            </select>

                            <select name="assigned_to" class="w-full rounded-lg border border-warm-300/50 px-3 py-2 text-sm text-brand-ink">
                                <optgroup label="Freelancers">
                                    @foreach ($freelancers as $freelancer)
                                        <option value="{{ $freelancer->id }}">{{ $freelancer->name }}</option>
                                    @endforeach
                                </optgroup>
                                <optgroup label="Admins">
                                    @foreach ($admins as $admin)
                                        <option value="{{ $admin->id }}">{{ $admin->name }}</option>
                                    @endforeach
                                </optgroup>
                            </select>

                            <input type="date" name="due_date" class="w-full rounded-lg border border-warm-300/50 px-3 py-2 text-sm text-brand-ink" value="{{ optional($task->due_date)->toDateString() }}">

                            <textarea name="admin_notes" rows="2" class="w-full rounded-lg border border-warm-300/50 px-3 py-2 text-sm text-brand-ink" placeholder="Notes for assignee">{{ $task->admin_notes }}</textarea>

                            <button class="w-full rounded-lg bg-brand-primary px-3 py-2 text-sm font-semibold text-white">Assign</button>
                        </form>

                        <form method="POST" action="{{ route('admin.client-tasks.status', $task) }}" class="rounded-xl border border-warm-300/50 p-3 space-y-2">
                            @csrf
                            @method('PATCH')
                            <p class="text-xs font-semibold uppercase tracking-widest text-brand-muted">Update Status</p>
                            <select name="status" class="w-full rounded-lg border border-warm-300/50 px-3 py-2 text-sm text-brand-ink">
                                @foreach (['pending' => 'Pending', 'assigned' => 'Assigned', 'in_progress' => 'In Progress', 'completed' => 'Completed'] as $value => $label)
                                    <option value="{{ $value }}" {{ $task->status === $value ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            <textarea name="admin_notes" rows="3" class="w-full rounded-lg border border-warm-300/50 px-3 py-2 text-sm text-brand-ink" placeholder="Optional status note">{{ $task->admin_notes }}</textarea>
                            <button class="w-full rounded-lg border border-warm-300/50 bg-warm-100 px-3 py-2 text-sm font-semibold text-brand-ink">Save Status</button>
                        </form>
                    </div>
                </article>
            @empty
                <div class="rounded-2xl border border-dashed border-warm-400/50 bg-warm-200/50 p-10 text-center text-sm text-brand-muted">No client tasks found.</div>
            @endforelse
        </div>

        <div class="mt-6">{{ $tasks->links() }}</div>
    </section>
</x-app-layout>
