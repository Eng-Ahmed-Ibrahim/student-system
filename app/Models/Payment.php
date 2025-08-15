<?php

namespace App\Models;

use App\Models\Student;
use App\Models\StudentFee;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
       protected $guarded = [];
       public function studentFee()
       {
              return $this->belongsTo(StudentFee::class, 'student_fee_id');
       }
       public function student()
       {
              return $this->belongsTo(Student::class, 'student_id');
       }
}
