{{-- Reusable inline media picker for a single image field --}}
{{-- Usage: @include('admin.pages.sections.partials.media-picker', ['fieldName' => 'bg_media_id', 'label' => 'Background Image']) --}}
<div x-data="singleMediaPicker('{{ route('admin.media.api') }}', '{{ $currentMediaId ?? '' }}')" class="space-y-3">
    <label class="block text-sm font-semibold text-brand-ink">{{ $label ?? 'Image' }}</label>

    {{-- Preview --}}
    <div x-show="preview" class="relative inline-block">
        <img :src="preview" alt="Selected image" class="h-40 w-full max-w-sm rounded-2xl border border-stone-200 object-cover">
        <button type="button" @click="clear()"
                class="absolute -right-2 -top-2 flex h-6 w-6 items-center justify-center rounded-full bg-red-500 text-white shadow">
            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" d="M6 18 18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>

    <button type="button" @click="open()"
            class="inline-flex items-center gap-2 rounded-2xl border border-stone-200 bg-white px-4 py-2.5 text-sm font-semibold text-brand-muted transition hover:border-brand-primary hover:text-brand-primary">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909M13.5 12h.008v.008H13.5V12Zm-3 8.25h13.5a2.25 2.25 0 0 0 2.25-2.25V6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v13.5c0 1.243 1.007 2.25 2.25 2.25Z"/>
        </svg>
        <span x-text="preview ? 'Change Image' : 'Select from Media Library'"></span>
    </button>

    <input type="hidden" name="{{ $fieldName }}" :value="mediaId">

    {{-- Modal --}}
    <template x-teleport="body">
        <div x-show="show" x-cloak
             class="fixed inset-0 z-50 flex items-center justify-center p-4"
             @keydown.escape.window="close()">

            <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="close()"></div>

            <div class="relative z-10 flex max-h-[85vh] w-full max-w-3xl flex-col rounded-3xl border border-white/20 bg-white shadow-2xl overflow-hidden">
                <div class="flex items-center justify-between border-b border-stone-100 px-6 py-4">
                    <h3 class="font-display text-lg text-brand-ink">Select Image</h3>
                    <button type="button" @click="close()" class="flex h-8 w-8 items-center justify-center rounded-xl border border-stone-200 text-brand-muted hover:border-brand-primary hover:text-brand-primary">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M6 18 18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <div class="flex-1 overflow-y-auto p-5">
                    <div x-show="loading" class="flex h-40 items-center justify-center">
                        <div class="h-8 w-8 animate-spin rounded-full border-2 border-brand-primary border-t-transparent"></div>
                    </div>
                    <div x-show="!loading" class="grid grid-cols-3 gap-3 sm:grid-cols-4 lg:grid-cols-5">
                        <template x-for="img in images" :key="img.id">
                            <button type="button" @click="pick(img)"
                                    :class="mediaId == img.id ? 'ring-2 ring-brand-primary ring-offset-2' : ''"
                                    class="group relative overflow-hidden rounded-2xl border border-stone-200 bg-stone-50 aspect-square transition hover:border-brand-primary">
                                <img :src="img.url" :alt="img.name" class="h-full w-full object-cover transition group-hover:scale-105">
                            </button>
                        </template>
                        <p x-show="images.length === 0 && !loading" class="col-span-full text-center text-sm text-brand-muted py-12">
                            No images in media library.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </template>
</div>

<script>
function singleMediaPicker(apiUrl, initialId) {
    return {
        show: false,
        loading: false,
        images: [],
        mediaId: initialId || null,
        preview: null,
        init() {
            if (initialId) this.loadPreview(parseInt(initialId));
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
                .then(data => {
                    this.images = data;
                    this.loading = false;
                });
        },
        pick(img) {
            this.mediaId = img.id;
            this.preview = img.url;
            this.close();
        },
        loadPreview(id) {
            fetch(apiUrl)
                .then(r => r.json())
                .then(data => {
                    this.images = data;
                    const found = data.find(i => i.id === id);
                    if (found) this.preview = found.url;
                });
        },
        clear() {
            this.mediaId = null;
            this.preview = null;
        },
    };
}
</script>
