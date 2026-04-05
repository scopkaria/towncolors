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
                    {{ $section->typeLabel() }}
                </span>
                <h1 class="font-display text-2xl text-brand-ink sm:text-3xl">Edit: {{ $section->label }}</h1>
            </div>
        </div>
    </x-slot>

    <form method="POST" action="{{ route('admin.pages.sections.update', [$page, $section]) }}" id="section-form">
        @csrf @method('PATCH')

        @if ($errors->any())
            <div class="rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-sm text-red-600">
                <ul class="list-disc pl-4 space-y-1">
                    @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                </ul>
            </div>
        @endif

        <div class="grid gap-6 lg:grid-cols-3">

            {{-- ── Left: section-specific fields ───────────────────────── --}}
            <div class="lg:col-span-2 space-y-5">

                <div class="rounded-3xl border border-white/70 bg-white/90 p-6 shadow-panel space-y-5">
                    <div class="flex items-center gap-3 pb-4 border-b border-stone-100">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-orange-50 text-brand-primary">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="{{ \App\Models\PageSection::TYPE_ICONS[$section->type] ?? '' }}"/>
                            </svg>
                        </div>
                        <div>
                            <p class="font-semibold text-brand-ink">{{ $section->typeLabel() }}</p>
                            <p class="text-xs text-brand-muted">Edit the content fields for this section.</p>
                        </div>
                    </div>

                    {{-- ═══════════ HERO ═══════════ --}}
                    @if ($section->type === 'hero')
                        <div>
                            <label class="block text-sm font-semibold text-brand-ink">Headline <span class="text-red-400">*</span></label>
                            <input type="text" name="title" value="{{ old('title', $section->get('title')) }}"
                                   class="mt-2 w-full rounded-2xl border-stone-200 bg-white px-4 py-3 text-sm text-brand-ink shadow-sm transition focus:border-brand-primary focus:ring-brand-primary">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-brand-ink">Subtitle</label>
                            <textarea name="subtitle" rows="2"
                                      class="mt-2 w-full rounded-2xl border-stone-200 bg-white px-4 py-3 text-sm text-brand-ink shadow-sm transition focus:border-brand-primary focus:ring-brand-primary">{{ old('subtitle', $section->get('subtitle')) }}</textarea>
                        </div>
                        @include('admin.pages.sections.partials.media-picker', [
                            'fieldName'      => 'bg_media_id',
                            'label'          => 'Background Image',
                            'currentMediaId' => $section->get('bg_media_id'),
                        ])
                    @endif

                    {{-- ═══════════ STORY ═══════════ --}}
                    @if ($section->type === 'story')
                        <div>
                            <label class="block text-sm font-semibold text-brand-ink">Company Story</label>
                            <p class="mt-1 text-xs text-brand-muted">Use the toolbar to format text.</p>
                            <div id="story-editor"
                                 class="mt-3 rounded-2xl border border-stone-200 bg-white text-sm text-brand-ink"
                                 style="min-height:320px;">{!! old('content', $section->get('content')) !!}</div>
                            <textarea name="content" id="story-content" class="hidden">{{ old('content', $section->get('content')) }}</textarea>
                        </div>
                    @endif

                    {{-- ═══════════ TIMELINE ═══════════ --}}
                    @if ($section->type === 'timeline')
                        @php $timelineItems = old('items_json') ? json_decode(old('items_json'), true) : ($section->get('items') ?? []); @endphp
                        <div>
                            <label class="block text-sm font-semibold text-brand-ink">Section Heading</label>
                            <input type="text" name="heading" value="{{ old('heading', $section->get('heading', 'Our Journey')) }}"
                                   class="mt-2 w-full rounded-2xl border-stone-200 bg-white px-4 py-3 text-sm text-brand-ink shadow-sm transition focus:border-brand-primary focus:ring-brand-primary">
                        </div>

                        <div x-data="{
                            items: {{ json_encode($timelineItems) }},
                            add() { this.items.push({year:'', label:'', description:''}); },
                            remove(i) { this.items.splice(i,1); }
                        }">
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
                    @endif

                    {{-- ═══════════ SERVICES ═══════════ --}}
                    @if ($section->type === 'services')
                        <div>
                            <label class="block text-sm font-semibold text-brand-ink">Section Heading</label>
                            <input type="text" name="heading" value="{{ old('heading', $section->get('heading', 'Our Services')) }}"
                                   class="mt-2 w-full rounded-2xl border-stone-200 bg-white px-4 py-3 text-sm text-brand-ink shadow-sm transition focus:border-brand-primary focus:ring-brand-primary">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-brand-ink">Intro Text</label>
                            <textarea name="intro" rows="3"
                                      class="mt-2 w-full rounded-2xl border-stone-200 bg-white px-4 py-3 text-sm text-brand-ink shadow-sm transition focus:border-brand-primary focus:ring-brand-primary">{{ old('intro', $section->get('intro')) }}</textarea>
                        </div>
                        <div class="rounded-2xl border border-orange-100 bg-orange-50/50 px-4 py-3">
                            <p class="text-xs text-brand-primary font-medium">Services are pulled dynamically from your service categories.</p>
                            @if ($services->isNotEmpty())
                                <p class="mt-1 text-[11px] text-brand-muted">{{ $services->count() }} {{ Str::plural('category', $services->count()) }} found:
                                    {{ $services->pluck('name')->take(4)->implode(', ') }}{{ $services->count() > 4 ? '…' : '' }}
                                </p>
                            @endif
                        </div>
                    @endif

                    {{-- ═══════════ VISION ═══════════ --}}
                    @if ($section->type === 'vision')
                        <div>
                            <label class="block text-sm font-semibold text-brand-ink">Section Heading</label>
                            <input type="text" name="heading" value="{{ old('heading', $section->get('heading', 'Our Vision')) }}"
                                   class="mt-2 w-full rounded-2xl border-stone-200 bg-white px-4 py-3 text-sm text-brand-ink shadow-sm transition focus:border-brand-primary focus:ring-brand-primary">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-brand-ink">Content</label>
                            <textarea name="content" rows="6"
                                      class="mt-2 w-full rounded-2xl border-stone-200 bg-white px-4 py-3 text-sm text-brand-ink shadow-sm transition focus:border-brand-primary focus:ring-brand-primary">{{ old('content', $section->get('content')) }}</textarea>
                        </div>
                    @endif

                    {{-- ═══════════ COMMUNITY ═══════════ --}}
                    @if ($section->type === 'community')
                        <div>
                            <label class="block text-sm font-semibold text-brand-ink">Section Heading</label>
                            <input type="text" name="heading" value="{{ old('heading', $section->get('heading', 'Community')) }}"
                                   class="mt-2 w-full rounded-2xl border-stone-200 bg-white px-4 py-3 text-sm text-brand-ink shadow-sm transition focus:border-brand-primary focus:ring-brand-primary">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-brand-ink">Content</label>
                            <textarea name="content" rows="6"
                                      class="mt-2 w-full rounded-2xl border-stone-200 bg-white px-4 py-3 text-sm text-brand-ink shadow-sm transition focus:border-brand-primary focus:ring-brand-primary">{{ old('content', $section->get('content')) }}</textarea>
                        </div>
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <label class="block text-sm font-semibold text-brand-ink">Link Label</label>
                                <input type="text" name="link_label" value="{{ old('link_label', $section->get('link_label')) }}"
                                       placeholder="Visit community hub"
                                       class="mt-2 w-full rounded-2xl border-stone-200 bg-white px-4 py-3 text-sm text-brand-ink shadow-sm transition focus:border-brand-primary focus:ring-brand-primary">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-brand-ink">Link URL</label>
                                <input type="url" name="link_url" value="{{ old('link_url', $section->get('link_url')) }}"
                                       placeholder="https://workmywork.towncolors.com"
                                       class="mt-2 w-full rounded-2xl border-stone-200 bg-white px-4 py-3 text-sm text-brand-ink shadow-sm transition focus:border-brand-primary focus:ring-brand-primary">
                            </div>
                        </div>
                    @endif

                    {{-- ═══════════ CLIENTS ═══════════ --}}
                    @if ($section->type === 'clients')
                        @php $selectedMediaIds = $section->get('media_ids', []); @endphp
                        <div x-data="clientsMediaPicker('{{ route('admin.media.api') }}', {{ json_encode($selectedMediaIds) }})">
                            <div>
                                <label class="block text-sm font-semibold text-brand-ink">Section Heading</label>
                                <input type="text" name="heading" value="{{ old('heading', $section->get('heading', 'Trusted By')) }}"
                                       class="mt-2 w-full rounded-2xl border-stone-200 bg-white px-4 py-3 text-sm text-brand-ink shadow-sm transition focus:border-brand-primary focus:ring-brand-primary">
                            </div>

                            <div class="mt-4">
                                <div class="flex items-center justify-between mb-3">
                                    <label class="block text-sm font-semibold text-brand-ink">Client Logos</label>
                                    <button type="button" @click="open()"
                                            class="inline-flex items-center gap-1.5 rounded-xl border border-stone-200 bg-white px-3 py-1.5 text-xs font-semibold text-brand-muted transition hover:border-brand-primary hover:text-brand-primary">
                                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M12 4v16m8-8H4"/></svg>
                                        Add Logo
                                    </button>
                                </div>
                                <div class="flex flex-wrap gap-3 min-h-16 rounded-2xl border border-dashed border-stone-200 bg-stone-50/50 p-3">
                                    <template x-for="(img, i) in selected" :key="img.id">
                                        <div class="relative group">
                                            <img :src="img.url" :alt="img.name" class="h-16 w-24 rounded-xl border border-stone-200 object-contain bg-white p-1">
                                            <button type="button" @click="remove(i)"
                                                    class="absolute -right-2 -top-2 hidden h-5 w-5 items-center justify-center rounded-full bg-red-500 text-white group-hover:flex">
                                                <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" d="M6 18 18 6M6 6l12 12"/></svg>
                                            </button>
                                        </div>
                                    </template>
                                    <p x-show="selected.length === 0" class="text-xs text-brand-muted self-center p-2">No logos selected yet.</p>
                                </div>
                                <input type="hidden" name="media_ids_json" :value="JSON.stringify(selected.map(s => s.id))">
                            </div>

                            @include('admin.pages.sections.partials.media-modal')
                        </div>
                    @endif

                    {{-- ═══════════ CTA ═══════════ --}}
                    @if ($section->type === 'cta')
                        <div>
                            <label class="block text-sm font-semibold text-brand-ink">CTA Headline <span class="text-red-400">*</span></label>
                            <input type="text" name="title" value="{{ old('title', $section->get('title')) }}"
                                   placeholder="Ready to start your project?"
                                   class="mt-2 w-full rounded-2xl border-stone-200 bg-white px-4 py-3 text-sm text-brand-ink shadow-sm transition focus:border-brand-primary focus:ring-brand-primary">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-brand-ink">Subtitle</label>
                            <input type="text" name="subtitle" value="{{ old('subtitle', $section->get('subtitle')) }}"
                                   placeholder="Let's build something great together."
                                   class="mt-2 w-full rounded-2xl border-stone-200 bg-white px-4 py-3 text-sm text-brand-ink shadow-sm transition focus:border-brand-primary focus:ring-brand-primary">
                        </div>
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <label class="block text-sm font-semibold text-brand-ink">Button Label</label>
                                <input type="text" name="button_label" value="{{ old('button_label', $section->get('button_label', 'Start a Project')) }}"
                                       class="mt-2 w-full rounded-2xl border-stone-200 bg-white px-4 py-3 text-sm text-brand-ink shadow-sm transition focus:border-brand-primary focus:ring-brand-primary">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-brand-ink">Button URL</label>
                                <input type="text" name="button_url" value="{{ old('button_url', $section->get('button_url', '/register')) }}"
                                       class="mt-2 w-full rounded-2xl border-stone-200 bg-white px-4 py-3 text-sm text-brand-ink shadow-sm transition focus:border-brand-primary focus:ring-brand-primary">
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- ── Right sidebar ────────────────────────────────────────── --}}
            <div class="space-y-5">

                {{-- Admin label --}}
                <div class="rounded-3xl border border-white/70 bg-white/90 p-6 shadow-panel">
                    <label class="block text-sm font-semibold text-brand-ink">Admin Label</label>
                    <p class="mt-1 text-xs text-brand-muted">Internal name (not shown publicly).</p>
                    <input type="text" name="label" value="{{ old('label', $section->label) }}"
                           class="mt-3 w-full rounded-2xl border-stone-200 bg-white px-4 py-3 text-sm text-brand-ink shadow-sm transition focus:border-brand-primary focus:ring-brand-primary">
                </div>

                {{-- Actions --}}
                <div class="rounded-3xl border border-white/70 bg-white/90 p-6 shadow-panel space-y-2">
                    <button type="submit" class="btn-primary w-full justify-center">
                        <svg class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                        </svg>
                        Save Changes
                    </button>
                    <a href="{{ route('admin.pages.sections.index', $page) }}" class="btn-secondary w-full justify-center">
                        Cancel
                    </a>
                </div>

                {{-- Danger --}}
                <div class="rounded-3xl border border-white/70 bg-white/90 p-6 shadow-panel">
                    <h3 class="text-sm font-semibold text-brand-ink">Danger Zone</h3>

                    <form method="POST" action="{{ route('admin.pages.sections.toggle', [$page, $section]) }}" class="mt-3">
                        @csrf @method('PATCH')
                        <button type="submit" class="w-full rounded-2xl border border-stone-200 bg-stone-50 px-4 py-2.5 text-xs font-semibold text-brand-muted transition hover:border-stone-400">
                            {{ $section->is_active ? 'Hide from page' : 'Show on page' }}
                        </button>
                    </form>

                    <form method="POST" action="{{ route('admin.pages.sections.destroy', [$page, $section]) }}" class="mt-2"
                          onsubmit="return confirm('Delete this section? This cannot be undone.')">
                        @csrf @method('DELETE')
                        <button type="submit" class="w-full rounded-2xl border border-red-100 bg-red-50 px-4 py-2.5 text-xs font-semibold text-red-600 transition hover:bg-red-100">
                            Delete Section
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </form>

    @push('scripts')
    @if ($section->type === 'story')
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const quill = new Quill('#story-editor', {
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

        document.getElementById('section-form').addEventListener('submit', function () {
            document.getElementById('story-content').value = quill.root.innerHTML;
        });
    });
    </script>
    @endif

    @if ($section->type === 'clients')
    <script>
    function clientsMediaPicker(apiUrl, initialIds) {
        return {
            show: false,
            loading: false,
            images: [],
            selected: [],
            init() {
                if (initialIds && initialIds.length > 0) {
                    fetch(apiUrl)
                        .then(r => r.json())
                        .then(data => {
                            this.images = data;
                            this.selected = data.filter(img => initialIds.includes(img.id));
                        });
                }
            },
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
    @endif
    @endpush
</x-app-layout>
