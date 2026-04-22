<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskSubmission extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_id',
        'submitted_by',
        'submission_notes',
        'submission_data',
        'submission_status',
        'submitted_at',
        'reviewed_at',
    ];

    protected $casts = [
        'submission_data' => 'json',
        'submitted_at' => 'datetime',
        'reviewed_at' => 'datetime',
    ];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function submitter()
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }
}
