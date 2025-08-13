<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\Group;
use App\Models\Student;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
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
        $groupsWithStudentCount = Group::select('id', 'name')->withCount('students')->orderBy('students_count', "DESC")->get();
        // جلب جدول الحصص لليوم الحالي  
        $today = Carbon::now()->format('l'); // "Saturday", "Sunday", etc.

        $todayGroups = Group::whereJsonContains('days', $today)->get();


        $monthlyPayments = DB::table('payments')
            ->selectRaw('MONTH(created_at) as month, sum(amount) as total')
            ->whereYear('created_at', now()->year)
            ->groupBy(DB::raw('MONTH(created_at)'))
            ->pluck('total', 'month');
        $total_payments = $monthlyPayments->sum();

        $monthlyPaymentsFormatted = [];
        for ($i = 1; $i <= 12; $i++) {
            // نضمن وجود كل الشهور من 1 لـ 12 حتى اللي مفيش فيها بيانات
            $monthlyPaymentsFormatted[] = $monthlyPayments[$i] ?? 0;
        }

        $monthNames = ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو', 'يوليو', 'أغسطس', 'سبتمبر', 'أكتوبر', 'نوفمبر', 'ديسمبر'];


        return view('admin.dashboard', [
            'studentsByGrade' => $studentsByGrade,
            'groupsWithStudentCount' => $groupsWithStudentCount,
            'todayGroups' => $todayGroups,
            'labels' => $labels,
            'data' => $data,
            'monthlyPaymentsFormatted' => $monthlyPaymentsFormatted,
            'monthNames' => $monthNames,
            'total_payments' => $total_payments,
        ]);
    }
}
