<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\ClientFile;
use App\Models\ClientFolder;
use App\Models\Project;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ClientFileController extends Controller
{
    private const DISK = 'client_files';

    /** Root file manager */
    public function index(Request $request): View
    {
        $user = $request->user();
        $this->ensureSubscribedIfClient($user);

        $folders = ClientFolder::where('user_id', $user->id)
            ->whereNull('parent_id')
            ->withCount('files')
            ->orderBy('name')
            ->get();

        $files = ClientFile::where('user_id', $user->id)
            ->whereNull('folder_id')
            ->with('uploader:id,name')
            ->latest()
            ->get();

        return view('client.files.index', [
            'folders'       => $folders,
            'files'         => $files,
            'currentFolder' => null,
            'breadcrumbs'   => [],
        ]);
    }

    /** View contents of a specific folder */
    public function folder(Request $request, ClientFolder $clientFolder): View
    {
        $this->ensureSubscribedIfClient($request->user());
        abort_unless($clientFolder->user_id === $request->user()->id, 403);

        $user = $request->user();

        $subFolders = ClientFolder::where('user_id', $user->id)
            ->where('parent_id', $clientFolder->id)
            ->withCount('files')
            ->orderBy('name')
            ->get();

        $files = ClientFile::where('user_id', $user->id)
            ->where('folder_id', $clientFolder->id)
            ->with('uploader:id,name')
            ->latest()
            ->get();

        return view('client.files.index', [
            'folders'       => $subFolders,
            'files'         => $files,
            'currentFolder' => $clientFolder,
            'breadcrumbs'   => $this->buildBreadcrumbs($clientFolder),
        ]);
    }

    /** Create a new folder */
    public function storeFolder(Request $request): RedirectResponse
    {
        $this->ensureSubscribedIfClient($request->user());
        $request->validate([
            'name'      => ['required', 'string', 'max:100'],
            'parent_id' => ['nullable', 'integer'],
        ]);

        $user = $request->user();

        if ($request->filled('parent_id')) {
            $parent = ClientFolder::findOrFail($request->parent_id);
            abort_unless($parent->user_id === $user->id, 403);
        }

        ClientFolder::create([
            'user_id'   => $user->id,
            'parent_id' => $request->parent_id ?: null,
            'name'      => $request->name,
        ]);

        return back()->with('success', 'Folder "' . $request->name . '" created.');
    }

    /** Rename a folder */
    public function renameFolder(Request $request, ClientFolder $clientFolder): RedirectResponse
    {
        $this->ensureSubscribedIfClient($request->user());
        abort_unless($clientFolder->user_id === $request->user()->id, 403);
        $request->validate(['name' => ['required', 'string', 'max:100']]);
        $clientFolder->update(['name' => $request->name]);

        return back()->with('success', 'Folder renamed.');
    }

    /** Delete a folder and all its contents */
    public function destroyFolder(Request $request, ClientFolder $clientFolder): RedirectResponse
    {
        $this->ensureSubscribedIfClient($request->user());
        abort_unless($clientFolder->user_id === $request->user()->id, 403);
        $this->deleteFolderFilesFromDisk($clientFolder);
        $clientFolder->delete();

        return redirect()->route('client.files.index')->with('success', 'Folder deleted.');
    }

    /** Upload a file (optionally into a folder) */
    public function store(Request $request): RedirectResponse
    {
        $this->ensureSubscribedIfClient($request->user());
        $request->validate([
            'file'        => ['required', 'file', 'max:102400', 'mimes:jpg,jpeg,png,gif,webp,mp4,pdf,doc,docx,txt,zip,rar'],
            'description' => ['nullable', 'string', 'max:200'],
            'folder_id'   => ['nullable', 'integer'],
        ]);

        $user = $request->user();

        if ($request->filled('folder_id')) {
            $folder = ClientFolder::findOrFail($request->folder_id);
            abort_unless($folder->user_id === $user->id, 403);
        }

        $uploaded = $request->file('file');
        $path     = $uploaded->store("client-{$user->id}", self::DISK);

        ClientFile::create([
            'user_id'       => $user->id,
            'folder_id'     => $request->folder_id ?: null,
            'uploaded_by'   => $user->id,
            'original_name' => $uploaded->getClientOriginalName(),
            'path'          => $path,
            'mime_type'     => $uploaded->getMimeType(),
            'size'          => $uploaded->getSize(),
            'description'   => $request->input('description'),
        ]);

        return back()->with('success', 'File uploaded successfully.');
    }

    /** Download a file as attachment */
    public function download(Request $request, ClientFile $clientFile): StreamedResponse
    {
        $this->ensureSubscribedIfClient($request->user());
        $this->authorizeFileAccess($request, $clientFile);
        abort_unless(Storage::disk(self::DISK)->exists($clientFile->path), 404);

        return Storage::disk(self::DISK)->download($clientFile->path, $clientFile->original_name);
    }

    /** Stream file inline for preview */
    public function preview(Request $request, ClientFile $clientFile): HttpResponse
    {
        $this->ensureSubscribedIfClient($request->user());
        $this->authorizeFileAccess($request, $clientFile);
        abort_unless(Storage::disk(self::DISK)->exists($clientFile->path), 404);

        $content = Storage::disk(self::DISK)->get($clientFile->path);

        return response($content, 200, [
            'Content-Type'        => $clientFile->mime_type ?? 'application/octet-stream',
            'Content-Disposition' => 'inline; filename="' . addslashes($clientFile->original_name) . '"',
        ]);
    }

    /** Delete a file */
    public function destroy(Request $request, ClientFile $clientFile): RedirectResponse
    {
        $this->ensureSubscribedIfClient($request->user());
        abort_unless($clientFile->user_id === $request->user()->id, 403);
        Storage::disk(self::DISK)->delete($clientFile->path);
        $clientFile->delete();

        return back()->with('success', 'File deleted.');
    }

    // ── Helpers ──────────────────────────────────────────────────────────────

    private function authorizeFileAccess(Request $request, ClientFile $clientFile): void
    {
        $user = $request->user();

        if ($clientFile->user_id === $user->id) return;
        if ($user->role->value === 'admin') return;

        if ($user->role->value === 'freelancer') {
            $hasProject = Project::where('client_id', $clientFile->user_id)
                ->where('freelancer_id', $user->id)
                ->exists();
            if ($hasProject) return;
        }

        abort(403, 'Access denied.');
    }

    private function buildBreadcrumbs(ClientFolder $folder): array
    {
        $crumbs  = [$folder];
        $current = $folder;

        while ($current->parent_id) {
            $current = $current->parent;
            array_unshift($crumbs, $current);
        }

        return $crumbs;
    }

    private function deleteFolderFilesFromDisk(ClientFolder $folder): void
    {
        $folder->load('files', 'children');

        foreach ($folder->files as $file) {
            Storage::disk(self::DISK)->delete($file->path);
        }

        foreach ($folder->children as $child) {
            $this->deleteFolderFilesFromDisk($child);
        }
    }

    private function ensureSubscribedIfClient($user): void
    {
        if ($user->role->value === 'client' && ! $user->hasFullAccess()) {
            $message = $user->hasUsedTrial()
                ? 'Your free trial has expired. Subscribe to continue.'
                : 'An active subscription or free trial is required to access client files.';
            abort(403, $message);
        }
    }
}
