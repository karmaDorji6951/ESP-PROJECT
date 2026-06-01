<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Evaluation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'evaluated_id',
        'evaluated_type',
        'scores',
        'comments',
        'attachments',
        'status',
    ];

    protected $casts = [
        'scores' => 'array',
    ];

    protected $appends = [
        'rating',
        'grade',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function evaluated()
    {
        return $this->morphTo();
    }

    public function getRatingAttribute(): int
    {
        $scores = array_values(array_filter((array) ($this->scores ?? []), fn ($value) => is_numeric($value)));

        if (count($scores) === 0) {
            return 0;
        }

        return (int) max(1, min(5, (int) round(array_sum($scores) / count($scores))));
    }

    public function getGradeAttribute(): string
    {
        $scores = array_values(array_filter((array) ($this->scores ?? []), fn ($value) => is_numeric($value)));

        if (count($scores) === 0) {
            return 'N/A';
        }

        $average = array_sum($scores) / count($scores);

        return match (true) {
            $average >= 4.5 => 'A',
            $average >= 3.5 => 'B',
            $average >= 2.5 => 'C',
            $average >= 1.5 => 'D',
            $average >= 1.0 => 'E',
            default => 'F',
        };
    }
}
