<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gewog extends Model
{
    use HasFactory;

    protected $table = 'tblgewog';

    protected $fillable = [
        'dzongkhag_id',
        'name',
    ];

    public function dzongkhag()
    {
        return $this->belongsTo(Dzongkhag::class, 'dzongkhag_id');
    }

    public function employees()
    {
        return $this->hasMany(Employee::class, 'gewog_id');
    }
}
