<?php

namespace App\Models;

use App\Models\Group;
use App\Models\Attendance;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $guarded = [];
    public function group()
    {
        return $this->belongsTo(Group::class, 'group_id');
    }
    public function attendance()
    {
        return $this->hasOne(Attendance::class,'student_id');
    }
}
