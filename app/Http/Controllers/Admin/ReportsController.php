<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\Payment;
use App\Models\Student;
use App\Helpers\Helpers;
use App\Models\Attendance;
use App\Models\ExamResult;
use App\Models\StudentFee;
use Illuminate\Http\Request;
use App\Exports\ExamResultsExport;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AttendanceTableExport;
use App\Exports\FinancialReportExport;

class ReportsController extends Controller
{
    public function financial(Request $request)
    {
        $year = now()->year;
        $type = $request->input('type');
        $from = $request->input('from');
        $to = $request->input('to');
        $grade_level = $request->input('grade_level');
        $studentId = $request->input('student_id');
        // @phpstan-ignore-next-line
        $paymentQuery = Payment::query()->with('student')->whereHas('student');
        $feeQuery = StudentFee::query()->with(['student'])
            ->withSum('payments', 'amount')
            ->whereHas('student')
            ->where('status', 'unpaid');

        // فلترة بالطالب
        if ($studentId) {
            $paymentQuery->where('student_id', $studentId);
            $feeQuery->where('student_id', $studentId);
        }
        if ($grade_level) {
            $paymentQuery->where('grade_level', $grade_level);
            $feeQuery->where('grade_level', $grade_level);
        }

        // تحديد النطاق الزمني
        if ($type === 'weekly') {
            $start = Carbon::now()->startOfWeek();
            $end = Carbon::now()->endOfWeek();
        } elseif ($type === 'daily') {
            $start = now()->toDateString();
            $end = now()->toDateString();
        } elseif ($type === 'monthly') {
            $start = Carbon::now()->startOfMonth();
            $end = Carbon::now()->endOfMonth();
        } elseif ($type === 'yearly') {
            $start = Carbon::now()->startOfYear();
            $end = Carbon::now()->endOfYear();
        } elseif ($type === 'custom' && $from && $to) {
            $start = Carbon::parse($from)->startOfDay();
            $end = Carbon::parse($to)->endOfDay();
        } else {
            $start = null;
            $end = null;
        }

        if ($start && $end) {
            $paymentQuery->whereBetween('payment_date', [$start, $end]);
            $feeQuery->whereBetween('date', [$start, $end]);
        }
        if ($request->has('download') && $request->download === 'excel') {
            $payments = $paymentQuery->get();
            $studentFees = $feeQuery->get();

            if ($type === 'weekly') {
                $excel_name = "تقرير أسبوعي - من " . $start->format('Y-m-d') . " إلى " . $end->format('Y-m-d') . ".xlsx";
            } elseif ($type === 'monthly') {
                $excel_name = "تقرير شهري - " . $start->format('F Y') . ".xlsx";
            } elseif ($type === 'yearly') {
                $excel_name = "تقرير سنوي - " . $start->year . ".xlsx";
            } elseif ($type === 'custom' && $from && $to) {
                $excel_name = "تقرير من " . $from . " إلى " . $to . ".xlsx";
            } else {
                $excel_name = "التقرير المالي.xlsx";
            }
            return Excel::download(
                new FinancialReportExport($payments, $studentFees),
                $excel_name
            );
        }
        $totalPayments = (clone $paymentQuery)->sum('amount');
        $totalFees = (clone $feeQuery)->sum('final_amount');

        $payments = $paymentQuery->paginate(20, ['*'], 'payments_page')->withQueryString();
        $studentFees = $feeQuery->paginate(20, ['*'], 'fees_page')->withQueryString();




        $students = Helpers::get_students();


        return view('admin.reports.financial', compact(
            'payments',
            'studentFees',
            'totalPayments',
            'totalFees',
            'students',
            'studentId',
            'type',
            'from',
            'to'
        ));
    }

