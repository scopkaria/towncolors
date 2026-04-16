<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.posts.index') }}" class="flex h-9 w-9 items-center justify-center rounded-xl border border-warm-300/50 bg-warm-100 text-brand-muted shadow-sm transition hover:border-brand-primary hover:text-brand-primary">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/></svg>
            </a>
            <div class="space-y-1">
                <span class="inline-flex rounded-full border border-accent/30 bg-accent-light px-3 py-0.5 text-[11px] font-semibold uppercase tracking-[0.32em] text-brand-primary">Blog</span>
                <h1 class="font-display text-2xl text-brand-ink sm:text-3xl">Edit: {{ $post->title }}</h1>
            </div>
        </div>
    </x-slot>

    <form method="POST" action="{{ route('admin.posts.update', $post) }}" id="post-form" enctype="multipart/form-data">
        @csrf @method('PATCH')

        <div class="grid gap-6 lg:grid-cols-3">

            {{-- ── Main content ── --}}
            <div class="lg:col-span-2 space-y-5">

                {{-- Title + Slug --}}
                <div class="rounded-3xl border border-white/70 bg-white/90 p-6 shadow-panel space-y-4 dark:border-slate-700/50 dark:bg-navy-800/90">
                    <div>
                        <label for="title" class="block text-sm font-semibold text-brand-ink">
                            Post Title <span class="text-red-400">*</span>
                        </label>
                        <input type="text" name="title" id="title" value="{{ old('title', $post->title) }}" required
                               class="mt-2 w-full rounded-2xl border-warm-300/50 bg-warm-100 px-4 py-3 text-sm text-brand-ink shadow-sm transition focus:border-brand-primary focus:ring-brand-primary dark:border-slate-600 dark:bg-navy-900/60 dark:text-warm-100">
                        @error('title')
                            <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="slug" class="block text-sm font-semibold text-brand-ink">
                            URL Slug
                            <span class="ml-1 text-xs font-normal text-brand-muted">(changing this will break existing links)</span>
                        </label>
                        <div class="mt-2 flex items-center rounded-2xl border border-warm-300/50 bg-warm-100 shadow-sm transition focus-within:border-brand-primary focus-within:ring-1 focus-within:ring-brand-primary dark:border-slate-600 dark:bg-navy-900/60">
                            <span class="select-none border-r border-warm-300/50 px-3 py-3 text-sm text-brand-muted dark:border-slate-600">/blog/</span>
                            <input type="text" name="slug" id="slug" value="{{ old('slug', $post->slug) }}"
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
                            {!! old('content', $post->content) !!}
                        </div>
                    </div>

                    <textarea name="content" id="content" class="hidden">{{ old('content', $post->content) }}</textarea>
                </div>

                {{-- Featured Image --}}
                <div class="rounded-3xl border border-white/70 bg-white/90 p-6 shadow-panel dark:border-slate-700/50 dark:bg-navy-800/90">
                    <label class="block text-sm font-semibold text-brand-ink">Featured Image</label>
                    <p class="mt-1 text-xs text-brand-muted">Upload a new image to replace the current one. Max 4 MB.</p>

                    <div class="mt-4">
                        {{-- Current image --}}
                        @if ($post->featured_image)
                            <div class="mb-4" id="current-image-wrap">
                                <p class="mb-2 text-xs font-semibold text-brand-muted">Current image</p>
                                <img src="{{ Storage::url($post->featured_image) }}"
                                     alt="{{ $post->title }}"
                                     class="h-40 w-full rounded-2xl object-cover">
                            </div>
                        @endif

                        <label for="featured_image"
                               class="flex cursor-pointer flex-col items-center justify-center gap-3 rounded-2xl border-2 border-dashed border-warm-300/50 bg-warm-200/50 py-8 transition hover:border-brand-primary hover:bg-accent/10 dark:border-slate-600 dark:bg-navy-900/40"
                               id="image-drop-label">
                            <svg class="h-7 w-7 text-brand-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0 4.5 4.5M12 3v13.5"/>
                            </svg>
                            <p class="text-sm font-medium text-brand-ink">Click to upload new image</p>
                            <input type="file" name="featured_image" id="featured_image" accept="image/*" class="hidden">
                        </label>
                        <div id="image-preview" class="mt-3 hidden">
                            <p class="mb-2 text-xs font-semibold text-emerald-600">New image selected</p>
                            <img id="image-preview-img" src="" alt="Preview" class="h-40 w-full rounded-2xl object-cover">
                            <button type="button" id="image-remove"
                                    class="mt-2 text-xs font-semibold text-red-500 hover:text-red-700">
                                Remove selection
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

                {{-- Status --}}
                <div class="rounded-3xl border border-white/70 bg-white/90 p-6 shadow-panel dark:border-slate-700/50 dark:bg-navy-800/90">
                    <h2 class="font-display text-base text-brand-ink">Status</h2>

                    <div class="mt-4 space-y-2">
                        <label class="flex cursor-pointer items-center gap-3 rounded-xl border border-warm-300/50 px-4 py-3 transition has-[:checked]:border-brand-primary has-[:checked]:bg-accent/10 dark:border-slate-600">
                            <input type="radio" name="status" value="published"
                                   class="text-brand-primary focus:ring-brand-primary"
                                   {{ old('status', $post->status) === 'published' ? 'checked' : '' }}>
                            <div>
                                <p class="text-sm font-semibold text-brand-ink">Published</p>
                                <p class="text-xs text-brand-muted">Visible to all visitors</p>
                            </div>
                        </label>
                        <label class="flex cursor-pointer items-center gap-3 rounded-xl border border-warm-300/50 px-4 py-3 transition has-[:checked]:border-brand-primary has-[:checked]:bg-accent/10 dark:border-slate-600">
                            <input type="radio" name="status" value="draft"
                                   class="text-brand-primary focus:ring-brand-primary"
                                   {{ old('status', $post->status) === 'draft' ? 'checked' : '' }}>
                            <div>
                                <p class="text-sm font-semibold text-brand-ink">Draft</p>
                                <p class="text-xs text-brand-muted">Hidden from public</p>
                            </div>
                        </label>
                    </div>

                    <div class="mt-5 flex flex-col gap-2">
                        <button type="submit" class="btn-primary w-full justify-center">
                            <svg class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                            Save Changes
                        </button>
                        @if ($post->isPublished())
                            <a href="{{ route('blog.show', $post) }}" target="_blank"
                               class="btn-secondary w-full justify-center text-center">
                                <svg class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/></svg>
                                View Live
                            </a>
                        @endif
                    </div>

                    <div class="mt-4 border-t border-warm-300/40 pt-4 dark:border-slate-700/50">
                        <p class="text-xs text-brand-muted">Delete is separated to avoid accidental destructive submission.</p>
                    </div>
                </div>

                {{-- SEO --}}
                <div class="rounded-3xl border border-white/70 bg-white/90 p-6 shadow-panel space-y-4 dark:border-slate-700/50 dark:bg-navy-800/90">
                    <h2 class="font-display text-base text-brand-ink">SEO</h2>

                    <div>
                        <label for="meta_title" class="block text-xs font-semibold text-brand-muted">Meta Title</label>
                        <input type="text" name="meta_title" id="meta_title"
                               value="{{ old('meta_title', $post->meta_title) }}"
                               placeholder="Defaults to post title"
                               class="mt-1.5 w-full rounded-2xl border-warm-300/50 bg-warm-100 px-4 py-2.5 text-sm text-brand-ink shadow-sm transition focus:border-brand-primary focus:ring-brand-primary dark:border-slate-600 dark:bg-navy-900/60 dark:text-warm-100">
                        @error('meta_title')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="meta_description" class="block text-xs font-semibold text-brand-muted">Meta Description</label>
                        <textarea name="meta_description" id="meta_description" rows="3"
                                  class="mt-1.5 w-full rounded-2xl border-warm-300/50 bg-warm-100 px-4 py-2.5 text-sm text-brand-ink shadow-sm transition focus:border-brand-primary focus:ring-brand-primary dark:border-slate-600 dark:bg-navy-900/60 dark:text-warm-100">{{ old('meta_description', $post->meta_description) }}</textarea>
                        @error('meta_description')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="rounded-2xl bg-warm-200/50 px-4 py-3 dark:bg-navy-900/40">
                        <p class="text-[10px] font-semibold uppercase tracking-wider text-brand-muted">Preview</p>
                        <p class="mt-1.5 text-sm font-semibold text-blue-700 underline dark:text-blue-400" id="seo-title-preview">{{ $post->meta_title ?: $post->title }}</p>
                        <p class="mt-0.5 text-xs text-green-700 dark:text-green-400" id="seo-url-preview">{{ url('/blog/' . $post->slug) }}</p>
                        <p class="mt-0.5 text-xs text-brand-muted line-clamp-2" id="seo-desc-preview">{{ $post->meta_description ?: 'Meta description will appear here.' }}</p>
                    </div>
                </div>

                {{-- Timestamps --}}
                <div class="rounded-3xl border border-white/70 bg-white/90 p-6 shadow-panel dark:border-slate-700/50 dark:bg-navy-800/90">
                    <h2 class="font-display text-base text-brand-ink">Details</h2>
                    <dl class="mt-4 space-y-2 text-xs">
                        <div class="flex items-center justify-between">
                            <dt class="text-brand-muted">Created</dt>
                            <dd class="font-medium text-brand-ink">{{ $post->created_at->format('M j, Y') }}</dd>
                        </div>
                        @if ($post->published_at)
                        <div class="flex items-center justify-between">
                            <dt class="text-brand-muted">Published</dt>
                            <dd class="font-medium text-brand-ink">{{ $post->published_at->format('M j, Y') }}</dd>
                        </div>
                        @endif
                        <div class="flex items-center justify-between">
                            <dt class="text-brand-muted">Last updated</dt>
                            <dd class="font-medium text-brand-ink">{{ $post->updated_at->diffForHumans() }}</dd>
                        </div>
                    </dl>
                </div>

                @include('admin.partials.seo-assistant')
            </div>
        </div>
    </form>

    <form method="POST" action="{{ route('admin.posts.destroy', $post) }}"
          onsubmit="return confirm('Permanently delete this post?')"
          class="mt-4">
        @csrf
        @method('DELETE')
        <button type="submit"
                class="rounded-2xl border border-red-100 bg-red-50 px-4 py-2 text-xs font-semibold text-red-600 transition hover:bg-red-100 dark:border-red-900/50 dark:bg-red-950/50 dark:text-red-400 dark:hover:bg-red-900/40">
            Delete Post
        </button>
    </form>

    @push('scripts')
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css" rel="stylesheet">
    <style>
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

        // SEO preview
        const titleInput  = document.getElementById('title');
        const slugInput   = document.getElementById('slug');
        const metaTitleEl = document.getElementById('meta_title');
        const metaDescEl  = document.getElementById('meta_description');
        const seoTitle    = document.getElementById('seo-title-preview');
        const seoUrl      = document.getElementById('seo-url-preview');
        const seoDesc     = document.getElementById('seo-desc-preview');

        function updatePreview() {
            seoTitle.textContent = metaTitleEl.value || titleInput.value || '{{ config("app.name") }}';
            seoUrl.textContent   = '{{ url("/blog/") }}/' + (slugInput.value || '…');
            seoDesc.textContent  = metaDescEl.value || 'Meta description will appear here.';
        }
        [titleInput, slugInput, metaTitleEl, metaDescEl].forEach(el => el.addEventListener('input', updatePreview));
        updatePreview();

        // Image preview
        const imageInput  = document.getElementById('featured_image');
        const previewWrap = document.getElementById('image-preview');
        const previewImg  = document.getElementById('image-preview-img');
        const removeBtn   = document.getElementById('image-remove');
        const dropLabel   = document.getElementById('image-drop-label');

        imageInput.addEventListener('change', function () {
            const file = this.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = e => {
                previewImg.src = e.target.result;
                previewWrap.classList.remove('hidden');
            };
            reader.readAsDataURL(file);
        });

        removeBtn.addEventListener('click', function () {
            imageInput.value = '';
            previewImg.src = '';
            previewWrap.classList.add('hidden');
        });
    </script>
    @include('admin.partials.seo-assistant-script')
    @endpush
</x-app-layout>
