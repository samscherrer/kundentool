<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Milestone;
use App\Models\Task;
use App\Models\Ticket;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class PortalMilestonesController extends Controller
{
    public function index(): View
    {
        $milestones = Milestone::where('organization_id', Auth::user()->organization_id)
            ->latest()
            ->get();

        return view('portal.milestones.index', compact('milestones'));
    }

    public function show(int $id): View
    {
        $milestone = Milestone::where('organization_id', Auth::user()->organization_id)
            ->findOrFail($id);

        $visibleItems = [];
        foreach ($milestone->items as $item) {
            if ($item->item_type === 'ticket') {
                $ticket = Ticket::where('organization_id', Auth::user()->organization_id)
                    ->find($item->item_id);
                if ($ticket) {
                    $visibleItems[] = ['type' => 'ticket', 'item' => $ticket];
                }
            }

            if ($item->item_type === 'task') {
                $task = Task::find($item->item_id);
                if ($task && $task->assigned_to_customer_user_id) {
                    $visibleItems[] = ['type' => 'task', 'item' => $task];
                }
            }

            if ($item->item_type === 'document') {
                $document = Document::find($item->item_id);
                if ($document && $document->currentVersion && $document->currentVersion->customer_visible) {
                    $visibleItems[] = ['type' => 'document', 'item' => $document];
                }
            }
        }

        return view('portal.milestones.show', compact('milestone', 'visibleItems'));
    }
}
