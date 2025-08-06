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
    if (app()->runningInConsole()) {
        return; // لا تنفذ أثناء الأوامر مثل package:discover
    }

    try {
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
    } catch (\Exception $e) {
        // مثلاً: سجل الخطأ أو تجاهله أثناء التشغيل في بيئة لا تتوفر بها قاعدة البيانات
        // logger()->error($e->getMessage());
    }
}

}
