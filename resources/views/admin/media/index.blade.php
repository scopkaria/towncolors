@php
$mediaJson = $media->isEmpty() ? [] : $media->map(fn ($item) => [
    'id'        => $item->id,
    'url'       => $item->url(),
    'file_name' => $item->file_name,
    'file_type' => $item->file_type,
    'size'      => $item->humanSize(),
    'ext'       => strtoupper(pathinfo($item->file_name, PATHINFO_EXTENSION)),
])->values()->toArray();
@endphp

<x-app-layout>
<div x-data="mediaLibrary(@js($mediaJson))" class="space-y-6">

    {{-- ── PAGE HEADER ──────────────────────────────────────────── --}}
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Media Library</h1>
            <p class="mt-0.5 text-sm text-gray-500">
                {{ $counts['all'] }} {{ Str::plural('file', $counts['all']) }} in total
            </p>
        </div>
        <button @click="showUpload = !showUpload"
                class="inline-flex items-center gap-2 rounded-lg bg-brand-primary px-4 py-2.5
                       text-sm font-semibold text-white shadow-sm hover:opacity-90 transition">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
            </svg>
            <span x-text="showUpload ? 'Close Upload' : 'Upload Files'"></span>
        </button>
    </div>

    {{-- ── UPLOAD PANEL ──────────────────────────────────────────── --}}
    <div x-show="showUpload" x-cloak>
        <div class="rounded-2xl border border-dashed border-gray-300 bg-white p-6 shadow-sm">
            <form action="{{ route('admin.media.store') }}" method="POST" enctype="multipart/form-data"
                  @submit="submitting = true">
                @csrf

                {{-- Drop zone --}}
                <div class="group flex cursor-pointer flex-col items-center justify-center rounded-xl
                            border-2 border-dashed border-gray-200 bg-gray-50 px-8 py-12 text-center
                            transition hover:border-brand-primary/50 hover:bg-orange-50/40"
                     :class="dragging ? 'border-brand-primary bg-orange-50/40' : ''"
                     @click="$refs.fileInput.click()"
                     @dragover.prevent="dragging = true"
                     @dragleave.prevent="dragging = false"
                     @drop.prevent="dragging = false; handleDrop($event)">
                    <div class="mb-3 flex h-14 w-14 items-center justify-center rounded-2xl
                                bg-brand-primary/10 transition group-hover:bg-brand-primary/20">
                        <svg class="h-7 w-7 text-brand-primary" fill="none" viewBox="0 0 24 24"
                             stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021
                                     18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" />
                        </svg>
                    </div>
                    <p class="text-sm text-gray-700">
                        <span class="font-semibold text-brand-primary">Click to browse</span>
                        or drag &amp; drop
                    </p>
                    <p class="mt-1 text-xs text-gray-400">
                        Images &middot; Videos &middot; PDF &middot; Word &middot; Excel &middot; TXT
                        &nbsp;|&nbsp; Max 100 MB per file
                    </p>
                    <input type="file" name="files[]" multiple x-ref="fileInput"
                           accept="image/*,video/mp4,video/webm,video/ogg,application/pdf,
                                   application/msword,
                                   application/vnd.openxmlformats-officedocument.wordprocessingml.document,
                                   application/vnd.ms-excel,
                                   application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,
                                   text/plain"
                           class="sr-only"
                           @change="handleFiles($event.target.files)">
                </div>

                {{-- Queue preview --}}
                <template x-if="queue.length > 0">
                    <div class="mt-4 space-y-2">
                        <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">
                            Ready to upload (<span x-text="queue.length"></span>)
                        </p>
                        <template x-for="(f, i) in queue" :key="i">
                            <div class="flex items-center gap-3 rounded-lg border border-gray-100
                                        bg-gray-50 px-4 py-2.5 text-sm">
                                <span class="flex-1 truncate text-gray-700" x-text="f.name"></span>
                                <span class="shrink-0 text-xs text-gray-400" x-text="humanSize(f.size)"></span>
                                <button type="button" @click="queue.splice(i,1); rebuildInput()"
                                        class="shrink-0 rounded p-0.5 text-gray-400 transition
                                               hover:bg-red-50 hover:text-red-500">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                         stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>
                        </template>
                    </div>
                </template>

                <div class="mt-5 flex items-center justify-end gap-3">
                    <button type="button" @click="showUpload = false; queue = []"
                            class="rounded-lg px-4 py-2 text-sm text-gray-500 hover:text-gray-800 transition">
                        Cancel
                    </button>
                    <button type="submit"
                            :disabled="submitting || queue.length === 0"
                            class="inline-flex items-center gap-2 rounded-lg bg-brand-primary px-5 py-2.5
                                   text-sm font-semibold text-white shadow-sm transition
                                   hover:opacity-90 disabled:opacity-50 disabled:cursor-not-allowed">
                        <svg x-show="submitting" class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/>
                        </svg>
                        <span x-text="submitting
                            ? 'Uploading\u2026'
                            : 'Upload ' + queue.length + (queue.length === 1 ? ' file' : ' files')">
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ── FLASH ALERTS ──────────────────────────────────────────── --}}
    @if(session('success'))
        <div class="flex items-center gap-3 rounded-xl border border-green-200 bg-green-50
                    px-4 py-3 text-sm text-green-800">
            <svg class="h-5 w-5 shrink-0 text-green-500" fill="none" viewBox="0 0 24 24"
                 stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="flex items-center gap-3 rounded-xl border border-red-200 bg-red-50
                    px-4 py-3 text-sm text-red-800">
            <svg class="h-5 w-5 shrink-0 text-red-500" fill="none" viewBox="0 0 24 24"
                 stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M12 9v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            {{ session('error') }}
        </div>
    @endif

    {{-- ── SELECTION BAR ────────────────────────────────────────── --}}
    <div x-show="selectedIds.length > 0" x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 -translate-y-1"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-1"
         class="flex flex-wrap items-center gap-3 rounded-xl border border-brand-primary/30
                bg-brand-primary/5 px-4 py-3">
        <div class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full
                    bg-brand-primary text-xs font-bold text-white"
             x-text="selectedIds.length"></div>
        <span class="text-sm font-medium text-gray-700">
            <span x-text="selectedIds.length"></span>
            <span x-text="selectedIds.length === 1 ? ' item selected' : ' items selected'"></span>
        </span>
        <div class="flex-1"></div>
        <button @click="copySelectedUrls()"
                class="inline-flex items-center gap-1.5 rounded-lg border border-brand-primary/30
                       bg-white px-3 py-1.5 text-xs font-semibold text-brand-primary
                       transition hover:bg-brand-primary hover:text-white">
            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24"
                 stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2
                         m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
            </svg>
            Copy URLs
        </button>
        <button @click="clearSelection()"
                class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200
                       bg-white px-3 py-1.5 text-xs font-semibold text-gray-500
                       transition hover:border-gray-300 hover:text-gray-800">
            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24"
                 stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
            </svg>
            Clear
        </button>
    </div>

    {{-- ── TOOLBAR ───────────────────────────────────────────────── --}}
    <div class="flex flex-wrap items-center gap-3">

        {{-- Type filter pills --}}
        <div class="flex flex-wrap gap-2">
            @foreach([
                ''         => ['label' => 'All',       'count' => $counts['all']],
                'image'    => ['label' => 'Images',    'count' => $counts['image']],
                'video'    => ['label' => 'Videos',    'count' => $counts['video']],
                'document' => ['label' => 'Documents', 'count' => $counts['document']],
            ] as $val => $meta)
                <a href="{{ route('admin.media.index', array_filter(['type' => $val ?: null, 'search' => request('search') ?: null])) }}"
                   class="inline-flex items-center gap-1.5 rounded-full border px-3.5 py-1.5 text-sm
                          font-medium transition
                          {{ request('type', '') === $val
                              ? 'border-brand-primary bg-brand-primary text-white'
                              : 'border-gray-200 bg-white text-gray-600 hover:border-brand-primary/40 hover:text-brand-primary' }}">
                    {{ $meta['label'] }}
                    <span class="rounded-full px-1.5 py-0.5 text-xs
                                 {{ request('type', '') === $val
                                     ? 'bg-white/25 text-white'
                                     : 'bg-gray-100 text-gray-500' }}">
                        {{ $meta['count'] }}
                    </span>
                </a>
            @endforeach
        </div>

        <div class="flex-1"></div>

        {{-- Search --}}
        <form method="GET" action="{{ route('admin.media.index') }}" class="flex items-center gap-2">
            @if(request('type'))
                <input type="hidden" name="type" value="{{ request('type') }}">
            @endif
            <div class="relative">
                <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400"
                     fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M21 21l-4.35-4.35M17 11A6 6 0 115 11a6 6 0 0112 0z"/>
                </svg>
                <input type="text" name="search" placeholder="Search files&hellip;"
                       value="{{ request('search') }}"
                       class="w-56 rounded-xl border border-gray-200 bg-white pl-9 pr-4 py-2
                              text-sm text-gray-800 outline-none transition
                              focus:border-brand-primary focus:ring-1 focus:ring-brand-primary
                              placeholder:text-gray-400">
            </div>
            @if(request('search'))
                <a href="{{ route('admin.media.index', request('type') ? ['type' => request('type')] : []) }}"
                   class="rounded-lg p-2 text-gray-400 hover:text-gray-700 transition" title="Clear search">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </a>
            @endif
        </form>

        {{-- Grid / List toggle --}}
        <div class="flex overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
            <button @click="viewMode = 'grid'"
                    :class="viewMode === 'grid' ? 'bg-brand-primary text-white' : 'text-gray-500 hover:text-gray-800'"
                    class="flex items-center px-3 py-2 transition">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6z
                             M14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6z
                             M4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2z
                             M14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                </svg>
            </button>
            <button @click="viewMode = 'list'"
                    :class="viewMode === 'list' ? 'bg-brand-primary text-white' : 'text-gray-500 hover:text-gray-800'"
                    class="flex items-center border-l border-gray-200 px-3 py-2 transition">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                </svg>
            </button>
        </div>
    </div>

    {{-- Search hint --}}
    @if(request('search'))
        <p class="text-sm text-gray-500">
            Results for
            <span class="font-semibold text-gray-800">&ldquo;{{ request('search') }}&rdquo;</span>
            &mdash; {{ $media->total() }} {{ Str::plural('file', $media->total()) }} found
        </p>
    @endif

    {{-- ── EMPTY STATE ──────────────────────────────────────────── --}}
    @if($media->isEmpty())
        <div class="flex flex-col items-center justify-center rounded-2xl border border-dashed
                    border-gray-200 bg-white py-28 text-center">
            <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-gray-100">
                <svg class="h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5
                             l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909M3 20.25h18a.75.75 0
                             00.75-.75v-15a.75.75 0 00-.75-.75H3a.75.75 0 00-.75.75v15c0 .414.336.75.75.75z" />
                </svg>
            </div>
            @if(request('search'))
                <p class="mt-4 text-sm font-medium text-gray-700">No files matched your search</p>
                <a href="{{ route('admin.media.index', request('type') ? ['type' => request('type')] : []) }}"
                   class="mt-2 text-sm text-brand-primary hover:underline">Clear search</a>
            @else
                <p class="mt-4 text-sm font-medium text-gray-700">No files yet</p>
                <p class="mt-1 text-xs text-gray-400">Upload your first file using the button above.</p>
            @endif
        </div>

    @else

        {{-- ── GRID VIEW ──────────────────────────────────────────── --}}
        <div x-show="viewMode === 'grid'"
             class="grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6">
            @foreach($media as $item)
                <div class="group relative overflow-hidden rounded-2xl border border-gray-200 bg-white
                            shadow-sm transition hover:border-brand-primary/30 hover:shadow-lg"
                     x-data="{ open: false }"
                     :class="$parent.isSelected({{ $item->id }})
                                ? 'ring-2 ring-brand-primary border-brand-primary/40'
                                : ''">

                    {{-- Selection checkbox --}}
                    <label class="absolute left-2 top-2 z-10 cursor-pointer" @click.stop>
                        <input type="checkbox" class="sr-only"
                               :checked="$parent.isSelected({{ $item->id }})"
                               @change="$parent.toggleSelect({{ $item->id }})">
                        <span class="flex h-5 w-5 items-center justify-center rounded border-2 transition duration-150"
                              :class="$parent.isSelected({{ $item->id }})
                                      ? 'border-brand-primary bg-brand-primary opacity-100'
                                      : 'border-white/80 bg-black/20 opacity-0 group-hover:opacity-100'">
                            <svg class="h-3 w-3 text-white" fill="none" viewBox="0 0 24 24"
                                 stroke="currentColor" stroke-width="3">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                            </svg>
                        </span>
                    </label>

                    {{-- Thumbnail — click opens lightbox --}}
                    <div class="aspect-square cursor-pointer overflow-hidden bg-gray-100"
                         @click.stop="$parent.openLightbox({{ $loop->index }})">
                        @if($item->file_type === 'image')
                            <img src="{{ $item->url() }}"
                                 alt="{{ $item->file_name }}"
                                 loading="lazy"
                                 class="h-full w-full object-cover transition duration-300 group-hover:scale-110">
                        @elseif($item->file_type === 'video')
                            <div class="flex h-full w-full items-center justify-center bg-slate-900">
                                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-white/20">
                                    <svg class="ml-0.5 h-5 w-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M8 5v14l11-7z"/>
                                    </svg>
                                </div>
                            </div>
                        @else
                            @php
                                $ext     = strtoupper(pathinfo($item->file_name, PATHINFO_EXTENSION));
                                $docBg   = match($ext) {
                                    'PDF'        => 'bg-red-50',
                                    'DOC','DOCX' => 'bg-blue-50',
                                    'XLS','XLSX' => 'bg-green-50',
                                    default      => 'bg-gray-50',
                                };
                                $docText = match($ext) {
                                    'PDF'        => 'text-red-500',
                                    'DOC','DOCX' => 'text-blue-500',
                                    'XLS','XLSX' => 'text-green-600',
                                    default      => 'text-gray-500',
                                };
                            @endphp
                            <div class="flex h-full w-full flex-col items-center justify-center gap-2 {{ $docBg }}">
                                <svg class="h-9 w-9 {{ $docText }} opacity-70" fill="none" viewBox="0 0 24 24"
                                     stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                          d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0
                                             01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <span class="rounded bg-white/70 px-2 py-0.5 text-xs font-bold {{ $docText }}">
                                    {{ $ext }}
                                </span>
                            </div>
                        @endif
                    </div>

                    {{-- Caption --}}
                    <div class="px-2.5 py-2">
                        <p class="truncate text-xs font-medium text-gray-700"
                           title="{{ $item->file_name }}">{{ $item->file_name }}</p>
                        <p class="text-xs text-gray-400">{{ $item->humanSize() }}</p>
                    </div>

                    {{-- Hover overlay --}}
                    <div class="absolute inset-0 flex flex-col items-center justify-center gap-2.5
                                rounded-2xl bg-slate-900/65 opacity-0 backdrop-blur-[2px]
                                transition-opacity duration-200 group-hover:opacity-100">
                        <button @click.stop="$parent.openLightbox({{ $loop->index }})"
                                class="flex w-32 items-center justify-center gap-1.5 rounded-lg bg-white
                                       px-3 py-1.5 text-xs font-semibold text-gray-800 shadow
                                       transition hover:bg-gray-50">
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24"
                                 stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M15 12a3 3 0 11-6 0 3 3 0 016 0z
                                         M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943
                                         9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            Preview
                        </button>
                        <button @click.stop="$parent.copyUrl('{{ $item->url() }}')"
                                class="flex w-32 items-center justify-center gap-1.5 rounded-lg
                                       bg-white/20 px-3 py-1.5 text-xs font-semibold text-white
                                       transition hover:bg-white/30">
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24"
                                 stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2
                                         m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                            </svg>
                            Copy URL
                        </button>
                        <button @click.stop="open = true"
                                class="flex w-32 items-center justify-center gap-1.5 rounded-lg
                                       bg-red-500 px-3 py-1.5 text-xs font-semibold text-white
                                       shadow transition hover:bg-red-600">
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24"
                                 stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0
                                         01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0
                                         00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            Delete
                        </button>
                    </div>

                    {{-- Delete confirm modal --}}
                    <template x-teleport="body">
                        <div x-show="open" x-cloak
                             class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 p-4"
                             @click.self="open = false">
                            <div class="w-full max-w-sm rounded-2xl bg-white p-6 shadow-2xl">
                                <div class="flex h-12 w-12 items-center justify-center rounded-full bg-red-100">
                                    <svg class="h-6 w-6 text-red-500" fill="none" viewBox="0 0 24 24"
                                         stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0
                                                 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0
                                                 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </div>
                                <h3 class="mt-4 text-base font-semibold text-gray-900">Delete file?</h3>
                                <p class="mt-1 break-all text-sm text-gray-500">{{ $item->file_name }}</p>
                                <p class="mt-1 text-xs text-gray-400">This permanently removes the file. Cannot be undone.</p>
                                <div class="mt-5 flex justify-end gap-3">
                                    <button @click="open = false"
                                            class="rounded-lg px-4 py-2 text-sm text-gray-600
                                                   hover:text-gray-900 transition">
                                        Cancel
                                    </button>
                                    <form action="{{ route('admin.media.destroy', $item) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="rounded-lg bg-red-500 px-4 py-2 text-sm font-semibold
                                                       text-white shadow-sm hover:bg-red-600 transition">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            @endforeach
        </div>

        {{-- ── LIST VIEW ──────────────────────────────────────────── --}}
        <div x-show="viewMode === 'list'" x-cloak>
            <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
                <table class="min-w-full divide-y divide-gray-100 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            {{-- Select-all header checkbox --}}
                            <th class="w-10 px-4 py-3">
                                <label class="flex cursor-pointer items-center justify-center" @click.stop>
                                    <input type="checkbox" class="sr-only"
                                           :checked="allSelected"
                                           @change="allSelected ? clearSelection() : selectAll()">
                                    <span class="flex h-5 w-5 items-center justify-center rounded border-2
                                                 transition duration-150 border-gray-300"
                                          :class="allSelected
                                                  ? 'border-brand-primary bg-brand-primary'
                                                  : (selectedIds.length > 0 ? 'border-brand-primary/60 bg-brand-primary/10' : '')">
                                        <svg x-show="allSelected" class="h-3 w-3 text-white" fill="none"
                                             viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                        </svg>
                                        <svg x-show="!allSelected && selectedIds.length > 0"
                                             class="h-3 w-3 text-brand-primary" fill="currentColor" viewBox="0 0 24 24">
                                            <rect x="4" y="11" width="16" height="2" rx="1"/>
                                        </svg>
                                    </span>
                                </label>
                            </th>
                            <th class="w-14 px-5 py-3"></th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                File name
                            </th>
                            <th class="hidden px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 sm:table-cell">
                                Type
                            </th>
                            <th class="hidden px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 md:table-cell">
                                Size
                            </th>
                            <th class="hidden px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 lg:table-cell">
                                Uploaded by
                            </th>
                            <th class="hidden px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 lg:table-cell">
                                Date
                            </th>
                            <th class="px-5 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($media as $item)
                            <tr class="group transition-colors"
                                :class="$parent.isSelected({{ $item->id }}) ? 'bg-brand-primary/5' : 'hover:bg-gray-50'"
                                x-data="{ open: false }">

                                {{-- Per-row checkbox --}}
                                <td class="px-4 py-3" @click.stop>
                                    <label class="flex cursor-pointer items-center justify-center">
                                        <input type="checkbox" class="sr-only"
                                               :checked="$parent.isSelected({{ $item->id }})"
                                               @change="$parent.toggleSelect({{ $item->id }})">
                                        <span class="flex h-5 w-5 items-center justify-center rounded border-2
                                                     transition duration-150 border-gray-300"
                                              :class="$parent.isSelected({{ $item->id }})
                                                      ? 'border-brand-primary bg-brand-primary'
                                                      : ''">
                                            <svg x-show="$parent.isSelected({{ $item->id }})"
                                                 class="h-3 w-3 text-white" fill="none" viewBox="0 0 24 24"
                                                 stroke="currentColor" stroke-width="3">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                            </svg>
                                        </span>
                                    </label>
                                </td>

                                {{-- Thumbnail — click opens lightbox --}}
                                <td class="cursor-pointer px-5 py-3"
                                    @click="$parent.openLightbox({{ $loop->index }})">
                                    @if($item->file_type === 'image')
                                        <img src="{{ $item->url() }}"
                                             alt="{{ $item->file_name }}"
                                             loading="lazy"
                                             class="h-10 w-10 rounded-lg border border-gray-200 object-cover">
                                    @elseif($item->file_type === 'video')
                                        <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-slate-800">
                                            <svg class="h-4 w-4 text-white" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M8 5v14l11-7z"/>
                                            </svg>
                                        </div>
                                    @else
                                        <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-gray-100">
                                            <svg class="h-5 w-5 text-gray-500" fill="none" viewBox="0 0 24 24"
                                                 stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                      d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1
                                                         1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                        </div>
                                    @endif
                                </td>

                                {{-- Filename — click opens lightbox --}}
                                <td class="cursor-pointer px-5 py-3"
                                    @click="$parent.openLightbox({{ $loop->index }})">
                                    <p class="max-w-xs truncate font-medium text-gray-800 hover:text-brand-primary transition"
                                       title="{{ $item->file_name }}">{{ $item->file_name }}</p>
                                </td>

                                <td class="hidden px-5 py-3 sm:table-cell">
                                    @php
                                        $badge = match($item->file_type) {
                                            'image'    => 'bg-blue-50 text-blue-700',
                                            'video'    => 'bg-purple-50 text-purple-700',
                                            'document' => 'bg-amber-50 text-amber-700',
                                            default    => 'bg-gray-100 text-gray-600',
                                        };
                                    @endphp
                                    <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium {{ $badge }}">
                                        {{ ucfirst($item->file_type) }}
                                    </span>
                                </td>
                                <td class="hidden px-5 py-3 text-gray-500 md:table-cell">
                                    {{ $item->humanSize() }}
                                </td>
                                <td class="hidden px-5 py-3 text-gray-500 lg:table-cell">
                                    {{ $item->uploader?->name ?? '&mdash;' }}
                                </td>
                                <td class="hidden whitespace-nowrap px-5 py-3 text-gray-400 lg:table-cell">
                                    {{ $item->created_at->format('M j, Y') }}
                                </td>
                                <td class="px-5 py-3">
                                    <div class="flex items-center justify-end gap-1
                                                opacity-0 group-hover:opacity-100 transition">
                                        <button @click="$parent.openLightbox({{ $loop->index }})"
                                                class="rounded-lg p-1.5 text-gray-400 hover:bg-gray-100
                                                       hover:text-gray-700 transition" title="Preview">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                                 stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                      d="M15 12a3 3 0 11-6 0 3 3 0 016 0z
                                                         M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943
                                                         9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                        </button>
                                        <button @click="$parent.copyUrl('{{ $item->url() }}')"
                                                class="rounded-lg p-1.5 text-gray-400 hover:bg-gray-100
                                                       hover:text-gray-700 transition" title="Copy URL">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                                 stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                      d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2
                                                         m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                            </svg>
                                        </button>
                                        <button @click="open = true"
                                                class="rounded-lg p-1.5 text-gray-400 hover:bg-red-50
                                                       hover:text-red-500 transition" title="Delete">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                                 stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                      d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0
                                                         01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0
                                                         00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </div>

                                    <template x-teleport="body">
                                        <div x-show="open" x-cloak
                                             class="fixed inset-0 z-50 flex items-center justify-center
                                                    bg-slate-900/60 p-4"
                                             @click.self="open = false">
                                            <div class="w-full max-w-sm rounded-2xl bg-white p-6 shadow-2xl">
                                                <h3 class="text-base font-semibold text-gray-900">Delete file?</h3>
                                                <p class="mt-2 break-all text-sm text-gray-500">{{ $item->file_name }}</p>
                                                <p class="mt-1 text-xs text-gray-400">This permanently removes the file. Cannot be undone.</p>
                                                <div class="mt-5 flex justify-end gap-3">
                                                    <button @click="open = false"
                                                            class="rounded-lg px-4 py-2 text-sm text-gray-600
                                                                   hover:text-gray-900 transition">
                                                        Cancel
                                                    </button>
                                                    <form action="{{ route('admin.media.destroy', $item) }}" method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                                class="rounded-lg bg-red-500 px-4 py-2 text-sm
                                                                       font-semibold text-white shadow-sm
                                                                       hover:bg-red-600 transition">
                                                            Delete
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Pagination --}}
        @if($media->hasPages())
            <div class="flex justify-center pt-2">
                {{ $media->links() }}
            </div>
        @endif

    @endif

    {{-- ── LIGHTBOX ─────────────────────────────────────────────── --}}
    <template x-teleport="body">
        <div x-show="lightbox.open" x-cloak
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-[70] flex flex-col bg-black/95">

            {{-- ── Top bar ── --}}
            <div class="flex shrink-0 items-center justify-between gap-4 px-4 py-3 md:px-6">
                <div class="flex min-w-0 items-center gap-3">
                    <span class="shrink-0 rounded-full px-2.5 py-0.5 text-xs font-medium ring-1"
                          :class="{
                              'bg-blue-900/60 text-blue-300 ring-blue-700/50':   currentLbItem?.file_type === 'image',
                              'bg-purple-900/60 text-purpleple-300 ring-purple-700/50': currentLbItem?.file_type === 'video',
                              'bg-amber-900/60 text-amber-300 ring-amber-700/50': currentLbItem?.file_type === 'document',
                          }"
                          x-text="currentLbItem?.file_type
                                  ? currentLbItem.file_type.charAt(0).toUpperCase() + currentLbItem.file_type.slice(1)
                                  : ''">
                    </span>
                    <p class="truncate text-sm font-medium text-white/80"
                       x-text="currentLbItem?.file_name"></p>
                </div>
                <div class="ml-4 flex shrink-0 items-center gap-1">
                    {{-- Counter --}}
                    <span class="mr-2 text-xs text-white/40" x-show="mediaItems.length > 1">
                        <span x-text="lightbox.index + 1"></span>
                        <span class="text-white/25">/</span>
                        <span x-text="mediaItems.length"></span>
                    </span>
                    {{-- Copy URL --}}
                    <button @click="copyUrl(currentLbItem?.url)"
                            class="rounded-lg p-2 text-white/60 transition hover:bg-white/10 hover:text-white"
                            title="Copy URL">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                             stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2
                                     m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                        </svg>
                    </button>
                    {{-- Open in new tab --}}
                    <a :href="currentLbItem?.url" target="_blank" rel="noopener"
                       class="rounded-lg p-2 text-white/60 transition hover:bg-white/10 hover:text-white"
                       title="Open in new tab">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                             stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4
                                     M14 4h6m0 0v6m0-6L10 14"/>
                        </svg>
                    </a>
                    {{-- Close --}}
                    <button @click="closeLightbox()"
                            class="rounded-lg p-2 text-white/60 transition hover:bg-white/10 hover:text-white"
                            title="Close (Esc)">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                             stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>

            {{-- ── Preview area ── --}}
            <div class="relative flex min-h-0 flex-1 items-center justify-center px-14 md:px-20">

                {{-- Prev --}}
                <button x-show="mediaItems.length > 1" @click="prevLb()"
                        class="absolute left-2 z-10 flex h-11 w-11 items-center justify-center
                               rounded-full bg-white/10 text-white transition hover:bg-white/25
                               focus:outline-none md:left-4">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                         stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                    </svg>
                </button>

                {{-- Image --}}
                <template x-if="currentLbItem?.file_type === 'image'">
                    <img :src="currentLbItem.url"
                         :alt="currentLbItem.file_name"
                         class="max-w-full rounded-lg object-contain shadow-2xl"
                         style="max-height: calc(100vh - 140px)">
                </template>

                {{-- Video --}}
                <template x-if="currentLbItem?.file_type === 'video'">
                    <video id="lb-video-el" controls
                           class="max-w-full rounded-lg shadow-2xl"
                           style="max-height: calc(100vh - 140px)"
                           :src="currentLbItem.url">
                    </video>
                </template>

                {{-- PDF / TXT — embeddable in browser --}}
                <template x-if="currentLbItem?.file_type === 'document'
                                 && (currentLbItem.ext === 'PDF' || currentLbItem.ext === 'TXT')">
                    <iframe :src="currentLbItem.url"
                            class="w-full rounded-lg border-0 bg-white shadow-2xl"
                            style="height: calc(100vh - 140px)">
                    </iframe>
                </template>

                {{-- Other document (Word, Excel, etc.) — download-only --}}
                <template x-if="currentLbItem?.file_type === 'document'
                                 && currentLbItem.ext !== 'PDF'
                                 && currentLbItem.ext !== 'TXT'">
                    <div class="flex flex-col items-center gap-6 text-center">
                        <div class="flex h-24 w-24 items-center justify-center rounded-3xl bg-white/10">
                            <svg class="h-12 w-12 text-white/70" fill="none" viewBox="0 0 24 24"
                                 stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                      d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586
                                         a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-white/80"
                               x-text="(currentLbItem?.ext ?? '') + ' Document'"></p>
                            <p class="mt-1 text-xs text-white/40">Preview not available in browser</p>
                        </div>
                        <a :href="currentLbItem?.url" :download="currentLbItem?.file_name"
                           class="inline-flex items-center gap-2 rounded-xl bg-white/15 px-6 py-3
                                  text-sm font-semibold text-white transition hover:bg-white/25">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                 stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                            </svg>
                            Download File
                        </a>
                    </div>
                </template>

                {{-- Next --}}
                <button x-show="mediaItems.length > 1" @click="nextLb()"
                        class="absolute right-2 z-10 flex h-11 w-11 items-center justify-center
                               rounded-full bg-white/10 text-white transition hover:bg-white/25
                               focus:outline-none md:right-4">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                         stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                    </svg>
                </button>
            </div>

            {{-- ── Bottom bar ── --}}
            <div class="flex shrink-0 items-center justify-between px-4 py-3 md:px-6">
                <p class="text-xs text-white/40" x-text="currentLbItem?.size ?? ''"></p>
                <div class="flex items-center gap-3">
                    {{-- Select / Deselect in lightbox --}}
                    <button @click="toggleSelect(currentLbItem?.id)"
                            class="inline-flex items-center gap-2 rounded-lg px-3 py-1.5 text-xs
                                   font-semibold transition ring-1"
                            :class="isSelected(currentLbItem?.id)
                                    ? 'bg-brand-primary/20 text-brand-primary ring-brand-primary/50 hover:bg-brand-primary/30'
                                    : 'bg-white/10 text-white/70 ring-white/20 hover:bg-white/20'">
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24"
                             stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span x-text="isSelected(currentLbItem?.id) ? 'Selected' : 'Select'"></span>
                    </button>
                </div>
            </div>

            {{-- Click-outside-backdrop to close (covers the whole modal except controls) --}}
            <div class="absolute inset-0 -z-10" @click="closeLightbox()"></div>
        </div>
    </template>

    {{-- ── COPY TOAST ──────────────────────────────────────────── --}}
    <div x-show="toast" x-cloak x-transition
         class="fixed bottom-6 right-6 z-[80] flex items-center gap-2 rounded-xl
                bg-slate-900 px-4 py-3 text-sm font-medium text-white shadow-xl">
        <svg class="h-4 w-4 text-green-400" fill="none" viewBox="0 0 24 24"
             stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
        </svg>
        URL copied to clipboard
    </div>

