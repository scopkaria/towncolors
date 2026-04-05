<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="space-y-3">
                <span class="inline-flex w-fit rounded-full border border-orange-200 bg-orange-50 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.32em] text-brand-primary">
                    CMS
                </span>
                <div class="space-y-2">
                    <h1 class="font-display text-3xl text-brand-ink sm:text-4xl">Pages</h1>
                    <p class="max-w-2xl text-sm leading-7 text-brand-muted">Create and manage public content pages.</p>
                </div>
            </div>
            <a href="{{ route('admin.pages.create') }}" class="btn-primary shrink-0">
                <svg class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M12 4v16m8-8H4"/></svg>
                New Page
            </a>
        </div>
    </x-slot>

    @if (session('success'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-medium text-emerald-700">
            {{ session('success') }}
        </div>
    @endif

    @if ($pages->isEmpty())
        <div class="rounded-3xl border border-white/70 bg-white/90 p-12 text-center shadow-panel">
            <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-stone-100">
                <svg class="h-8 w-8 text-brand-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"/>
                </svg>
            </div>
            <h3 class="mt-4 font-display text-xl text-brand-ink">No pages yet</h3>
            <p class="mt-2 text-sm text-brand-muted">Create your first page to publish content on your site.</p>
            <a href="{{ route('admin.pages.create') }}" class="btn-primary mt-6 inline-flex">New Page</a>
        </div>
    @else
        <div class="rounded-3xl border border-white/70 bg-white/90 shadow-panel overflow-hidden">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-stone-100">
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-brand-muted">Title</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-brand-muted">URL</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-brand-muted">Status</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-brand-muted">Updated</th>
                        <th class="px-6 py-4"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @foreach ($pages as $page)
                        <tr class="group transition hover:bg-stone-50/60">
                            <td class="px-6 py-4">
                                <p class="font-semibold text-brand-ink">{{ $page->title }}</p>
                                @if ($page->meta_description)
                                    <p class="mt-0.5 line-clamp-1 text-xs text-brand-muted">{{ $page->meta_description }}</p>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <a href="{{ route('pages.show', $page) }}" target="_blank"
                                   class="inline-flex items-center gap-1 rounded-lg bg-stone-100 px-2.5 py-1 font-mono text-xs text-brand-muted transition hover:bg-orange-50 hover:text-brand-primary">
                                    /page/{{ $page->slug }}
                                    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/></svg>
                                </a>
                            </td>
                            <td class="px-6 py-4">
                                @if ($page->is_published)
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-600">
                                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                        Published
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-stone-100 px-2.5 py-1 text-xs font-semibold text-stone-500">
                                        <span class="h-1.5 w-1.5 rounded-full bg-stone-400"></span>
                                        Draft
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-xs text-brand-muted">
                                {{ $page->updated_at->diffForHumans() }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.pages.sections.index', $page) }}"
                                       class="rounded-xl border border-orange-200 bg-orange-50 px-3 py-1.5 text-xs font-semibold text-brand-primary transition hover:bg-orange-100">
                                        Sections
                                    </a>
                                    <a href="{{ route('admin.pages.edit', $page) }}"
                                       class="rounded-xl border border-stone-200 bg-white px-3 py-1.5 text-xs font-semibold text-brand-muted transition hover:border-brand-primary hover:text-brand-primary">
                                        Edit
                                    </a>
                                    <form method="POST" action="{{ route('admin.pages.destroy', $page) }}"
                                          onsubmit="return confirm('Delete page \'{{ addslashes($page->title) }}\'? This cannot be undone.')">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                                class="rounded-xl border border-stone-200 bg-white px-3 py-1.5 text-xs font-semibold text-brand-muted transition hover:border-red-200 hover:text-red-500">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</x-app-layout>
