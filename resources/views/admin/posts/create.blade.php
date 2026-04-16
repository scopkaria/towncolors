<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.posts.index') }}" class="flex h-9 w-9 items-center justify-center rounded-xl border border-warm-300/50 bg-warm-100 text-brand-muted shadow-sm transition hover:border-brand-primary hover:text-brand-primary">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/></svg>
            </a>
            <div class="space-y-1">
                <span class="inline-flex rounded-full border border-accent/30 bg-accent-light px-3 py-0.5 text-[11px] font-semibold uppercase tracking-[0.32em] text-brand-primary">Blog</span>
                <h1 class="font-display text-2xl text-brand-ink sm:text-3xl">New Post</h1>
            </div>
        </div>
    </x-slot>

    <form method="POST" action="{{ route('admin.posts.store') }}" id="post-form" enctype="multipart/form-data">
        @csrf

        <div class="grid gap-6 lg:grid-cols-3">

            {{-- ── Main content ── --}}
            <div class="lg:col-span-2 space-y-5">

                {{-- Title + Slug --}}
                <div class="rounded-3xl border border-white/70 bg-white/90 p-6 shadow-panel space-y-4 dark:border-slate-700/50 dark:bg-navy-800/90">
                    <div>
                        <label for="title" class="block text-sm font-semibold text-brand-ink">
                            Post Title <span class="text-red-400">*</span>
                        </label>
                        <input type="text" name="title" id="title" value="{{ old('title') }}" required
                               placeholder="e.g. How to Build a Great Freelance Portfolio"
                               class="mt-2 w-full rounded-2xl border-warm-300/50 bg-warm-100 px-4 py-3 text-sm text-brand-ink shadow-sm transition focus:border-brand-primary focus:ring-brand-primary dark:border-slate-600 dark:bg-navy-900/60 dark:text-warm-100">
                        @error('title')
                            <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="slug" class="block text-sm font-semibold text-brand-ink">
                            URL Slug
                            <span class="ml-1 text-xs font-normal text-brand-muted">(auto-generated if left empty)</span>
                        </label>
                        <div class="mt-2 flex items-center rounded-2xl border border-warm-300/50 bg-warm-100 shadow-sm transition focus-within:border-brand-primary focus-within:ring-1 focus-within:ring-brand-primary dark:border-slate-600 dark:bg-navy-900/60">
                            <span class="select-none border-r border-warm-300/50 px-3 py-3 text-sm text-brand-muted dark:border-slate-600">/blog/</span>
                            <input type="text" name="slug" id="slug" value="{{ old('slug') }}"
                                   placeholder="great-freelance-portfolio"
                                   class="flex-1 rounded-r-2xl border-0 bg-transparent px-3 py-3 font-mono text-sm text-brand-ink focus:ring-0 dark:text-warm-100">
                        </div>
                        @error('slug')
                            <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Rich text content --}}
                <div class="rounded-3xl border border-white/70 bg-white/90 p-6 shadow-panel dark:border-slate-700/50 dark:bg-navy-800/90">
                    <label class="block text-sm font-semibold text-brand-ink">Content</label>
                    <p class="mt-1 text-xs text-brand-muted">Use the toolbar to format text, add headings, lists, images, and links.</p>

                    <div class="mt-4">
                        <div id="editor"
                             class="rounded-2xl border border-warm-300/50 bg-warm-100 text-sm text-brand-ink dark:border-slate-600 dark:bg-navy-900/60"
                             style="min-height: 480px;">
                            {!! old('content') !!}
                        </div>
                    </div>

                    <textarea name="content" id="content" class="hidden">{{ old('content') }}</textarea>
                </div>

                {{-- Featured Image --}}
                <div class="rounded-3xl border border-white/70 bg-white/90 p-6 shadow-panel dark:border-slate-700/50 dark:bg-navy-800/90">
                    <label class="block text-sm font-semibold text-brand-ink">Featured Image</label>
                    <p class="mt-1 text-xs text-brand-muted">Recommended size: 1200 × 630px. Max 4 MB. JPG, PNG, WebP.</p>

                    <div class="mt-4">
                        <label for="featured_image"
                               class="flex cursor-pointer flex-col items-center justify-center gap-3 rounded-2xl border-2 border-dashed border-warm-300/50 bg-warm-200/50 py-10 transition hover:border-brand-primary hover:bg-accent/10 dark:border-slate-600 dark:bg-navy-900/40"
                               id="image-drop-label">
                            <svg class="h-8 w-8 text-brand-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z"/>
                            </svg>
                            <div class="text-center">
                                <p class="text-sm font-medium text-brand-ink">Click to upload</p>
                                <p class="text-xs text-brand-muted">or drag and drop</p>
                            </div>
                            <input type="file" name="featured_image" id="featured_image" accept="image/*" class="hidden">
                        </label>
                        {{-- Preview --}}
                        <div id="image-preview" class="mt-3 hidden">
                            <img id="image-preview-img" src="" alt="Preview" class="h-40 w-full rounded-2xl object-cover">
                            <button type="button" id="image-remove"
                                    class="mt-2 text-xs font-semibold text-red-500 hover:text-red-700">
                                Remove image
                            </button>
                        </div>
                        @error('featured_image')
                            <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- ── Sidebar ── --}}
            <div class="space-y-5">

                {{-- Publish / Status --}}
                <div class="rounded-3xl border border-white/70 bg-white/90 p-6 shadow-panel dark:border-slate-700/50 dark:bg-navy-800/90">
                    <h2 class="font-display text-base text-brand-ink">Status</h2>

                    <div class="mt-4 space-y-2">
                        <label class="flex cursor-pointer items-center gap-3 rounded-xl border border-warm-300/50 px-4 py-3 transition has-[:checked]:border-brand-primary has-[:checked]:bg-accent/10 dark:border-slate-600">
                            <input type="radio" name="status" value="published"
                                   class="text-brand-primary focus:ring-brand-primary"
                                   {{ old('status', 'draft') === 'published' ? 'checked' : '' }}>
                            <div>
                                <p class="text-sm font-semibold text-brand-ink">Published</p>
                                <p class="text-xs text-brand-muted">Visible to all visitors</p>
                            </div>
                        </label>
                        <label class="flex cursor-pointer items-center gap-3 rounded-xl border border-warm-300/50 px-4 py-3 transition has-[:checked]:border-brand-primary has-[:checked]:bg-accent/10 dark:border-slate-600">
                            <input type="radio" name="status" value="draft"
                                   class="text-brand-primary focus:ring-brand-primary"
                                   {{ old('status', 'draft') === 'draft' ? 'checked' : '' }}>
                            <div>
                                <p class="text-sm font-semibold text-brand-ink">Draft</p>
                                <p class="text-xs text-brand-muted">Hidden from public</p>
                            </div>
                        </label>
                    </div>

                    <div class="mt-5 flex flex-col gap-2">
                        <button type="submit" class="btn-primary w-full justify-center">
                            <svg class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                            Create Post
                        </button>
                        <a href="{{ route('admin.posts.index') }}" class="btn-secondary w-full justify-center text-center">Cancel</a>
                    </div>
                </div>

                {{-- SEO --}}
                <div class="rounded-3xl border border-white/70 bg-white/90 p-6 shadow-panel space-y-4 dark:border-slate-700/50 dark:bg-navy-800/90">
                    <h2 class="font-display text-base text-brand-ink">SEO</h2>

                    <div>
                        <label for="meta_title" class="block text-xs font-semibold text-brand-muted">Meta Title</label>
                        <input type="text" name="meta_title" id="meta_title" value="{{ old('meta_title') }}"
                               placeholder="Defaults to post title"
                               class="mt-1.5 w-full rounded-2xl border-warm-300/50 bg-warm-100 px-4 py-2.5 text-sm text-brand-ink shadow-sm transition focus:border-brand-primary focus:ring-brand-primary dark:border-slate-600 dark:bg-navy-900/60 dark:text-warm-100">
                        @error('meta_title')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="meta_description" class="block text-xs font-semibold text-brand-muted">Meta Description</label>
                        <textarea name="meta_description" id="meta_description" rows="3"
                                  placeholder="Brief summary for search engines (max 500 chars)"
                                  class="mt-1.5 w-full rounded-2xl border-warm-300/50 bg-warm-100 px-4 py-2.5 text-sm text-brand-ink shadow-sm transition focus:border-brand-primary focus:ring-brand-primary dark:border-slate-600 dark:bg-navy-900/60 dark:text-warm-100">{{ old('meta_description') }}</textarea>
                        @error('meta_description')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="rounded-2xl bg-warm-200/50 px-4 py-3 dark:bg-navy-900/40">
                        <p class="text-[10px] font-semibold uppercase tracking-wider text-brand-muted">Preview</p>
                        <p class="mt-1.5 text-sm font-semibold text-blue-700 underline dark:text-blue-400" id="seo-title-preview">{{ config('app.name') }}</p>
                        <p class="mt-0.5 text-xs text-green-700 dark:text-green-400" id="seo-url-preview">{{ url('/blog/') }}/</p>
                        <p class="mt-0.5 text-xs text-brand-muted line-clamp-2" id="seo-desc-preview">Meta description will appear here.</p>
                    </div>
                </div>

                @include('admin.partials.seo-assistant')
            </div>
        </div>
    </form>

    @push('scripts')
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css" rel="stylesheet">
    <style>
        /* Quill dark mode overrides */
        .dark .ql-toolbar.ql-snow { border-color: rgba(100,116,139,0.5); background: rgba(27,38,50,0.6); }
        .dark .ql-toolbar .ql-stroke { stroke: #C9C1B1; }
        .dark .ql-toolbar .ql-fill { fill: #C9C1B1; }
        .dark .ql-toolbar .ql-picker-label { color: #C9C1B1; }
        .dark .ql-toolbar .ql-picker-options { background: #1B2632; border-color: rgba(100,116,139,0.5); }
        .dark .ql-toolbar .ql-picker-item { color: #C9C1B1; }
        .dark .ql-toolbar button:hover .ql-stroke, .dark .ql-toolbar .ql-picker-label:hover .ql-stroke { stroke: #FFB162; }
        .dark .ql-toolbar button:hover .ql-fill { fill: #FFB162; }
        .dark .ql-toolbar button.ql-active .ql-stroke { stroke: #FFB162; }
        .dark .ql-toolbar button.ql-active .ql-fill { fill: #FFB162; }
        .dark .ql-container.ql-snow { border-color: rgba(100,116,139,0.5); }
        .dark .ql-editor { color: #EEE9DF; }
        .dark .ql-editor.ql-blank::before { color: rgba(201,193,177,0.5); }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>
    <script>
        // Quill editor
        const uploadEndpoint = '{{ route('admin.posts.upload-image') }}';

        async function uploadEditorImage(file) {
            const formData = new FormData();
            formData.append('image', file);

            const response = await fetch(uploadEndpoint, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                },
                body: formData,
            });

            if (!response.ok) {
                throw new Error('Image upload failed');
            }

            return response.json();
        }

        const quill = new Quill('#editor', {
            theme: 'snow',
            placeholder: 'Start writing your post…',
            modules: {
                toolbar: {
                    container: [
                        [{ header: [1, 2, 3, 4, false] }],
                        ['bold', 'italic', 'underline', 'strike'],
                        [{ list: 'ordered' }, { list: 'bullet' }],
                        ['blockquote', 'code-block'],
                        ['link', 'image'],
                        [{ align: [] }],
                        ['clean'],
                    ],
                    handlers: {
                        image: function () {
                            const input = document.createElement('input');
                            input.setAttribute('type', 'file');
                            input.setAttribute('accept', 'image/*');
                            input.click();

                            input.onchange = async () => {
                                const file = input.files?.[0];
                                if (!file) {
                                    return;
                                }

                                try {
                                    const payload = await uploadEditorImage(file);
                                    const range = quill.getSelection(true);
                                    quill.insertEmbed(range.index, 'image', payload.url, 'user');
                                    quill.setSelection(range.index + 1, 0);
                                } catch (error) {
                                    alert('Could not upload image. Please try again.');
                                }
                            };
                        },
                    },
                },
            },
        });

        document.getElementById('post-form').addEventListener('submit', function () {
            document.getElementById('content').value = quill.getSemanticHTML();
        });

        // Auto-generate slug
        const titleInput = document.getElementById('title');
        const slugInput  = document.getElementById('slug');
        let slugEdited = false;
        slugInput.addEventListener('input', () => { slugEdited = true; });
        titleInput.addEventListener('input', () => {
            if (slugEdited) return;
            slugInput.value = titleInput.value
                .toLowerCase().trim()
                .replace(/[^a-z0-9\s-]/g, '')
                .replace(/\s+/g, '-')
                .replace(/-+/g, '-');
        });

        // SEO preview
        const metaTitleEl = document.getElementById('meta_title');
        const metaDescEl  = document.getElementById('meta_description');
        const seoTitle    = document.getElementById('seo-title-preview');
        const seoUrl      = document.getElementById('seo-url-preview');
        const seoDesc     = document.getElementById('seo-desc-preview');
        const baseUrl     = '{{ url("/blog/") }}/';

        function updatePreview() {
            seoTitle.textContent = metaTitleEl.value || titleInput.value || '{{ config("app.name") }}';
            seoUrl.textContent   = baseUrl + (slugInput.value || '…');
            seoDesc.textContent  = metaDescEl.value || 'Meta description will appear here.';
        }
        [titleInput, slugInput, metaTitleEl, metaDescEl].forEach(el => el.addEventListener('input', updatePreview));
        updatePreview();

        // Image preview
        const imageInput   = document.getElementById('featured_image');
        const previewWrap  = document.getElementById('image-preview');
        const previewImg   = document.getElementById('image-preview-img');
        const removeBtn    = document.getElementById('image-remove');
        const dropLabel    = document.getElementById('image-drop-label');

        imageInput.addEventListener('change', function () {
            const file = this.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = e => {
                previewImg.src = e.target.result;
                previewWrap.classList.remove('hidden');
                dropLabel.classList.add('hidden');
            };
            reader.readAsDataURL(file);
        });

        removeBtn.addEventListener('click', function () {
            imageInput.value = '';
            previewImg.src = '';
            previewWrap.classList.add('hidden');
            dropLabel.classList.remove('hidden');
        });
    </script>
    @include('admin.partials.seo-assistant-script')
    @endpush
</x-app-layout>
