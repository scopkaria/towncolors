<x-app-layout>
    <x-slot name="header">
        <div class="space-y-3">
            <div class="flex items-center gap-3">
                <a href="{{ route('client.projects.index') }}" class="rounded-2xl border border-warm-300/50 bg-warm-100 p-2 text-brand-muted transition hover:border-accent/30 hover:text-brand-primary">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" d="M15.75 19.5 8.25 12l7.5-7.5"/></svg>
                </a>
                <span class="inline-flex rounded-full border border-accent/30 bg-accent-light px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.32em] text-brand-primary">
                    Project details
                </span>
            </div>
            <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                <div class="space-y-2">
                    <h1 class="font-display text-3xl text-brand-ink sm:text-4xl">{{ $project->title }}</h1>
                    <p class="text-sm text-brand-muted">Created {{ $project->created_at->format('M d, Y') }}</p>
                </div>
                <x-status-badge :status="$project->status" />
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        {{-- Chat Button --}}
        <a href="{{ route('chat.show', $project) }}" class="btn-primary inline-flex items-center gap-2 self-start">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" d="M8.625 12a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H8.25m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H12m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 0 1-2.555-.337A5.972 5.972 0 0 1 5.41 20.97a5.969 5.969 0 0 1-.474-.065 4.48 4.48 0 0 0 .978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25Z"/></svg>
            Open Chat
        </a>

        {{-- Info Cards --}}
        <div class="grid gap-4 md:grid-cols-3">
            <div class="card-premium rounded-3xl border border-white/70 bg-white/90 p-5 shadow-card">
                <p class="text-sm font-medium text-brand-muted">Status</p>
                <div class="mt-3"><x-status-badge :status="$project->status" /></div>
            </div>
            <div class="card-premium rounded-3xl border border-white/70 bg-white/90 p-5 shadow-card">
                <p class="text-sm font-medium text-brand-muted">Freelancer</p>
                <p class="mt-3 font-display text-xl text-brand-ink">{{ $project->freelancer?->name ?? 'Unassigned' }}</p>
            </div>
            <div class="card-premium rounded-3xl border border-white/70 bg-white/90 p-5 shadow-card">
                <p class="text-sm font-medium text-brand-muted">Files</p>
                <p class="mt-3 font-display text-xl text-brand-ink">{{ $project->files->count() }}</p>
            </div>
        </div>

        {{-- Description --}}
        <div class="rounded-3xl border border-white/70 bg-white/90 p-6 shadow-panel">
            <p class="text-sm font-semibold uppercase tracking-[0.24em] text-brand-primary">Description</p>
            <div class="mt-4 text-sm leading-7 text-brand-muted whitespace-pre-line">{{ $project->description }}</div>
        </div>

        {{-- Categories --}}
        @if ($project->categories->isNotEmpty())
            <div class="rounded-3xl border border-white/70 bg-white/90 p-6 shadow-panel">
                <p class="text-sm font-semibold uppercase tracking-[0.24em] text-brand-primary">Service Types</p>
                <div class="mt-4 flex flex-wrap gap-2">
                    @foreach ($project->categories as $cat)
                        <span class="inline-flex rounded-full px-3 py-1 text-xs font-bold text-white"
                              style="background-color: {{ $cat->color }}">
                            {{ $cat->name }}
                        </span>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Files --}}
        @if ($project->files->isNotEmpty())
            <div class="rounded-3xl border border-white/70 bg-white/90 p-6 shadow-panel">
                <p class="text-sm font-semibold uppercase tracking-[0.24em] text-brand-primary">Attachments</p>
                <div class="mt-4 space-y-2">
                    @foreach ($project->files as $file)
                        <a href="{{ asset('storage/' . $file->file_path) }}" target="_blank"
                            class="flex items-center gap-3 rounded-2xl border border-warm-300/50 bg-warm-200/50 px-4 py-3 text-sm text-brand-ink transition duration-200 hover:border-accent/30 hover:bg-accent/10">
                            <svg class="h-5 w-5 shrink-0 text-brand-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"/></svg>
                            <span class="truncate">{{ basename($file->file_path) }}</span>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</x-app-layout>
