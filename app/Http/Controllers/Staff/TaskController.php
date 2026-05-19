<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\TaskSubmission;
use App\Models\User;
use App\Notifications\TaskCompletedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;

class TaskController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $tasks = Task::where('employee_id', $user->employee_id)->paginate(15);
        return view('staff.tasks.index', compact('tasks'));
    }

    public function show(Task $task)
    {
        $user = Auth::user();
        if ($task->employee_id != $user->employee_id) {
            abort(403);
        }

        $task->load([
            'assigner',
            'evaluation.evaluator',
            'latestSubmission',
        ]);

        return view('staff.tasks.show', compact('task'));
    }

    public function perform(Request $request, Task $task)
    {
        try {
            $user = Auth::user();
            
            // Verify the user owns this task
            if ($task->employee_id != $user->employee_id) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
            }

            // Validate request
            $validated = $request->validate([
                'notes' => 'required|string|min:10',
                'status' => 'required|in:In Progress,Completed',
                'photo_evidence' => 'nullable|file|max:10240', // 10MB max, any file type
            ]);

            // Handle file upload
            $filePath = null;
            if ($request->hasFile('photo_evidence')) {
                $file = $request->file('photo_evidence');
                $filePath = $file->store('task_evidence', 'public');
            }

            // Update task status
            $task->update([
                'status' => $validated['status'],
                'completed_at' => $validated['status'] === 'Completed' ? now() : null,
            ]);

            // Keep timetable entry (if any) in sync
            if ($task->timetable) {
                $timetableStatus = match ($validated['status']) {
                    'Completed' => 'completed',
                    'In Progress' => 'in_progress',
                    default => 'scheduled',
                };

                $task->timetable->update([
                    'status' => $timetableStatus,
                ]);
            }

            // Create task submission record
            TaskSubmission::create([
                'task_id' => $task->id,
                'submitted_by' => $user->id,
                'submission_notes' => $validated['notes'],
                'submission_status' => $validated['status'] === 'Completed' ? 'Completed' : 'In Progress',
                'submitted_at' => now(),
                'photo_evidence' => $filePath,
            ]);

            // Notify supervisor if task is completed
            if ($validated['status'] === 'Completed') {
                $this->notifySupervisor($task, $user);
            }

            return response()->json([
                'success' => true,
                'message' => 'Work submitted successfully' . ($filePath ? ' with evidence file' : ''),
                'task_status' => $validated['status'],
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Task submission error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while submitting your work. Please try again.'
            ], 500);
        }
    }

    private function notifySupervisor(Task $task, User $staff)
    {
        try {
            // Notify the supervisor who assigned the task
            $supervisor = User::query()
                ->whereKey($task->assigned_by)
                ->whereHas('role', function ($query) {
                    $query->where('slug', 'supervisor');
                })
                ->first();

            if (! $supervisor) {
                return;
            }

            // Create notification data
            $notificationData = [
                'task_id' => $task->id,
                'title' => $task->title,
                'description' => $task->description,
                'completed_by' => $staff->name,
                'completed_at' => now()->format('Y-m-d H:i:s'),
                'message' => "Task '{$task->title}' has been completed by {$staff->name}",
                'type' => 'task_completed',
            ];

            // Send immediately (avoid queue worker requirement)
            Notification::sendNow($supervisor, new TaskCompletedNotification($notificationData));

        } catch (\Exception $e) {
            \Log::error('Supervisor notification error: ' . $e->getMessage());
            // Don't fail the submission if notification fails
        }
    }
}
