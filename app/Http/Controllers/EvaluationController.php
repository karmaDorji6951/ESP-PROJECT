<?php

namespace App\Http\Controllers;

use App\Models\Evaluation;
use App\Models\Role;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

class EvaluationController extends Controller
{
    public function create(Request $request)
    {
        $evaluatedType = $request->query('evaluated_type');
        $evaluatedId = $request->query('evaluated_id');
        $evaluatedTask = null;
        $selectedRoleId = null;

        if ($evaluatedType === Task::class && $evaluatedId) {
            $evaluatedTask = Task::with(['employee.user.role', 'assigner', 'timetable', 'evaluation', 'latestEvaluation'])->find($evaluatedId);
            if ($evaluatedTask && $evaluatedTask->status !== 'Completed') {
                return redirect()->back()->with('error', 'This task is not marked as completed yet.');
            }
            if ($evaluatedTask && $evaluatedTask->reviewed_evaluation) {
                return redirect()->back()->with('warning', 'This task has already been evaluated.');
            }
            $selectedRoleId = $evaluatedTask?->employee?->user?->role?->id;
        }

        $evaluationTaskGroups = Role::query()
            ->orderBy('name')
            ->get()
            ->map(function (Role $role) {
                $tasks = Task::with(['employee.user.role', 'assigner', 'timetable', 'evaluation', 'latestEvaluation'])
                    ->where('status', 'Completed')
                    ->whereDoesntHave('evaluation')
                    ->whereDoesntHave('evaluations')
                    ->whereHas('employee.user.role', function ($query) use ($role) {
                        $query->whereKey($role->id);
                    })
                    ->latest()
                    ->limit(10)
                    ->get();

                return [
                    'role' => $role,
                    'tasks' => $tasks,
                ];
            })
            ->filter(fn (array $group) => $group['tasks']->isNotEmpty())
            ->values();

        if (! $evaluatedTask) {
            $evaluatedTask = $evaluationTaskGroups->first()['tasks']->first() ?? null;
            $selectedRoleId = $evaluationTaskGroups->first()['role']->id ?? null;
        }

        if (! $selectedRoleId && $evaluatedTask) {
            $selectedRoleId = $evaluatedTask->employee?->user?->role?->id;
        }

        return view('evaluations.create', compact('evaluatedTask', 'evaluationTaskGroups', 'selectedRoleId'));
    }

    public function index()
    {
        $evaluations = Evaluation::with('user')->latest()->paginate(20);

        $evaluations->getCollection()->transform(function (Evaluation $evaluation) {
            if ($this->isTaskEvaluationType($evaluation->evaluated_type) && $evaluation->evaluated_id) {
                $task = Task::with(['employee', 'assigner', 'timetable'])->find($evaluation->evaluated_id);
                $evaluation->setRelation('evaluated', $task);
            }

            return $evaluation;
        });

        return view('supervisor.evaluations.index', compact('evaluations'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'evaluated_id' => 'nullable|integer',
            'evaluated_type' => 'nullable|string',
            'scores' => 'nullable',
            'comments' => 'nullable|string',
            'attachments' => 'nullable|file|max:10240',
        ]);

        $evaluatedType = $this->normalizeEvaluatedType($data['evaluated_type'] ?? null);
        $evaluatedId = $data['evaluated_id'] ?? null;

        if ($evaluatedType === Task::class && $evaluatedId) {
            $task = Task::query()->find($evaluatedId);
            if (! $task) {
                return back()->withErrors(['evaluated_id' => 'Selected task could not be found.'])->withInput();
            }

            if ($task->status !== 'Completed') {
                return back()->withErrors(['evaluated_id' => 'This task is not marked as completed yet.'])->withInput();
            }
        }

        $evaluation = new Evaluation();
        $evaluation->user_id = Auth::id();
        $evaluation->evaluated_id = $evaluatedId;
        $evaluation->evaluated_type = $evaluatedType;
        $scores = $data['scores'] ?? null;
        if (is_string($scores)) {
            $decoded = json_decode($scores, true);
            $scores = json_last_error() === JSON_ERROR_NONE ? $decoded : null;
        }
        $evaluation->scores = $scores;
        $evaluation->comments = $data['comments'] ?? null;
        if ($request->hasFile('attachments')) {
            $evaluation->attachments = $request->file('attachments')->store('evaluations');
        }
        $evaluation->status = 'submitted';
        $evaluation->save();

        // After submitting an evaluation, prefer redirecting to the evaluated Task's show page
        $user = Auth::user();
        $role = trim((string) optional($user->role)->slug);
        $role = $role !== '' ? strtolower($role) : strtolower(trim((string) optional($user->role)->name));

        if ($evaluatedType === Task::class && $evaluatedId) {
            if ($role === 'supervisor' && Route::has('supervisor.tasks.show')) {
                return redirect()->route('supervisor.tasks.show', $evaluatedId)->with('success', 'Evaluation submitted successfully.');
            }

            if ($role === 'staff' && Route::has('staff.tasks.show')) {
                return redirect()->route('staff.tasks.show', $evaluatedId)->with('success', 'Evaluation submitted successfully.');
            }
        }

        // Fallbacks
        if ($role === 'supervisor') {
            return redirect()->route('supervisor.tasks.index')->with('success', 'Evaluation submitted successfully.');
        }

        return redirect()->route('tasks.index')->with('success', 'Evaluation submitted successfully.');
    }

    private function normalizeEvaluatedType(?string $evaluatedType): ?string
    {
        if (! $evaluatedType) {
            return null;
        }

        return $this->isTaskEvaluationType($evaluatedType) ? Task::class : $evaluatedType;
    }

    private function isTaskEvaluationType(?string $evaluatedType): bool
    {
        if (! $evaluatedType) {
            return false;
        }

        return in_array($evaluatedType, [Task::class, 'AppModelsTask', 'App\\Models\\Task'], true);
    }

    public function download(Evaluation $evaluation)
    {
        if (empty($evaluation->attachments)) {
            abort(404);
        }

        if (!Storage::exists($evaluation->attachments)) {
            abort(404);
        }

        return Storage::download($evaluation->attachments);
    }
}
