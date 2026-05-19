<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dzongkhag extends Model
{
    use HasFactory;

    protected $table = 'tbldzongkhag';

    protected $fillable = [
        'name',
    ];

    public function gewogs()
    {
        return $this->hasMany(Gewog::class, 'dzongkhag_id');
    }

    public function employees()
    {
        return $this->hasMany(Employee::class, 'dzongkhag_id');
    }
}
