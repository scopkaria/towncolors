<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- SEO --}}
    <title>{{ $page->meta_title ?: $page->title }} · {{ config('app.name', 'Towncore') }}</title>
    <meta name="description" content="{{ $page->meta_description ?: '' }}">
    <link rel="canonical" href="{{ route('pages.show', $page) }}">

    {{-- Open Graph --}}
    <meta property="og:type" content="article">
    <meta property="og:title" content="{{ $page->meta_title ?: $page->title }}">
    <meta property="og:description" content="{{ $page->meta_description ?: '' }}">
    <meta property="og:url" content="{{ route('pages.show', $page) }}">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=manrope:400,500,600,700,800|sora:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @include('partials.theme-vars')

    <style>
        /* Prose styles for Quill-generated HTML */
        .page-content h1 { font-family: var(--font-display, inherit); font-size: 2rem; font-weight: 700; color: #1c1917; margin: 1.75rem 0 0.75rem; line-height: 1.25; }
        .page-content h2 { font-size: 1.5rem; font-weight: 700; color: #1c1917; margin: 1.5rem 0 0.625rem; line-height: 1.3; }
        .page-content h3 { font-size: 1.25rem; font-weight: 600; color: #1c1917; margin: 1.25rem 0 0.5rem; line-height: 1.4; }
        .page-content h4 { font-size: 1rem; font-weight: 600; color: #1c1917; margin: 1rem 0 0.5rem; }
        .page-content p  { color: #78716c; line-height: 1.8; margin: 0.875rem 0; font-size: 0.9375rem; }
        .page-content a  { color: #f97316; text-decoration: underline; }
        .page-content a:hover { color: #ea580c; }
        .page-content ul, .page-content ol { padding-left: 1.5rem; margin: 0.875rem 0; color: #78716c; line-height: 1.8; }
        .page-content li { margin: 0.25rem 0; }
        .page-content ul li { list-style-type: disc; }
        .page-content ol li { list-style-type: decimal; }
        .page-content blockquote { border-left: 3px solid #f97316; padding-left: 1.25rem; margin: 1.25rem 0; color: #a8a29e; font-style: italic; }
        .page-content pre  { background: #f5f5f4; border-radius: 0.75rem; padding: 1rem 1.25rem; overflow-x: auto; font-size: 0.8125rem; margin: 1rem 0; }
        .page-content code { background: #f5f5f4; border-radius: 0.25rem; padding: 0.1em 0.35em; font-size: 0.875em; }
        .page-content strong { font-weight: 700; color: #292524; }
        .page-content em { font-style: italic; }
        .page-content img { max-width: 100%; border-radius: 1rem; margin: 1.25rem 0; }
        .page-content hr { border: 0; border-top: 1px solid #e7e5e4; margin: 2rem 0; }
    </style>
</head>
<body class="font-sans antialiased">

    <div class="min-h-screen bg-gradient-to-br from-stone-50 via-white to-orange-50/20">

        {{-- ── Nav ── --}}
        <header class="sticky top-0 z-30 border-b border-white/70 bg-white/80 backdrop-blur-xl">
            <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-5 py-4 sm:px-8">
                <a href="{{ url('/') }}" class="flex items-center gap-3">
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-slate-950 text-white shadow-card">
                        <x-application-logo class="h-6 w-6" />
                    </span>
                    <span class="font-display text-xl text-brand-ink">{{ config('app.name', 'Towncore') }}</span>
                </a>

                <nav class="flex items-center gap-4">
                    <a href="{{ route('portfolio.public') }}" class="hidden text-sm font-medium text-brand-muted transition hover:text-brand-primary sm:block">Portfolio</a>
                    @auth
                        <a href="{{ route(auth()->user()->role->value . '.dashboard') }}" class="btn-secondary">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="btn-secondary">Log in</a>
                        <a href="{{ route('register') }}" class="btn-primary">Get started</a>
                    @endauth
                </nav>
            </div>
        </header>

        {{-- ── Page header ── --}}
        <div class="border-b border-stone-100 bg-white/60">
            <div class="mx-auto max-w-4xl px-5 py-12 sm:px-8 sm:py-16">
                <nav class="mb-5 flex items-center gap-2 text-xs text-brand-muted">
                    <a href="{{ url('/') }}" class="transition hover:text-brand-primary">Home</a>
                    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/></svg>
                    <span class="text-brand-ink">{{ $page->title }}</span>
                </nav>

                <h1 class="font-display text-4xl leading-tight text-brand-ink sm:text-5xl">
                    {{ $page->title }}
                </h1>

                <div class="mt-4 flex items-center gap-3 text-xs text-brand-muted">
                    <time datetime="{{ $page->updated_at->toIso8601String() }}">
                        Updated {{ $page->updated_at->format('F j, Y') }}
                    </time>
                </div>
            </div>
        </div>

        {{-- ── Content ── --}}
        <main class="mx-auto max-w-4xl px-5 py-12 sm:px-8 sm:py-16">
            @if ($page->content)
                <article class="page-content">
                    {!! $page->content !!}
                </article>
            @else
                <p class="text-sm italic text-brand-muted">This page has no content yet.</p>
            @endif
        </main>

        {{-- ── Footer ── --}}
        <footer class="border-t border-stone-100 py-10 text-center">
            <div class="mx-auto max-w-7xl px-5 sm:px-8">
                <a href="{{ url('/') }}" class="inline-flex items-center gap-3">
                    <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-slate-950 text-white">
                        <x-application-logo class="h-5 w-5" />
                    </span>
                    <span class="font-display text-base text-brand-ink">{{ config('app.name', 'Towncore') }}</span>
                </a>
                <p class="mt-3 text-xs text-brand-muted">
                    &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
                </p>
            </div>
        </footer>

    </div>

</body>
</html>
