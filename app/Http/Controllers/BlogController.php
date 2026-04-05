<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\View\View;

class BlogController extends Controller
{
    public function index(): View
    {
        $posts = Post::published()->latest('published_at')->get();

        return view('blog.index', compact('posts'));
    }

    public function show(Post $post): View
    {
        abort_unless($post->isPublished(), 404);

        return view('blog.show', compact('post'));
    }
}
