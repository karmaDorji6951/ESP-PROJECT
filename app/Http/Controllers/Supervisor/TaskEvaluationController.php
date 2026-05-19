<?php

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\TaskEvaluation;
use Illuminate\Http\Request;

class TaskEvaluationController extends Controller
{
    public function create(Task $task)
    {
        if ($task->assigned_by !== auth()->id()) {
            abort(404);
        }

        $task->load([
            'employee.user.role',
            'latestSubmission.submitter.role',
            'evaluation',
        ]);

        $submission = $task->latestSubmission;
        if (!$submission) {
            return redirect()->route('supervisor.tasks.show', $task)->with('error', 'No submission found for this task.');
        }

        if ($task->status !== 'Completed') {
            return redirect()->route('supervisor.tasks.show', $task)->with('error', 'This task is not marked as completed yet.');
        }

        $employee = $task->employee;
        $staffUser = $employee?->user;

        if (!$employee || !$staffUser || optional($staffUser->role)->slug !== 'staff') {
            return redirect()->route('supervisor.tasks.show', $task)->with('error', 'Evaluation is only available for staff (employee-linked) tasks.');
        }

        if ((int) $submission->submitted_by !== (int) $staffUser->id) {
            return redirect()->route('supervisor.tasks.show', $task)->with('error', 'The latest submission was not submitted by the assigned staff member.');
        }

        $evaluation = $task->evaluation;

        return view('supervisor.tasks.evaluate', compact('task', 'submission', 'evaluation', 'staffUser', 'employee'));
    }

    public function store(Request $request, Task $task)
    {
        if ($task->assigned_by !== auth()->id()) {
            abort(404);
        }

        $task->load(['employee.user.role', 'latestSubmission.submitter.role']);

        $submission = $task->latestSubmission;
        if (!$submission) {
            return redirect()->route('supervisor.tasks.show', $task)->with('error', 'No submission found for this task.');
        }

        if ($task->status !== 'Completed') {
            return redirect()->route('supervisor.tasks.show', $task)->with('error', 'This task is not marked as completed yet.');
        }

        $employee = $task->employee;
        $staffUser = $employee?->user;

        if (!$employee || !$staffUser || optional($staffUser->role)->slug !== 'staff') {
            return redirect()->route('supervisor.tasks.show', $task)->with('error', 'Evaluation is only available for staff (employee-linked) tasks.');
        }

        if ((int) $submission->submitted_by !== (int) $staffUser->id) {
            return redirect()->route('supervisor.tasks.show', $task)->with('error', 'The latest submission was not submitted by the assigned staff member.');
        }

        $validated = $request->validate([
            'quality' => ['nullable', 'integer', 'min:1', 'max:5'],
            'timeliness' => ['nullable', 'integer', 'min:1', 'max:5'],
            'evidence' => ['nullable', 'integer', 'min:1', 'max:5'],
            'remarks' => ['nullable', 'string', 'max:2000'],
        ]);

        $criteria = [
            'quality' => $validated['quality'] ?? null,
            'timeliness' => $validated['timeliness'] ?? null,
            'evidence' => $validated['evidence'] ?? null,
        ];

        $scores = array_values(array_filter($criteria, fn ($v) => $v !== null));
        if (count($scores) === 0) {
            return redirect()->route('supervisor.tasks.evaluation.create', $task)
                ->withErrors(['quality' => 'Select at least one evaluation criterion (Quality, Timeliness, or Evidence).'])
                ->withInput();
        }

        $average = array_sum($scores) / count($scores);
        $rating = (int) max(1, min(5, (int) round($average)));
        $grade = match (true) {
            $average >= 4.5 => 'A',
            $average >= 3.5 => 'B',
            $average >= 2.5 => 'C',
            $average >= 1.5 => 'D',
            $average >= 1.0 => 'E',
            default => 'F',
        };

        TaskEvaluation::updateOrCreate(
            ['task_id' => $task->id],
            [
                'task_submission_id' => $submission->id,
                'evaluated_by' => auth()->id(),
                'staff_user_id' => $staffUser->id,
                'criteria' => $criteria,
                'rating' => $rating,
                'grade' => $grade,
                'remarks' => $validated['remarks'] ?? null,
                'evaluated_at' => now(),
            ]
        );

        return redirect()->route('supervisor.tasks.show', $task)->with('success', 'Task evaluation saved.');
    }
}
