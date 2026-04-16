<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="space-y-1">
                <span class="inline-flex w-fit rounded-full border border-accent/30 bg-accent-light px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.32em] text-brand-primary">Admin · Project Management</span>
                <h1 class="font-display text-3xl text-brand-ink sm:text-4xl">Projects</h1>
                <p class="text-sm text-brand-muted">Manage, assign, and track every project on the platform.</p>
            </div>
            <div class="flex shrink-0 items-center gap-2">
                <a href="{{ route('admin.projects.index') }}"
                   class="inline-flex items-center gap-2 rounded-2xl border border-warm-300/50 bg-warm-100 px-4 py-2.5 text-sm font-semibold text-brand-muted transition hover:border-warm-400/50 hover:text-brand-ink">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
                    </svg>
                    Export
                </a>
                <a href="{{ route('client.projects.create') }}"
                   class="inline-flex items-center gap-2 rounded-2xl bg-navy-800 px-4 py-2.5 text-sm font-semibold text-white shadow-card transition hover:bg-slate-800">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    New Project
                </a>
            </div>
        </div>
    </x-slot>

    @if (session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
             x-transition:leave="transition duration-300 ease-in"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="flex items-center gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-medium text-emerald-700">
            <svg class="h-4 w-4 shrink-0 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
            </svg>
            {{ session('success') }}
        </div>
    @endif

    {{-- Stats summary row --}}
    <div class="grid grid-cols-3 gap-3 sm:grid-cols-6">
        @php
            $tabs = [
                ['label' => 'All',         'key' => 'all',         'route' => route('admin.projects.index'),                            'active' => !$currentStatus && !$unassigned, 'colour' => 'text-brand-ink'],
                ['label' => 'Pending',     'key' => 'pending',     'route' => route('admin.projects.index', ['status' => 'pending']),    'active' => $currentStatus === 'pending',     'colour' => 'text-amber-600'],
                ['label' => 'Assigned',    'key' => 'assigned',    'route' => route('admin.projects.index', ['status' => 'assigned']),   'active' => $currentStatus === 'assigned',    'colour' => 'text-blue-600'],
                ['label' => 'In Progress', 'key' => 'in_progress', 'route' => route('admin.projects.index', ['status' => 'in_progress']),'active' => $currentStatus === 'in_progress', 'colour' => 'text-brand-primary'],
                ['label' => 'Completed',   'key' => 'completed',   'route' => route('admin.projects.index', ['status' => 'completed']),  'active' => $currentStatus === 'completed',   'colour' => 'text-emerald-600'],
                ['label' => 'Unassigned',  'key' => 'unassigned',  'route' => route('admin.projects.index', ['unassigned' => 1]),        'active' => $unassigned,                      'colour' => 'text-stone-500'],
            ];
        @endphp
        @foreach ($tabs as $tab)
            <a href="{{ $tab['route'] }}"
               class="rounded-2xl border p-4 text-center shadow-sm transition hover:shadow-md
                      {{ $tab['active'] ? 'border-brand-primary/30 bg-warm-100 ring-1 ring-brand-primary/20' : 'border-white/70 bg-white/90 hover:border-brand-primary/20' }}">
                <p class="text-[10px] font-semibold uppercase tracking-[0.28em] text-brand-muted">{{ $tab['label'] }}</p>
                <p class="mt-2 font-display text-2xl {{ $tab['active'] ? 'text-brand-primary' : $tab['colour'] }}">{{ $counts[$tab['key']] }}</p>
            </a>
        @endforeach
    </div>

    {{-- Main table panel (Alpine.js) --}}
    @php
        $allProjectIds = $projects->pluck('id')->toArray();
        $statusList = ['pending', 'assigned', 'in_progress', 'completed'];
    @endphp

    <div x-data="projectsPage()" x-init="init()" class="relative">

        {{-- Search bar --}}
        <div class="flex flex-col gap-3 rounded-3xl border border-white/70 bg-white/90 px-5 py-4 shadow-card sm:flex-row sm:items-center">
            <div class="relative flex-1">
                <svg class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-brand-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                </svg>
                <input type="text"
                       x-model="search"
                       placeholder="Search by title, client, or freelancer…"
                       class="w-full rounded-xl border border-warm-300/50 bg-warm-100 py-2.5 pl-10 pr-4 text-sm text-brand-ink placeholder:text-brand-muted focus:border-brand-primary focus:outline-none focus:ring-1 focus:ring-brand-primary" />
            </div>
            <div class="flex items-center gap-2">
                <span class="text-xs text-brand-muted">{{ $projects->count() }} projects</span>
                <button @click="search = ''"
                        x-show="search.length > 0"
                        class="rounded-xl border border-warm-300/50 px-3 py-2 text-xs font-semibold text-brand-muted transition hover:border-warm-400/50 hover:text-brand-ink">
                    Clear
                </button>
            </div>
        </div>

        @if ($projects->isEmpty())
            <div class="rounded-3xl border border-white/70 bg-white/90 p-16 text-center shadow-panel">
                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-warm-200/50">
                    <svg class="h-7 w-7 text-brand-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12.75V12A2.25 2.25 0 0 1 4.5 9.75h15A2.25 2.25 0 0 1 21.75 12v.75m-8.69-6.44-2.12-2.12a1.5 1.5 0 0 0-1.061-.44H4.5A2.25 2.25 0 0 0 2.25 6v12a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9a2.25 2.25 0 0 0-2.25-2.25h-5.379a1.5 1.5 0 0 1-1.06-.44Z" />
                    </svg>
                </div>
                <p class="mt-4 text-sm font-semibold text-brand-ink">No projects found</p>
                <p class="mt-1 text-xs text-brand-muted">
                    {{ $currentStatus ? 'No "'.str_replace('_', ' ', $currentStatus).'" projects.' : ($unassigned ? 'All projects are assigned.' : 'No projects have been created yet.') }}
                </p>
            </div>
        @else

            {{-- Table --}}
            <div class="overflow-hidden rounded-3xl border border-white/70 bg-white/90 shadow-panel">

                {{-- Bulk actions bar --}}
                <div x-show="selectedIds.length > 0"
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0 -translate-y-2"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     class="flex items-center gap-3 border-b border-accent/20 bg-accent/10 px-6 py-3">
                    <span class="text-xs font-semibold text-brand-primary" x-text="selectedIds.length + ' selected'"></span>
                    <div class="ml-2 flex items-center gap-2">
                        <form method="POST" action="{{ route('admin.projects.index') }}" class="inline">
                            @csrf @method('PATCH')
                            <template x-for="id in selectedIds" :key="id">
                                <input type="hidden" name="ids[]" :value="id">
                            </template>
                            <input type="hidden" name="bulk_status" value="completed">
                            <button type="submit"
                                    class="rounded-xl border border-emerald-200 bg-emerald-50 px-3 py-1.5 text-xs font-semibold text-emerald-700 transition hover:bg-emerald-100">
                                Mark Completed
                            </button>
                        </form>
                    </div>
                    <button @click="selectedIds = []; allSelected = false"
                            class="ml-auto text-xs font-semibold text-brand-muted hover:text-brand-ink">
                        Clear
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead>
                            <tr class="border-b border-warm-300/40 bg-warm-200/40">
                                <th class="w-10 px-5 py-3.5">
                                    <input type="checkbox"
                                           class="h-4 w-4 rounded border-warm-400/50 text-brand-primary focus:ring-brand-primary"
                                           :checked="allSelected"
                                           @change="toggleAll({{ json_encode($allProjectIds) }})">
                                </th>
                                <th class="px-4 py-3.5 text-[11px] font-semibold uppercase tracking-[0.2em] text-brand-muted">Project</th>
                                <th class="hidden px-4 py-3.5 text-[11px] font-semibold uppercase tracking-[0.2em] text-brand-muted md:table-cell">Client</th>
                                <th class="hidden px-4 py-3.5 text-[11px] font-semibold uppercase tracking-[0.2em] text-brand-muted lg:table-cell">Freelancer</th>
                                <th class="px-4 py-3.5 text-[11px] font-semibold uppercase tracking-[0.2em] text-brand-muted">Status</th>
                                <th class="hidden px-4 py-3.5 text-[11px] font-semibold uppercase tracking-[0.2em] text-brand-muted xl:table-cell">Progress</th>
                                <th class="hidden px-4 py-3.5 text-[11px] font-semibold uppercase tracking-[0.2em] text-brand-muted lg:table-cell">Updated</th>
                                <th class="px-4 py-3.5 text-right text-[11px] font-semibold uppercase tracking-[0.2em] text-brand-muted">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-warm-300/80">
                            @forelse ($projects as $project)
                                @php
                                    $progress = match($project->status) {
                                        'assigned'    => 25,
                                        'in_progress' => 65,
                                        'completed'   => 100,
                                        default       => 5,
                                    };
                                    $progressColor = match($project->status) {
                                        'assigned'    => 'bg-blue-400',
                                        'in_progress' => 'bg-accent',
                                        'completed'   => 'bg-emerald-500',
                                        default       => 'bg-stone-300',
                                    };
                                    $panelData = [
                                        'id'            => $project->id,
                                        'title'         => $project->title,
                                        'description'   => $project->description,
                                        'status'        => $project->status,
                                        'client_name'   => $project->client?->name,
                                        'client_email'  => $project->client?->email,
                                        'freelancer_name'  => $project->freelancer?->name,
                                        'freelancer_email' => $project->freelancer?->email,
                                        'categories'    => $project->categories->map(fn($c) => ['name' => $c->name, 'color' => $c->color])->values()->toArray(),
                                        'files_count'   => $project->files_count,
                                        'updated_at'    => $project->updated_at->format('M d, Y'),
                                        'created_at'    => $project->created_at->format('M d, Y'),
                                        'progress'      => $progress,
                                        'show_url'      => route('admin.projects.show', $project),
                                        'chat_url'      => route('chat.show', $project),
                                        'assign_url'    => route('admin.projects.assign', $project),
                                        'status_url'    => route('admin.projects.status', $project),
                                        'invoice_url'   => route('admin.invoices.create', ['project_id' => $project->id]),
                                    ];
                                @endphp
                                <tr class="group cursor-default transition-colors hover:bg-warm-200/70"
                                    data-search-title="{{ Str::lower($project->title) }}"
                                    data-search-client="{{ Str::lower($project->client?->name ?? '') }}"
                                    data-search-freelancer="{{ Str::lower($project->freelancer?->name ?? '') }}"
                                    x-show="isVisible($el)">
                                    {{-- Checkbox --}}
                                    <td class="px-5 py-4">
                                        <input type="checkbox"
                                               class="h-4 w-4 rounded border-warm-400/50 text-brand-primary focus:ring-brand-primary"
                                               :checked="selectedIds.includes({{ $project->id }})"
                                               @change="toggleId({{ $project->id }})">
                                    </td>

                                    {{-- Project title + categories --}}
                                    <td class="max-w-[220px] px-4 py-4">
                                        <div class="space-y-1.5">
                                            @if ($project->categories->isNotEmpty())
                                                <div class="flex flex-wrap gap-1">
                                                    @foreach ($project->categories as $cat)
                                                        <span class="inline-flex rounded-full px-2 py-0.5 text-[9px] font-bold uppercase tracking-wide text-white"
                                                              style="background-color: {{ $cat->color }}">
                                                            {{ $cat->name }}
                                                        </span>
                                                    @endforeach
                                                </div>
                                            @endif
                                            <p class="truncate font-semibold text-brand-ink">{{ $project->title }}</p>
                                            <p class="truncate text-xs text-brand-muted">{{ Str::limit($project->description, 55) }}</p>
                                        </div>
                                    </td>

                                    {{-- Client --}}
                                    <td class="hidden px-4 py-4 md:table-cell">
                                        <div class="flex items-center gap-2">
                                            <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-warm-200 text-[10px] font-bold text-stone-500">
                                                {{ strtoupper(substr($project->client?->name ?? '?', 0, 1)) }}
                                            </div>
                                            <div class="min-w-0">
                                                <p class="truncate text-sm font-medium text-brand-ink">{{ $project->client?->name }}</p>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Freelancer --}}
                                    <td class="hidden px-4 py-4 lg:table-cell">
                                        @if ($project->freelancer)
                                            <div class="flex items-center gap-2">
                                                <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-blue-100 text-[10px] font-bold text-blue-600">
                                                    {{ strtoupper(substr($project->freelancer->name, 0, 1)) }}
                                                </div>
                                                <p class="truncate text-sm font-medium text-brand-ink">{{ $project->freelancer->name }}</p>
                                            </div>
                                        @else
                                            <span class="inline-flex items-center gap-1 rounded-full border border-warm-300/50 bg-warm-200/50 px-2.5 py-1 text-[11px] font-semibold text-stone-500">
                                                <span class="h-1.5 w-1.5 rounded-full bg-stone-300"></span>
                                                Unassigned
                                            </span>
                                        @endif
                                    </td>

                                    {{-- Status badge --}}
                                    <td class="px-4 py-4">
                                        <x-status-badge :status="$project->status" />
                                    </td>

                                    {{-- Progress --}}
                                    <td class="hidden px-4 py-4 xl:table-cell">
                                        <div class="w-28 space-y-1.5">
                                            <div class="flex items-center justify-between">
                                                <span class="text-xs font-semibold text-brand-ink">{{ $progress }}%</span>
                                            </div>
                                            <div class="h-1.5 w-full overflow-hidden rounded-full bg-warm-200">
                                                <div class="h-full rounded-full transition-all duration-500 {{ $progressColor }}"
                                                     style="width: {{ $progress }}%"></div>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Updated --}}
                                    <td class="hidden px-4 py-4 lg:table-cell">
                                        <p class="text-xs text-brand-muted">{{ $project->updated_at->format('M j, Y') }}</p>
                                        <p class="mt-0.5 text-[10px] text-stone-400">{{ $project->updated_at->diffForHumans() }}</p>
                                    </td>

                                    {{-- Actions --}}
                                    <td class="px-4 py-4 text-right">
                                        <div class="flex items-center justify-end gap-1.5">
                                            {{-- View (opens slide panel) --}}
                                            <button type="button"
                                                    @click="openPanel({{ json_encode($panelData) }})"
                                                    class="inline-flex items-center gap-1.5 rounded-xl border border-warm-300/50 bg-warm-100 px-3 py-2 text-xs font-semibold text-brand-ink transition hover:border-brand-primary/30 hover:bg-accent-light hover:text-brand-primary">
                                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                                </svg>
                                                View
                                            </button>

                                            {{-- Chat --}}
                                            <a href="{{ route('chat.show', $project) }}"
                                               class="inline-flex items-center justify-center rounded-xl border border-warm-300/50 bg-warm-100 p-2 text-brand-muted transition hover:border-accent/30 hover:bg-accent-light hover:text-brand-primary"
                                               title="Open Chat">
                                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 0 1 .865-.501 48.172 48.172 0 0 0 3.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0 0 12 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018Z" />
                                                </svg>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-12 text-center text-sm text-brand-muted">No projects found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- No search results row --}}
                <div x-show="noResults && search.length > 0"
                     class="border-t border-warm-300/40 px-6 py-10 text-center">
                    <p class="text-sm font-medium text-brand-ink">No results for "<span x-text="search"></span>"</p>
                    <p class="mt-1 text-xs text-brand-muted">Try a different title, client, or freelancer name.</p>
                </div>
            </div>

        @endif

        {{-- ═══ RIGHT SLIDE PANEL ═══════════════════════════════════════════ --}}
        <div x-show="panelOpen"
             x-cloak
             class="fixed inset-0 z-50"
             @keydown.escape.window="closePanel()">

            {{-- Backdrop --}}
            <div class="absolute inset-0 bg-navy-900/40 backdrop-blur-sm"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 @click="closePanel()"></div>

            {{-- Panel --}}
            <div class="absolute inset-y-0 right-0 flex w-full max-w-lg flex-col overflow-hidden border-l border-warm-300/50 bg-warm-100 shadow-2xl"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="translate-x-full opacity-0"
                 x-transition:enter-end="translate-x-0 opacity-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="translate-x-0 opacity-100"
                 x-transition:leave-end="translate-x-full opacity-0">

                {{-- Panel header --}}
                <div class="flex shrink-0 items-start justify-between border-b border-warm-300/40 bg-warm-200/60 px-6 py-5">
                    <div class="min-w-0 flex-1 space-y-2">
                        {{-- Category pills --}}
                        <div class="flex flex-wrap gap-1.5" x-show="activeProject?.categories?.length">
                            <template x-for="cat in (activeProject?.categories ?? [])" :key="cat.name">
                                <span class="inline-flex rounded-full px-2.5 py-0.5 text-[10px] font-bold text-white"
                                      :style="'background-color:' + cat.color"
                                      x-text="cat.name"></span>
                            </template>
                        </div>
                        <h2 class="font-display text-xl text-brand-ink" x-text="activeProject?.title ?? ''"></h2>
                        <div class="flex items-center gap-2">
                            {{-- Status badge --}}
                            <span class="inline-flex items-center rounded-full border px-3 py-1 text-xs font-semibold uppercase tracking-wider"
                                  :class="statusClasses(activeProject?.status)">
                                <span x-text="statusLabel(activeProject?.status)"></span>
                            </span>
                            <span class="text-xs text-stone-400" x-text="'Updated ' + (activeProject?.updated_at ?? '')"></span>
                        </div>
                    </div>
                    <button type="button"
                            @click="closePanel()"
                            class="ml-4 shrink-0 rounded-xl border border-warm-300/50 p-2 text-brand-muted transition hover:border-warm-400/50 hover:text-brand-ink">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                {{-- Panel scrollable content --}}
                <div class="flex-1 overflow-y-auto px-6 py-5 space-y-6">

                    {{-- Progress --}}
                    <div class="rounded-2xl border border-warm-300/40 bg-warm-200/60 px-5 py-4">
                        <div class="flex items-center justify-between">
                            <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-brand-muted">Progress</p>
                            <span class="font-display text-xl text-brand-ink" x-text="(activeProject?.progress ?? 0) + '%'"></span>
                        </div>
                        <div class="mt-3 h-2 w-full overflow-hidden rounded-full bg-warm-300">
                            <div class="h-full rounded-full transition-all duration-700"
                                 :class="progressBarColor(activeProject?.status)"
                                 :style="'width:' + (activeProject?.progress ?? 0) + '%'"></div>
                        </div>
                    </div>

                    {{-- People --}}
                    <div class="grid grid-cols-2 gap-3">
                        <div class="rounded-2xl border border-warm-300/40 bg-warm-100 p-4">
                            <p class="text-[10px] font-semibold uppercase tracking-[0.24em] text-brand-muted">Client</p>
                            <div class="mt-2 flex items-center gap-2">
                                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-warm-200 text-[11px] font-bold text-warm-700"
                                     x-text="(activeProject?.client_name ?? '?').charAt(0).toUpperCase()"></div>
                                <div class="min-w-0">
                                    <p class="truncate text-sm font-semibold text-brand-ink" x-text="activeProject?.client_name ?? '—'"></p>
                                    <p class="truncate text-xs text-brand-muted" x-text="activeProject?.client_email ?? ''"></p>
                                </div>
                            </div>
                        </div>
                        <div class="rounded-2xl border border-warm-300/40 bg-warm-100 p-4">
                            <p class="text-[10px] font-semibold uppercase tracking-[0.24em] text-brand-muted">Freelancer</p>
                            <div class="mt-2 flex items-center gap-2" x-show="activeProject?.freelancer_name">
                                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-blue-100 text-[11px] font-bold text-blue-600"
                                     x-text="(activeProject?.freelancer_name ?? '?').charAt(0).toUpperCase()"></div>
                                <div class="min-w-0">
                                    <p class="truncate text-sm font-semibold text-brand-ink" x-text="activeProject?.freelancer_name ?? ''"></p>
                                    <p class="truncate text-xs text-brand-muted" x-text="activeProject?.freelancer_email ?? ''"></p>
                                </div>
                            </div>
                            <p class="mt-2 text-sm text-stone-400" x-show="!activeProject?.freelancer_name">Not assigned yet</p>
                        </div>
                    </div>

                    {{-- Description --}}
                    <div>
                        <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-brand-muted">Description</p>
                        <p class="mt-2 text-sm leading-7 text-brand-muted whitespace-pre-line" x-text="activeProject?.description ?? ''"></p>
                    </div>

                    {{-- Files --}}
                    <div>
                        <div class="flex items-center justify-between">
                            <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-brand-muted">Files</p>
                            <span class="rounded-full border border-warm-300/50 bg-warm-200/50 px-2.5 py-0.5 text-xs font-semibold text-brand-muted"
                                  x-text="(activeProject?.files_count ?? 0) + ' file' + ((activeProject?.files_count ?? 0) === 1 ? '' : 's')"></span>
                        </div>
                        <a :href="activeProject?.show_url + '#files'"
                           class="mt-2 flex items-center gap-2 rounded-xl border border-warm-300/50 px-4 py-2.5 text-xs font-semibold text-brand-muted transition hover:border-warm-400/50 hover:text-brand-ink">
                            <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M18.375 12.739l-7.693 7.693a4.5 4.5 0 0 1-6.364-6.364l10.94-10.94A3 3 0 1 1 19.5 7.372L8.552 18.32m.009-.01-.01.01m5.699-9.941-7.81 7.81a1.5 1.5 0 0 0 2.112 2.13" />
                            </svg>
                            View all files on project page
                        </a>
                    </div>

                    {{-- ── Assign Freelancer form ── --}}
                    <div class="rounded-2xl border border-warm-300/40 bg-warm-100 p-5">
                        <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-brand-primary">Assign Freelancer</p>
                        <form method="POST" :action="'{{ url('admin/projects') }}/' + activeProject?.id + '/assign'" class="mt-4 space-y-3">
                            @csrf @method('PATCH')
                            <select name="freelancer_id"
                                    class="w-full rounded-xl border-warm-300/50 bg-warm-100 px-3 py-2.5 text-sm text-brand-ink focus:border-brand-primary focus:ring-brand-primary">
                                <option value="">Select a freelancer…</option>
                                @foreach ($freelancers as $fl)
                                    <option value="{{ $fl->id }}">{{ $fl->name }} ({{ $fl->email }})</option>
                                @endforeach
                            </select>
                            <button type="submit" class="w-full rounded-xl bg-navy-800 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-800">
                                Assign
                            </button>
                        </form>
                    </div>

                    {{-- ── Change Status form ── --}}
                    <div class="rounded-2xl border border-warm-300/40 bg-warm-100 p-5">
                        <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-brand-primary">Change Status</p>
                        <form method="POST" :action="'{{ url('admin/projects') }}/' + activeProject?.id + '/status'" class="mt-4 space-y-3">
                            @csrf @method('PATCH')
                            <select name="status"
                                    class="w-full rounded-xl border-warm-300/50 bg-warm-100 px-3 py-2.5 text-sm text-brand-ink focus:border-brand-primary focus:ring-brand-primary">
                                @foreach ($statusList as $s)
                                    <option value="{{ $s }}">{{ str_replace('_', ' ', ucfirst($s)) }}</option>
                                @endforeach
                            </select>
                            <button type="submit" class="w-full rounded-xl bg-navy-800 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-800">
                                Update Status
                            </button>
                        </form>
                    </div>

                </div>

                {{-- Panel footer --}}
                <div class="shrink-0 border-t border-warm-300/40 bg-warm-200/60 px-6 py-4">
                    <div class="flex items-center gap-3">
                        <a :href="activeProject?.show_url"
                           class="flex flex-1 items-center justify-center gap-2 rounded-xl border border-warm-300/50 bg-warm-100 px-4 py-2.5 text-sm font-semibold text-brand-ink transition hover:border-warm-400/50">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
                            </svg>
                            Full Details
                        </a>
                        <a :href="activeProject?.chat_url"
                           class="flex flex-1 items-center justify-center gap-2 rounded-xl bg-navy-800 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-800">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 0 1 .865-.501 48.172 48.172 0 0 0 3.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0 0 12 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018Z" />
                            </svg>
                            Open Chat
                        </a>
                        <a :href="activeProject?.invoice_url"
                           class="flex items-center justify-center rounded-xl border border-warm-300/50 bg-warm-100 p-2.5 text-brand-muted transition hover:border-accent/30 hover:bg-accent-light hover:text-brand-primary"
                           title="Create Invoice">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>

    </div>{{-- end x-data --}}

    @push('scripts')
    <script>
    function projectsPage() {
        return {
            search:      '',
            panelOpen:   false,
            activeProject: null,
            selectedIds: [],
            allSelected: false,
            noResults:   false,

            init() {
                this.$watch('search', () => {
                    this.$nextTick(() => {
                        const rows = this.$root.querySelectorAll('tbody tr[data-search-title]');
                        const anyVisible = Array.from(rows).some(r => r.style.display !== 'none');
                        this.noResults = !anyVisible;
                    });
                });
            },

            isVisible(el) {
                if (!this.search) return true;
                const q = this.search.toLowerCase();
                const t = el.dataset.searchTitle ?? '';
                const c = el.dataset.searchClient ?? '';
                const f = el.dataset.searchFreelancer ?? '';
                return t.includes(q) || c.includes(q) || f.includes(q);
            },

            openPanel(project) {
                this.activeProject = project;
                this.panelOpen     = true;
                document.body.style.overflow = 'hidden';
            },

            closePanel() {
                this.panelOpen = false;
                document.body.style.overflow = '';
            },

            toggleAll(ids) {
                this.allSelected = !this.allSelected;
                this.selectedIds = this.allSelected ? ids : [];
            },

            toggleId(id) {
                if (this.selectedIds.includes(id)) {
                    this.selectedIds = this.selectedIds.filter(i => i !== id);
                } else {
                    this.selectedIds.push(id);
                }
            },

            statusClasses(status) {
                const map = {
                    pending:     'border-amber-200 bg-amber-50 text-amber-700',
                    assigned:    'border-blue-200 bg-blue-50 text-blue-700',
                    in_progress: 'border-accent/30 bg-accent-light text-accent-hover',
                    completed:   'border-emerald-200 bg-emerald-50 text-emerald-700',
                };
                return map[status] ?? map.pending;
            },

            statusLabel(status) {
                const map = {
                    pending:     'Pending',
                    assigned:    'Assigned',
                    in_progress: 'In Progress',
                    completed:   'Completed',
                };
                return map[status] ?? (status ?? '');
            },

            progressBarColor(status) {
                const map = {
                    assigned:    'bg-blue-400',
                    in_progress: 'bg-accent',
                    completed:   'bg-emerald-500',
                };
                return map[status] ?? 'bg-stone-300';
            },
        };
    }
    </script>
    @endpush

</x-app-layout>
