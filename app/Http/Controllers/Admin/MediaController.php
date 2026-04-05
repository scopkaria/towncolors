<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Media;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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
        $query = Media::with('uploader')->latest();

        if ($type = $request->query('type')) {
            $query->where('file_type', $type);
        }

        if ($search = trim((string) $request->query('search', ''))) {
            $query->where('file_name', 'like', '%' . $search . '%');
        }

        $media = $query->paginate(24)->withQueryString();

        $counts = [
            'all'      => Media::count(),
            'image'    => Media::where('file_type', 'image')->count(),
            'video'    => Media::where('file_type', 'video')->count(),
            'document' => Media::where('file_type', 'document')->count(),
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
     * Return image-type media as JSON for the settings logo picker.
     */
    public function api(): \Illuminate\Http\JsonResponse
    {
        $media = Media::where('file_type', 'image')
            ->latest()
            ->get(['id', 'file_name', 'file_path'])
            ->map(fn ($m) => [
                'id'       => $m->id,
                'name'     => $m->file_name,
                'url'      => $m->url(),
            ]);

        return response()->json($media);
    }

    public function destroy(Media $medium): RedirectResponse
    {
        Storage::disk(self::DISK)->delete($medium->file_path);
        $medium->delete();

        return back()->with('success', '"' . $medium->file_name . '" deleted.');
    }
}
