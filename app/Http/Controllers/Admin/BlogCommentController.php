<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogComment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BlogCommentController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->string('status')->toString();

        $comments = BlogComment::query()
            ->with(['post:id,title,slug', 'parent:id,author_name', 'replies'])
            ->when(in_array($status, ['pending', 'approved', 'rejected'], true), function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.posts.comments', compact('comments', 'status'));
    }

    public function update(Request $request, BlogComment $comment): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:pending,approved,rejected'],
        ]);

        $payload = ['status' => $validated['status']];

        if ($validated['status'] === 'approved') {
            $payload['approved_at'] = now();
            $payload['approved_by'] = $request->user()->id;
        }

        if ($validated['status'] !== 'approved') {
            $payload['approved_at'] = null;
            $payload['approved_by'] = null;
        }

        $comment->update($payload);

        return back()->with('success', 'Comment moderation updated.');
    }

    public function destroy(BlogComment $comment): RedirectResponse
    {
        $comment->delete();

        return back()->with('success', 'Comment removed.');
    }
}
