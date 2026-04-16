<?php

namespace App\Http\Controllers;

use App\Models\BlogComment;
use App\Models\Post;
use Illuminate\View\View;

class BlogController extends Controller
{
    public function index(): View
    {
        $posts = Post::published()->with('categories')->latest('published_at')->get();

        return view('blog.index', compact('posts'));
    }

    public function show(Post $post): View
    {
        abort_unless($post->isPublished(), 404);

        $post->load('categories', 'tags');

        $tocPayload = $this->buildTableOfContents($post->content ?? '');

        $related = Post::published()
            ->whereKeyNot($post->id)
            ->latest('published_at')
            ->take(9)
            ->get();

        $comments = BlogComment::query()
            ->where('post_id', $post->id)
            ->whereNull('parent_id')
            ->approved()
            ->with(['replies' => function ($query) {
                $query->approved()->orderBy('created_at');
            }])
            ->orderBy('created_at', 'desc')
            ->get();

        $contentHtml = $tocPayload['content'];
        $toc = $tocPayload['toc'];

        return view('blog.show', compact('post', 'toc', 'related', 'comments', 'contentHtml'));
    }

    private function buildTableOfContents(string $html): array
    {
        if (trim($html) === '') {
            return ['toc' => [], 'content' => ''];
        }

        $dom = new \DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML('<?xml encoding="utf-8" ?>' . $html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();

        $xpath = new \DOMXPath($dom);
        $nodes = $xpath->query('//h2|//h3');

        $toc = [];
        $counter = 1;

        /** @var \DOMElement $node */
        foreach ($nodes as $node) {
            $text = trim($node->textContent ?? '');
            if ($text === '') {
                continue;
            }

            $id = $node->getAttribute('id');
            if ($id === '') {
                $id = 'section-' . $counter++;
                $node->setAttribute('id', $id);
            }

            $toc[] = [
                'id' => $id,
                'text' => $text,
                'level' => strtolower($node->tagName),
            ];
        }

        return [
            'toc' => $toc,
            'content' => $dom->saveHTML(),
        ];
    }
}
