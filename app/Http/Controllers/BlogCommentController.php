<?php

namespace App\Http\Controllers;

use App\Models\BlogComment;
use App\Models\Post;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class BlogCommentController extends Controller
{
    public function store(Request $request, Post $post): RedirectResponse
    {
        $validated = $request->validate([
            'author_name' => ['required', 'string', 'max:120'],
            'author_email' => ['required', 'email', 'max:180'],
            'content' => ['required', 'string', 'min:3', 'max:3000'],
            'parent_id' => ['nullable', 'integer', 'exists:blog_comments,id'],
            'website' => ['nullable', 'max:0'],
        ]);

        if (! empty($validated['website'] ?? null)) {
            return back()->with('success', 'Thanks! Your comment is awaiting approval.');
        }

        if (! $post->isPublished()) {
            abort(404);
        }

        $parentId = $validated['parent_id'] ?? null;
        if ($parentId) {
            $parent = BlogComment::query()
                ->where('post_id', $post->id)
                ->where('id', $parentId)
                ->first();

            if (! $parent) {
                return back()->withErrors(['content' => 'Invalid reply target.']);
            }
        }

        $content = trim($validated['content']);
        $email = strtolower($validated['author_email']);

        $duplicate = BlogComment::query()
            ->where('post_id', $post->id)
            ->where('author_email', $email)
            ->where('content', $content)
            ->where('created_at', '>=', now()->subMinutes(5))
            ->exists();

        if ($duplicate) {
            return back()->with('success', 'Thanks! Your comment is already pending moderation.');
        }

        BlogComment::create([
            'post_id' => $post->id,
            'parent_id' => $parentId,
            'author_name' => $validated['author_name'],
            'author_email' => $email,
            'content' => $content,
            'status' => 'pending',
            'ip_address' => $request->ip(),
        ]);

        return back()->with('success', 'Thanks! Your comment is awaiting approval.');
    }
}
