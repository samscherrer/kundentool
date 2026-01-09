<?php

namespace App\Http\Controllers;

use App\Models\Milestone;
use App\Models\MilestoneItem;
use App\Models\MilestoneSnapshot;
use App\Models\StreamEvent;
use App\Services\StreamService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AppMilestonesController extends Controller
{
    public function index(): View
    {
        $milestones = Milestone::latest()->get();

        return view('app.milestones.index', compact('milestones'));
    }

    public function create(): View
    {
        return view('app.milestones.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'organization_id' => ['required', 'integer'],
            'title' => ['required', 'string'],
            'type' => ['required', 'string'],
            'planned_at' => ['required', 'date'],
        ]);

        $milestone = Milestone::create([
            'tenant_id' => Auth::user()->tenant_id,
            'organization_id' => $data['organization_id'],
            'title' => $data['title'],
            'type' => $data['type'],
            'planned_at' => $data['planned_at'],
            'status' => 'planned',
            'created_by_user_id' => Auth::id(),
        ]);

        return redirect()->route('app.milestones.show', $milestone->id);
    }

    public function show(int $id): View
    {
        $milestone = Milestone::findOrFail($id);
        $items = $milestone->items()->get();

        return view('app.milestones.show', compact('milestone', 'items'));
    }

    public function addItem(Request $request, int $id): RedirectResponse
    {
        $milestone = Milestone::findOrFail($id);

        $data = $request->validate([
            'item_type' => ['required', 'string'],
            'item_id' => ['required', 'integer'],
        ]);

        MilestoneItem::create([
            'milestone_id' => $milestone->id,
            'item_type' => $data['item_type'],
            'item_id' => $data['item_id'],
        ]);

        return back()->with('status', 'Item hinzugefügt.');
    }

    public function complete(int $id, StreamService $stream): RedirectResponse
    {
        $milestone = Milestone::findOrFail($id);
        $milestone->update([
            'status' => 'done',
            'actual_at' => now(),
        ]);

        $snapshot = [
            'actual_at' => $milestone->actual_at,
            'items' => $milestone->items()->get()->toArray(),
        ];

        MilestoneSnapshot::create([
            'milestone_id' => $milestone->id,
            'snapshot_json' => $snapshot,
        ]);

        $stream->log(
            Auth::user(),
            'milestone',
            $milestone->id,
            'milestone_status_changed',
            'public',
            ['from' => 'in_progress', 'to' => 'done'],
            $milestone->organization_id
        );

        return back()->with('status', 'Meilenstein abgeschlossen.');
    }
}
