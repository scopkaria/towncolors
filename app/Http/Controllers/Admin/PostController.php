<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\BroadcastBlogPost;
use App\Models\BlogCategory;
use App\Models\BlogTag;
use App\Models\Media;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PostController extends Controller
{
    public function index(): View
    {
        $posts = Post::with('categories')->latest()->get();

        return view('admin.posts.index', compact('posts'));
    }

    public function create(): View
    {
        $categories = BlogCategory::orderBy('name')->get();
        return view('admin.posts.create', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title'            => ['required', 'string', 'max:255'],
            'slug'             => ['nullable', 'string', 'max:255', 'unique:posts,slug', 'regex:/^[a-z0-9-]+$/'],
            'content'          => ['nullable', 'string'],
            'featured_image'   => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,svg', 'max:5120'],
            'meta_title'       => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'status'           => ['required', 'in:draft,published'],
            'categories'       => ['nullable', 'array'],
            'categories.*'     => ['integer', 'exists:blog_categories,id'],
            'tags'             => ['nullable', 'string', 'max:1000'],
        ]);

        $validated['slug'] = $validated['slug']
            ? $validated['slug']
            : $this->uniqueSlug($validated['title']);

        if ($request->hasFile('featured_image')) {
            $validated['featured_image'] = $request->file('featured_image')->store('posts', 'public');
        }

        $validated['body_images'] = $this->extractBodyImages($validated['content'] ?? '');

        $wasPublishedNow = $validated['status'] === 'published';

        if ($wasPublishedNow) {
            $validated['published_at'] = now();
        }

        $categoryIds = $request->input('categories', []);
        $tagString   = $request->input('tags', '');

        $post = Post::create($validated);

        // Sync categories
        $post->categories()->sync($categoryIds);

        // Sync tags
        $this->syncTags($post, $tagString);

        if ($wasPublishedNow) {
            BroadcastBlogPost::dispatch($post);
        }

        return redirect()->route('admin.posts.index')
            ->with('success', 'Post "' . $post->title . '" created.');
    }

    public function edit(Post $post): View
    {
        $categories = BlogCategory::orderBy('name')->get();
        $post->load('categories', 'tags');
        return view('admin.posts.edit', compact('post', 'categories'));
    }

    public function update(Request $request, Post $post): RedirectResponse
    {
        $validated = $request->validate([
            'title'            => ['required', 'string', 'max:255'],
            'slug'             => ['nullable', 'string', 'max:255', 'unique:posts,slug,' . $post->id, 'regex:/^[a-z0-9-]+$/'],
            'content'          => ['nullable', 'string'],
            'featured_image'   => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,svg', 'max:5120'],
            'meta_title'       => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'status'           => ['required', 'in:draft,published'],
            'categories'       => ['nullable', 'array'],
            'categories.*'     => ['integer', 'exists:blog_categories,id'],
            'tags'             => ['nullable', 'string', 'max:1000'],
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

        $validated['body_images'] = $this->extractBodyImages($validated['content'] ?? '');

        $wasAlreadyPublished = $post->status === 'published';
        $becomingPublished    = $validated['status'] === 'published';

        // Set published_at when first publishing
        if ($becomingPublished && ! $wasAlreadyPublished) {
            $validated['published_at'] = now();
        }

        $post->update($validated);

        // Sync categories and tags
        $post->categories()->sync($request->input('categories', []));
        $this->syncTags($post, $request->input('tags', ''));

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

        foreach ($post->body_images ?? [] as $image) {
            if (str_starts_with((string) $image, '/storage/posts/')) {
                Storage::disk('public')->delete(str_replace('/storage/', '', (string) $image));
            }
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

    private function syncTags(Post $post, ?string $tagString): void
    {
        $names = collect(explode(',', $tagString ?? ''))
            ->map(fn ($n) => trim($n))
            ->filter()
            ->unique();

        $tagIds = $names->map(fn ($name) => BlogTag::firstOrCreate(
            ['slug' => Str::slug($name)],
            ['name' => $name]
        )->id);

        $post->tags()->sync($tagIds);
    }

    public function uploadImage(Request $request): JsonResponse
    {
        $request->validate([
            'image' => ['required', 'file', 'mimes:jpg,jpeg,png,webp,svg', 'max:5120'],
        ]);

        $path = $request->file('image')->store('posts', 'public');

        Media::create([
            'file_name' => $request->file('image')->getClientOriginalName(),
            'file_path' => $path,
            'file_type' => 'image',
            'size' => $request->file('image')->getSize(),
            'uploaded_by' => $request->user()->id,
        ]);

        return response()->json([
            'url' => Storage::disk('public')->url($path),
            'path' => $path,
        ]);
    }

    private function extractBodyImages(string $html): array
    {
        if (trim($html) === '') {
            return [];
        }

        preg_match_all('/<img[^>]+src=["\']([^"\']+)["\']/i', $html, $matches);

        return collect($matches[1] ?? [])
            ->map(fn ($src) => trim((string) $src))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }
}
