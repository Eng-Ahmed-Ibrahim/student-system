<?php

namespace App\Models;

use App\Models\Payment;
use App\Models\Student;
use Illuminate\Database\Eloquent\Model;

class StudentFee extends Model
{
    protected $guarded = [];
    protected $appends = ['paid_amount'];

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
    public function getPaidAmountAttribute()
    {
        return $this->payments()->sum('amount');
    }

    public function getDueAmountAttribute()
    {
        return max(0, $this->amount - $this->paid_amount);
    }
}
