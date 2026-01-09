<?php

namespace App\Http\Controllers;

use App\Models\ReviewComment;
use App\Models\ReviewRequest;
use App\Models\ReviewReviewer;
use App\Services\AuditService;
use App\Services\StreamService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PortalReviewsController extends Controller
{
    public function show(int $reviewRequestId): View
    {
        $review = ReviewRequest::findOrFail($reviewRequestId);
        $document = $review->version->document;

        if (Auth::user()->organization_id !== $document->organization_id) {
            abort(403);
        }

        $comments = $review->comments()->where('visibility', 'public')->latest()->get();

        return view('portal.reviews.show', compact('review', 'document', 'comments'));
    }

    public function comment(Request $request, int $reviewRequestId, StreamService $stream): RedirectResponse
    {
        $review = ReviewRequest::findOrFail($reviewRequestId);
        $document = $review->version->document;

        if (Auth::user()->organization_id !== $document->organization_id) {
            abort(403);
        }

        $data = $request->validate([
            'body' => ['required', 'string'],
            'context_type' => ['required', 'string'],
            'context_json' => ['nullable', 'array'],
        ]);

        $comment = ReviewComment::create([
            'review_request_id' => $review->id,
            'author_user_id' => Auth::id(),
            'visibility' => 'public',
            'body' => $data['body'],
            'context_type' => $data['context_type'],
            'context_json' => $data['context_json'] ?? null,
            'created_at' => now(),
        ]);

        $stream->log(
            Auth::user(),
            'document',
            $document->id,
            'review_commented',
            'public',
            ['review_request_id' => $review->id, 'comment_id' => $comment->id],
            $document->organization_id
        );

        return back()->with('status', 'Kommentar gespeichert.');
    }

    public function decide(Request $request, int $reviewRequestId, StreamService $stream, AuditService $audit): RedirectResponse
    {
        $review = ReviewRequest::findOrFail($reviewRequestId);
        $document = $review->version->document;

        $reviewer = ReviewReviewer::where('review_request_id', $review->id)
            ->where('reviewer_user_id', Auth::id())
            ->first();

        if (! Auth::user()->hasRole('customer_admin') && ! $reviewer) {
            abort(403);
        }

        $data = $request->validate([
            'decision' => ['required', 'in:approved,approved_with_changes,changes_requested'],
            'decision_note' => ['nullable', 'string'],
        ]);

        $review->update([
            'status' => $data['decision'],
        ]);

        if ($reviewer) {
            $reviewer->update([
                'decision' => $data['decision'],
                'decision_note' => $data['decision_note'] ?? null,
                'decided_at' => now(),
            ]);
        }

        $stream->log(
            Auth::user(),
            'document',
            $document->id,
            'review_decided',
            'public',
            [
                'review_request_id' => $review->id,
                'decision' => $data['decision'],
                'decision_note' => $data['decision_note'] ?? null,
            ],
            $document->organization_id
        );

        $audit->log(Auth::user(), 'review_decided', 'review_request', $review->id);

        return back()->with('status', 'Entscheidung gespeichert.');
    }
}
