<?php

namespace App\Models;

use App\Models\Group;
use App\Models\Payment;
use App\Models\Attendance;
use App\Models\StudentFee;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $guarded = [];
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function group()
    {
        return $this->belongsTo(Group::class, 'group_id');
    }
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function attendance()
    {
        return $this->hasMany(Attendance::class, 'student_id');
    }
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function fees()
    {
        return $this->hasMany(StudentFee::class);
    }
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
    public function getDueAmountAttribute()
    {
        $totalFees = $this->fees()->sum('amount');
        $totalPaid = $this->payments()->sum('amount');

        return max(0, $totalFees - $totalPaid);
    }
}
