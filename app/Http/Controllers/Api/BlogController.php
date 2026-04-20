<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BlogController extends Controller
{
    public function index()
    {
        $posts = Post::published()
            ->latest('published_at')
            ->paginate(15, ['id', 'title', 'slug', 'featured_image', 'meta_description', 'published_at']);

        return response()->json($posts);
    }

    public function show(Post $post)
    {
        if (! $post->isPublished()) {
            abort(404);
        }

        return response()->json($post);
    }

    // ── Admin CRUD ────────────────────────────────────

    public function adminIndex(Request $request)
    {
        if ($request->user()->role->value !== 'admin') {
            abort(403);
        }

        $query = Post::query();

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $posts = $query->latest()->paginate(15);

        return response()->json($posts);
    }

    public function store(Request $request)
    {
        if ($request->user()->role->value !== 'admin') {
            abort(403);
        }

        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'meta_description' => ['nullable', 'string', 'max:300'],
            'status' => ['required', 'in:draft,published'],
            'featured_image' => ['nullable', 'image', 'max:10240'],
        ]);

        $imagePath = null;
        if ($request->hasFile('featured_image')) {
            $imagePath = $request->file('featured_image')->store('blog', 'public');
        }

        $post = Post::create([
            'title' => $request->title,
            'slug' => $this->makeUniqueSlug($request->title),
            'content' => $request->content,
            'meta_description' => $request->meta_description,
            'featured_image' => $imagePath,
            'status' => $request->status,
            'published_at' => $request->status === 'published' ? now() : null,
        ]);

        return response()->json($post, 201);
    }

    public function update(Request $request, Post $post)
    {
        if ($request->user()->role->value !== 'admin') {
            abort(403);
        }

        $request->validate([
            'title' => ['sometimes', 'string', 'max:255'],
            'content' => ['sometimes', 'string'],
            'meta_description' => ['nullable', 'string', 'max:300'],
            'status' => ['sometimes', 'in:draft,published'],
            'featured_image' => ['nullable', 'image', 'max:10240'],
        ]);

        if ($request->has('title')) {
            $post->title = $request->title;
        }
        if ($request->has('content')) {
            $post->content = $request->content;
        }
        if ($request->has('meta_description')) {
            $post->meta_description = $request->meta_description;
        }
        if ($request->has('status')) {
            $post->status = $request->status;
            if ($request->status === 'published' && ! $post->published_at) {
                $post->published_at = now();
            }
        }
        if ($request->hasFile('featured_image')) {
            $post->featured_image = $request->file('featured_image')->store('blog', 'public');
        }

        $post->save();

        return response()->json($post);
    }

    public function destroy(Request $request, Post $post)
    {
        if ($request->user()->role->value !== 'admin') {
            abort(403);
        }

        $post->delete();

        return response()->json(null, 204);
    }

    private function makeUniqueSlug(string $title): string
    {
        $slug = Str::slug($title);
        $original = $slug;
        $i = 1;
        while (Post::where('slug', $slug)->exists()) {
            $slug = $original . '-' . $i++;
        }
        return $slug;
    }
}
