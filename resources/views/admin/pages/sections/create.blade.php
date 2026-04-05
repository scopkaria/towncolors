<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.pages.sections.index', $page) }}"
               class="flex h-9 w-9 items-center justify-center rounded-xl border border-stone-200 bg-white text-brand-muted shadow-sm transition hover:border-brand-primary hover:text-brand-primary">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/>
                </svg>
            </a>
            <div class="space-y-1">
                <span class="inline-flex rounded-full border border-orange-200 bg-orange-50 px-3 py-0.5 text-[11px] font-semibold uppercase tracking-[0.32em] text-brand-primary">
                    Sections Builder
                </span>
                <h1 class="font-display text-2xl text-brand-ink sm:text-3xl">Add Section — {{ $page->title }}</h1>
            </div>
        </div>
    </x-slot>

    <form method="POST" action="{{ route('admin.pages.sections.store', $page) }}"
          x-data="sectionCreate()"
          @submit.prevent="submit">
        @csrf

        {{-- Validation errors --}}
        @if ($errors->any())
            <div class="rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-sm text-red-600">
                <ul class="list-disc pl-4 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid gap-6 lg:grid-cols-3">

            {{-- ── Left: type picker + fields ─────────────────────────── --}}
            <div class="lg:col-span-2 space-y-5">

                {{-- Step 1: Choose type --}}
                <div class="rounded-3xl border border-white/70 bg-white/90 p-6 shadow-panel">
                    <h2 class="font-display text-base text-brand-ink">1. Choose Section Type</h2>
                    <p class="mt-1 text-xs text-brand-muted">Select the type of content block to add.</p>

                    <input type="hidden" name="type" x-model="type">

                    <div class="mt-5 grid grid-cols-2 gap-3 sm:grid-cols-4">
                        @foreach (\App\Models\PageSection::TYPES as $key => $label)
                            <button type="button"
                                    @click="type = '{{ $key }}'"
                                    :class="type === '{{ $key }}'
                                        ? 'border-brand-primary bg-orange-50 text-brand-primary ring-1 ring-brand-primary/30'
                                        : 'border-stone-200 bg-white text-brand-muted hover:border-orange-200 hover:bg-orange-50/40 hover:text-brand-primary'"
                                    class="flex flex-col items-center gap-2 rounded-2xl border p-4 text-center transition">
                                <svg class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ \App\Models\PageSection::TYPE_ICONS[$key] }}"/>
                                </svg>
                                <span class="text-xs font-semibold leading-tight">{{ $label }}</span>
                            </button>
                        @endforeach
                    </div>
                </div>

                {{-- Step 2: Section fields (dynamic per type) --}}
                <div class="rounded-3xl border border-white/70 bg-white/90 p-6 shadow-panel space-y-5" x-show="type" x-cloak>
                    <h2 class="font-display text-base text-brand-ink">2. Fill in Content</h2>

                    {{-- ═══ HERO ═══ --}}
                    <div x-show="type === 'hero'" x-cloak class="space-y-4">
                        <div>
                            <label class="block text-sm font-semibold text-brand-ink">Headline <span class="text-red-400">*</span></label>
                            <input type="text" name="title" placeholder="e.g. We Build Digital Products"
                                   class="mt-2 w-full rounded-2xl border-stone-200 bg-white px-4 py-3 text-sm text-brand-ink shadow-sm transition focus:border-brand-primary focus:ring-brand-primary">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-brand-ink">Subtitle</label>
                            <textarea name="subtitle" rows="2" placeholder="Short supporting text under the headline."
                                      class="mt-2 w-full rounded-2xl border-stone-200 bg-white px-4 py-3 text-sm text-brand-ink shadow-sm transition focus:border-brand-primary focus:ring-brand-primary"></textarea>
                        </div>
                        @include('admin.pages.sections.partials.media-picker', ['fieldName' => 'bg_media_id', 'label' => 'Background Image'])
                    </div>

                    {{-- ═══ STORY ═══ --}}
                    <div x-show="type === 'story'" x-cloak class="space-y-4">
                        <div>
                            <label class="block text-sm font-semibold text-brand-ink">Company Story</label>
                            <p class="mt-1 text-xs text-brand-muted">Rich text — bold, italic, lists, links all supported.</p>
                            <div id="story-editor" class="mt-3 rounded-2xl border border-stone-200 bg-white text-sm text-brand-ink" style="min-height:260px;"></div>
                            <textarea name="content" id="story-content" class="hidden"></textarea>
                        </div>
                    </div>

                    {{-- ═══ TIMELINE ═══ --}}
                    <div x-show="type === 'timeline'" x-cloak class="space-y-4"
                         x-data="{
                             items: [
                                 {year:'2019', label:'Founded', description:''},
                                 {year:'2021', label:'Rebuilt with partner', description:''},
                                 {year:'2024', label:'Fully independent', description:''}
                             ],
                             add() { this.items.push({year:'', label:'', description:''}); },
                             remove(i) { this.items.splice(i,1); },
                         }">
                        <div>
                            <label class="block text-sm font-semibold text-brand-ink">Section Heading</label>
                            <input type="text" name="heading" value="Our Journey"
                                   class="mt-2 w-full rounded-2xl border-stone-200 bg-white px-4 py-3 text-sm text-brand-ink shadow-sm transition focus:border-brand-primary focus:ring-brand-primary">
                        </div>

                        <div>
                            <div class="flex items-center justify-between mb-3">
                                <label class="block text-sm font-semibold text-brand-ink">Timeline Items</label>
                                <button type="button" @click="add()"
                                        class="inline-flex items-center gap-1.5 rounded-xl border border-stone-200 bg-white px-3 py-1.5 text-xs font-semibold text-brand-muted transition hover:border-brand-primary hover:text-brand-primary">
                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M12 4v16m8-8H4"/></svg>
                                    Add Item
                                </button>
                            </div>

                            <div class="space-y-3">
                                <template x-for="(item, i) in items" :key="i">
                                    <div class="flex items-start gap-3 rounded-2xl border border-stone-100 bg-stone-50 p-4">
                                        <div class="w-20 shrink-0">
                                            <label class="text-[10px] font-semibold uppercase tracking-wider text-brand-muted">Year</label>
                                            <input type="text" x-model="item.year" maxlength="6" placeholder="2024"
                                                   class="mt-1 w-full rounded-xl border-stone-200 bg-white px-3 py-2 text-sm text-brand-ink focus:border-brand-primary focus:ring-brand-primary">
                                        </div>
                                        <div class="flex-1">
                                            <label class="text-[10px] font-semibold uppercase tracking-wider text-brand-muted">Milestone</label>
                                            <input type="text" x-model="item.label" placeholder="Milestone title"
                                                   class="mt-1 w-full rounded-xl border-stone-200 bg-white px-3 py-2 text-sm text-brand-ink focus:border-brand-primary focus:ring-brand-primary">
                                        </div>
                                        <div class="flex-1">
                                            <label class="text-[10px] font-semibold uppercase tracking-wider text-brand-muted">Description (optional)</label>
                                            <input type="text" x-model="item.description" placeholder="Short sentence..."
                                                   class="mt-1 w-full rounded-xl border-stone-200 bg-white px-3 py-2 text-sm text-brand-ink focus:border-brand-primary focus:ring-brand-primary">
                                        </div>
                                        <button type="button" @click="remove(i)"
                                                class="mt-6 flex h-8 w-8 shrink-0 items-center justify-center rounded-xl border border-stone-200 text-brand-muted hover:border-red-200 hover:text-red-500">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M6 18 18 6M6 6l12 12"/></svg>
                                        </button>
                                    </div>
                                </template>
                            </div>

                            <input type="hidden" name="items_json" :value="JSON.stringify(items)">
                        </div>
                    </div>

                    {{-- ═══ SERVICES ═══ --}}
                    <div x-show="type === 'services'" x-cloak class="space-y-4">
                        <div>
                            <label class="block text-sm font-semibold text-brand-ink">Section Heading</label>
                            <input type="text" name="heading" value="Our Services"
                                   class="mt-2 w-full rounded-2xl border-stone-200 bg-white px-4 py-3 text-sm text-brand-ink shadow-sm transition focus:border-brand-primary focus:ring-brand-primary">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-brand-ink">Intro Text</label>
                            <textarea name="intro" rows="3" placeholder="Brief description shown above the services grid..."
                                      class="mt-2 w-full rounded-2xl border-stone-200 bg-white px-4 py-3 text-sm text-brand-ink shadow-sm transition focus:border-brand-primary focus:ring-brand-primary"></textarea>
                        </div>
                        <p class="rounded-2xl border border-orange-100 bg-orange-50/50 px-4 py-3 text-xs text-brand-primary">
                            Services are pulled automatically from your service categories. Manage them under <strong>Admin → Categories</strong>.
                        </p>
                    </div>

                    {{-- ═══ VISION ═══ --}}
                    <div x-show="type === 'vision'" x-cloak class="space-y-4">
                        <div>
                            <label class="block text-sm font-semibold text-brand-ink">Section Heading</label>
                            <input type="text" name="heading" value="Our Vision"
                                   class="mt-2 w-full rounded-2xl border-stone-200 bg-white px-4 py-3 text-sm text-brand-ink shadow-sm transition focus:border-brand-primary focus:ring-brand-primary">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-brand-ink">Content</label>
                            <textarea name="content" rows="5" placeholder="What drives the company forward..."
                                      class="mt-2 w-full rounded-2xl border-stone-200 bg-white px-4 py-3 text-sm text-brand-ink shadow-sm transition focus:border-brand-primary focus:ring-brand-primary"></textarea>
                        </div>
                    </div>

                    {{-- ═══ COMMUNITY ═══ --}}
                    <div x-show="type === 'community'" x-cloak class="space-y-4">
                        <div>
                            <label class="block text-sm font-semibold text-brand-ink">Section Heading</label>
                            <input type="text" name="heading" value="Community"
                                   class="mt-2 w-full rounded-2xl border-stone-200 bg-white px-4 py-3 text-sm text-brand-ink shadow-sm transition focus:border-brand-primary focus:ring-brand-primary">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-brand-ink">Content</label>
                            <textarea name="content" rows="5" placeholder="Community involvement, partnerships, social impact..."
                                      class="mt-2 w-full rounded-2xl border-stone-200 bg-white px-4 py-3 text-sm text-brand-ink shadow-sm transition focus:border-brand-primary focus:ring-brand-primary"></textarea>
                        </div>
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <label class="block text-sm font-semibold text-brand-ink">Link Label</label>
                                <input type="text" name="link_label" placeholder="Visit community hub"
                                       class="mt-2 w-full rounded-2xl border-stone-200 bg-white px-4 py-3 text-sm text-brand-ink shadow-sm transition focus:border-brand-primary focus:ring-brand-primary">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-brand-ink">Link URL</label>
                                <input type="url" name="link_url" placeholder="https://workmywork.towncolors.com"
                                       class="mt-2 w-full rounded-2xl border-stone-200 bg-white px-4 py-3 text-sm text-brand-ink shadow-sm transition focus:border-brand-primary focus:ring-brand-primary">
                            </div>
                        </div>
                    </div>

                    {{-- ═══ CLIENTS ═══ --}}
                    <div x-show="type === 'clients'" x-cloak class="space-y-4"
                         x-data="mediaPicker('{{ route('admin.media.api') }}')">
                        <div>
                            <label class="block text-sm font-semibold text-brand-ink">Section Heading</label>
                            <input type="text" name="heading" value="Trusted By"
                                   class="mt-2 w-full rounded-2xl border-stone-200 bg-white px-4 py-3 text-sm text-brand-ink shadow-sm transition focus:border-brand-primary focus:ring-brand-primary">
                        </div>

                        <div>
                            <div class="flex items-center justify-between mb-3">
                                <label class="block text-sm font-semibold text-brand-ink">Client Logos</label>
                                <button type="button" @click="open()"
                                        class="inline-flex items-center gap-1.5 rounded-xl border border-stone-200 bg-white px-3 py-1.5 text-xs font-semibold text-brand-muted transition hover:border-brand-primary hover:text-brand-primary">
                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M12 4v16m8-8H4"/></svg>
                                    Add from Media Library
                                </button>
                            </div>
                            <div class="flex flex-wrap gap-3 min-h-12">
                                <template x-for="(img, i) in selected" :key="img.id">
                                    <div class="relative group">
                                        <img :src="img.url" :alt="img.name" class="h-16 w-24 rounded-xl border border-stone-200 object-contain bg-stone-50 p-1">
                                        <button type="button" @click="remove(i)"
                                                class="absolute -right-2 -top-2 hidden h-5 w-5 items-center justify-center rounded-full bg-red-500 text-white group-hover:flex">
                                            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" d="M6 18 18 6M6 6l12 12"/></svg>
                                        </button>
                                    </div>
                                </template>
                                <p x-show="selected.length === 0" class="text-xs text-brand-muted self-center">No logos selected.</p>
                            </div>
                            <input type="hidden" name="media_ids_json" :value="JSON.stringify(selected.map(s => s.id))">
                        </div>

                        @include('admin.pages.sections.partials.media-modal')
                    </div>

                    {{-- ═══ CTA ═══ --}}
                    <div x-show="type === 'cta'" x-cloak class="space-y-4">
                        <div>
                            <label class="block text-sm font-semibold text-brand-ink">CTA Headline <span class="text-red-400">*</span></label>
                            <input type="text" name="title" placeholder="Ready to start your project?"
                                   class="mt-2 w-full rounded-2xl border-stone-200 bg-white px-4 py-3 text-sm text-brand-ink shadow-sm transition focus:border-brand-primary focus:ring-brand-primary">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-brand-ink">Subtitle</label>
                            <input type="text" name="subtitle" placeholder="Let's build something great together."
                                   class="mt-2 w-full rounded-2xl border-stone-200 bg-white px-4 py-3 text-sm text-brand-ink shadow-sm transition focus:border-brand-primary focus:ring-brand-primary">
                        </div>
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <label class="block text-sm font-semibold text-brand-ink">Button Label</label>
                                <input type="text" name="button_label" value="Start a Project"
                                       class="mt-2 w-full rounded-2xl border-stone-200 bg-white px-4 py-3 text-sm text-brand-ink shadow-sm transition focus:border-brand-primary focus:ring-brand-primary">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-brand-ink">Button URL</label>
                                <input type="text" name="button_url" value="/register"
                                       class="mt-2 w-full rounded-2xl border-stone-200 bg-white px-4 py-3 text-sm text-brand-ink shadow-sm transition focus:border-brand-primary focus:ring-brand-primary">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── Right: sidebar ───────────────────────────────────────── --}}
            <div class="space-y-5">

                {{-- Admin label --}}
                <div class="rounded-3xl border border-white/70 bg-white/90 p-6 shadow-panel">
                    <h2 class="font-display text-base text-brand-ink">Admin Label</h2>
                    <p class="mt-1 text-xs text-brand-muted">Internal name for this section (not shown on public site).</p>
                    <input type="text" name="label" placeholder="e.g. Hero Section"
                           class="mt-3 w-full rounded-2xl border-stone-200 bg-white px-4 py-3 text-sm text-brand-ink shadow-sm transition focus:border-brand-primary focus:ring-brand-primary">
                </div>

                {{-- Save --}}
                <div class="rounded-3xl border border-white/70 bg-white/90 p-6 shadow-panel">
                    <button type="submit" class="btn-primary w-full justify-center" :disabled="!type" :class="!type ? 'opacity-50 cursor-not-allowed' : ''">
                        <svg class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                        </svg>
                        Add Section
                    </button>
                    <a href="{{ route('admin.pages.sections.index', $page) }}" class="btn-secondary mt-2 w-full justify-center">
                        Cancel
                    </a>
                </div>
            </div>
        </div>
    </form>

    @push('scripts')
    <script>
    function sectionCreate() {
        return {
            type: '{{ old('type', '') }}',
            submit() {
                // Sync Quill editor content before submit
                const quill = window.__storyQuill;
                if (quill) {
                    document.getElementById('story-content').value = quill.root.innerHTML;
                }
                this.$el.submit();
            }
        };
    }

    function mediaPicker(apiUrl) {
        return {
            show: false,
            loading: false,
            images: [],
            selected: [],
            open() {
                this.show = true;
                if (this.images.length === 0) this.load();
            },
            close() { this.show = false; },
            load() {
                this.loading = true;
                fetch(apiUrl)
                    .then(r => r.json())
                    .then(data => { this.images = data; this.loading = false; });
            },
            pick(img) {
                if (!this.selected.find(s => s.id === img.id)) {
                    this.selected.push(img);
                }
                this.close();
            },
            remove(i) { this.selected.splice(i, 1); },
        };
    }
    </script>

    {{-- Quill for story rich text --}}
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        window.__storyQuill = new Quill('#story-editor', {
            theme: 'snow',
            modules: {
                toolbar: [
                    ['bold', 'italic', 'underline'],
                    [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                    [{ 'header': [2, 3, false] }],
                    ['link'],
                    ['clean']
                ]
            }
        });
    });
    </script>
    @endpush
</x-app-layout>
