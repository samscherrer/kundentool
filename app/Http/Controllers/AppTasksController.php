<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Worklog;
use App\Services\StreamService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AppTasksController extends Controller
{
    public function addWorklog(Request $request, int $id, StreamService $stream): RedirectResponse
    {
        $task = Task::findOrFail($id);

        $data = $request->validate([
            'work_date' => ['required', 'date'],
            'hours' => ['required', 'numeric', 'min:0.25'],
            'billable' => ['nullable', 'boolean'],
            'description_internal' => ['required', 'string'],
            'description_invoice' => ['nullable', 'string'],
        ]);

        $worklog = Worklog::create([
            'tenant_id' => $task->tenant_id,
            'organization_id' => $task->organization_id,
            'user_id' => Auth::id(),
            'task_id' => $task->id,
            'work_date' => $data['work_date'],
            'hours' => $data['hours'],
            'billable' => (bool) ($data['billable'] ?? true),
            'description_internal' => $data['description_internal'],
            'description_invoice' => $data['description_invoice'] ?? null,
        ]);

        $stream->log(
            Auth::user(),
            'task',
            $task->id,
            'worklog_added',
            'internal',
            ['hours' => $worklog->hours, 'ref_type' => 'task', 'ref_id' => $task->id],
            $task->organization_id
        );

        return back()->with('status', 'Stunden erfasst.');
    }
}
