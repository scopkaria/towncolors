<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProjectCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(): View
    {
        // Load root categories with their children (and each child's project count)
        $rootCategories = ProjectCategory::whereNull('parent_id')
            ->withCount('projects')
            ->with(['children' => fn ($q) => $q->withCount('projects')])
            ->orderBy('name')
            ->get();

        return view('admin.categories.index', compact('rootCategories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'             => ['required', 'string', 'max:100', 'unique:project_categories,name'],
            'description'      => ['nullable', 'string', 'max:500'],
            'long_description' => ['nullable', 'string'],
            'color'            => ['required', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'parent_id'        => ['nullable', 'integer', 'exists:project_categories,id'],
            'image'            => ['nullable', 'image', 'max:2048'],
            'featured_image'   => ['nullable', 'image', 'max:4096'],
            'price_range'        => ['nullable', 'string', 'max:100'],
            'estimated_duration' => ['nullable', 'string', 'max:100'],
        ]);

        if ($request->hasFile('image')) {
            $validated['image_path'] = $request->file('image')->store('categories', 'public');
        }

        if ($request->hasFile('featured_image')) {
            $validated['featured_image'] = $request->file('featured_image')->store('categories/featured', 'public');
        }

        $validated['slug'] = $this->uniqueSlug($validated['name']);

        unset($validated['image']);
        ProjectCategory::create($validated);

        return back()->with('success', 'Category "' . $validated['name'] . '" created.');
    }

    public function update(Request $request, ProjectCategory $category): RedirectResponse
    {
        $validated = $request->validate([
            'name'             => ['required', 'string', 'max:100', 'unique:project_categories,name,' . $category->id],
            'description'      => ['nullable', 'string', 'max:500'],
            'long_description' => ['nullable', 'string'],
            'color'            => ['required', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'parent_id'        => ['nullable', 'integer', 'exists:project_categories,id', function ($attr, $value, $fail) use ($category) {
                if ((int) $value === $category->id) {
                    $fail('A category cannot be its own parent.');
                }
                $child = ProjectCategory::find($value);
                if ($child && $child->parent_id === $category->id) {
                    $fail('Cannot set a child category as the parent (circular reference).');
                }
            }],
            'image'          => ['nullable', 'image', 'max:2048'],
            'featured_image' => ['nullable', 'image', 'max:4096'],
            'price_range'        => ['nullable', 'string', 'max:100'],
            'estimated_duration' => ['nullable', 'string', 'max:100'],
        ]);

        if ($request->hasFile('image')) {
            if ($category->image_path) {
                Storage::disk('public')->delete($category->image_path);
            }
            $validated['image_path'] = $request->file('image')->store('categories', 'public');
        }

        if ($request->hasFile('featured_image')) {
            if ($category->featured_image) {
                Storage::disk('public')->delete($category->featured_image);
            }
            $validated['featured_image'] = $request->file('featured_image')->store('categories/featured', 'public');
        }

        // Regenerate slug only if name changed
        if ($validated['name'] !== $category->name) {
            $validated['slug'] = $this->uniqueSlug($validated['name'], $category->id);
        }

        unset($validated['image']);
        $category->update($validated);

        return back()->with('success', 'Category "' . $category->name . '" updated.');
    }

    private function uniqueSlug(string $name, ?int $excludeId = null): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $i = 1;
        while (
            \DB::table('project_categories')
                ->where('slug', $slug)
                ->when($excludeId, fn ($q) => $q->where('id', '!=', $excludeId))
                ->exists()
        ) {
            $slug = $base . '-' . $i++;
        }

        return $slug;
    }

    public function destroy(ProjectCategory $category): RedirectResponse
    {
        // Promote children to root (or to grandparent) before deletion
        $category->children()->update(['parent_id' => $category->parent_id]);

        if ($category->image_path) {
            Storage::disk('public')->delete($category->image_path);
        }

        $category->delete();

        return back()->with('success', 'Category deleted. Sub-categories have been promoted.');
    }
}
