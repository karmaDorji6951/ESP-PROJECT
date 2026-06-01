<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    use HasFactory;

    protected $table = 'feedback';

    protected $fillable = [
        'user_id',
        'recipient_user_id',
        'building_department_id',
        'area_department_id',
        'subject',
        'message',
        'feedback_type',
        'priority',
        'status',
        'is_anonymous',
    ];

    public function sender()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function recipient()
    {
        return $this->belongsTo(User::class, 'recipient_user_id');
    }

    public function buildingDepartment()
    {
        return $this->belongsTo(Department::class, 'building_department_id');
    }

    public function areaDepartment()
    {
        return $this->belongsTo(Department::class, 'area_department_id');
    }
}