</div>

@push('scripts')
<script>
function mediaLibrary(mediaItems) {
    return {
        showUpload: false,
        viewMode:   'grid',
        dragging:   false,
        submitting: false,
        queue:      [],
        toast:      false,

        // ── Data ────────────────────────────────────────────────────────────
        mediaItems: mediaItems ?? [],

        // ── Multi-select ────────────────────────────────────────────────────
        selectedIds: [],

        // ── Lightbox ────────────────────────────────────────────────────────
        lightbox: { open: false, index: 0 },

        get currentLbItem() {
            return this.mediaItems[this.lightbox.index] ?? null;
        },

        get allSelected() {
            return this.mediaItems.length > 0 &&
                   this.selectedIds.length === this.mediaItems.length;
        },

        // ── Lifecycle ───────────────────────────────────────────────────────
        init() {
            // Reload <video> when navigating within lightbox
            this.$watch('lightbox.index', () => {
                this.$nextTick(() => {
                    const v = document.getElementById('lb-video-el');
                    if (v) v.load();
                });
            });

            // Keyboard navigation for lightbox
            window.addEventListener('keydown', (e) => {
                if (!this.lightbox.open) return;
                if (e.key === 'Escape')     { this.closeLightbox(); return; }
                if (e.key === 'ArrowRight') { this.nextLb(); return; }
                if (e.key === 'ArrowLeft')  { this.prevLb(); return; }
            });
        },

        // ── Lightbox methods ────────────────────────────────────────────────
        openLightbox(index) {
            this.lightbox.index = index;
            this.lightbox.open  = true;
            document.body.style.overflow = 'hidden';
        },

        closeLightbox() {
            this.lightbox.open = false;
            document.body.style.overflow = '';
            // Pause video if playing
            const v = document.getElementById('lb-video-el');
            if (v) v.pause();
        },

        nextLb() {
            this.lightbox.index = (this.lightbox.index + 1) % this.mediaItems.length;
        },

        prevLb() {
            this.lightbox.index =
                (this.lightbox.index - 1 + this.mediaItems.length) % this.mediaItems.length;
        },

        // ── Multi-select methods ────────────────────────────────────────────
        toggleSelect(id) {
            if (id == null) return;
            const idx = this.selectedIds.indexOf(id);
            if (idx === -1) this.selectedIds.push(id);
            else            this.selectedIds.splice(idx, 1);
        },

        isSelected(id) {
            return id != null && this.selectedIds.includes(id);
        },

        selectAll() {
            this.selectedIds = this.mediaItems.map(m => m.id);
        },

        clearSelection() {
            this.selectedIds = [];
        },

        async copySelectedUrls() {
            const urls = this.mediaItems
                .filter(m => this.selectedIds.includes(m.id))
                .map(m => m.url)
                .join('\n');
            await this._writeToClipboard(urls);
            this.showToast();
        },

        // ── Upload helpers ──────────────────────────────────────────────────
        handleFiles(fileList) {
            this.queue = Array.from(fileList);
        },

        handleDrop(event) {
            this.queue = Array.from(event.dataTransfer.files);
            this.rebuildInput();
        },

        rebuildInput() {
            try {
                const dt = new DataTransfer();
                this.queue.forEach(f => dt.items.add(f));
                this.$refs.fileInput.files = dt.files;
            } catch (e) {}
        },

        // ── Utilities ───────────────────────────────────────────────────────
        humanSize(bytes) {
            if (bytes < 1024)          return bytes + ' B';
            if (bytes < 1_048_576)     return (bytes / 1024).toFixed(1) + ' KB';
            if (bytes < 1_073_741_824) return (bytes / 1_048_576).toFixed(1) + ' MB';
            return (bytes / 1_073_741_824).toFixed(2) + ' GB';
        },

        async copyUrl(url) {
            if (!url) return;
            await this._writeToClipboard(url);
            this.showToast();
        },

        async _writeToClipboard(text) {
            try {
                await navigator.clipboard.writeText(text);
            } catch (e) {
                const el = document.createElement('textarea');
                el.value = text;
                el.style.position = 'fixed';
                el.style.opacity  = '0';
                document.body.appendChild(el);
                el.select();
                document.execCommand('copy');
                document.body.removeChild(el);
            }
        },

        showToast() {
            this.toast = true;
            setTimeout(() => { this.toast = false; }, 2500);
        },
    };
}
</script>
@endpush
</x-app-layout>
