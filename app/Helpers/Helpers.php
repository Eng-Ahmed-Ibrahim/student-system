<?php

namespace App\Helpers;

use App\Models\Group;
use Illuminate\Support\Facades\Cache;

class Helpers
{
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
