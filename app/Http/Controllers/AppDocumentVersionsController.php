<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\DocumentVersion;
use App\Services\AuditService;
use App\Services\StreamService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AppDocumentVersionsController extends Controller
{
    public function store(Request $request, int $id, StreamService $stream, AuditService $audit): RedirectResponse
    {
        $document = Document::findOrFail($id);

        $data = $request->validate([
            'file' => ['required', 'file', 'max:' . (int) env('UPLOAD_MAX_MB', 500) * 1024],
            'customer_visible' => ['nullable', 'boolean'],
            'changelog' => ['nullable', 'string'],
        ]);

        $file = $request->file('file');
        $extension = strtolower($file->getClientOriginalExtension());
        if (in_array($extension, ['php', 'exe'], true)) {
            return back()->withErrors(['file' => 'Dateityp nicht erlaubt.']);
        }

        $storageKey = 'documents/' . uniqid('doc_', true) . '.' . $extension;
        $file->storeAs('private', $storageKey);

        $nextVersion = (int) ($document->versions()->max('version_number') ?? 0) + 1;

        $version = DocumentVersion::create([
            'document_id' => $document->id,
            'version_number' => $nextVersion,
            'file_storage_key' => $storageKey,
            'original_filename' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
            'sha256_hash' => hash_file('sha256', $file->getRealPath()),
            'changelog' => $data['changelog'] ?? null,
            'customer_visible' => (bool) ($data['customer_visible'] ?? false),
            'created_by_user_id' => Auth::id(),
        ]);

        $document->update(['current_version_id' => $version->id]);

        $stream->log(
            Auth::user(),
            'document',
            $document->id,
            'doc_version_uploaded',
            $version->customer_visible ? 'public' : 'internal',
            [
                'document_id' => $document->id,
                'version_number' => $version->version_number,
                'filename' => $version->original_filename,
            ],
            $document->organization_id
        );

        $audit->log(Auth::user(), 'doc_uploaded', 'document_version', $version->id);

        return back()->with('status', 'Neue Version hochgeladen.');
    }
}
