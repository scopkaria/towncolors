<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Media;
use App\Models\Page;
use App\Models\PageSection;
use App\Models\ProjectCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PageSectionController extends Controller
{
    // ── Index ─────────────────────────────────────────────────────────────

    public function index(Page $page): View
    {
        $sections = $page->sections()->orderBy('order_index')->get();

        return view('admin.pages.sections.index', compact('page', 'sections'));
    }

    // ── Create ────────────────────────────────────────────────────────────

    public function create(Page $page): View
    {
        $images = Media::where('file_type', 'image')->latest()->get();

        return view('admin.pages.sections.create', compact('page', 'images'));
    }

    // ── Store ─────────────────────────────────────────────────────────────

    public function store(Request $request, Page $page): RedirectResponse
    {
        $request->validate([
            'type'  => ['required', 'in:' . implode(',', array_keys(PageSection::TYPES))],
            'label' => ['nullable', 'string', 'max:120'],
        ]);

        $type     = $request->input('type');
        $data     = $this->buildData($type, $request);
        $maxOrder = (int) ($page->sections()->max('order_index') ?? -1);

        $page->sections()->create([
            'type'        => $type,
            'label'       => trim((string) $request->input('label')) ?: PageSection::TYPES[$type],
            'order_index' => $maxOrder + 1,
            'data'        => $data,
            'is_active'   => true,
        ]);

        return redirect()
            ->route('admin.pages.sections.index', $page)
            ->with('success', 'Section added successfully.');
    }

    // ── Edit ──────────────────────────────────────────────────────────────

    public function edit(Page $page, PageSection $section): View
    {
        $images   = Media::where('file_type', 'image')->latest()->get();
        $services = ProjectCategory::whereNull('parent_id')->orderBy('name')->get();

        return view('admin.pages.sections.edit', compact('page', 'section', 'images', 'services'));
    }

    // ── Update ────────────────────────────────────────────────────────────

    public function update(Request $request, Page $page, PageSection $section): RedirectResponse
    {
        $request->validate([
            'label' => ['nullable', 'string', 'max:120'],
        ]);

        $data = $this->buildData($section->type, $request);

        $section->update([
            'label' => trim((string) $request->input('label')) ?: $section->typeLabel(),
            'data'  => $data,
        ]);

        return redirect()
            ->route('admin.pages.sections.index', $page)
            ->with('success', 'Section "' . $section->label . '" updated.');
    }

    // ── Destroy ───────────────────────────────────────────────────────────

    public function destroy(Page $page, PageSection $section): RedirectResponse
    {
        $section->delete();

        return redirect()
            ->route('admin.pages.sections.index', $page)
            ->with('success', 'Section deleted.');
    }

    // ── Toggle visibility ─────────────────────────────────────────────────

    public function toggle(Page $page, PageSection $section): RedirectResponse
    {
        $section->update(['is_active' => ! $section->is_active]);

        return back()->with('success',
            $section->is_active ? 'Section shown on page.' : 'Section hidden from page.');
    }

    // ── Reorder (AJAX) ────────────────────────────────────────────────────

    public function reorder(Request $request, Page $page): JsonResponse
    {
        $request->validate([
            'ids'   => ['required', 'array'],
            'ids.*' => ['integer', 'exists:page_sections,id'],
        ]);

        foreach ($request->input('ids') as $index => $id) {
            $page->sections()->where('id', $id)->update(['order_index' => $index]);
        }

        return response()->json(['ok' => true]);
    }

    // ── Helpers ───────────────────────────────────────────────────────────

    private function buildData(string $type, Request $request): array
    {
        return match ($type) {
            'hero' => [
                'title'       => $request->input('title', ''),
                'subtitle'    => $request->input('subtitle', ''),
                'bg_media_id' => $request->filled('bg_media_id')
                    ? (int) $request->input('bg_media_id')
                    : null,
            ],

            'story' => [
                'content' => $request->input('content', ''),
            ],

            'timeline' => [
                'heading' => $request->input('heading', 'Our Journey'),
                'items'   => json_decode((string) $request->input('items_json', '[]'), true) ?: [],
            ],

            'services' => [
                'heading' => $request->input('heading', 'Our Services'),
                'intro'   => $request->input('intro', ''),
            ],

            'vision' => [
                'heading' => $request->input('heading', 'Our Vision'),
                'content' => $request->input('content', ''),
            ],

            'community' => [
                'heading'    => $request->input('heading', 'Community'),
                'content'    => $request->input('content', ''),
                'link_label' => $request->input('link_label', ''),
                'link_url'   => $request->input('link_url', ''),
            ],

            'clients' => [
                'heading'   => $request->input('heading', 'Trusted By'),
                'media_ids' => json_decode((string) $request->input('media_ids_json', '[]'), true) ?: [],
            ],

            'cta' => [
                'title'        => $request->input('title', ''),
                'subtitle'     => $request->input('subtitle', ''),
                'button_label' => $request->input('button_label', 'Get started'),
                'button_url'   => $request->input('button_url', '/register'),
            ],

            default => [],
        };
    }
}
