<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Timetable extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'date',
        'start_time',
        'end_time',
        'location',
        'priority',
        'status',
        'employee_id',
        'assigned_by',
        'assigned_to_role',
        'task_id',
    ];

    protected $casts = [
        'date' => 'date',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function scopeForUser($query, $user)
    {
        if ($user->role->slug === 'admin' || $user->role->slug === 'supervisor') {
            return $query;
        }

        if ($user->role->slug === 'staff' && $user->employee) {
            return $query->where('employee_id', $user->employee->id)
                        ->orWhere('assigned_to_role', $user->role->slug);
        }

        return $query->where('assigned_to_role', $user->role->slug);
    }

    public function scopeForDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('date', [$startDate, $endDate]);
    }

    public function getPriorityColorAttribute()
    {
        return match($this->priority) {
            'high' => '#ef4444',
            'medium' => '#f59e0b',
            'low' => '#10b981',
            default => '#6b7280',
        };
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'scheduled' => '#3b82f6',
            'in_progress' => '#f59e0b',
            'completed' => '#10b981',
            'cancelled' => '#ef4444',
            default => '#6b7280',
        };
    }
}
