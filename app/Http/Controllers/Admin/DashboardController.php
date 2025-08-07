<?php
namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\Group;
use App\Models\Student;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        // عدد الطلاب لكل صف (1، 2، 3)

    $studentsByGrade = Student::whereIn('grade_level', [1, 2, 3])
        ->selectRaw('grade_level, COUNT(*) as total')
        ->groupBy('grade_level')
        ->pluck('total', 'grade_level')
        ->toArray();

    // تحويل الأرقام لأسماء الصفوف
    $gradeNames = [
        1 => 'الصف الأول الثانوي',
        2 => 'الصف الثاني الثانوي',
        3 => 'الصف الثالث الثانوي',
    ];

    $labels = [];
    $data = [];

    foreach ([1, 2, 3] as $grade) {
        $labels[] = $gradeNames[$grade];
        $data[] = $studentsByGrade[$grade] ?? 0;
    }

        // عدد الطلاب لكل مجموعة (حتى المجموعات اللي مفيهاش طلاب)
        $groupsWithStudentCount = Group::select('id','name')->withCount('students')->orderBy('students_count',"DESC")->get();
        // جلب جدول الحصص لليوم الحالي  
        $today = Carbon::now()->format('l'); // "Saturday", "Sunday", etc.

        $todayGroups = Group::whereJsonContains('days', $today)->get();

        return view('admin.dashboard', [
            'studentsByGrade' => $studentsByGrade,
            'groupsWithStudentCount' => $groupsWithStudentCount,
            'todayGroups' => $todayGroups,
            'labels'=>$labels, 
            'data'=>$data
        ]);
    }
}
