<x-public-layout title="Blog">

    @push('head')
    <meta name="description" content="Read the latest articles, tips, and insights from {{ config('app.name') }}.">
    <link rel="canonical" href="{{ route('blog.index') }}">
    <meta property="og:type" content="website">
    <meta property="og:title" content="Blog · {{ config('app.name') }}">
    <meta property="og:url" content="{{ route('blog.index') }}">
    @endpush

    {{-- ===================== HERO ===================== --}}
    <section class="relative overflow-hidden bg-brand-ink py-20 sm:py-28">
        <div class="pointer-events-none absolute -top-24 -right-24 h-80 w-80 rounded-full bg-brand-primary/10 blur-3xl"></div>
        <div class="pointer-events-none absolute -bottom-24 -left-24 h-72 w-72 rounded-full bg-brand-primary/8 blur-3xl"></div>

        <div class="relative mx-auto max-w-3xl px-4 text-center sm:px-6 lg:px-8">
            <span class="mb-4 inline-block rounded-full border border-brand-primary/30 bg-brand-primary/10 px-4 py-1.5 text-xs font-semibold uppercase tracking-widest text-brand-primary reveal">
                Blog
            </span>
            <h1 class="font-display text-[1.75rem] font-bold leading-tight text-white sm:text-4xl lg:text-5xl reveal reveal-delay-1">
                Insights &amp; <span class="text-brand-primary">Articles</span>
            </h1>
            <p class="mt-4 text-base text-white/60 sm:text-lg reveal reveal-delay-2">
                Tips, stories, and updates from our team and community.
            </p>
        </div>
    </section>

    {{-- ===================== POSTS GRID ===================== --}}
    <section class="bg-brand-surface py-16 sm:py-24">
        <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">

            @if ($posts->isEmpty())
                <div class="rounded-3xl border border-dashed border-stone-300 bg-white py-20 text-center dark:border-slate-600/50 dark:bg-slate-800/60">
                    <svg class="mx-auto h-12 w-12 text-stone-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" d="M12 7.5h1.5m-1.5 3h1.5m-7.5 3h7.5m-7.5 3h7.5m3-9h3.375c.621 0 1.125.504 1.125 1.125V18a2.25 2.25 0 0 1-2.25 2.25M16.5 7.5V18a2.25 2.25 0 0 0 2.25 2.25M16.5 7.5V4.875c0-.621-.504-1.125-1.125-1.125H4.125C3.504 3.75 3 4.254 3 4.875V18a2.25 2.25 0 0 0 2.25 2.25h13.5M6 7.5h3v3H6v-3Z"/>
                    </svg>
                    <h3 class="mt-4 font-display text-xl text-brand-ink">No posts yet</h3>
                    <p class="mt-2 text-sm text-brand-muted">Check back soon — we'll be publishing soon.</p>
                </div>
            @else
                {{-- First post: featured (full-width) --}}
                @php $featured = $posts->first(); $rest = $posts->skip(1); @endphp

                <article class="group relative mb-10 overflow-hidden rounded-3xl bg-white shadow-lg transition hover:shadow-xl dark:bg-slate-800/80 dark:shadow-slate-900/50 reveal">
                    <a href="{{ route('blog.show', $featured) }}" class="block">
                        @if ($featured->featured_image)
                            <div class="relative overflow-hidden">
                                <img src="{{ Storage::url($featured->featured_image) }}"
                                     alt="{{ $featured->title }}"
                                     class="h-64 w-full object-cover transition duration-500 group-hover:scale-105 sm:h-80 lg:h-96">
                                <div class="absolute inset-0 bg-gradient-to-t from-brand-ink/60 via-brand-ink/10 to-transparent"></div>
                            </div>
                        @else
                            <div class="flex h-52 items-center justify-center bg-gradient-to-br from-brand-primary/10 to-brand-primary/5 sm:h-64">
                                <svg class="h-16 w-16 text-brand-primary/30" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                                    <path stroke-linecap="round" d="M12 7.5h1.5m-1.5 3h1.5m-7.5 3h7.5m-7.5 3h7.5m3-9h3.375c.621 0 1.125.504 1.125 1.125V18a2.25 2.25 0 0 1-2.25 2.25M16.5 7.5V18a2.25 2.25 0 0 0 2.25 2.25M16.5 7.5V4.875c0-.621-.504-1.125-1.125-1.125H4.125C3.504 3.75 3 4.254 3 4.875V18a2.25 2.25 0 0 0 2.25 2.25h13.5M6 7.5h3v3H6v-3Z"/>
                                </svg>
                            </div>
                        @endif
                        <div class="p-7 sm:p-9">
                            <div class="flex items-center gap-3">
                                <span class="rounded-full bg-brand-primary/10 px-3 py-1 text-xs font-semibold text-brand-primary">Latest</span>
                                <time class="text-xs text-brand-muted" datetime="{{ ($featured->published_at ?? $featured->created_at)->toDateString() }}">
                                    {{ ($featured->published_at ?? $featured->created_at)->format('F j, Y') }}
                                </time>
                            </div>
                            <h2 class="mt-3 font-display text-2xl font-bold text-brand-ink transition group-hover:text-brand-primary sm:text-3xl">
                                {{ $featured->title }}
                            </h2>
                            @if ($featured->meta_description)
                                <p class="mt-3 line-clamp-2 text-base text-brand-muted leading-relaxed">
                                    {{ $featured->meta_description }}
                                </p>
                            @endif
                            <span class="mt-5 inline-flex items-center gap-1.5 text-sm font-semibold text-brand-primary">
                                Read article
                                <svg class="h-4 w-4 transition group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/></svg>
                            </span>
                        </div>
                    </a>
                </article>

                {{-- Remaining posts: 3-col grid --}}
                @if ($rest->isNotEmpty())
                    <div class="grid gap-7 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach ($rest as $i => $post)
                            <article class="group flex flex-col overflow-hidden rounded-2xl bg-white shadow-sm transition hover:shadow-lg dark:bg-slate-800/80 dark:shadow-slate-900/50 reveal @if($i % 3 === 1) reveal-delay-1 @elseif($i % 3 === 2) reveal-delay-2 @endif">
                                <a href="{{ route('blog.show', $post) }}" class="block flex-1">
                                    {{-- Image --}}
                                    @if ($post->featured_image)
                                        <div class="overflow-hidden">
                                            <img src="{{ Storage::url($post->featured_image) }}"
                                                 alt="{{ $post->title }}"
                                                 class="h-48 w-full object-cover transition duration-500 group-hover:scale-105">
                                        </div>
                                    @else
                                        <div class="flex h-36 items-center justify-center bg-gradient-to-br from-stone-100 to-stone-50">
                                            <svg class="h-10 w-10 text-stone-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                                <path stroke-linecap="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z"/>
                                            </svg>
                                        </div>
                                    @endif

                                    {{-- Text --}}
                                    <div class="flex flex-1 flex-col p-6">
                                        <time class="text-xs text-brand-muted" datetime="{{ ($post->published_at ?? $post->created_at)->toDateString() }}">
                                            {{ ($post->published_at ?? $post->created_at)->format('M j, Y') }}
                                        </time>
                                        <h2 class="mt-2 font-display text-lg font-bold leading-snug text-brand-ink transition group-hover:text-brand-primary">
                                            {{ $post->title }}
                                        </h2>
                                        @if ($post->meta_description)
                                            <p class="mt-2 line-clamp-2 text-sm text-brand-muted leading-relaxed">
                                                {{ $post->meta_description }}
                                            </p>
                                        @endif
                                        <span class="mt-4 inline-flex items-center gap-1 text-xs font-semibold text-brand-primary">
                                            Read more
                                            <svg class="h-3.5 w-3.5 transition group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/></svg>
                                        </span>
                                    </div>
                                </a>
                            </article>
                        @endforeach
                    </div>
                @endif
            @endif
        </div>
    </section>

    {{-- ===================== CTA STRIP ===================== --}}
    <section class="bg-brand-ink py-14 sm:py-20">
        <div class="mx-auto max-w-3xl px-4 text-center sm:px-6 lg:px-8">
            <h2 class="font-display text-2xl font-bold text-white sm:text-3xl reveal">
                Ready to start your project?
            </h2>
            <p class="mt-3 text-sm text-white/60 sm:text-base reveal reveal-delay-1">
                Connect with talented freelancers and bring your ideas to life.
            </p>
            <div class="mt-8 flex flex-col items-center justify-center gap-3 sm:flex-row reveal reveal-delay-2">
                <a href="{{ route('contact.show') }}" class="btn-primary">Get In Touch</a>
                <a href="{{ route('portfolio.public') }}" class="btn-secondary">View Portfolio</a>
            </div>
        </div>
    </section>

</x-public-layout>
