<x-public-layout :title="$post->meta_title ?: $post->title">

    @push('head')
    <meta name="description" content="{{ $post->meta_description ?: '' }}">
    <link rel="canonical" href="{{ route('blog.show', $post) }}">
    <meta property="og:type" content="article">
    <meta property="og:title" content="{{ $post->meta_title ?: $post->title }}">
    <meta property="og:description" content="{{ $post->meta_description ?: '' }}">
    <meta property="og:url" content="{{ route('blog.show', $post) }}">
    @if ($post->featuredImageUrl())
        <meta property="og:image" content="{{ $post->featuredImageUrl() }}">
    @endif
    <style>
        .post-content h2 { font-family: var(--font-display, inherit); font-size: 2rem; font-weight: 700; color: #111827; margin: 2rem 0 0.75rem; scroll-margin-top: 7rem; }
        .post-content h3 { font-size: 1.4rem; font-weight: 700; color: #111827; margin: 1.5rem 0 0.5rem; scroll-margin-top: 7rem; }
        .post-content p { font-size: 1.04rem; line-height: 1.9; color: #374151; margin: 1rem 0; }
        .post-content ul, .post-content ol { padding-left: 1.35rem; margin: 1rem 0; color: #374151; line-height: 1.9; }
        .post-content blockquote { border-left: 4px solid #FFB162; padding: 0.75rem 1.25rem; margin: 1.25rem 0; background: #fff7ed; border-radius: 0 0.75rem 0.75rem 0; color: #57534e; }
        .post-content img { max-width: 100%; height: auto; border-radius: 1rem; margin: 1.6rem 0; box-shadow: 0 15px 40px rgba(2, 6, 23, 0.08); }
        .post-content a { color: #A35139; text-decoration: underline; text-underline-offset: 2px; }
        .post-content pre { background: #0f172a; color: #e2e8f0; border-radius: 0.875rem; padding: 1rem 1.25rem; margin: 1.5rem 0; overflow-x: auto; }
        .post-content code { background: #f5f5f4; color: #c2410c; padding: 0.15rem 0.35rem; border-radius: 0.375rem; }
        .post-content pre code { background: transparent; color: inherit; }

        /* Dark mode overrides */
        .dark .post-content h2, .dark .post-content h3 { color: #EEE9DF; }
        .dark .post-content p, .dark .post-content ul, .dark .post-content ol { color: #C9C1B1; }
        .dark .post-content blockquote { background: rgba(27, 38, 50, 0.6); color: #C9C1B1; border-color: #FFB162; }
        .dark .post-content code { background: #1e293b; color: #fb923c; }
        .dark .post-content a { color: #FFB162; }

        /* Related-articles slider */
        .related-slider { display: grid; grid-auto-flow: column; grid-auto-columns: calc((100% - 2 * 1.5rem) / 3); gap: 1.5rem; overflow-x: hidden; scroll-snap-type: x mandatory; }
        .related-slider > * { scroll-snap-align: start; }
        @media (max-width: 1023px) { .related-slider { grid-auto-columns: calc((100% - 1.5rem) / 2); } }
        @media (max-width: 639px)  { .related-slider { grid-auto-columns: 100%; } }
    </style>
    @endpush

    {{-- ── HERO: Featured image with bottom-center title ── --}}
    <section class="relative flex min-h-[720px] items-end justify-center overflow-hidden bg-navy-800 pb-16 sm:min-h-[800px] sm:pb-24">
        @if ($post->featuredImageUrl())
            <img src="{{ $post->featuredImageUrl() }}" alt="{{ $post->title }}" class="absolute inset-0 h-full w-full object-cover opacity-40">
        @endif
        <div class="absolute inset-0 bg-gradient-to-t from-navy-800 via-navy-900/50 to-transparent"></div>

        <div class="relative mx-auto max-w-5xl px-6 text-center">
            {{-- Categories --}}
            @if ($post->categories->isNotEmpty())
                <div class="mb-5 flex flex-wrap items-center justify-center gap-2">
                    @foreach ($post->categories as $cat)
                        <span class="rounded-full border border-white/20 bg-white/10 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.18em] text-white/80 backdrop-blur">{{ $cat->name }}</span>
                    @endforeach
                </div>
            @endif

            <h1 class="font-display text-4xl font-bold leading-tight text-white sm:text-5xl lg:text-6xl">
                {{ $post->title }}
            </h1>
            <p class="mx-auto mt-5 max-w-3xl text-base leading-8 text-slate-300 sm:text-lg">
                {{ $post->meta_description ?: 'Insights and practical guidance from our team.' }}
            </p>

            <div class="mt-6 flex flex-wrap items-center justify-center gap-4 text-sm text-slate-400">
                <time datetime="{{ ($post->published_at ?? $post->created_at)->toDateString() }}">
                    {{ ($post->published_at ?? $post->created_at)->format('F j, Y') }}
                </time>
                @if ($post->tags->isNotEmpty())
                    <span class="hidden sm:inline">·</span>
                    <div class="flex flex-wrap items-center gap-1.5">
                        @foreach ($post->tags as $tag)
                            <span class="rounded-md bg-white/5 px-2 py-0.5 text-xs text-slate-400">#{{ $tag->name }}</span>
                        @endforeach
                    </div>
                @endif
            </div>

            <nav class="mt-6 flex items-center justify-center gap-2 text-xs text-slate-500">
                <a href="{{ url('/') }}" class="hover:text-white transition">Home</a>
                <span>/</span>
                <a href="{{ route('blog.index') }}" class="hover:text-white transition">Blog</a>
                <span>/</span>
                <span class="text-slate-300 line-clamp-1 max-w-[200px]">{{ $post->title }}</span>
            </nav>
        </div>
    </section>

    {{-- ── CONTENT: Transparent bg so particles show through ── --}}
    <section class="relative py-14 sm:py-20">
        <div class="mx-auto max-w-[1600px] px-4 sm:px-8 lg:px-10">
            <div class="grid gap-10 lg:grid-cols-12">

                {{-- Table of Contents sidebar --}}
                <aside class="hidden lg:col-span-3 lg:block">
                    <div class="sticky top-24 rounded-2xl border border-warm-300/60 bg-white/80 p-5 shadow-sm backdrop-blur-sm dark:border-slate-700/50 dark:bg-navy-800/80">
                        <p class="text-[11px] font-semibold uppercase tracking-[0.2em] text-brand-muted">Table of Contents</p>
                        <div class="mt-4 space-y-2">
                            @forelse ($toc as $item)
                                <a href="#{{ $item['id'] }}"
                                   class="block rounded-lg px-2 py-1.5 text-sm transition {{ $item['level'] === 'h3' ? 'ml-3 text-brand-muted hover:text-brand-primary' : 'font-semibold text-brand-ink hover:text-brand-primary' }}">
                                    {{ $item['text'] }}
                                </a>
                            @empty
                                <p class="text-sm text-brand-muted">No section headings available.</p>
                            @endforelse
                        </div>
                    </div>
                </aside>

                {{-- Article body --}}
                <article class="lg:col-span-9">
                    <div class="post-content rounded-3xl border border-white/70 bg-white/90 p-6 shadow-panel backdrop-blur-sm sm:p-10 lg:p-12 dark:border-slate-700/50 dark:bg-navy-800/90" style="overflow-wrap: break-word; word-wrap: break-word;">
                        {!! $contentHtml !!}
                    </div>

                    {{-- Share buttons --}}
                    <div class="mt-6 flex flex-wrap items-center gap-3" x-data="{ copied: false }">
                        <span class="text-xs font-semibold uppercase tracking-[0.15em] text-brand-muted">Share</span>
                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(route('blog.show', $post)) }}" target="_blank" rel="noopener noreferrer" class="flex h-9 w-9 items-center justify-center rounded-full border border-warm-300/50 bg-white/80 text-brand-muted transition hover:border-[#1877F2] hover:text-[#1877F2] dark:border-slate-700/50 dark:bg-navy-800/80" title="Share on Facebook">
                            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                        </a>
                        <a href="https://twitter.com/intent/tweet?url={{ urlencode(route('blog.show', $post)) }}&text={{ urlencode($post->title) }}" target="_blank" rel="noopener noreferrer" class="flex h-9 w-9 items-center justify-center rounded-full border border-warm-300/50 bg-white/80 text-brand-muted transition hover:border-black hover:text-black dark:border-slate-700/50 dark:bg-navy-800/80 dark:hover:border-white dark:hover:text-white" title="Share on X">
                            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                        </a>
                        <a href="https://www.linkedin.com/shareArticle?mini=true&url={{ urlencode(route('blog.show', $post)) }}&title={{ urlencode($post->title) }}" target="_blank" rel="noopener noreferrer" class="flex h-9 w-9 items-center justify-center rounded-full border border-warm-300/50 bg-white/80 text-brand-muted transition hover:border-[#0A66C2] hover:text-[#0A66C2] dark:border-slate-700/50 dark:bg-navy-800/80" title="Share on LinkedIn">
                            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
                        </a>
                        <button type="button" @click="navigator.clipboard.writeText('{{ route('blog.show', $post) }}'); copied = true; setTimeout(() => copied = false, 2000)" class="flex h-9 items-center gap-1.5 rounded-full border border-warm-300/50 bg-white/80 px-3 text-xs font-semibold text-brand-muted transition hover:border-accent hover:text-brand-primary dark:border-slate-700/50 dark:bg-navy-800/80" title="Copy link">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M13.19 8.688a4.5 4.5 0 0 1 1.242 7.244l-4.5 4.5a4.5 4.5 0 0 1-6.364-6.364l1.757-1.757m9.86-2.54a4.5 4.5 0 0 0-1.242-7.244l-4.5-4.5a4.5 4.5 0 0 0-6.364 6.364L4.343 8.69" /></svg>
                            <span x-text="copied ? 'Copied!' : 'Copy link'"></span>
                        </button>
                    </div>
                </article>
            </div>
        </div>
    </section>

    {{-- ── CTA: Homepage-style gradient strip ── --}}
    <section class="relative border-y border-white/10 bg-gradient-to-r from-slate-900 via-slate-800 to-slate-900 py-16 sm:py-20">
        <div class="mx-auto max-w-4xl px-5 text-center">
            <span class="inline-flex rounded-full border border-accent/30 bg-accent/10 px-4 py-1 text-[11px] font-semibold uppercase tracking-[0.28em] text-accent">Let&rsquo;s Talk</span>
            <h2 class="mt-5 font-display text-3xl font-bold text-white sm:text-4xl">Ready to start your project?</h2>
            <p class="mt-4 text-base text-slate-300">Talk to us about your idea, scope, and timeline. We will help you plan the best execution path.</p>
            <div class="mt-8 flex flex-wrap items-center justify-center gap-3">
                <a href="{{ route('contact.show') }}" class="btn-primary">Talk to Us</a>
                <a href="{{ route('services.index') }}" class="inline-flex items-center justify-center rounded-xl border border-white/20 bg-white/10 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-white/20">Explore Services</a>
            </div>
        </div>
    </section>

    {{-- ── RELATED ARTICLES: Proper slider (3 / 2 / 1 responsive) ── --}}
    <section class="relative py-16 sm:py-20" x-data="relatedSlider()">
        <div class="mx-auto max-w-7xl px-4 sm:px-8">
            <div class="mb-8 flex items-end justify-between">
                <div>
                    <span class="inline-flex rounded-full border border-accent/60 bg-accent/10 px-3 py-0.5 text-[11px] font-semibold uppercase tracking-[0.28em] text-brand-primary backdrop-blur">Keep Reading</span>
                    <h2 class="mt-3 font-display text-3xl text-brand-ink">Related Articles</h2>
                    <p class="mt-1 text-sm text-brand-muted">More insights from our blog.</p>
                </div>
                <div class="hidden items-center gap-2 sm:flex">
                    <button type="button" @click="prev()" class="flex h-10 w-10 items-center justify-center rounded-full border border-warm-300/50 bg-white/80 text-brand-muted shadow-sm backdrop-blur transition hover:border-accent hover:text-brand-primary dark:border-slate-700/50 dark:bg-navy-800/80">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M15.75 19.5 8.25 12l7.5-7.5"/></svg>
                    </button>
                    <button type="button" @click="next()" class="flex h-10 w-10 items-center justify-center rounded-full border border-warm-300/50 bg-white/80 text-brand-muted shadow-sm backdrop-blur transition hover:border-accent hover:text-brand-primary dark:border-slate-700/50 dark:bg-navy-800/80">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/></svg>
                    </button>
                </div>
            </div>

            @if ($related->isEmpty())
                <div class="rounded-2xl border border-dashed border-warm-300/60 bg-white/60 p-10 text-center text-sm text-brand-muted backdrop-blur dark:border-slate-700/50 dark:bg-navy-800/60">No related posts available yet.</div>
            @else
                <div x-ref="track" class="related-slider" style="transition: transform .5s cubic-bezier(.4,0,.2,1);">
                    @foreach ($related as $item)
                        <article class="group flex flex-col overflow-hidden rounded-2xl border border-warm-300/60 bg-white/80 shadow-sm backdrop-blur-sm transition hover:-translate-y-1 hover:shadow-lg dark:border-slate-700/50 dark:bg-navy-800/80">
                            <a href="{{ route('blog.show', $item) }}" class="flex flex-1 flex-col">
                                @if ($item->featuredImageUrl())
                                    <div class="overflow-hidden">
                                        <img src="{{ $item->featuredImageUrl() }}" alt="{{ $item->title }}" class="h-44 w-full object-cover transition duration-500 group-hover:scale-105" loading="lazy">
                                    </div>
                                @else
                                    <div class="flex h-36 items-center justify-center bg-gradient-to-br from-warm-200 to-warm-200/50 dark:from-navy-800 dark:to-navy-800/50">
                                        <svg class="h-10 w-10 text-stone-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z"/></svg>
                                    </div>
                                @endif
                                <div class="flex flex-1 flex-col p-5">
                                    <p class="text-xs text-brand-muted">{{ ($item->published_at ?? $item->created_at)->format('M j, Y') }}</p>
                                    <h3 class="mt-2 font-display text-lg font-bold leading-snug text-brand-ink transition group-hover:text-brand-primary line-clamp-2">{{ $item->title }}</h3>
                                    @if ($item->meta_description)
                                        <p class="mt-2 flex-1 text-sm text-brand-muted line-clamp-2">{{ $item->meta_description }}</p>
                                    @endif
                                    <span class="mt-4 inline-flex items-center gap-1 text-sm font-semibold text-brand-primary">
                                        Learn More
                                        <svg class="h-3.5 w-3.5 transition group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/></svg>
                                    </span>
                                </div>
                            </a>
                        </article>
                    @endforeach
                </div>

                {{-- Dots --}}
                <div class="mt-6 flex items-center justify-center gap-1.5" x-show="totalPages > 1">
                    <template x-for="i in totalPages" :key="i">
                        <button type="button" @click="goTo(i - 1)" class="h-2 rounded-full transition-all" :class="page === i - 1 ? 'w-6 bg-brand-primary' : 'w-2 bg-stone-300 hover:bg-stone-400'"></button>
                    </template>
                </div>
            @endif
        </div>
    </section>

    {{-- ── COMMENTS ── --}}
    <section class="relative py-16 sm:py-20" x-data="{ replyTo: null, replyLabel: '' }">
        <div class="mx-auto max-w-5xl px-4 sm:px-8">
            <div class="mb-8">
                <h2 class="font-display text-3xl text-brand-ink">Discussion</h2>
                <p class="mt-2 text-sm text-brand-muted">All comments are moderated before going live to keep the conversation useful and spam-free.</p>
            </div>

            @if (session('success'))
                <div class="mb-5 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700 dark:border-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300">{{ session('success') }}</div>
            @endif

            <form method="POST" action="{{ route('blog.comments.store', $post) }}" class="rounded-2xl border border-warm-300/60 bg-white/80 p-5 shadow-sm backdrop-blur-sm sm:p-6 dark:border-slate-700/50 dark:bg-navy-800/80">
                @csrf
                <input type="hidden" name="parent_id" :value="replyTo">

                <div class="mb-3 rounded-xl border border-amber-200 bg-amber-50 px-3 py-2 text-xs text-amber-700 dark:border-amber-700 dark:bg-amber-900/30 dark:text-amber-300" x-show="replyTo">
                    Replying to <span class="font-semibold" x-text="replyLabel"></span>
                    <button type="button" class="ml-2 font-semibold" @click="replyTo = null; replyLabel = ''">Cancel</button>
                </div>

                <div class="grid gap-3 sm:grid-cols-2">
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-[0.12em] text-brand-muted">Name</label>
                        <input type="text" name="author_name" required value="{{ old('author_name') }}" class="mt-1.5 w-full rounded-xl border-warm-300/50 px-3 py-2.5 text-sm focus:border-brand-primary focus:ring-brand-primary dark:border-slate-600 dark:bg-navy-900/60 dark:text-warm-100">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-[0.12em] text-brand-muted">Email</label>
                        <input type="email" name="author_email" required value="{{ old('author_email') }}" class="mt-1.5 w-full rounded-xl border-warm-300/50 px-3 py-2.5 text-sm focus:border-brand-primary focus:ring-brand-primary dark:border-slate-600 dark:bg-navy-900/60 dark:text-warm-100">
                    </div>
                </div>

                <div class="mt-3 hidden">
                    <label>Website</label>
                    <input type="text" name="website" value="">
                </div>

                <div class="mt-3">
                    <label class="block text-xs font-semibold uppercase tracking-[0.12em] text-brand-muted">Comment</label>
                    <textarea name="content" rows="5" required class="mt-1.5 w-full rounded-xl border-warm-300/50 px-3 py-2.5 text-sm focus:border-brand-primary focus:ring-brand-primary dark:border-slate-600 dark:bg-navy-900/60 dark:text-warm-100">{{ old('content') }}</textarea>
                </div>

                <button type="submit" class="btn-primary mt-4">Submit Comment</button>
            </form>

            <div class="mt-8 space-y-4">
                @forelse ($comments as $comment)
                    <article class="rounded-2xl border border-warm-300/60 bg-white/80 p-5 shadow-sm backdrop-blur-sm dark:border-slate-700/50 dark:bg-navy-800/80">
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <p class="font-semibold text-brand-ink">{{ $comment->author_name }}</p>
                                <p class="text-xs text-brand-muted">{{ $comment->created_at->format('M j, Y \a\t H:i') }}</p>
                            </div>
                            <button type="button" @click="replyTo = {{ $comment->id }}; replyLabel = '{{ addslashes($comment->author_name) }}'" class="text-xs font-semibold text-brand-primary">Reply</button>
                        </div>
                        <p class="mt-3 text-sm leading-7 text-brand-muted">{{ $comment->content }}</p>

                        @if ($comment->replies->isNotEmpty())
                            <div class="mt-4 space-y-3 border-l-2 border-warm-300/40 pl-4 dark:border-slate-700/40">
                                @foreach ($comment->replies as $reply)
                                    <div class="rounded-xl border border-warm-300/60 bg-warm-200/80 p-3 dark:border-slate-700/50 dark:bg-navy-900/60">
                                        <div class="flex items-center justify-between gap-3">
                                            <p class="text-sm font-semibold text-brand-ink">{{ $reply->author_name }}</p>
                                            <p class="text-[11px] text-brand-muted">{{ $reply->created_at->format('M j, Y H:i') }}</p>
                                        </div>
                                        <p class="mt-2 text-sm leading-7 text-brand-muted">{{ $reply->content }}</p>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </article>
                @empty
                    <div class="rounded-2xl border border-dashed border-warm-300/60 bg-white/60 p-10 text-center text-sm text-brand-muted backdrop-blur dark:border-slate-700/50 dark:bg-navy-800/60">No approved comments yet. Be the first to comment.</div>
                @endforelse
            </div>
        </div>
    </section>

    @push('scripts')
    <script>
        function relatedSlider() {
            return {
                page: 0,
                totalPages: 1,
                perPage: 3,
                autoTimer: null,
                init() {
                    this.calc();
                    window.addEventListener('resize', () => this.calc());
                    this.startAuto();
                },
                calc() {
                    const total = this.$refs.track ? this.$refs.track.children.length : 0;
                    const w = window.innerWidth;
                    this.perPage = w >= 1024 ? 3 : (w >= 640 ? 2 : 1);
                    this.totalPages = Math.max(1, Math.ceil(total / this.perPage));
                    if (this.page >= this.totalPages) this.page = this.totalPages - 1;
                    this.slide();
                },
                slide() {
                    if (!this.$refs.track) return;
                    const total = this.$refs.track.children.length;
                    if (total <= this.perPage) {
                        this.$refs.track.style.transform = 'translateX(0)';
                        return;
                    }
                    const gap = 24;
                    const trackW = this.$refs.track.parentElement.offsetWidth;
                    const cardW = (trackW - gap * (this.perPage - 1)) / this.perPage;
                    const maxOffset = (total - this.perPage) * (cardW + gap);
                    const offset = Math.min(this.page * this.perPage * (cardW + gap), maxOffset);
                    this.$refs.track.style.transform = `translateX(-${offset}px)`;
                },
                next() { this.page = (this.page + 1) % this.totalPages; this.slide(); this.restartAuto(); },
                prev() { this.page = (this.page - 1 + this.totalPages) % this.totalPages; this.slide(); this.restartAuto(); },
                goTo(p) { this.page = p; this.slide(); this.restartAuto(); },
                startAuto() { this.autoTimer = setInterval(() => this.next(), 6000); },
                restartAuto() { clearInterval(this.autoTimer); this.startAuto(); },
            };
        }
    </script>
    @endpush

</x-public-layout>
