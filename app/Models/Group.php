<?php

namespace App\Models;

use App\Models\Student;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    protected $guarded = [];
        public function students()
    {
        return $this->hasMany(Student::class,'group_id');
    }
    protected $casts = [
        'days' => 'array',
    ];
}
