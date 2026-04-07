<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;

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
}
