<x-public-layout :title="$category->name . ' Service'">

    @php $settings = \App\Models\Setting::first(); @endphp

    @push('head')
    <meta name="description" content="Professional {{ strtolower($category->name) }} services by {{ config('app.name') }}.">
    <link rel="canonical" href="{{ route('services.show', $category) }}">
    <meta property="og:type" content="website">
    <meta property="og:title" content="{{ $category->name }} Services - {{ config('app.name') }}">
    <meta property="og:description" content="Professional {{ strtolower($category->name) }} services with measurable delivery outcomes.">
    @if ($category->featured_image)
        <meta property="og:image" content="{{ asset('storage/' . $category->featured_image) }}">
    @endif
    <meta property="og:url" content="{{ route('services.show', $category) }}">
    @endpush

    <x-public-hero
        badge="Professional Service"
        :title="'Expert ' . $category->name"
        :subtitle="$settings?->heroSubtitle('service') ?: ($category->description ?: 'Scalable service delivery tailored to your goals, timeline, and growth plan.')"
        :media="$category->featured_image ? asset('storage/' . $category->featured_image) : $settings?->heroMediaUrl('service')"
    />

    <section class="border-b border-warm-300/40 bg-warm-100 py-8 dark:border-slate-700/40 dark:bg-navy-900">
        <div class="mx-auto grid max-w-7xl gap-4 px-4 sm:grid-cols-4 sm:px-8">
            <div class="rounded-2xl border border-warm-300/50 bg-warm-200/50 p-4 dark:border-slate-700/50 dark:bg-navy-800/50">
                <p class="text-xs font-semibold uppercase tracking-[0.14em] text-brand-primary">Completed Projects</p>
                <p class="mt-1 font-display text-3xl text-brand-ink">{{ $projects->count() }}+</p>
            </div>
            <div class="rounded-2xl border border-warm-300/50 bg-warm-200/50 p-4 dark:border-slate-700/50 dark:bg-navy-800/50">
                <p class="text-xs font-semibold uppercase tracking-[0.14em] text-brand-primary">Service Category</p>
                <p class="mt-1 text-sm font-semibold text-brand-ink">{{ $category->name }}</p>
            </div>
            <div class="rounded-2xl border border-warm-300/50 bg-warm-200/50 p-4 dark:border-slate-700/50 dark:bg-navy-800/50">
                <p class="text-xs font-semibold uppercase tracking-[0.14em] text-brand-primary">Price Range</p>
                <p class="mt-1 text-sm font-semibold text-brand-ink">{{ $category->price_range ?: 'Custom quote' }}</p>
            </div>
            <div class="rounded-2xl border border-warm-300/50 bg-warm-200/50 p-4 dark:border-slate-700/50 dark:bg-navy-800/50">
                <p class="text-xs font-semibold uppercase tracking-[0.14em] text-brand-primary">Estimated Duration</p>
                <p class="mt-1 text-sm font-semibold text-brand-ink">{{ $category->estimated_duration ?: 'Depends on scope' }}</p>
            </div>
        </div>
    </section>

    @if ($category->long_description)
        <section class="py-14 sm:py-18">
            <div class="mx-auto max-w-5xl px-4 sm:px-8">
                <article class="rounded-3xl border border-warm-300/50 bg-warm-100 p-7 shadow-sm sm:p-10 dark:border-slate-700/50 dark:bg-navy-800">
                    <h2 class="font-display text-3xl text-brand-ink">About This Service</h2>
                    <div class="prose prose-stone mt-5 max-w-none text-sm leading-8 text-brand-muted">
                        {!! nl2br(e($category->long_description)) !!}
                    </div>
                </article>
            </div>
        </section>
    @endif

    @if (!empty($category->gallery_images))
        <section class="border-y border-warm-300/40 bg-warm-200/50 py-14 sm:py-18 dark:border-slate-700/40 dark:bg-navy-900/60">
            <div class="mx-auto max-w-7xl px-4 sm:px-8">
                <div class="mb-6 flex items-end justify-between gap-3">
                    <h2 class="font-display text-3xl text-brand-ink">Service Gallery</h2>
                    <p class="text-xs font-semibold uppercase tracking-[0.14em] text-brand-muted">{{ count($category->gallery_images) }} media item(s)</p>
                </div>
                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($category->gallery_images as $galleryImage)
                        <a href="{{ asset('storage/' . $galleryImage) }}" target="_blank" rel="noopener" class="group block overflow-hidden rounded-2xl border border-warm-300/50 bg-warm-100 shadow-sm dark:border-slate-700/50 dark:bg-navy-800">
                            <img src="{{ asset('storage/' . $galleryImage) }}" alt="{{ $category->name }} gallery" class="h-56 w-full object-cover transition duration-300 group-hover:scale-105">
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <section id="projects" class="py-14 sm:py-20">
        <div class="mx-auto max-w-7xl px-4 sm:px-8">
            <div class="mb-8 flex flex-wrap items-end justify-between gap-3">
                <div>
                    <h2 class="font-display text-3xl text-brand-ink">Related Project Work</h2>
                    <p class="mt-2 text-sm text-brand-muted">Recent completed projects under {{ $category->name }}.</p>
                </div>
                <a href="{{ route('portfolio.public') }}" class="btn-secondary">View Portfolio</a>
            </div>

            @if ($projects->isEmpty())
                <div class="rounded-3xl border border-dashed border-warm-400/50 bg-warm-100 p-14 text-center dark:border-slate-600/50 dark:bg-navy-800">
                    <h3 class="font-display text-2xl text-brand-ink">No completed projects yet</h3>
                    <p class="mt-2 text-sm text-brand-muted">Projects will appear here once marked completed.</p>
                </div>
            @else
                <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($projects as $project)
                        <article class="overflow-hidden rounded-3xl border border-warm-300/50 bg-warm-100 shadow-sm dark:border-slate-700/50 dark:bg-navy-800">
                            <div class="h-48 overflow-hidden bg-warm-200/50 dark:bg-navy-800/50">
                                @if ($project->featured_image)
                                    <img src="{{ asset('storage/' . $project->featured_image) }}" alt="{{ $project->title }}" class="h-full w-full object-cover">
                                @endif
                            </div>
                            <div class="p-5">
                                <h3 class="font-display text-xl text-brand-ink">{{ $project->title }}</h3>
                                <p class="mt-2 line-clamp-3 text-sm leading-7 text-brand-muted">{{ $project->description ?: 'Completed service project delivered to client requirements.' }}</p>
                                @if ($project->client)
                                    <p class="mt-3 text-xs font-semibold uppercase tracking-[0.12em] text-brand-primary">Client: {{ $project->client->name }}</p>
                                @endif
                            </div>
                        </article>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    @if ($relatedCategories->isNotEmpty())
        <section class="border-t border-warm-300/40 bg-warm-200/50 py-14 sm:py-18 dark:border-slate-700/40 dark:bg-navy-900/60">
            <div class="mx-auto max-w-7xl px-4 sm:px-8">
                <h2 class="font-display text-3xl text-brand-ink">Explore Other Services</h2>
                <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($relatedCategories as $related)
                        <a href="{{ route('services.show', $related) }}" class="rounded-2xl border border-warm-300/50 bg-warm-100 p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-card dark:border-slate-700/50 dark:bg-navy-800">
                            <p class="font-display text-xl text-brand-ink">{{ $related->name }}</p>
                            <p class="mt-2 text-sm text-brand-muted">{{ $related->projects_count }} project{{ $related->projects_count !== 1 ? 's' : '' }}</p>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

</x-public-layout>
