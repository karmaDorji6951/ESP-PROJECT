<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskEvaluation extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_id',
        'task_submission_id',
        'evaluated_by',
        'staff_user_id',
        'criteria',
        'rating',
        'grade',
        'remarks',
        'evaluated_at',
    ];

    protected $casts = [
        'criteria' => 'array',
        'evaluated_at' => 'datetime',
    ];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function submission()
    {
        return $this->belongsTo(TaskSubmission::class, 'task_submission_id');
    }

    public function evaluator()
    {
        return $this->belongsTo(User::class, 'evaluated_by');
    }

    public function staff()
    {
        return $this->belongsTo(User::class, 'staff_user_id');
    }
}
