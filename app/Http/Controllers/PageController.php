<?php

namespace App\Http\Controllers;

use App\Models\Media;
use App\Models\Page;
use App\Models\ProjectCategory;
use Illuminate\View\View;

class PageController extends Controller
{
    public function show(Page $page): View
    {
        abort_unless($page->is_published, 404);

        return view('pages.show', compact('page'));
    }

    public function about(): View
    {
        $page = Page::where('slug', 'about')
            ->with(['sections' => fn ($q) => $q->where('is_active', true)->orderBy('order_index')])
            ->firstOrFail();

        abort_unless($page->is_published, 404);

        // Services pulled dynamically for the services section
        $services = ProjectCategory::whereNull('parent_id')
            ->orderBy('name')
            ->get();

        // Collect all media IDs referenced by sections (hero bg + client logos)
        $mediaIds = $page->sections
            ->flatMap(function ($section) {
                $ids = [];
                if ($section->type === 'hero' && ! empty($section->data['bg_media_id'])) {
                    $ids[] = (int) $section->data['bg_media_id'];
                }
                if ($section->type === 'clients' && ! empty($section->data['media_ids'])) {
                    foreach ($section->data['media_ids'] as $id) {
                        $ids[] = (int) $id;
                    }
                }
                return $ids;
            })
            ->unique()
            ->filter();

        $medias = Media::whereIn('id', $mediaIds)->get()->keyBy('id');

        return view('about', compact('page', 'services', 'medias'));
    }
}
