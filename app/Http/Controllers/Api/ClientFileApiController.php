<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ClientFile;
use App\Models\ClientFolder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ClientFileApiController extends Controller
{
    /** List files + folders at root or inside a folder */
    public function index(Request $request): JsonResponse
    {
        $user     = $request->user();
        $folderId = $request->query('folder_id');

        $folders = ClientFolder::where('user_id', $user->id)
            ->where('parent_id', $folderId)
            ->orderBy('name')
            ->get()
            ->map(fn ($f) => [
                'id'         => $f->id,
                'name'       => $f->name,
                'type'       => 'folder',
                'parent_id'  => $f->parent_id,
                'created_at' => $f->created_at->toIso8601String(),
            ]);

        $files = ClientFile::where('user_id', $user->id)
            ->where('folder_id', $folderId)
            ->orderByDesc('created_at')
            ->get()
            ->map(fn ($f) => [
                'id'            => $f->id,
                'name'          => $f->original_name,
                'type'          => 'file',
                'mime_type'     => $f->mime_type,
                'size'          => $f->size,
                'size_formatted'=> $f->formattedSize(),
                'is_image'      => $f->isImage(),
                'is_pdf'        => $f->isPdf(),
                'is_previewable'=> $f->isPreviewable(),
                'url'           => $f->url(),
                'folder_id'     => $f->folder_id,
                'created_at'    => $f->created_at->toIso8601String(),
            ]);

        // Breadcrumb trail
        $breadcrumbs = [];
        if ($folderId) {
            $current = ClientFolder::find($folderId);
            while ($current) {
                array_unshift($breadcrumbs, ['id' => $current->id, 'name' => $current->name]);
                $current = $current->parent;
            }
        }

        return response()->json([
            'folders'     => $folders,
            'files'       => $files,
            'breadcrumbs' => $breadcrumbs,
            'current_folder_id' => $folderId,
        ]);
    }

    /** Upload a file */
    public function upload(Request $request): JsonResponse
    {
        $request->validate([
            'file'        => ['required', 'file', 'max:51200'], // 50MB
            'folder_id'   => ['nullable', 'exists:client_folders,id'],
            'description' => ['nullable', 'string', 'max:255'],
        ]);

        $user = $request->user();
        $file = $request->file('file');

        $path = $file->store("client-files/{$user->id}", 'public');

        $record = ClientFile::create([
            'user_id'       => $user->id,
            'folder_id'     => $request->folder_id,
            'uploaded_by'   => $user->id,
            'original_name' => $file->getClientOriginalName(),
            'path'          => $path,
            'mime_type'     => $file->getClientMimeType(),
            'size'          => $file->getSize(),
            'description'   => $request->description,
        ]);

        return response()->json([
            'message' => 'File uploaded successfully.',
            'file'    => [
                'id'             => $record->id,
                'name'           => $record->original_name,
                'size_formatted' => $record->formattedSize(),
                'mime_type'      => $record->mime_type,
                'url'            => $record->url(),
            ],
        ], 201);
    }

    /** Download / get file URL */
    public function download(ClientFile $file): JsonResponse
    {
        $this->authorizeFile($file);

        return response()->json([
            'url'  => $file->url(),
            'name' => $file->original_name,
        ]);
    }

    /** Delete a file */
    public function destroy(Request $request, ClientFile $file): JsonResponse
    {
        $this->authorizeFile($file);

        Storage::disk('public')->delete($file->path);
        $file->delete();

        return response()->json(['message' => 'File deleted.']);
    }

    /** Create folder */
    public function createFolder(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name'      => ['required', 'string', 'max:100'],
            'parent_id' => ['nullable', 'exists:client_folders,id'],
        ]);

        $folder = ClientFolder::create([
            'user_id'   => $request->user()->id,
            'parent_id' => $data['parent_id'] ?? null,
            'name'      => $data['name'],
        ]);

        return response()->json([
            'message' => 'Folder created.',
            'folder'  => [
                'id'   => $folder->id,
                'name' => $folder->name,
                'type' => 'folder',
            ],
        ], 201);
    }

    /** Rename folder */
    public function renameFolder(Request $request, ClientFolder $folder): JsonResponse
    {
        abort_unless($folder->user_id === $request->user()->id, 403);

        $data = $request->validate(['name' => ['required', 'string', 'max:100']]);
        $folder->update($data);

        return response()->json(['message' => 'Folder renamed.']);
    }

    /** Delete folder */
    public function destroyFolder(Request $request, ClientFolder $folder): JsonResponse
    {
        abort_unless($folder->user_id === $request->user()->id, 403);

        // Delete all files in folder
        $folder->files->each(function ($file) {
            Storage::disk('public')->delete($file->path);
            $file->delete();
        });

        // Delete sub-folders recursively
        $folder->children->each(function ($child) use ($request) {
            $this->destroyFolder($request, $child);
        });

        $folder->delete();

        return response()->json(['message' => 'Folder deleted.']);
    }

    private function authorizeFile(ClientFile $file): void
    {
        abort_unless($file->user_id === request()->user()->id, 403);
    }
}
