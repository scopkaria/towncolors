<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="space-y-2">
                <span class="inline-flex w-fit rounded-full border border-accent/30 bg-accent-light px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.32em] text-brand-primary">
                    Blog
                </span>
                <h1 class="font-display text-3xl text-brand-ink">Comments Moderation</h1>
            </div>
            <a href="{{ route('admin.posts.index') }}" class="btn-secondary">Back to Posts</a>
        </div>
    </x-slot>

    @if (session('success'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-medium text-emerald-700">
            {{ session('success') }}
        </div>
    @endif

    <div class="mb-5 flex flex-wrap gap-2">
        @foreach (['' => 'All', 'pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected'] as $value => $label)
            <a href="{{ route('admin.posts.comments.index', ['status' => $value ?: null]) }}"
               class="rounded-xl border px-3 py-1.5 text-xs font-semibold uppercase tracking-[0.12em] {{ $status === $value ? 'border-brand-primary bg-brand-primary text-white' : 'border-warm-300/50 bg-warm-100 text-brand-muted hover:border-accent/30 hover:text-brand-primary' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>

    <div class="space-y-3">
        @forelse ($comments as $comment)
            <article class="rounded-2xl border border-white/70 bg-white/90 p-5 shadow-card">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <p class="font-semibold text-brand-ink">{{ $comment->author_name }}</p>
                        <p class="text-xs text-brand-muted">{{ $comment->author_email }} • {{ $comment->created_at->diffForHumans() }}</p>
                        <p class="mt-1 text-xs text-brand-muted">Post: {{ $comment->post?->title }}</p>
                        @if ($comment->parent)
                            <p class="mt-1 text-xs text-brand-muted">Reply to: {{ $comment->parent->author_name }}</p>
                        @endif
                    </div>
                    <span class="rounded-full px-2.5 py-1 text-[11px] font-bold uppercase tracking-[0.12em] {{ $comment->status === 'approved' ? 'bg-emerald-50 text-emerald-600' : ($comment->status === 'rejected' ? 'bg-red-50 text-red-600' : 'bg-amber-50 text-amber-600') }}">
                        {{ $comment->status }}
                    </span>
                </div>

                <div class="mt-3 rounded-xl border border-warm-300/50 bg-warm-200/50 p-3 text-sm leading-7 text-brand-ink">
                    {{ $comment->content }}
                </div>

                <div class="mt-3 flex flex-wrap items-center gap-2">
                    <form method="POST" action="{{ route('admin.posts.comments.update', $comment) }}">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="approved">
                        <button type="submit" class="rounded-xl border border-emerald-200 bg-emerald-50 px-3 py-2 text-xs font-semibold text-emerald-700 hover:bg-emerald-100">Approve</button>
                    </form>
                    <form method="POST" action="{{ route('admin.posts.comments.update', $comment) }}">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="pending">
                        <button type="submit" class="rounded-xl border border-amber-200 bg-amber-50 px-3 py-2 text-xs font-semibold text-amber-700 hover:bg-amber-100">Set Pending</button>
                    </form>
                    <form method="POST" action="{{ route('admin.posts.comments.update', $comment) }}">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="rejected">
                        <button type="submit" class="rounded-xl border border-red-200 bg-red-50 px-3 py-2 text-xs font-semibold text-red-700 hover:bg-red-100">Reject</button>
                    </form>
                    <form method="POST" action="{{ route('admin.posts.comments.destroy', $comment) }}" onsubmit="return confirm('Delete this comment?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="rounded-xl border border-red-200 bg-warm-100 px-3 py-2 text-xs font-semibold text-red-600 hover:bg-red-50">Delete</button>
                    </form>
                </div>
            </article>
        @empty
            <div class="rounded-2xl border border-dashed border-warm-300/50 bg-warm-100 p-12 text-center">
                <p class="font-display text-xl text-brand-ink">No comments found</p>
            </div>
        @endforelse
    </div>

    <div class="mt-6">
        {{ $comments->links() }}
    </div>
</x-app-layout>
