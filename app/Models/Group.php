<?php

namespace App\Models;

use App\Models\Exam;
use App\Models\Student;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    protected $guarded = [];
        /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
        public function students()
    {
        return $this->hasMany(Student::class,'group_id');
    }
        /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
        public function exams()
    {
        return $this->hasMany(Exam::class,'group_id');
    }
    protected $casts = [
        'days' => 'array',
    ];
}
