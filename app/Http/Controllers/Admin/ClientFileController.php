<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClientFile;
use App\Models\ClientFolder;
use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Http\Request;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ClientFileController extends Controller
{
    private const DISK = 'client_files';

    public function index(): View
    {
        $clients = User::where('role', UserRole::CLIENT)
            ->withCount(['clientFiles as files_count', 'clientFolders as folders_count'])
            ->orderBy('name')
            ->get(['id', 'name', 'email', 'created_at']);

        return view('admin.client-files.index', compact('clients'));
    }

    /** Per-client workspace root */
    public function showClient(User $user): View
    {
        abort_unless($user->role->value === 'client', 404);

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

        return view('admin.client-files.show', [
            'client'        => $user,
            'folders'       => $folders,
            'files'         => $files,
            'currentFolder' => null,
            'breadcrumbs'   => [],
        ]);
    }

    /** Per-client folder view */
    public function showClientFolder(User $user, ClientFolder $clientFolder): View
    {
        abort_unless($user->role->value === 'client', 404);
        abort_unless($clientFolder->user_id === $user->id, 403);

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

        return view('admin.client-files.show', [
            'client'        => $user,
            'folders'       => $subFolders,
            'files'         => $files,
            'currentFolder' => $clientFolder,
            'breadcrumbs'   => $this->buildBreadcrumbs($clientFolder),
        ]);
    }

    public function download(ClientFile $clientFile): StreamedResponse
    {
        abort_unless(Storage::disk(self::DISK)->exists($clientFile->path), 404);

        return Storage::disk(self::DISK)->download($clientFile->path, $clientFile->original_name);
    }

    /** Stream file inline for preview */
    public function preview(ClientFile $clientFile): HttpResponse
    {
        abort_unless(Storage::disk(self::DISK)->exists($clientFile->path), 404);

        $content = Storage::disk(self::DISK)->get($clientFile->path);

        return response($content, 200, [
            'Content-Type'        => $clientFile->mime_type ?? 'application/octet-stream',
            'Content-Disposition' => 'inline; filename="' . addslashes($clientFile->original_name) . '"',
        ]);
    }

    public function destroy(ClientFile $clientFile): \Illuminate\Http\RedirectResponse
    {
        Storage::disk(self::DISK)->delete($clientFile->path);
        $clientFile->delete();

        return back()->with('success', 'File deleted.');
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
}
