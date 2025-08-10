<?php

namespace App\Providers;

use App\Models\Group;
use App\Models\Student;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */

    public function boot(): void
    {
        $allowedDomains = [
            'myclient.com',
            'www.myclient.com',
            // 'localhost',
            // '127.0.0.1',
        ];


        if (app()->runningInConsole()) {
            return;
        }

        $currentDomain = request()->getHost();

        if (!in_array($currentDomain, $allowedDomains)) {
            abort(403, 'Unauthorized.');
        }




        if (!Schema::hasTable('groups') || !Schema::hasTable('students')) {
            return;
        }

        $groupCounts = Group::whereIn('grade_level', [1, 2, 3])
            ->selectRaw('grade_level, COUNT(*) as count')
            ->groupBy('grade_level')
            ->pluck('count', 'grade_level');

        $studentCounts = Student::whereIn('grade_level', [1, 2, 3])
            ->selectRaw('grade_level, COUNT(*) as count')
            ->groupBy('grade_level')
            ->pluck('count', 'grade_level');

        View::share([
            'groupCounts' => $groupCounts,
            'studentCounts' => $studentCounts,
        ]);
    }
}
