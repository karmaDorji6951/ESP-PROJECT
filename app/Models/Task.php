<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'assigned_by',
        'title',
        'description',
        'assignment_type',
        'schedule_start_date',
        'schedule_end_date',
        'status',
        'deadline',
        'completed_at',
    ];

    protected $casts = [
        'schedule_start_date' => 'date',
        'schedule_end_date' => 'date',
        'deadline' => 'date',
        'completed_at' => 'datetime',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function assigner()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function submissions()
    {
        return $this->hasMany(TaskSubmission::class);
    }

    public function latestSubmission()
    {
        return $this->hasOne(TaskSubmission::class)->latest();
    }

    public function evaluation()
    {
        return $this->hasOne(TaskEvaluation::class);
    }

    public function timetable()
    {
        return $this->hasOne(Timetable::class);
    }
}