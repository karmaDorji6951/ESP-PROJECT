<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

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
        'assigned_to_role_id',
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

    public function assignedToRole()
    {
        return $this->belongsTo(Role::class, 'assigned_to_role_id');
    }

    public function getAssignedToRoleAttribute()
    {
        // Avoid recursion: `$this->assignedToRole` would resolve the accessor for `assigned_to_role`.
        return $this->assignedToRole()->value('slug');
    }

    public function setAssignedToRoleAttribute($value): void
    {
        if ($value === null || $value === '') {
            $this->attributes['assigned_to_role_id'] = null;
            return;
        }

        // Avoid model dependency loops and keep it lightweight.
        $roleId = DB::table('roles')->where('slug', $value)->value('id');
        $this->attributes['assigned_to_role_id'] = $roleId;
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
                        ->orWhere('assigned_to_role_id', $user->role_id);
        }

        return $query->where('assigned_to_role_id', $user->role_id);
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
