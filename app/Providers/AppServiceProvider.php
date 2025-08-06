<?php

namespace App\Providers;

use App\Models\Group;
use App\Models\Student;
use Illuminate\Support\Facades\View;
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
        $groupCounts = Group::whereIn('grade_level', [1, 2, 3])
            ->selectRaw('grade_level, COUNT(*) as count')
            ->groupBy('grade_level')
            ->pluck('count', 'grade_level');

        // عدد الطلاب حسب grade_level
        $studentCounts = Student::whereIn('grade_level', [1, 2, 3])
            ->selectRaw('grade_level, COUNT(*) as count')
            ->groupBy('grade_level')
            ->pluck('count', 'grade_level');

        // مشاركة القيم مع جميع الـ views
        View::share([
            'groupCounts' => $groupCounts,
            'studentCounts' => $studentCounts,
        ]);
    }
}
