<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.pages.index') }}" class="flex h-9 w-9 items-center justify-center rounded-xl border border-stone-200 bg-white text-brand-muted shadow-sm transition hover:border-brand-primary hover:text-brand-primary">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/></svg>
            </a>
            <div class="space-y-1">
                <span class="inline-flex rounded-full border border-orange-200 bg-orange-50 px-3 py-0.5 text-[11px] font-semibold uppercase tracking-[0.32em] text-brand-primary">CMS</span>
                <h1 class="font-display text-2xl text-brand-ink sm:text-3xl">Edit: {{ $page->title }}</h1>
            </div>
        </div>
    </x-slot>

    <form method="POST" action="{{ route('admin.pages.update', $page) }}" id="page-form">
        @csrf @method('PATCH')

        <div class="grid gap-6 lg:grid-cols-3">

            {{-- ── Main content ── --}}
            <div class="lg:col-span-2 space-y-5">

                {{-- Title + slug --}}
                <div class="rounded-3xl border border-white/70 bg-white/90 p-6 shadow-panel space-y-4">
                    <div>
                        <label for="title" class="block text-sm font-semibold text-brand-ink">
                            Page Title <span class="text-red-400">*</span>
                        </label>
                        <input type="text" name="title" id="title" value="{{ old('title', $page->title) }}" required
                               class="mt-2 w-full rounded-2xl border-stone-200 bg-white px-4 py-3 text-sm text-brand-ink shadow-sm transition focus:border-brand-primary focus:ring-brand-primary">
                        @error('title')
                            <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="slug" class="block text-sm font-semibold text-brand-ink">
                            URL Slug
                            <span class="ml-1 text-xs font-normal text-brand-muted">(changing this will break existing links)</span>
                        </label>
                        <div class="mt-2 flex items-center rounded-2xl border border-stone-200 bg-white shadow-sm transition focus-within:border-brand-primary focus-within:ring-1 focus-within:ring-brand-primary">
                            <span class="select-none border-r border-stone-200 px-3 py-3 text-sm text-brand-muted">/page/</span>
                            <input type="text" name="slug" id="slug" value="{{ old('slug', $page->slug) }}"
                                   class="flex-1 rounded-r-2xl border-0 bg-transparent px-3 py-3 font-mono text-sm text-brand-ink focus:ring-0">
                        </div>
                        @error('slug')
                            <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Rich text content --}}
                <div class="rounded-3xl border border-white/70 bg-white/90 p-6 shadow-panel">
                    <label class="block text-sm font-semibold text-brand-ink">Content</label>
                    <p class="mt-1 text-xs text-brand-muted">Use the toolbar to format text, add headings, lists, and links.</p>

                    <div class="mt-4">
                        <div id="editor"
                             class="rounded-2xl border border-stone-200 bg-white text-sm text-brand-ink"
                             style="min-height: 420px;">
                            {!! old('content', $page->content) !!}
                        </div>
                    </div>

                    <textarea name="content" id="content" class="hidden">{{ old('content', $page->content) }}</textarea>
                </div>
            </div>

            {{-- ── Sidebar ── --}}
            <div class="space-y-5">

                {{-- Publish controls --}}
                <div class="rounded-3xl border border-white/70 bg-white/90 p-6 shadow-panel">
                    <h2 class="font-display text-base text-brand-ink">Visibility</h2>

                    <div class="mt-4 flex items-center justify-between">
                        <span class="text-sm text-brand-muted">Published</span>
                        <label class="relative inline-flex cursor-pointer items-center">
                            <input type="hidden" name="is_published" value="0">
                            <input type="checkbox" name="is_published" value="1"
                                   class="sr-only peer"
                                   {{ old('is_published', $page->is_published ? '1' : '0') == '1' ? 'checked' : '' }}>
                            <div class="peer h-6 w-11 rounded-full bg-stone-200 transition after:absolute after:left-[2px] after:top-[2px] after:h-5 after:w-5 after:rounded-full after:bg-white after:shadow-sm after:transition peer-checked:bg-brand-primary peer-checked:after:translate-x-full"></div>
                        </label>
                    </div>

                    <div class="mt-5 flex flex-col gap-2">
                        <button type="submit" class="btn-primary w-full justify-center">
                            <svg class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                            Save Changes
                        </button>
                        <a href="{{ route('pages.show', $page) }}" target="_blank"
                           class="btn-secondary w-full justify-center text-center">
                            <svg class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/></svg>
                            View Live
                        </a>
                    </div>

                    <div class="mt-4 border-t border-stone-100 pt-4">
                        <form method="POST" action="{{ route('admin.pages.destroy', $page) }}"
                              onsubmit="return confirm('Permanently delete this page?')">
                            @csrf @method('DELETE')
                            <button type="submit"
                                    class="w-full rounded-2xl border border-red-100 bg-red-50 px-4 py-2 text-xs font-semibold text-red-600 transition hover:bg-red-100">
                                Delete Page
                            </button>
                        </form>
                    </div>
                </div>

                {{-- SEO --}}
                <div class="rounded-3xl border border-white/70 bg-white/90 p-6 shadow-panel space-y-4">
                    <h2 class="font-display text-base text-brand-ink">SEO</h2>

                    <div>
                        <label for="meta_title" class="block text-xs font-semibold text-brand-muted">Meta Title</label>
                        <input type="text" name="meta_title" id="meta_title"
                               value="{{ old('meta_title', $page->meta_title) }}"
                               placeholder="Defaults to page title"
                               class="mt-1.5 w-full rounded-2xl border-stone-200 bg-white px-4 py-2.5 text-sm text-brand-ink shadow-sm transition focus:border-brand-primary focus:ring-brand-primary">
                        @error('meta_title')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="meta_description" class="block text-xs font-semibold text-brand-muted">Meta Description</label>
                        <textarea name="meta_description" id="meta_description" rows="3"
                                  class="mt-1.5 w-full rounded-2xl border-stone-200 bg-white px-4 py-2.5 text-sm text-brand-ink shadow-sm transition focus:border-brand-primary focus:ring-brand-primary">{{ old('meta_description', $page->meta_description) }}</textarea>
                        @error('meta_description')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="rounded-2xl bg-stone-50 px-4 py-3">
                        <p class="text-[10px] font-semibold uppercase tracking-wider text-brand-muted">Preview</p>
                        <p class="mt-1.5 text-sm font-semibold text-blue-700 underline" id="seo-title-preview">{{ $page->meta_title ?: $page->title }}</p>
                        <p class="mt-0.5 text-xs text-green-700" id="seo-url-preview">{{ url('/page/' . $page->slug) }}</p>
                        <p class="mt-0.5 text-xs text-brand-muted line-clamp-2" id="seo-desc-preview">{{ $page->meta_description ?: 'Meta description will appear here.' }}</p>
                    </div>
                </div>

                @include('admin.partials.seo-assistant')
            </div>
        </div>
    </form>

    @push('scripts')
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>
    <script>
        const quill = new Quill('#editor', {
            theme: 'snow',
            placeholder: 'Start writing…',
            modules: {
                toolbar: [
                    [{ heading: [1, 2, 3, 4, false] }],
                    ['bold', 'italic', 'underline', 'strike'],
                    [{ list: 'ordered' }, { list: 'bullet' }],
                    ['blockquote', 'code-block'],
                    ['link'],
                    [{ align: [] }],
                    ['clean'],
                ],
            },
        });

        document.getElementById('page-form').addEventListener('submit', function () {
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
        const baseUrl     = '{{ url("/page/") }}/';

        function updatePreview() {
            seoTitle.textContent = metaTitleEl.value || titleInput.value || '{{ config("app.name") }}';
            seoUrl.textContent   = baseUrl + (slugInput.value || '…');
            seoDesc.textContent  = metaDescEl.value || 'Meta description will appear here.';
        }
        [titleInput, slugInput, metaTitleEl, metaDescEl].forEach(el => el.addEventListener('input', updatePreview));
    </script>
    @include('admin.partials.seo-assistant-script')
    @endpush
</x-app-layout>
