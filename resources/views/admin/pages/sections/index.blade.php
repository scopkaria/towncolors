<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.pages.index') }}"
                   class="flex h-9 w-9 items-center justify-center rounded-xl border border-stone-200 bg-white text-brand-muted shadow-sm transition hover:border-brand-primary hover:text-brand-primary">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/>
                    </svg>
                </a>
                <div class="space-y-1">
                    <span class="inline-flex rounded-full border border-orange-200 bg-orange-50 px-3 py-0.5 text-[11px] font-semibold uppercase tracking-[0.32em] text-brand-primary">
                        Sections Builder
                    </span>
                    <h1 class="font-display text-2xl text-brand-ink sm:text-3xl">
                        {{ $page->title }}
                    </h1>
                </div>
            </div>
            <a href="{{ route('admin.pages.sections.create', $page) }}" class="btn-primary shrink-0">
                <svg class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" d="M12 4v16m8-8H4"/>
                </svg>
                Add Section
            </a>
        </div>
    </x-slot>

    {{-- Flash --}}
    @if (session('success'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-medium text-emerald-700">
            {{ session('success') }}
        </div>
    @endif

    @if ($sections->isEmpty())
        <div class="rounded-3xl border border-white/70 bg-white/90 p-12 text-center shadow-panel">
            <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-stone-100">
                <svg class="h-8 w-8 text-brand-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25Z"/>
                </svg>
            </div>
            <h3 class="mt-4 font-display text-xl text-brand-ink">No sections yet</h3>
            <p class="mt-2 text-sm text-brand-muted">Add your first section to start building the page.</p>
            <a href="{{ route('admin.pages.sections.create', $page) }}" class="btn-primary mt-6 inline-flex">
                Add Section
            </a>
        </div>
    @else

        {{-- Tip --}}
        <p class="text-xs text-brand-muted">
            Drag rows to reorder sections. Changes save automatically.
        </p>

        {{-- Sections list --}}
        <div id="sections-list" class="space-y-3">
            @foreach ($sections as $section)
                <div class="section-row flex items-center gap-4 rounded-2xl border border-white/70 bg-white/90 px-5 py-4 shadow-sm transition"
                     data-id="{{ $section->id }}">

                    {{-- Drag handle --}}
                    <div class="drag-handle cursor-grab select-none text-brand-muted/40 hover:text-brand-muted active:cursor-grabbing">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" d="M3.75 5h16.5M3.75 12h16.5M3.75 19h16.5"/>
                        </svg>
                    </div>

                    {{-- Order badge --}}
                    <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-stone-100 text-xs font-bold text-brand-muted">
                        {{ $loop->iteration }}
                    </span>

                    {{-- Type icon --}}
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl
                                {{ $section->is_active ? 'bg-orange-50 text-brand-primary' : 'bg-stone-100 text-brand-muted' }}">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ \App\Models\PageSection::TYPE_ICONS[$section->type] ?? '' }}"/>
                        </svg>
                    </div>

                    {{-- Info --}}
                    <div class="flex-1 min-w-0">
                        <p class="truncate font-semibold text-brand-ink text-sm">{{ $section->label }}</p>
                        <p class="text-xs text-brand-muted">{{ $section->typeLabel() }}</p>
                    </div>

                    {{-- Active badge --}}
                    @if ($section->is_active)
                        <span class="hidden sm:inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-600">
                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>Visible
                        </span>
                    @else
                        <span class="hidden sm:inline-flex items-center gap-1.5 rounded-full bg-stone-100 px-2.5 py-1 text-xs font-semibold text-stone-500">
                            <span class="h-1.5 w-1.5 rounded-full bg-stone-400"></span>Hidden
                        </span>
                    @endif

                    {{-- Actions --}}
                    <div class="flex items-center gap-2 shrink-0">
                        <a href="{{ route('admin.pages.sections.edit', [$page, $section]) }}"
                           class="rounded-xl border border-stone-200 bg-white px-3 py-1.5 text-xs font-semibold text-brand-muted transition hover:border-brand-primary hover:text-brand-primary">
                            Edit
                        </a>

                        <form method="POST" action="{{ route('admin.pages.sections.toggle', [$page, $section]) }}">
                            @csrf @method('PATCH')
                            <button type="submit"
                                    class="rounded-xl border border-stone-200 bg-white px-3 py-1.5 text-xs font-semibold text-brand-muted transition hover:border-stone-400">
                                {{ $section->is_active ? 'Hide' : 'Show' }}
                            </button>
                        </form>

                        <form method="POST" action="{{ route('admin.pages.sections.destroy', [$page, $section]) }}"
                              onsubmit="return confirm('Delete section \'{{ addslashes($section->label) }}\'?')">
                            @csrf @method('DELETE')
                            <button type="submit"
                                    class="rounded-xl border border-stone-200 bg-white px-3 py-1.5 text-xs font-semibold text-brand-muted transition hover:border-red-200 hover:text-red-500">
                                Delete
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Preview link --}}
        <div class="flex justify-end">
            <a href="{{ route('about') }}" target="_blank"
               class="btn-secondary inline-flex">
                <svg class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/>
                </svg>
                Preview About Page
            </a>
        </div>
    @endif

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.3/Sortable.min.js"></script>
    <script>
    (function () {
        const list = document.getElementById('sections-list');
        if (!list) return;

        Sortable.create(list, {
            handle: '.drag-handle',
            animation: 150,
            ghostClass: 'opacity-40',
            onEnd: function () {
                const ids = Array.from(list.querySelectorAll('.section-row'))
                    .map(el => parseInt(el.dataset.id));

                fetch('{{ route('admin.pages.sections.reorder', $page) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ ids }),
                }).then(r => r.json()).then(data => {
                    if (!data.ok) console.error('Reorder failed');
                });
            },
        });
    })();
    </script>
    @endpush
</x-app-layout>
