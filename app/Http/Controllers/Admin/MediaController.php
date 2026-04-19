<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\Media;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class MediaController extends Controller
{
    private const DISK      = 'public';
    private const DIRECTORY = 'media';

    /**
     * Allowed MIME types and their max sizes (bytes).
     * 20 MB for images/documents, 100 MB for video.
     */
    private const ALLOWED = [
        // images
        'image/jpeg'    => 20_971_520,
        'image/png'     => 20_971_520,
        'image/gif'     => 20_971_520,
        'image/webp'    => 20_971_520,
        'image/svg+xml' => 5_242_880,
        // video
        'video/mp4'     => 104_857_600,
        'video/webm'    => 104_857_600,
        'video/ogg'     => 104_857_600,
        // documents
        'application/pdf'                                                      => 20_971_520,
        'application/msword'                                                   => 20_971_520,
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 20_971_520,
        'application/vnd.ms-excel'                                             => 20_971_520,
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'   => 20_971_520,
        'text/plain'                                                           => 5_242_880,
    ];

    public function index(Request $request): View
    {
        $query = $this->scopeForViewer(Media::with('uploader')->latest(), $request);

        if ($type = $request->query('type')) {
            $query->where('file_type', $type);
        }

        if ($search = trim((string) $request->query('search', ''))) {
            $query->where('file_name', 'like', '%' . $search . '%');
        }

        $media = $query->paginate(24)->withQueryString();

        $base = $this->scopeForViewer(Media::query(), $request);
        $counts = [
            'all'      => (clone $base)->count(),
            'image'    => (clone $base)->where('file_type', 'image')->count(),
            'video'    => (clone $base)->where('file_type', 'video')->count(),
            'document' => (clone $base)->where('file_type', 'document')->count(),
        ];

        return view('admin.media.index', compact('media', 'counts'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'files'   => ['required', 'array', 'min:1'],
            'files.*' => ['required', 'file', 'max:102400'], // 100 MB hard cap
        ]);

        $uploaded = 0;

        foreach ($request->file('files') as $file) {
            $mime = $file->getMimeType() ?? '';

            // Reject disallowed MIME types
            if (! array_key_exists($mime, self::ALLOWED)) {
                continue;
            }

            // Enforce per-type size limit
            if ($file->getSize() > self::ALLOWED[$mime]) {
                continue;
            }

            $path = $file->store(self::DIRECTORY, self::DISK);

            Media::create([
                'file_name'   => $file->getClientOriginalName(),
                'file_path'   => $path,
                'file_type'   => Media::typeFromMime($mime),
                'size'        => $file->getSize(),
                'uploaded_by' => $request->user()->id,
            ]);

            $uploaded++;
        }

        $message = $uploaded > 0
            ? $uploaded . ' ' . ($uploaded === 1 ? 'file' : 'files') . ' uploaded successfully.'
            : 'No valid files were uploaded. Check file types and sizes.';

        return back()->with($uploaded > 0 ? 'success' : 'error', $message);
    }

    /**
     * Return media items as JSON for reusable media pickers.
     */
    public function api(Request $request): JsonResponse
    {
        $query = $this->scopeForViewer(Media::query(), $request)
            ->when($request->filled('type'), function ($builder) use ($request) {
                $type = (string) $request->query('type');
                if (in_array($type, ['image', 'video', 'document'], true)) {
                    $builder->where('file_type', $type);
                }
            })
            ->when($request->filled('search'), function ($builder) use ($request) {
                $search = trim((string) $request->query('search'));
                $builder->where('file_name', 'like', '%' . $search . '%');
            })
            ->latest()
            ->limit(300);

        $media = $query
            ->get(['id', 'file_name', 'file_path', 'file_type', 'size'])
            ->map(fn ($m) => [
                'id'       => $m->id,
                'name'     => $m->file_name,
                'type'     => $m->file_type,
                'url'      => $m->url(),
                'size'     => $m->humanSize(),
            ]);

        return response()->json($media);
    }

    /**
     * Async upload for modal pickers (WordPress-like flow).
     */
    public function apiUpload(Request $request): JsonResponse
    {
        $request->validate([
            'files'   => ['required', 'array', 'min:1'],
            'files.*' => ['required', 'file', 'max:102400'],
        ]);

        $uploaded = [];

        foreach ($request->file('files') as $file) {
            $mime = $file->getMimeType() ?? '';

            if (! array_key_exists($mime, self::ALLOWED)) {
                continue;
            }

            if ($file->getSize() > self::ALLOWED[$mime]) {
                continue;
            }

            $path = $file->store(self::DIRECTORY, self::DISK);

            $media = Media::create([
                'file_name'   => $file->getClientOriginalName(),
                'file_path'   => $path,
                'file_type'   => Media::typeFromMime($mime),
                'size'        => $file->getSize(),
                'uploaded_by' => $request->user()->id,
            ]);

            $uploaded[] = [
                'id'   => $media->id,
                'name' => $media->file_name,
                'type' => $media->file_type,
                'url'  => $media->url(),
                'size' => $media->humanSize(),
            ];
        }

        return response()->json([
            'items' => $uploaded,
            'count' => count($uploaded),
        ]);
    }

    public function destroy(Media $medium): RedirectResponse
    {
        Storage::disk(self::DISK)->delete($medium->file_path);
        $medium->delete();

        return back()->with('success', '"' . $medium->file_name . '" deleted.');
    }

    private function scopeForViewer(Builder $query, Request $request): Builder
    {
        $user = $request->user();

        if (! $user || $user->role !== UserRole::ADMIN) {
            $query->where('uploaded_by', $user?->id ?? 0);
        }

        return $query;
    }
}
