<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="space-y-3">
                <span class="inline-flex w-fit rounded-full border border-accent/30 bg-accent-light px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.32em] text-brand-primary">
                    Project management
                </span>
                <div class="space-y-2">
                    <h1 class="font-display text-3xl text-brand-ink sm:text-4xl">Your Projects</h1>
                    <p class="max-w-2xl text-sm leading-7 text-brand-muted">Track every project from brief to completion.</p>
                </div>
            </div>
            <a href="{{ route('client.projects.create') }}" class="btn-primary shrink-0">
                <svg class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M12 4v16m8-8H4"/></svg>
                New Project
            </a>
        </div>
    </x-slot>

    @if (session('success'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-medium text-emerald-700">
            {{ session('success') }}
        </div>
    @endif

    @if ($projects->isEmpty())
        <div class="rounded-3xl border border-white/70 bg-white/90 p-12 text-center shadow-panel">
            <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-warm-200">
                <svg class="h-8 w-8 text-brand-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" d="M2.25 12.75V12A2.25 2.25 0 0 1 4.5 9.75h15A2.25 2.25 0 0 1 21.75 12v.75m-8.69-6.44-2.12-2.12a1.5 1.5 0 0 0-1.061-.44H4.5A2.25 2.25 0 0 0 2.25 6v12a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9a2.25 2.25 0 0 0-2.25-2.25h-5.379a1.5 1.5 0 0 1-1.06-.44Z"/></svg>
            </div>
            <h3 class="mt-4 font-display text-xl text-brand-ink">No projects yet</h3>
            <p class="mt-2 text-sm text-brand-muted">Create your first project to get started.</p>
            <a href="{{ route('client.projects.create') }}" class="btn-primary mt-6 inline-flex">New Project</a>
        </div>
    @else
        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
            @foreach ($projects as $project)
                <a href="{{ route('client.projects.show', $project) }}" class="card-premium group rounded-3xl border border-white/70 bg-white/90 p-6 shadow-card">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            @if ($project->categories->isNotEmpty())
                                <div class="mb-2 flex flex-wrap gap-1">
                                    @foreach ($project->categories as $cat)
                                        <span class="inline-flex rounded-full px-2.5 py-0.5 text-[10px] font-bold text-white"
                                              style="background-color: {{ $cat->color }}">
                                            {{ $cat->name }}
                                        </span>
                                    @endforeach
                                </div>
                            @endif
                            <h3 class="font-display text-xl text-brand-ink group-hover:text-brand-primary transition duration-200">{{ $project->title }}</h3>
                        </div>
                        <x-status-badge :status="$project->status" />
                    </div>
                    <p class="mt-3 line-clamp-2 text-sm leading-7 text-brand-muted">{{ $project->description }}</p>
                    <div class="mt-5 flex items-center justify-between border-t border-warm-300/40 pt-4">
                        <span class="text-xs text-brand-muted">{{ $project->created_at->diffForHumans() }}</span>
                        <div class="flex items-center gap-2">
                            @if ($project->freelancer)
                                <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-medium text-blue-600">{{ $project->freelancer->name }}</span>
                            @else
                                <span class="rounded-full bg-warm-200 px-3 py-1 text-xs font-medium text-stone-500">Unassigned</span>
                            @endif
                        </div>
                    </div>
                    <div class="mt-3">
                        <a href="{{ route('chat.show', $project) }}" class="inline-flex items-center gap-2 rounded-2xl border border-accent/30 bg-accent/10 px-4 py-2 text-xs font-semibold text-brand-primary transition hover:bg-brand-primary hover:text-white" onclick="event.stopPropagation()">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" d="M8.625 12a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H8.25m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H12m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 0 1-2.555-.337A5.972 5.972 0 0 1 5.41 20.97a5.969 5.969 0 0 1-.474-.065 4.48 4.48 0 0 0 .978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25Z"/></svg>
                            Open Chat
                        </a>
                    </div>
                </a>
            @endforeach
        </div>
    @endif
</x-app-layout>
