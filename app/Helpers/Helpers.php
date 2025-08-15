<?php

namespace App\Helpers;

use App\Models\Group;
use App\Models\Student;
use Illuminate\Support\Facades\Cache;

class Helpers
{
    public static function  get_students()
    {
        return Cache::rememberForever('students', function () {
            return Student::where('blocked',0)->get();
        });
    }
    public static function  recache_students()
    {
        Cache::forget('students');
        self::get_students();
    }
    public static function  get_groups()
    {
        return Cache::rememberForever('groups', function () {
            return Group::all();
        });
    }
    public static function  recache_groups()
    {
        Cache::forget('groups');
        self::get_groups();
    }
}
