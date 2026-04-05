<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\BroadcastBlogPost;
use App\Models\Post;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PostController extends Controller
{
    public function index(): View
    {
        $posts = Post::latest()->get();

        return view('admin.posts.index', compact('posts'));
    }

    public function create(): View
    {
        return view('admin.posts.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title'            => ['required', 'string', 'max:255'],
            'slug'             => ['nullable', 'string', 'max:255', 'unique:posts,slug', 'regex:/^[a-z0-9-]+$/'],
            'content'          => ['nullable', 'string'],
            'featured_image'   => ['nullable', 'image', 'max:4096'],
            'meta_title'       => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'status'           => ['required', 'in:draft,published'],
        ]);

        $validated['slug'] = $validated['slug']
            ? $validated['slug']
            : $this->uniqueSlug($validated['title']);

        if ($request->hasFile('featured_image')) {
            $validated['featured_image'] = $request->file('featured_image')->store('posts', 'public');
        }

        $wasPublishedNow = $validated['status'] === 'published';

        if ($wasPublishedNow) {
            $validated['published_at'] = now();
        }

        $post = Post::create($validated);

        if ($wasPublishedNow) {
            BroadcastBlogPost::dispatch($post);
        }

        return redirect()->route('admin.posts.index')
            ->with('success', 'Post "' . $post->title . '" created.');
    }

    public function edit(Post $post): View
    {
        return view('admin.posts.edit', compact('post'));
    }

    public function update(Request $request, Post $post): RedirectResponse
    {
        $validated = $request->validate([
            'title'            => ['required', 'string', 'max:255'],
            'slug'             => ['nullable', 'string', 'max:255', 'unique:posts,slug,' . $post->id, 'regex:/^[a-z0-9-]+$/'],
            'content'          => ['nullable', 'string'],
            'featured_image'   => ['nullable', 'image', 'max:4096'],
            'meta_title'       => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'status'           => ['required', 'in:draft,published'],
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = $this->uniqueSlug($validated['title'], $post->id);
        }

        if ($request->hasFile('featured_image')) {
            if ($post->featured_image) {
                Storage::disk('public')->delete($post->featured_image);
            }
            $validated['featured_image'] = $request->file('featured_image')->store('posts', 'public');
        }

        $wasAlreadyPublished = $post->status === 'published';
        $becomingPublished    = $validated['status'] === 'published';

        // Set published_at when first publishing
        if ($becomingPublished && ! $wasAlreadyPublished) {
            $validated['published_at'] = now();
        }

        $post->update($validated);

        // Broadcast: new publish OR update to already-live post
        if ($becomingPublished) {
            BroadcastBlogPost::dispatch($post->fresh());
        }

        return redirect()->route('admin.posts.index')
            ->with('success', 'Post "' . $post->title . '" updated.');
    }

    public function destroy(Post $post): RedirectResponse
    {
        if ($post->featured_image) {
            Storage::disk('public')->delete($post->featured_image);
        }

        $title = $post->title;
        $post->delete();

        return redirect()->route('admin.posts.index')
            ->with('success', 'Post "' . $title . '" deleted.');
    }

    private function uniqueSlug(string $title, ?int $excludeId = null): string
    {
        $base = Str::slug($title);
        $slug = $base;
        $i    = 1;

        while (
            \DB::table('posts')
                ->where('slug', $slug)
                ->when($excludeId, fn($q) => $q->where('id', '!=', $excludeId))
                ->exists()
        ) {
            $slug = $base . '-' . $i++;
        }

        return $slug;
    }
}
