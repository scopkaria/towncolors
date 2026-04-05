<x-public-layout :title="$post->meta_title ?: $post->title">

    @push('head')
    <meta name="description" content="{{ $post->meta_description ?: '' }}">
    <link rel="canonical" href="{{ route('blog.show', $post) }}">
    <meta property="og:type" content="article">
    <meta property="og:title" content="{{ $post->meta_title ?: $post->title }}">
    <meta property="og:description" content="{{ $post->meta_description ?: '' }}">
    <meta property="og:url" content="{{ route('blog.show', $post) }}">
    @if ($post->featured_image)
    <meta property="og:image" content="{{ Storage::url($post->featured_image) }}">
    @endif
    @if ($post->published_at)
    <meta property="article:published_time" content="{{ $post->published_at->toIso8601String() }}">
    @endif
    <style>
        .post-content h1 { font-family: var(--font-display, inherit); font-size: 2rem; font-weight: 700; color: #1c1917; margin: 2rem 0 0.75rem; line-height: 1.25; }
        .post-content h2 { font-size: 1.5rem; font-weight: 700; color: #1c1917; margin: 1.75rem 0 0.625rem; line-height: 1.3; padding-bottom: 0.375rem; border-bottom: 2px solid #f5f5f4; }
        .post-content h3 { font-size: 1.25rem; font-weight: 600; color: #1c1917; margin: 1.5rem 0 0.5rem; line-height: 1.4; }
        .post-content h4 { font-size: 1rem; font-weight: 600; color: #1c1917; margin: 1.25rem 0 0.5rem; }
        .post-content p  { color: #57534e; line-height: 1.85; margin: 1rem 0; font-size: 1rem; }
        .post-content a  { color: #f97316; text-decoration: underline; text-underline-offset: 2px; }
        .post-content a:hover { color: #ea580c; }
        .post-content ul, .post-content ol { padding-left: 1.75rem; margin: 1rem 0; color: #57534e; line-height: 1.85; }
        .post-content li { margin: 0.375rem 0; font-size: 1rem; }
        .post-content ul li { list-style-type: disc; }
        .post-content ol li { list-style-type: decimal; }
        .post-content blockquote { border-left: 4px solid #f97316; padding: 0.75rem 1.5rem; margin: 1.5rem 0; color: #78716c; font-style: italic; background: #fff7ed; border-radius: 0 0.75rem 0.75rem 0; }
        .post-content pre  { background: #1e293b; color: #e2e8f0; border-radius: 0.875rem; padding: 1.25rem 1.5rem; overflow-x: auto; font-size: 0.875rem; margin: 1.5rem 0; }
        .post-content code { background: #f5f5f4; border-radius: 0.25rem; padding: 0.15em 0.4em; font-size: 0.875em; color: #ea580c; }
        .post-content pre code { background: transparent; color: inherit; padding: 0; }
        .post-content strong { font-weight: 700; color: #292524; }
        .post-content em { font-style: italic; }
        .post-content img { max-width: 100%; border-radius: 1rem; margin: 1.75rem 0; box-shadow: 0 4px 24px rgb(0 0 0 / 0.08); }
        .post-content hr { border: 0; border-top: 1px solid #e7e5e4; margin: 2.5rem 0; }
        .post-content table { width: 100%; border-collapse: collapse; margin: 1.5rem 0; font-size: 0.9375rem; }
        .post-content th { background: #f5f5f4; font-weight: 600; color: #1c1917; padding: 0.75rem 1rem; text-align: left; border-bottom: 2px solid #e7e5e4; }
        .post-content td { padding: 0.75rem 1rem; border-bottom: 1px solid #e7e5e4; color: #57534e; }
    </style>
    @endpush

    {{-- ===================== FEATURED IMAGE HERO ===================== --}}
    @if ($post->featured_image)
    <div class="relative overflow-hidden bg-brand-ink">
        <img src="{{ Storage::url($post->featured_image) }}"
             alt="{{ $post->title }}"
             class="h-64 w-full object-cover opacity-50 sm:h-80 lg:h-[28rem]">
        <div class="absolute inset-0 bg-gradient-to-t from-brand-ink via-brand-ink/50 to-transparent"></div>

        {{-- Title overlay on image --}}
        <div class="absolute bottom-0 left-0 right-0 p-6 sm:p-10 lg:p-14">
            <div class="mx-auto max-w-4xl">
                <div class="mb-3 flex items-center gap-3">
                    <a href="{{ route('blog.index') }}" class="text-xs font-semibold text-white/60 transition hover:text-white">Blog</a>
                    <span class="text-white/30">/</span>
                    <time class="text-xs text-white/60" datetime="{{ ($post->published_at ?? $post->created_at)->toDateString() }}">
                        {{ ($post->published_at ?? $post->created_at)->format('F j, Y') }}
                    </time>
                </div>
                <h1 class="font-display text-2xl font-bold leading-tight text-white sm:text-4xl lg:text-5xl">
                    {{ $post->title }}
                </h1>
            </div>
        </div>
    </div>
    @else
    {{-- Dark header without image --}}
    <section class="relative overflow-hidden bg-brand-ink py-20 sm:py-28">
        <div class="pointer-events-none absolute -top-24 -right-24 h-80 w-80 rounded-full bg-brand-primary/10 blur-3xl"></div>
        <div class="pointer-events-none absolute -bottom-24 -left-24 h-72 w-72 rounded-full bg-brand-primary/8 blur-3xl"></div>
        <div class="relative mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
            <div class="mb-4 flex items-center gap-3">
                <a href="{{ route('blog.index') }}" class="text-xs font-semibold text-white/60 transition hover:text-white">Blog</a>
                <span class="text-white/30">/</span>
                <time class="text-xs text-white/60" datetime="{{ ($post->published_at ?? $post->created_at)->toDateString() }}">
                    {{ ($post->published_at ?? $post->created_at)->format('F j, Y') }}
                </time>
            </div>
            <h1 class="font-display text-[1.75rem] font-bold leading-tight text-white sm:text-4xl lg:text-5xl">
                {{ $post->title }}
            </h1>
            @if ($post->meta_description)
                <p class="mt-4 text-base text-white/60 sm:text-lg">{{ $post->meta_description }}</p>
            @endif
        </div>
    </section>
    @endif

    {{-- ===================== ARTICLE BODY ===================== --}}
    <section class="bg-brand-surface py-12 sm:py-20">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
            <div class="grid gap-10 lg:grid-cols-4">

                {{-- Main article --}}
                <article class="lg:col-span-3">
                    {{-- If image was shown as hero, show the meta bar separately --}}
                    @if ($post->featured_image)
                    <div class="mb-8 flex flex-wrap items-center gap-4 border-b border-stone-200 pb-6">
                        <div class="flex items-center gap-2 text-sm text-brand-muted">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5"/></svg>
                            <time datetime="{{ ($post->published_at ?? $post->created_at)->toDateString() }}">
                                {{ ($post->published_at ?? $post->created_at)->format('F j, Y') }}
                            </time>
                        </div>
                        <a href="{{ route('blog.index') }}"
                           class="inline-flex items-center gap-1 text-sm font-medium text-brand-primary hover:underline">
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/></svg>
                            Back to Blog
                        </a>
                    </div>
                    @endif

                    <div class="post-content rounded-2xl bg-white p-7 shadow-sm sm:p-10">
                        {!! $post->content !!}
                    </div>

                    {{-- Back link --}}
                    <div class="mt-8">
                        <a href="{{ route('blog.index') }}"
                           class="inline-flex items-center gap-2 rounded-xl border border-stone-200 bg-white px-5 py-2.5 text-sm font-semibold text-brand-muted shadow-sm transition hover:border-brand-primary hover:text-brand-primary">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/></svg>
                            All Posts
                        </a>
                    </div>
                </article>

                {{-- Sidebar --}}
                <aside class="hidden lg:block">
                    <div class="sticky top-24 space-y-6">
                        {{-- Share --}}
                        <div class="rounded-2xl border border-stone-200 bg-white p-5 shadow-sm">
                            <h3 class="text-xs font-bold uppercase tracking-widest text-brand-muted">Share</h3>
                            <div class="mt-3 flex flex-col gap-2">
                                <a href="https://twitter.com/intent/tweet?url={{ urlencode(route('blog.show', $post)) }}&text={{ urlencode($post->title) }}"
                                   target="_blank" rel="noopener noreferrer"
                                   class="flex items-center gap-2 rounded-xl border border-stone-200 px-3 py-2 text-xs font-medium text-brand-muted transition hover:border-sky-300 hover:text-sky-600">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-4.714-6.231-5.401 6.231H2.746l7.73-8.835L1.254 2.25H8.08l4.261 5.638L18.244 2.25zm-1.161 17.52h1.833L7.084 4.126H5.117L17.083 19.77z"/></svg>
                                    Share on X
                                </a>
                                <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode(route('blog.show', $post)) }}"
                                   target="_blank" rel="noopener noreferrer"
                                   class="flex items-center gap-2 rounded-xl border border-stone-200 px-3 py-2 text-xs font-medium text-brand-muted transition hover:border-blue-300 hover:text-blue-600">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 0 1-2.063-2.065 2.064 2.064 0 1 1 2.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
                                    Share on LinkedIn
                                </a>
                            </div>
                        </div>

                        {{-- CTA --}}
                        <div class="rounded-2xl border border-brand-primary/20 bg-gradient-to-br from-brand-primary/5 to-orange-50 p-5">
                            <h3 class="font-display text-sm font-bold text-brand-ink">Have a project?</h3>
                            <p class="mt-1.5 text-xs text-brand-muted leading-relaxed">Work with our talented freelancers to bring your idea to life.</p>
                            <a href="{{ route('contact.show') }}" class="btn-primary mt-4 w-full justify-center text-xs">
                                Get In Touch
                            </a>
                        </div>
                    </div>
                </aside>

            </div>
        </div>
    </section>

</x-public-layout>
