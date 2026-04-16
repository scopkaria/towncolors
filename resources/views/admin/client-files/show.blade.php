<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
            <div class="space-y-2">
                <span class="inline-flex w-fit rounded-full border border-accent/30 bg-accent-light px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.32em] text-brand-primary">
                    Admin · Client Files
                </span>

                <nav class="flex flex-wrap items-center gap-1.5 text-sm">
                    <a href="{{ route('admin.client-files.index') }}" class="text-brand-muted hover:text-brand-ink">All Client Files</a>
                    <svg class="h-3.5 w-3.5 text-brand-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                    </svg>
                    <a href="{{ route('admin.clients.files', $client) }}"
                       class="font-semibold {{ is_null($currentFolder) ? 'text-brand-ink' : 'text-brand-muted hover:text-brand-ink' }}">
                        {{ $client->name }}
                    </a>
                    @foreach ($breadcrumbs as $crumb)
                        <svg class="h-3.5 w-3.5 text-brand-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                        </svg>
                        @if ($crumb->id === $currentFolder?->id)
                            <span class="font-semibold text-brand-ink">{{ $crumb->name }}</span>
                        @else
                            <a href="{{ route('admin.clients.files.folder', [$client, $crumb]) }}" class="text-brand-muted hover:text-brand-ink">{{ $crumb->name }}</a>
                        @endif
                    @endforeach
                </nav>

                <div>
                    <h1 class="font-display text-2xl text-brand-ink">{{ $client->name }}'s Files</h1>
                    <p class="text-xs text-brand-muted">{{ $client->email }} &middot; {{ $files->count() + $folders->count() }} item(s)</p>
                </div>
            </div>
        </div>
    </x-slot>

    @if (session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
             class="flex items-center gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-medium text-emerald-700">
            <svg class="h-4 w-4 shrink-0 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
            </svg>
            {{ session('success') }}
        </div>
    @endif

    {{-- Preview Modal --}}
    <div x-data="{ preview: { open: false, url: '', type: '', name: '' } }"
         @open-preview.window="preview = { open: true, url: $event.detail.url, type: $event.detail.type, name: $event.detail.name }"
         @keydown.escape.window="preview.open = false; preview.url = ''">
        <div x-cloak x-show="preview.open"
             class="fixed inset-0 z-50 flex items-center justify-center bg-navy-900/80 backdrop-blur-sm px-4 py-8">
            <div class="relative flex w-full max-w-5xl flex-col overflow-hidden rounded-3xl border border-white/20 bg-slate-900 shadow-panel" style="max-height:90vh">
                <div class="flex shrink-0 items-center justify-between px-6 py-4 border-b border-white/10">
                    <p class="truncate text-sm font-semibold text-white" x-text="preview.name"></p>
                    <button @click="preview.open = false; preview.url = ''" class="shrink-0 rounded-xl border border-white/20 p-1.5 text-white/70 hover:text-white">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 20 20" stroke="currentColor" stroke-width="1.5"><path d="M6 6l8 8M14 6l-8 8" stroke-linecap="round" /></svg>
                    </button>
                </div>
                <div class="flex flex-1 items-center justify-center overflow-auto bg-navy-800 p-4">
                    <template x-if="preview.type.startsWith('image/')">
                        <img :src="preview.url" :alt="preview.name" class="max-h-[78vh] max-w-full rounded-xl object-contain" />
                    </template>
                    <template x-if="preview.type === 'application/pdf'">
                        <iframe :src="preview.url" class="h-[78vh] w-full rounded-xl border-0" title="PDF Preview"></iframe>
                    </template>
                    <template x-if="preview.type.startsWith('video/')">
                        <video :src="preview.url" controls class="max-h-[78vh] max-w-full rounded-xl"></video>
                    </template>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Folders ──────────────────────────────────────────────────────────── --}}
    @if ($folders->isNotEmpty())
        <section>
            <p class="mb-3 text-[11px] font-semibold uppercase tracking-[0.28em] text-brand-muted">Folders</p>
            <div class="grid gap-3 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-4">
                @foreach ($folders as $folder)
                    <a href="{{ route('admin.clients.files.folder', [$client, $folder]) }}"
                       class="flex items-center gap-3 rounded-2xl border border-white/70 bg-white/90 p-4 shadow-card transition hover:border-amber-200 hover:shadow-panel">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-amber-50 text-amber-500">
                            <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M19.5 21a3 3 0 0 0 3-3v-4.5a3 3 0 0 0-3-3h-15a3 3 0 0 0-3 3V18a3 3 0 0 0 3 3h15ZM1.5 10.146V6a3 3 0 0 1 3-3h5.379a2.25 2.25 0 0 1 1.59.659l2.122 2.121c.14.141.331.22.53.22H19.5a3 3 0 0 1 3 3v1.146A4.483 4.483 0 0 0 19.5 12h-15a4.483 4.483 0 0 0-3 1.146Z" />
                            </svg>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="truncate font-semibold text-brand-ink">{{ $folder->name }}</p>
                            <p class="text-xs text-brand-muted">{{ $folder->files_count }} file(s)</p>
                        </div>
                        <svg class="h-4 w-4 shrink-0 text-brand-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                        </svg>
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    {{-- ── Files ────────────────────────────────────────────────────────────── --}}
    @if ($files->isNotEmpty())
        <section>
            <p class="mb-3 text-[11px] font-semibold uppercase tracking-[0.28em] text-brand-muted">Files</p>
            <div class="rounded-3xl border border-white/70 bg-white/90 shadow-panel overflow-hidden">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-warm-300/40">
                            <th class="px-5 py-4 text-left text-[11px] font-semibold uppercase tracking-[0.28em] text-brand-muted">File</th>
                            <th class="hidden px-5 py-4 text-left text-[11px] font-semibold uppercase tracking-[0.28em] text-brand-muted sm:table-cell">Size</th>
                            <th class="hidden px-5 py-4 text-left text-[11px] font-semibold uppercase tracking-[0.28em] text-brand-muted md:table-cell">Uploaded</th>
                            <th class="px-5 py-4 text-right text-[11px] font-semibold uppercase tracking-[0.28em] text-brand-muted">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-warm-200/50">
                        @foreach ($files as $file)
                            @php
                                $iconData = match($file->iconName()) {
                                    'image'   => ['bg' => 'bg-blue-50 text-blue-500',    'path' => 'm2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909M13.5 12h.008v.008H13.5V12Zm-3 8.25h13.5a2.25 2.25 0 0 0 2.25-2.25V6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v13.5c0 1.243 1.007 2.25 2.25 2.25Z'],
                                    'video'   => ['bg' => 'bg-purple-50 text-purple-500', 'path' => 'm15.75 10.5 4.72-4.72a.75.75 0 0 1 1.28.53v11.38a.75.75 0 0 1-1.28.53l-4.72-4.72M4.5 18.75h9a2.25 2.25 0 0 0 2.25-2.25v-9a2.25 2.25 0 0 0-2.25-2.25h-9A2.25 2.25 0 0 0 2.25 7.5v9a2.25 2.25 0 0 0 2.25 2.25Z'],
                                    'pdf'     => ['bg' => 'bg-red-50 text-red-500',       'path' => 'M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z'],
                                    'archive' => ['bg' => 'bg-amber-50 text-amber-500',   'path' => 'M20.25 7.5l-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z'],
                                    default   => ['bg' => 'bg-warm-200 text-brand-muted','path' => 'M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z'],
                                };
                            @endphp
                            <tr class="hover:bg-warm-200/50 transition-colors">
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl {{ $iconData['bg'] }}">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $iconData['path'] }}" />
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="font-medium text-brand-ink">{{ $file->original_name }}</p>
                                            @if ($file->description)
                                                <p class="text-xs text-brand-muted">{{ $file->description }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="hidden px-5 py-4 text-brand-muted sm:table-cell">{{ $file->formattedSize() }}</td>
                                <td class="hidden px-5 py-4 text-brand-muted md:table-cell">{{ $file->created_at->diffForHumans() }}</td>
                                <td class="px-5 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        @if ($file->isPreviewable())
                                            <button type="button"
                                                    @click="$dispatch('open-preview', { url: '{{ route('admin.client-files.preview', $file) }}', type: '{{ $file->mime_type }}', name: '{{ addslashes($file->original_name) }}' })"
                                                    class="rounded-xl border border-warm-300/50 bg-warm-100 px-3 py-1.5 text-xs font-semibold text-brand-ink transition hover:border-blue-300 hover:text-blue-600">
                                                Preview
                                            </button>
                                        @endif
                                        <a href="{{ route('admin.client-files.download', $file) }}"
                                           class="rounded-xl border border-warm-300/50 bg-warm-100 px-3 py-1.5 text-xs font-semibold text-brand-ink transition hover:border-brand-primary/40">
                                            Download
                                        </a>
                                        <form method="POST" action="{{ route('admin.client-files.destroy', $file) }}"
                                              onsubmit="return confirm('Delete this file permanently?')">
                                            @csrf @method('DELETE')
                                            <button type="submit"
                                                    class="rounded-xl border border-red-100 bg-red-50 px-3 py-1.5 text-xs font-semibold text-red-600 transition hover:bg-red-100">
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
        </section>
    @endif

    {{-- ── Empty state ──────────────────────────────────────────────────────── --}}
    @if ($files->isEmpty() && $folders->isEmpty())
        <div class="rounded-3xl border border-dashed border-warm-400/50 bg-warm-200/50 p-16 text-center">
            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-warm-200">
                <svg class="h-7 w-7 text-brand-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12.75V12A2.25 2.25 0 0 1 4.5 9.75h15A2.25 2.25 0 0 1 21.75 12v.75m-8.69-6.44-2.12-2.12a1.5 1.5 0 0 0-1.061-.44H4.5A2.25 2.25 0 0 0 2.25 6v12a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9a2.25 2.25 0 0 0-2.25-2.25h-5.379a1.5 1.5 0 0 1-1.06-.44Z" />
                </svg>
            </div>
            <p class="mt-4 text-sm font-semibold text-brand-ink">
                @if ($currentFolder) This folder is empty @else No files uploaded yet @endif
            </p>
            <p class="mt-1 text-xs text-brand-muted">{{ $client->name }} has not uploaded any files here.</p>
        </div>
    @endif

</x-app-layout>