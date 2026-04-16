<x-app-layout>
    <x-slot name="header">
        <div class="space-y-3">
            <span class="inline-flex w-fit rounded-full border border-accent/30 bg-accent-light px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.32em] text-brand-primary">Website</span>
            <h1 class="font-display text-3xl text-brand-ink sm:text-4xl">FAQ Management</h1>
            <p class="max-w-2xl text-sm leading-7 text-brand-muted">Create FAQ entries, attach one or many categories, and manage grouped sections.</p>
        </div>
    </x-slot>

    @if (session('success'))
        <div class="mb-5 rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-medium text-emerald-700">{{ session('success') }}</div>
    @endif

    @php
        $groupedFaqs = collect($faqs)->flatMap(function ($faq) {
            return collect($faq->categories_list)->map(fn ($category) => [
                'category' => $category,
                'faq' => $faq,
            ]);
        })->groupBy('category')->sortKeys();
    @endphp

    <div class="grid gap-6 lg:grid-cols-3" x-data="faqCategoryPicker(@js($categoryOptions->values()))">
        <div class="lg:col-span-1">
            <div class="sticky top-24 rounded-3xl border border-white/70 bg-white/90 p-6 shadow-panel">
                <h2 class="font-display text-lg text-brand-ink">Add FAQ</h2>
                <form method="POST" action="{{ route('admin.faq.store') }}" class="mt-6 space-y-4">
                    @csrf

                    <div>
                        <label class="block text-sm font-semibold text-brand-ink">Categories</label>
                        <select class="mt-2 w-full rounded-2xl border-warm-300/50 bg-warm-100 px-4 py-3 text-sm text-brand-ink shadow-sm focus:border-brand-primary focus:ring-brand-primary"
                                @change="addCategory($event.target.value); $event.target.value=''">
                            <option value="">Select category</option>
                            <template x-for="option in options" :key="option">
                                <option :value="option" x-text="option"></option>
                            </template>
                        </select>

                        <input type="text"
                               x-model="newCategory"
                               @keydown.enter.prevent="addCategory(newCategory); newCategory=''"
                               placeholder="Add custom category and press Enter"
                               class="mt-2 w-full rounded-2xl border-warm-300/50 bg-warm-100 px-4 py-3 text-sm text-brand-ink shadow-sm focus:border-brand-primary focus:ring-brand-primary">

                        <div class="mt-2 flex flex-wrap gap-2">
                            <template x-for="category in selected" :key="category">
                                <span class="inline-flex items-center gap-1 rounded-full border border-accent/30 bg-accent-light px-3 py-1 text-xs font-semibold text-brand-primary">
                                    <span x-text="category"></span>
                                    <button type="button" @click="removeCategory(category)" class="text-brand-primary/70 hover:text-brand-primary">�</button>
                                </span>
                            </template>
                        </div>

                        <template x-for="category in selected" :key="'input-' + category">
                            <input type="hidden" name="categories[]" :value="category">
                        </template>

                        <input type="hidden" name="category" :value="selected[0] || 'General'">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-brand-ink">Question</label>
                        <input type="text" name="question" value="{{ old('question') }}" required class="mt-2 w-full rounded-2xl border-warm-300/50 bg-warm-100 px-4 py-3 text-sm text-brand-ink shadow-sm focus:border-brand-primary focus:ring-brand-primary">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-brand-ink">Answer</label>
                        <textarea name="answer" rows="5" required class="mt-2 w-full rounded-2xl border-warm-300/50 bg-warm-100 px-4 py-3 text-sm text-brand-ink shadow-sm focus:border-brand-primary focus:ring-brand-primary">{{ old('answer') }}</textarea>
                    </div>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="block text-sm font-semibold text-brand-ink">Sort</label>
                            <input type="number" name="sort_order" min="0" value="{{ old('sort_order', 0) }}" class="mt-2 w-full rounded-2xl border-warm-300/50 bg-warm-100 px-4 py-3 text-sm text-brand-ink shadow-sm focus:border-brand-primary focus:ring-brand-primary">
                        </div>
                        <label class="mt-8 flex items-center gap-2 text-sm font-semibold text-brand-ink">
                            <input type="checkbox" name="is_active" value="1" checked class="h-4 w-4 rounded border-warm-400/50 text-brand-primary focus:ring-brand-primary">
                            Active
                        </label>
                    </div>
                    <button type="submit" class="btn-primary w-full justify-center">Save FAQ</button>
                </form>
            </div>
        </div>

        <div class="space-y-4 lg:col-span-2">
            @forelse ($groupedFaqs as $category => $rows)
                <section class="rounded-3xl border border-white/70 bg-white/90 p-5 shadow-card" x-data="{ open: true }">
                    <button type="button" @click="open = !open" class="flex w-full items-center justify-between gap-3 text-left">
                        <div>
                            <h2 class="font-display text-xl text-brand-ink">{{ $category }}</h2>
                            <p class="text-xs text-brand-muted">{{ $rows->count() }} FAQ{{ $rows->count() !== 1 ? 's' : '' }}</p>
                        </div>
                        <svg class="h-5 w-5 text-brand-muted transition" :class="open ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6"/>
                        </svg>
                    </button>

                    <div class="mt-4 space-y-3" x-show="open" x-transition>
                        @foreach ($rows as $row)
                            @php($faq = $row['faq'])
                            <div class="rounded-2xl border border-warm-300/50 bg-warm-100 p-4">
                                <form method="POST" action="{{ route('admin.faq.update', $faq) }}" class="space-y-3" x-data="faqEditPicker(@js($categoryOptions->values()), @js($faq->categories_list))">
                                    @csrf
                                    @method('PATCH')

                                    <div>
                                        <label class="block text-xs font-semibold uppercase tracking-[0.12em] text-brand-muted">Categories</label>
                                        <select class="mt-2 w-full rounded-xl border-warm-300/50 bg-warm-100 px-3 py-2 text-xs font-semibold text-brand-ink shadow-sm focus:border-brand-primary focus:ring-brand-primary"
                                                @change="addCategory($event.target.value); $event.target.value=''">
                                            <option value="">Select category</option>
                                            <template x-for="option in options" :key="option">
                                                <option :value="option" x-text="option"></option>
                                            </template>
                                        </select>

                                        <input type="text"
                                               x-model="newCategory"
                                               @keydown.enter.prevent="addCategory(newCategory); newCategory=''"
                                               placeholder="Type category and press Enter"
                                               class="mt-2 w-full rounded-xl border-warm-300/50 bg-warm-100 px-3 py-2 text-sm text-brand-ink shadow-sm focus:border-brand-primary focus:ring-brand-primary">

                                        <div class="mt-2 flex flex-wrap gap-2">
                                            <template x-for="categoryName in selected" :key="categoryName">
                                                <span class="inline-flex items-center gap-1 rounded-full border border-accent/30 bg-accent-light px-2.5 py-1 text-[11px] font-semibold text-brand-primary">
                                                    <span x-text="categoryName"></span>
                                                    <button type="button" @click="removeCategory(categoryName)">�</button>
                                                </span>
                                            </template>
                                        </div>

                                        <template x-for="categoryName in selected" :key="'hidden-' + categoryName">
                                            <input type="hidden" name="categories[]" :value="categoryName">
                                        </template>
                                        <input type="hidden" name="category" :value="selected[0] || 'General'">
                                    </div>

                                    <div class="grid gap-3 sm:grid-cols-2">
                                        <input type="number" name="sort_order" value="{{ $faq->sort_order }}" min="0" class="rounded-xl border-warm-300/50 bg-warm-100 px-3 py-2 text-sm text-brand-ink shadow-sm focus:border-brand-primary focus:ring-brand-primary">
                                        <label class="flex items-center gap-2 rounded-xl border border-warm-300/50 bg-warm-200/50 px-3 py-2 text-sm font-semibold text-brand-ink">
                                            <input type="checkbox" name="is_active" value="1" {{ $faq->is_active ? 'checked' : '' }} class="h-4 w-4 rounded border-warm-400/50 text-brand-primary focus:ring-brand-primary">
                                            Active
                                        </label>
                                    </div>

                                    <input type="text" name="question" value="{{ $faq->question }}" class="w-full rounded-xl border-warm-300/50 bg-warm-100 px-3 py-2 text-sm font-semibold text-brand-ink shadow-sm focus:border-brand-primary focus:ring-brand-primary">
                                    <textarea name="answer" rows="3" class="w-full rounded-xl border-warm-300/50 bg-warm-100 px-3 py-2 text-sm text-brand-muted shadow-sm focus:border-brand-primary focus:ring-brand-primary">{{ $faq->answer }}</textarea>

                                    <div class="flex flex-wrap gap-2">
                                        <button type="submit" class="btn-primary py-2 text-xs">Update</button>
                                    </div>
                                </form>

                                <form method="POST" action="{{ route('admin.faq.destroy', $faq) }}" onsubmit="return confirm('Delete this FAQ?')" class="mt-2">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="rounded-xl border border-red-200 bg-red-50 px-3 py-2 text-xs font-semibold uppercase tracking-[0.12em] text-red-600 transition hover:bg-red-100">Delete</button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                </section>
            @empty
                <div class="rounded-3xl border border-dashed border-warm-300/50 bg-white/80 p-12 text-center">
                    <h3 class="font-display text-xl text-brand-ink">No FAQ records yet</h3>
                    <p class="mt-2 text-sm text-brand-muted">Add your first entry using the form on the left.</p>
                </div>
            @endforelse
        </div>
    </div>

    @push('scripts')
    <script>
        function normalizeCategory(value) {
            return String(value || '').trim();
        }

        function faqCategoryPicker(initialOptions) {
            return {
                options: initialOptions || [],
                selected: ['General'],
                newCategory: '',
                addCategory(value) {
                    const category = normalizeCategory(value);
                    if (!category || this.selected.includes(category)) {
                        return;
                    }
                    this.selected.push(category);
                    if (!this.options.includes(category)) {
                        this.options.push(category);
                    }
                },
                removeCategory(value) {
                    this.selected = this.selected.filter(item => item !== value);
                    if (!this.selected.length) {
                        this.selected = ['General'];
                    }
                },
            };
        }

        function faqEditPicker(initialOptions, selectedCategories) {
            return {
                options: initialOptions || [],
                selected: (selectedCategories || []).length ? selectedCategories : ['General'],
                newCategory: '',
                addCategory(value) {
                    const category = normalizeCategory(value);
                    if (!category || this.selected.includes(category)) {
                        return;
                    }
                    this.selected.push(category);
                    if (!this.options.includes(category)) {
                        this.options.push(category);
                    }
                },
                removeCategory(value) {
                    this.selected = this.selected.filter(item => item !== value);
                    if (!this.selected.length) {
                        this.selected = ['General'];
                    }
                },
            };
        }
    </script>
    @endpush
</x-app-layout>
