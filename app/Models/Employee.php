<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'name',
        'cid',
        'phone',
        'role_title',
        'department',
        'department_id',
        'dzongkhag_id',
        'gewog_id',
        'address',
        'joining_date',
        'photo_path',
        'status',
    ];

    protected $casts = [
        'joining_date' => 'date',
    ];

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function leaves()
    {
        return $this->hasMany(LeaveRequest::class);
    }

    public function user()
    {
        return $this->hasOne(User::class, 'employee_id');
    }

    public function departmentRelation()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function getDepartmentAttribute()
    {
        // Prefer the related Department name if available, fall back to legacy column value
        if ($this->relationLoaded('departmentRelation') && $this->departmentRelation) {
            return $this->departmentRelation->name;
        }

        if ($this->department_id) {
            return optional($this->departmentRelation)->name;
        }

        return $this->attributes['department'] ?? null;
    }

    public function getAreaAttribute()
    {
        return $this->department;
    }

    public function getBuildingAttribute()
    {
        $area = $this->departmentRelation;

        if (! $area) {
            return null;
        }

        return $area->parent?->name ?? $area->name;
    }

    public function dzongkhag()
    {
        return $this->belongsTo(\App\Models\Dzongkhag::class, 'dzongkhag_id');
    }

    public function gewog()
    {
        return $this->belongsTo(\App\Models\Gewog::class, 'gewog_id');
    }
}