    public function attendance(Request $request)
    {
        $studentId = $request->input('student_id');
        $groupId = $request->input('group_id');
        $type = $request->input('type');
        $from = $request->input('from');
        $to = $request->input('to');

        $query = Attendance::with('student', 'student.group')->whereHas('student');

        // فلترة حسب الفترة الزمنية
        if ($type === 'monthly') {
            $query->whereMonth('date', now()->month)
                ->whereYear('date', now()->year);
        } elseif ($type === 'yearly') {
            $query->whereYear('date', now()->year);
        } elseif ($type === 'daily') {
            $query->whereDate('date', now()->toDateString());
        } elseif ($type === 'custom' && $from && $to) {
            $query->whereBetween('date', [$from, $to]);
        }

        // فلترة حسب الطالب أو المجموعة
        if ($studentId && $studentId != 'all') {
            $query->where('student_id', $studentId);
        }
        if ($groupId && $groupId != 'all') {
            $query->whereHas('student', function ($q) use ($groupId) {
                $q->where('group_id', $groupId);
            });

            // $query->where('group_id', $groupId);
        }

        $presentCount = (clone $query)->where('status', 1)->count();
        $absentCount  = (clone $query)->where('status', 0)->count();

        if ($request->has('download') && $request->download === 'excel') {
            $attendances = $query->orderBy('status', "DESC")->get();

            $grades = ['الصف الاول الثانوي', 'الصف الثاني الثانوي', 'الصف الثالث الثانوي'];

            // تحديد اسم الشيت حسب النوع
            if ($type === 'daily') {
                $excel_name = "تقرير يومي - " . now()->format('Y-m-d') . ".xlsx";
            } elseif ($type === 'monthly') {
                $excel_name = "تقرير شهري - " . now()->format('F Y') . ".xlsx";
            } elseif ($type === 'yearly') {
                $excel_name = "تقرير سنوي - " . now()->year . ".xlsx";
            } elseif ($type === 'custom' && $from && $to) {
                $excel_name = "تقرير من " . $from . " إلى " . $to . ".xlsx";
            } else {
                $excel_name = "تقرير الحضور والغياب.xlsx";
            }

            return Excel::download(new AttendanceTableExport($attendances, $grades), $excel_name);
        }

        $attendances = $query->orderBy('status', "DESC")->paginate(20)->withQueryString();
        // حساب الحضور والغياب
        // $presentCount = $attendances->where('status', 1)->count();
        // $absentCount = $attendances->where('status', 0)->count();

        $students = Helpers::get_students();
        $groups = Helpers::get_groups();


        return view('admin.reports.attendance', compact(
            'attendances',
            'presentCount',
            'absentCount',
            'students',
            'studentId',
            'groupId',
            'groups',
            'type',
            'from',
            'to'
        ));
    }
    public function examResults(Request $request)
    {
        $studentId = $request->input('student_id');
        $groupId = $request->input('group_id');
        $type = $request->input('type');
        $from = $request->input('from');
        $to = $request->input('to');

        $query = ExamResult::with('student.group', 'exam')->whereHas('student');

        if ($studentId && $studentId != 'all') {
            $query->where('student_id', $studentId);
        }

        if ($groupId && $groupId != 'all') {
            $query->whereHas('student', function ($q) use ($groupId) {
                $q->where('group_id', $groupId);
            });
        }

        // فلترة حسب النوع
        if ($type === 'daily') {
            $query->whereDate('created_at', now()->toDateString());
        } elseif ($type === 'monthly') {
            $query->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year);
        } elseif ($type === 'yearly') {
            $query->whereYear('created_at', now()->year);
        } elseif ($type === 'custom' && $from && $to) {
            $query->whereBetween('created_at', [$from, $to]);
        }

        if ($request->has('download') && $request->download === 'excel') {
            $results = $query->get();

            if ($type === 'daily') {
                $excel_name = "تقرير يومي - " . now()->format('Y-m-d');
            } elseif ($type === 'monthly') {
                $excel_name = "تقرير شهري - " . now()->format('F Y');
            } elseif ($type === 'yearly') {
                $excel_name = "تقرير سنوي - " . now()->year;
            } elseif ($type === 'custom' && $from && $to) {
                $excel_name = "تقرير من " . Carbon::parse($from)->format('Y-m-d') . " إلى " . Carbon::parse($to)->format('Y-m-d');
            } else {
                $excel_name = "تقرير عام";
            }

            return Excel::download(new ExamResultsExport($results),  "$excel_name.xlsx");
        }
        $results = $query->paginate(20)->withQueryString();


        $students = Helpers::get_students();
        $groups = Helpers::get_groups();

        return view('admin.reports.exams_results', compact(
            'results',
            'students',
            'groups',
            'studentId',
            'groupId',
            'type',
            'from',
            'to'
        ));
    }
}
