<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
            <div class="space-y-2">
                <span class="inline-flex w-fit rounded-full border border-accent/30 bg-accent-light px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.32em] text-brand-primary">
                    Private Files
                </span>

                {{-- Breadcrumb --}}
                <nav class="flex flex-wrap items-center gap-1.5 text-sm">
                    <a href="{{ route('client.files.index') }}"
                       class="font-semibold {{ is_null($currentFolder) ? 'text-brand-ink' : 'text-brand-muted hover:text-brand-ink' }}">
                        My Files
                    </a>
                    @foreach ($breadcrumbs as $crumb)
                        <svg class="h-3.5 w-3.5 shrink-0 text-brand-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                        </svg>
                        @if ($crumb->id === $currentFolder?->id)
                            <span class="font-semibold text-brand-ink">{{ $crumb->name }}</span>
                        @else
                            <a href="{{ route('client.files.folder', $crumb) }}" class="text-brand-muted hover:text-brand-ink">{{ $crumb->name }}</a>
                        @endif
                    @endforeach
                </nav>

                @if ($currentFolder)
                    <p class="text-xs text-brand-muted">{{ $files->count() + $folders->count() }} item(s) in this folder</p>
                @else
                    <p class="text-xs text-brand-muted">Your private storage — visible only to you, admins, and assigned freelancers.</p>
                @endif
            </div>

            {{-- Toolbar --}}
            <div class="flex shrink-0 items-center gap-2"
                 x-data="{
                     newFolder: false,
                     upload: false,
                 }"
                 @keydown.escape.window="newFolder = false; upload = false">

                <button @click="newFolder = true"
                        class="inline-flex items-center gap-2 rounded-2xl border border-warm-300/50 bg-warm-100 px-4 py-2.5 text-sm font-semibold text-brand-ink shadow-sm transition hover:border-brand-primary/30">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 10.5v6m3-3H9m4.06-7.19-2.12-2.12a1.5 1.5 0 0 0-1.061-.44H4.5A2.25 2.25 0 0 0 2.25 6v12a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9a2.25 2.25 0 0 0-2.25-2.25h-5.379a1.5 1.5 0 0 1-1.06-.44Z" />
                    </svg>
                    New Folder
                </button>

                <button @click="upload = true"
                        class="inline-flex items-center gap-2 rounded-2xl bg-navy-800 px-4 py-2.5 text-sm font-semibold text-white shadow-card transition hover:bg-slate-800">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0 4.5 4.5M12 3v13.5" />
                    </svg>
                    Upload File
                </button>

                {{-- New Folder Modal --}}
                <div x-cloak x-show="newFolder"
                     class="fixed inset-0 z-50 flex items-center justify-center bg-navy-900/50 backdrop-blur-sm px-4">
                    <div class="w-full max-w-sm rounded-3xl border border-white/70 bg-warm-100 p-7 shadow-panel">
                        <div class="flex items-center justify-between mb-5">
                            <h2 class="font-display text-xl text-brand-ink">New Folder</h2>
                            <button @click="newFolder = false" class="rounded-xl border border-warm-300/50 p-1.5 text-brand-muted hover:text-brand-ink">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 20 20" stroke="currentColor" stroke-width="1.5"><path d="M6 6l8 8M14 6l-8 8" stroke-linecap="round" /></svg>
                            </button>
                        </div>
                        <form method="POST" action="{{ route('client.folders.store') }}" class="space-y-4">
                            @csrf
                            <input type="hidden" name="parent_id" value="{{ $currentFolder?->id }}">
                            <div>
                                <label class="block text-sm font-semibold text-brand-ink mb-1.5">Folder name</label>
                                <input type="text" name="name" required autofocus maxlength="100"
                                       class="w-full rounded-xl border border-warm-300/50 px-4 py-2.5 text-sm text-brand-ink focus:border-brand-primary focus:outline-none"
                                       placeholder="e.g. Project Assets" />
                            </div>
                            <div class="flex gap-3 pt-1">
                                <button type="submit" class="flex-1 rounded-2xl bg-navy-800 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-800">Create</button>
                                <button type="button" @click="newFolder = false" class="rounded-2xl border border-warm-300/50 px-5 py-2.5 text-sm font-semibold text-brand-muted transition hover:text-brand-ink">Cancel</button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Upload File Modal --}}
                <div x-cloak x-show="upload"
                     class="fixed inset-0 z-50 flex items-center justify-center bg-navy-900/50 backdrop-blur-sm px-4">
                    <div class="w-full max-w-md rounded-3xl border border-white/70 bg-warm-100 p-7 shadow-panel">
                        <div class="flex items-center justify-between mb-5">
                            <h2 class="font-display text-xl text-brand-ink">Upload a File</h2>
                            <button @click="upload = false" class="rounded-xl border border-warm-300/50 p-1.5 text-brand-muted hover:text-brand-ink">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 20 20" stroke="currentColor" stroke-width="1.5"><path d="M6 6l8 8M14 6l-8 8" stroke-linecap="round" /></svg>
                            </button>
                        </div>
                        <form method="POST" action="{{ route('client.files.store') }}" enctype="multipart/form-data" class="space-y-4">
                            @csrf
                            <input type="hidden" name="folder_id" value="{{ $currentFolder?->id }}">
                            <div>
                                <label class="block text-sm font-semibold text-brand-ink mb-1.5">File</label>
                                <input type="file" name="file" required
                                       accept=".jpg,.jpeg,.png,.gif,.webp,.mp4,.pdf,.doc,.docx,.txt,.zip,.rar"
                                       class="w-full rounded-xl border border-warm-300/50 px-4 py-2.5 text-sm text-brand-ink file:mr-4 file:rounded-lg file:border-0 file:bg-warm-200 file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-brand-ink" />
                                <p class="mt-1 text-xs text-brand-muted">Images, Videos, PDFs, Documents, ZIP — max 100 MB</p>
                                @error('file') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-brand-ink mb-1.5">Description <span class="font-normal text-brand-muted">(optional)</span></label>
                                <input type="text" name="description" maxlength="200"
                                       class="w-full rounded-xl border border-warm-300/50 px-4 py-2.5 text-sm text-brand-ink focus:border-brand-primary focus:outline-none"
                                       placeholder="What is this file?" />
                            </div>
                            <div class="flex gap-3 pt-1">
                                <button type="submit" class="flex-1 rounded-2xl bg-navy-800 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-800">Upload</button>
                                <button type="button" @click="upload = false" class="rounded-2xl border border-warm-300/50 px-5 py-2.5 text-sm font-semibold text-brand-muted transition hover:text-brand-ink">Cancel</button>
                            </div>
                        </form>
                    </div>
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

    @error('file')
        <div class="rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-sm font-medium text-red-700">{{ $message }}</div>
    @enderror

    {{-- Rename Folder Modal (page-level, dispatched by event) --}}
    <div x-data="{
             rename: { open: false, id: null, name: '' }
         }"
         @open-rename.window="rename = { open: true, id: $event.detail.id, name: $event.detail.name }; $nextTick(() => $refs.renameInput && $refs.renameInput.focus())"
         @keydown.escape.window="rename.open = false">
        <div x-cloak x-show="rename.open"
             class="fixed inset-0 z-50 flex items-center justify-center bg-navy-900/50 backdrop-blur-sm px-4">
            <div class="w-full max-w-sm rounded-3xl border border-white/70 bg-warm-100 p-7 shadow-panel">
                <div class="flex items-center justify-between mb-5">
                    <h2 class="font-display text-xl text-brand-ink">Rename Folder</h2>
                    <button @click="rename.open = false" class="rounded-xl border border-warm-300/50 p-1.5 text-brand-muted hover:text-brand-ink">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 20 20" stroke="currentColor" stroke-width="1.5"><path d="M6 6l8 8M14 6l-8 8" stroke-linecap="round" /></svg>
                    </button>
                </div>
                <form method="POST" :action="`{{ url('client/folders') }}/${rename.id}`" class="space-y-4">
                    @csrf
                    @method('PATCH')
                    <div>
                        <label class="block text-sm font-semibold text-brand-ink mb-1.5">New name</label>
                        <input type="text" name="name" x-model="rename.name" x-ref="renameInput"
                               required maxlength="100"
                               class="w-full rounded-xl border border-warm-300/50 px-4 py-2.5 text-sm text-brand-ink focus:border-brand-primary focus:outline-none" />
                    </div>
                    <div class="flex gap-3 pt-1">
                        <button type="submit" class="flex-1 rounded-2xl bg-navy-800 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-800">Save</button>
                        <button type="button" @click="rename.open = false" class="rounded-2xl border border-warm-300/50 px-5 py-2.5 text-sm font-semibold text-brand-muted transition hover:text-brand-ink">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Preview Modal (page-level) --}}
    <div x-data="{
             preview: { open: false, url: '', type: '', name: '' }
         }"
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

    {{-- ── Folders ─────────────────────────────────────────────────────────── --}}
    @if ($folders->isNotEmpty())
        <section>
            <p class="mb-3 text-[11px] font-semibold uppercase tracking-[0.28em] text-brand-muted">Folders</p>
            <div class="grid gap-3 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-4">
                @foreach ($folders as $folder)
                    <div class="group relative rounded-2xl border border-white/70 bg-white/90 p-4 shadow-card transition hover:border-amber-200 hover:shadow-panel">
                        <a href="{{ route('client.files.folder', $folder) }}" class="flex items-center gap-3">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-amber-50 text-amber-500">
                                <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M19.5 21a3 3 0 0 0 3-3v-4.5a3 3 0 0 0-3-3h-15a3 3 0 0 0-3 3V18a3 3 0 0 0 3 3h15ZM1.5 10.146V6a3 3 0 0 1 3-3h5.379a2.25 2.25 0 0 1 1.59.659l2.122 2.121c.14.141.331.22.53.22H19.5a3 3 0 0 1 3 3v1.146A4.483 4.483 0 0 0 19.5 12h-15a4.483 4.483 0 0 0-3 1.146Z" />
                                </svg>
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="truncate font-semibold text-brand-ink">{{ $folder->name }}</p>
                                <p class="text-xs text-brand-muted">{{ $folder->files_count }} file(s)</p>
                            </div>
                        </a>

                        {{-- Hover actions --}}
                        <div class="absolute right-3 top-3 flex items-center gap-1 opacity-0 transition-opacity group-hover:opacity-100">
                            <button type="button"
                                    @click.prevent="$dispatch('open-rename', { id: {{ $folder->id }}, name: '{{ addslashes($folder->name) }}' })"
                                    class="rounded-lg border border-warm-300/50 bg-warm-100 p-1.5 text-brand-muted transition hover:text-brand-ink"
                                    title="Rename folder">
                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Z" />
                                </svg>
                            </button>
                            <form method="POST" action="{{ route('client.folders.destroy', $folder) }}"
                                  onsubmit="return confirm('Delete folder &quot;{{ addslashes($folder->name) }}&quot; and ALL its files?\nThis cannot be undone.')">
                                @csrf @method('DELETE')
                                <button type="submit"
                                        class="rounded-lg border border-red-100 bg-red-50 p-1.5 text-red-500 transition hover:bg-red-100"
                                        title="Delete folder">
                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    {{-- ── Files ───────────────────────────────────────────────────────────── --}}
    @if ($files->isNotEmpty())
        <section>
            <p class="mb-3 text-[11px] font-semibold uppercase tracking-[0.28em] text-brand-muted">Files</p>
            <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
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
                    <article class="flex flex-col rounded-3xl border border-white/70 bg-white/90 p-5 shadow-card transition hover:shadow-panel">
                        <div class="flex items-start gap-4">
                            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl {{ $iconData['bg'] }}">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $iconData['path'] }}" />
                                </svg>
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="truncate font-semibold text-brand-ink" title="{{ $file->original_name }}">{{ $file->original_name }}</p>
                                @if ($file->description)
                                    <p class="mt-0.5 text-xs text-brand-muted line-clamp-1">{{ $file->description }}</p>
                                @endif
                                <p class="mt-1 text-xs text-brand-muted">{{ $file->formattedSize() }} &middot; {{ $file->created_at->diffForHumans() }}</p>
                            </div>
                        </div>

                        <div class="mt-4 flex items-center gap-2">
                            @if ($file->isPreviewable())
                                <button type="button"
                                        @click="$dispatch('open-preview', { url: '{{ route('client.files.preview', $file) }}', type: '{{ $file->mime_type }}', name: '{{ addslashes($file->original_name) }}' })"
                                        class="flex-1 rounded-xl border border-warm-300/50 py-2 text-center text-xs font-semibold text-brand-ink transition hover:border-blue-300 hover:text-blue-600">
                                    Preview
                                </button>
                            @endif
                            <a href="{{ route('client.files.download', $file) }}"
                               class="flex-1 rounded-xl border border-warm-300/50 py-2 text-center text-xs font-semibold text-brand-ink transition hover:border-brand-primary/30">
                                Download
                            </a>
                            <form method="POST" action="{{ route('client.files.destroy', $file) }}"
                                  onsubmit="return confirm('Delete this file? This cannot be undone.')">
                                @csrf @method('DELETE')
                                <button type="submit"
                                        class="rounded-xl border border-red-100 bg-red-50 px-3 py-2 text-xs font-semibold text-red-600 transition hover:bg-red-100"
                                        title="Delete">
                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </article>
                @endforeach
            </div>
        </section>
    @endif

    {{-- ── Empty state ──────────────────────────────────────────────────────── --}}
    @if ($files->isEmpty() && $folders->isEmpty())
        <div class="rounded-3xl border border-dashed border-warm-400/50 bg-warm-200/50 p-16 text-center">
            <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-warm-200">
                <svg class="h-8 w-8 text-brand-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12.75V12A2.25 2.25 0 0 1 4.5 9.75h15A2.25 2.25 0 0 1 21.75 12v.75m-8.69-6.44-2.12-2.12a1.5 1.5 0 0 0-1.061-.44H4.5A2.25 2.25 0 0 0 2.25 6v12a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9a2.25 2.25 0 0 0-2.25-2.25h-5.379a1.5 1.5 0 0 1-1.06-.44Z" />
                </svg>
            </div>
            <p class="mt-4 text-sm font-semibold text-brand-ink">
                @if ($currentFolder) This folder is empty @else No files or folders yet @endif
            </p>
            <p class="mt-1 text-xs text-brand-muted">Use the buttons above to create a folder or upload a file.</p>
        </div>
    @endif

</x-app-layout>