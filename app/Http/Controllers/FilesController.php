<?php

namespace App\Http\Controllers;

use App\Models\DocumentVersion;
use App\Models\FileAccessLog;
use App\Services\AuditService;
use App\Services\StreamService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FilesController extends Controller
{
    public function show(Request $request, int $id, AuditService $audit, StreamService $stream): StreamedResponse
    {
        $version = DocumentVersion::findOrFail($id);
        $document = $version->document;

        if (Auth::user()->tenant_id !== $document->tenant_id) {
            abort(403);
        }

        if (Auth::user()->isCustomer() && ! $version->customer_visible) {
            abort(403);
        }

        if (Auth::user()->isCustomer() && Auth::user()->organization_id !== $document->organization_id) {
            abort(403);
        }

        FileAccessLog::create([
            'tenant_id' => Auth::user()->tenant_id,
            'user_id' => Auth::id(),
            'document_version_id' => $version->id,
            'action' => 'download',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        $audit->log(Auth::user(), 'file_downloaded', 'document_version', $version->id);
        $stream->log(
            Auth::user(),
            'document',
            $document->id,
            'file_downloaded',
            'internal',
            ['document_version_id' => $version->id],
            $document->organization_id
        );

        $path = storage_path('app/private/' . $version->file_storage_key);
        $size = filesize($path);
        $start = 0;
        $end = $size - 1;

        $headers = [
            'Content-Type' => $version->mime_type,
            'Content-Disposition' => ($request->query('download') ? 'attachment' : 'inline') . '; filename="' . $version->original_filename . '"',
            'Accept-Ranges' => 'bytes',
        ];

        if ($request->hasHeader('Range')) {
            $range = $request->header('Range');
            if (preg_match('/bytes=(\d+)-(\d*)/', $range, $matches)) {
                $start = (int) $matches[1];
                $end = $matches[2] !== '' ? (int) $matches[2] : $end;
            }

            $headers['Content-Range'] = "bytes {$start}-{$end}/{$size}";
            $headers['Content-Length'] = $end - $start + 1;
            $status = 206;
        } else {
            $headers['Content-Length'] = $size;
            $status = 200;
        }

        return response()->stream(function () use ($path, $start, $end) {
            $handle = fopen($path, 'rb');
            fseek($handle, $start);
            $remaining = $end - $start + 1;

            while ($remaining > 0 && ! feof($handle)) {
                $chunk = fread($handle, min(8192, $remaining));
                $remaining -= strlen($chunk);
                echo $chunk;
            }

            fclose($handle);
        }, $status, $headers);
    }
}
