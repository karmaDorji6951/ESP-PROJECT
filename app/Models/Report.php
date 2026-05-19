<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'report_type',
        'date_range',
        'start_date',
        'end_date',
        'period_label',
        'format',
        'file_path',
        'report_id',
        'summary_data',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'summary_data' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
