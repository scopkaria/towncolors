<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="space-y-3">
                <span class="inline-flex w-fit rounded-full border border-accent/30 bg-accent-light px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.32em] text-brand-primary">
                    Blog
                </span>
                <div class="space-y-2">
                    <h1 class="font-display text-3xl text-brand-ink sm:text-4xl">Posts</h1>
                    <p class="max-w-2xl text-sm leading-7 text-brand-muted">Create and manage blog posts.</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.posts.comments.index') }}" class="btn-secondary shrink-0">Comments</a>
                <a href="{{ route('admin.posts.create') }}" class="btn-primary shrink-0">
                    <svg class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M12 4v16m8-8H4"/></svg>
                    New Post
                </a>
            </div>
        </div>
    </x-slot>

    @if (session('success'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-medium text-emerald-700">
            {{ session('success') }}
        </div>
    @endif

    @if ($posts->isEmpty())
        <div class="rounded-3xl border border-white/70 bg-white/90 p-12 text-center shadow-panel">
            <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-warm-200">
                <svg class="h-8 w-8 text-brand-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" d="M12 7.5h1.5m-1.5 3h1.5m-7.5 3h7.5m-7.5 3h7.5m3-9h3.375c.621 0 1.125.504 1.125 1.125V18a2.25 2.25 0 0 1-2.25 2.25M16.5 7.5V18a2.25 2.25 0 0 0 2.25 2.25M16.5 7.5V4.875c0-.621-.504-1.125-1.125-1.125H4.125C3.504 3.75 3 4.254 3 4.875V18a2.25 2.25 0 0 0 2.25 2.25h13.5M6 7.5h3v3H6v-3Z"/>
                </svg>
            </div>
            <h3 class="mt-4 font-display text-xl text-brand-ink">No posts yet</h3>
            <p class="mt-2 text-sm text-brand-muted">Write your first blog post to share with your audience.</p>
            <a href="{{ route('admin.posts.create') }}" class="btn-primary mt-6 inline-flex">New Post</a>
        </div>
    @else
        <div class="rounded-3xl border border-white/70 bg-white/90 shadow-panel overflow-hidden">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-warm-300/40">
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-brand-muted">Post</th>
                        <th class="hidden px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-brand-muted sm:table-cell">URL</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-brand-muted">Status</th>
                        <th class="hidden px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-brand-muted md:table-cell">Date</th>
                        <th class="px-6 py-4"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-warm-300/40">
                    @foreach ($posts as $post)
                        <tr class="group transition hover:bg-warm-200/60">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    @if ($post->featured_image)
                                        <img src="{{ Storage::url($post->featured_image) }}"
                                             alt=""
                                             class="h-10 w-14 flex-shrink-0 rounded-lg object-cover">
                                    @else
                                        <div class="flex h-10 w-14 flex-shrink-0 items-center justify-center rounded-lg bg-warm-200">
                                            <svg class="h-4 w-4 text-brand-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z"/></svg>
                                        </div>
                                    @endif
                                    <div>
                                        <a href="{{ route('admin.posts.edit', $post) }}" class="font-semibold text-brand-ink hover:text-brand-primary transition">{{ $post->title }}</a>
                                        @if ($post->meta_description)
                                            <p class="mt-0.5 line-clamp-1 text-xs text-brand-muted">{{ $post->meta_description }}</p>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="hidden px-6 py-4 sm:table-cell">
                                @if ($post->isPublished())
                                    <a href="{{ route('blog.show', $post) }}" target="_blank"
                                       class="inline-flex items-center gap-1 rounded-lg bg-warm-200 px-2.5 py-1 font-mono text-xs text-brand-muted transition hover:bg-accent-light hover:text-brand-primary">
                                        /blog/{{ $post->slug }}
                                        <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/></svg>
                                    </a>
                                @else
                                    <span class="font-mono text-xs text-stone-400">/blog/{{ $post->slug }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if ($post->isPublished())
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-600">
                                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                        Published
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-warm-200 px-2.5 py-1 text-xs font-semibold text-stone-500">
                                        <span class="h-1.5 w-1.5 rounded-full bg-stone-400"></span>
                                        Draft
                                    </span>
                                @endif
                            </td>
                            <td class="hidden px-6 py-4 text-xs text-brand-muted md:table-cell">
                                {{ ($post->published_at ?? $post->created_at)->format('M j, Y') }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('admin.posts.edit', $post) }}"
                                   class="inline-flex items-center gap-1.5 rounded-xl border border-warm-300/50 bg-warm-100 px-3 py-1.5 text-xs font-semibold text-brand-muted shadow-sm transition hover:border-brand-primary hover:text-brand-primary">
                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125"/></svg>
                                    Edit
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</x-app-layout>
