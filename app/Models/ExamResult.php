<?php

namespace App\Models;

use App\Models\Exam;
use App\Models\Student;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class ExamResult extends Model
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
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }
    protected static function booted()
    {
        static::addGlobalScope('orderByScore', function (Builder $builder) {
            $builder->orderBy('score', "DESC");
        });
    }
}
