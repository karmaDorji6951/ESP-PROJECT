<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Task;
use App\Models\Employee;
use App\Models\Department;

class FeedbackController extends Controller
{
    public function index()
    {
        $received = Feedback::query()
            ->with(['sender', 'recipient', 'buildingDepartment', 'areaDepartment'])
            ->where('recipient_user_id', auth()->id())
            ->latest()
            ->paginate(15);

        return view('feedback.index', [
            'received' => $received,
        ]);
    }

    public function create()
    {
        $users = User::query()
            ->select(['id', 'name'])
            ->where('id', '!=', auth()->id())
            ->orderBy('name')
            ->get();

        $departments = Department::with('children')
            ->whereNull('parent_id')
            ->orderBy('name')
            ->get();

        $departmentsForJs = $departments
            ->map(function ($department) {
                return [
                    'id' => $department->id,
                    'name' => $department->name,
                    'children' => ($department->children ?? collect())
                        ->map(function ($child) {
                            return [
                                'id' => $child->id,
                                'name' => $child->name,
                            ];
                        })
                        ->values(),
                ];
            })
            ->values();

        return view('feedback.create', [
            'users' => $users,
            'departments' => $departments,
            'departmentsForJs' => $departmentsForJs,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'subject' => ['required', 'string', 'min:3', 'max:120'],
            'message' => ['required', 'string', 'min:5', 'max:5000'],
            'feedback_type' => ['required', 'string', 'in:Complaint,Suggestion,Appreciation'],
            'priority' => ['required', 'string', 'in:Low,Medium,High'],
            'is_anonymous' => ['nullable', 'boolean'],
            'building_department_id' => ['nullable', 'integer', 'exists:departments,id'],
            'area_department_id' => ['nullable', 'integer', 'exists:departments,id'],
            'recipient_user_id' => ['nullable', 'integer', 'exists:users,id'],
            'recipient_user_ids' => ['nullable', 'array'],
            'recipient_user_ids.*' => ['integer', 'exists:users,id'],
        ]);

        $buildingDepartmentId = $validated['building_department_id'] ?? null;
        $areaDepartmentId = $validated['area_department_id'] ?? null;
        $targetDepartmentId = $areaDepartmentId ?: $buildingDepartmentId;

        $payload = [
            'user_id' => auth()->id(),
            'subject' => $validated['subject'],
            'message' => $validated['message'],
            'feedback_type' => $validated['feedback_type'],
            'priority' => $validated['priority'],
            'status' => 'Pending',
            'is_anonymous' => (bool) ($validated['is_anonymous'] ?? false),
            'building_department_id' => $buildingDepartmentId,
            'area_department_id' => $areaDepartmentId,
        ];

        $created = 0;

        // If explicit recipients provided (checkboxes/select), create for each
        if (!empty($validated['recipient_user_ids'])) {
            $ids = array_unique($validated['recipient_user_ids']);
            foreach ($ids as $rid) {
                Feedback::create($payload + ['recipient_user_id' => $rid]);
                $created++;
            }
        }

        // If a building or area is selected, deliver feedback to assigned staff there.
        if (!empty($targetDepartmentId)) {
            $deptId = (int) $targetDepartmentId;

            // include child areas when a building is selected
            if (!empty($areaDepartmentId)) {
                $deptIds = [$deptId];
            } else {
                $deptIds = Department::query()
                    ->where('id', $deptId)
                    ->orWhere('parent_id', $deptId)
                    ->pluck('id')
                    ->toArray();
            }

            $employees = Employee::query()
                ->whereIn('department_id', $deptIds)
                ->with('user')
                ->get();

            foreach ($employees as $emp) {
                if ($emp->user && $emp->user->id) {
                    // Avoid duplicate creation if already created via explicit recipients
                    if (!empty($validated['recipient_user_ids']) && in_array($emp->user->id, $validated['recipient_user_ids'])) {
                        continue;
                    }
                    Feedback::create($payload + ['recipient_user_id' => $emp->user->id]);
                    $created++;
                }
            }
        }

        // Fallback: single recipient_user_id (legacy behaviour)
        if ($created === 0 && $request->filled('recipient_user_id')) {
            Feedback::create($payload + ['recipient_user_id' => $validated['recipient_user_id']]);
            $created++;
        }

        return redirect()
            ->route('feedback.index')
            ->with('success', $created > 0 ? 'Feedback submitted. Thank you!' : 'No recipients found for the selected building or area.');
    }

    /**
     * Return tasks and assigned staff for a given building or area as JSON.
     */
    public function departmentTasks(Request $request)
    {
        $deptId = $request->query('department_id');
        if (!$deptId) {
            return response()->json(['tasks' => []]);
        }

        $deptIds = Department::query()->where('id', $deptId)->orWhere('parent_id', $deptId)->pluck('id')->toArray();

        $tasks = Task::query()
            ->with(['employee.user', 'submissions'])
            ->whereHas('employee', function ($q) use ($deptIds) {
                $q->whereIn('department_id', $deptIds);
            })
            ->orderByDesc('created_at')
            ->take(200)
            ->get();

        $employees = Employee::query()
            ->with('user')
            ->whereIn('department_id', $deptIds)
            ->orderBy('name')
            ->get();

        $payload = $tasks->map(function ($t) {
            return [
                'id' => $t->id,
                'title' => $t->title,
                'employee_name' => optional($t->employee)->name,
                'employee_user_id' => optional(optional($t->employee)->user)->id,
                'submissions_count' => $t->submissions->count(),
                'latest_submission_at' => optional($t->submissions->sortByDesc('submitted_at')->first())->submitted_at,
            ];
        });

        $staffPayload = $employees->map(function ($employee) {
            return [
                'employee_id' => $employee->id,
                'name' => $employee->name,
                'role_title' => $employee->role_title,
                'user_id' => $employee->user?->id,
            ];
        });

        return response()->json(['tasks' => $payload, 'staff' => $staffPayload]);
    }
}
