<x-app-layout>
    <x-slot name="header">
        <div class="space-y-3">
            <span class="inline-flex w-fit rounded-full border border-orange-200 bg-orange-50 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.32em] text-brand-primary">
                My assignments
            </span>
            <div class="space-y-2">
                <h1 class="font-display text-3xl text-brand-ink sm:text-4xl">Assigned Projects</h1>
                <p class="max-w-2xl text-sm leading-7 text-brand-muted">Projects assigned to you by the platform.</p>
            </div>
        </div>
    </x-slot>

    @if (session('success'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-medium text-emerald-700">
            {{ session('success') }}
        </div>
    @endif

    @if ($projects->isEmpty())
        <div class="rounded-3xl border border-white/70 bg-white/90 p-12 text-center shadow-panel">
            <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-stone-100">
                <svg class="h-8 w-8 text-brand-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" d="M20.25 14.15v4.25c0 1.094-.787 2.036-1.872 2.18-2.087.277-4.216.42-6.378.42s-4.291-.143-6.378-.42c-1.085-.144-1.872-1.086-1.872-2.18v-4.25m16.5 0a2.18 2.18 0 0 0 .75-1.661V8.706c0-1.081-.768-2.015-1.837-2.175a48.114 48.114 0 0 0-3.413-.387m4.5 8.006c-.194.165-.42.295-.673.38A23.978 23.978 0 0 1 12 15.75c-2.648 0-5.195-.429-7.577-1.22a2.016 2.016 0 0 1-.673-.38m0 0A2.18 2.18 0 0 1 3 12.489V8.706c0-1.081.768-2.015 1.837-2.175a48.111 48.111 0 0 1 3.413-.387m7.5 0V5.25A2.25 2.25 0 0 0 13.5 3h-3a2.25 2.25 0 0 0-2.25 2.25v.894m7.5 0a48.667 48.667 0 0 0-7.5 0"/></svg>
            </div>
            <h3 class="mt-4 font-display text-xl text-brand-ink">No assignments yet</h3>
            <p class="mt-2 text-sm text-brand-muted">You'll see projects here once an admin assigns them to you.</p>
        </div>
    @else
        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
            @foreach ($projects as $project)
                <a href="{{ route('freelancer.projects.show', $project) }}" class="card-premium group rounded-3xl border border-white/70 bg-white/90 p-6 shadow-card">
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
                    <div class="mt-5 flex items-center justify-between border-t border-stone-100 pt-4">
                        <span class="text-xs text-brand-muted">{{ $project->created_at->diffForHumans() }}</span>
                        <span class="text-xs font-medium text-brand-ink">{{ $project->client->name }}</span>
                    </div>
                    <div class="mt-3">
                        <a href="{{ route('chat.show', $project) }}" class="inline-flex items-center gap-2 rounded-2xl border border-orange-200 bg-orange-50/50 px-4 py-2 text-xs font-semibold text-brand-primary transition hover:bg-brand-primary hover:text-white" onclick="event.stopPropagation()">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" d="M8.625 12a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H8.25m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H12m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 0 1-2.555-.337A5.972 5.972 0 0 1 5.41 20.97a5.969 5.969 0 0 1-.474-.065 4.48 4.48 0 0 0 .978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25Z"/></svg>
                            Open Chat
                        </a>
                    </div>
                </a>
            @endforeach
        </div>
    @endif
</x-app-layout>
