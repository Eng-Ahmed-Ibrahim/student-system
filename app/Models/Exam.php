<?php

namespace App\Models;

use App\Models\Group;
use App\Models\ExamResult;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Exam extends Model
{
   protected $guarded=[];
           /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
        public function results()
    {
        return $this->hasMany(ExamResult::class,'exam_id');
    }
        /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class, 'group_id');
    }
}
