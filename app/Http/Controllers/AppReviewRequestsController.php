<?php

namespace App\Http\Controllers;

use App\Models\DocumentVersion;
use App\Models\ReviewRequest;
use App\Models\ReviewReviewer;
use App\Services\AuditService;
use App\Services\StreamService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AppReviewRequestsController extends Controller
{
    public function store(Request $request, int $id, StreamService $stream, AuditService $audit): RedirectResponse
    {
        $version = DocumentVersion::findOrFail($id);

        $data = $request->validate([
            'message_to_customer' => ['nullable', 'string'],
            'reviewer_user_ids' => ['nullable', 'array'],
            'reviewer_user_ids.*' => ['integer'],
        ]);

        $review = ReviewRequest::create([
            'document_version_id' => $version->id,
            'status' => 'open',
            'requested_by_user_id' => Auth::id(),
            'requested_at' => now(),
            'message_to_customer' => $data['message_to_customer'] ?? null,
        ]);

        foreach ($data['reviewer_user_ids'] ?? [] as $reviewerId) {
            ReviewReviewer::create([
                'review_request_id' => $review->id,
                'reviewer_user_id' => $reviewerId,
                'role' => 'approver',
            ]);
        }

        $stream->log(
            Auth::user(),
            'document',
            $version->document_id,
            'review_requested',
            'public',
            [
                'review_request_id' => $review->id,
                'document_id' => $version->document_id,
                'version_number' => $version->version_number,
            ],
            $version->document->organization_id
        );

        $audit->log(Auth::user(), 'review_requested', 'review_request', $review->id);

        return back()->with('status', 'Review angefordert.');
    }
}
