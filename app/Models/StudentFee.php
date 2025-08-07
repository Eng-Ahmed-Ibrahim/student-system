<?php

namespace App\Models;

use App\Models\Payment;
use App\Models\Student;
use Illuminate\Database\Eloquent\Model;

class StudentFee extends Model
{
        protected $guarded = [];

        /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
