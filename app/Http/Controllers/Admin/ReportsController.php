<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\Payment;
use App\Models\Student;
use App\Helpers\Helpers;
use App\Models\Attendance;
use App\Models\StudentFee;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ReportsController extends Controller
{
        public function financial(Request $request)
        {
            $type = $request->input('type');
            $from = $request->input('from');
            $to = $request->input('to');
            $studentId = $request->input('student_id');
            // @phpstan-ignore-next-line
            $paymentQuery = Payment::query()->with('student');
            $feeQuery = StudentFee::query()->with('student')->where('status', 'unpaid');

            // فلترة بالطالب
            if ($studentId) {
                $paymentQuery->where('student_id', $studentId);
                $feeQuery->where('student_id', $studentId);
            }

            // تحديد النطاق الزمني
            if ($type === 'weekly') {
                $start = Carbon::now()->startOfWeek();
                $end = Carbon::now()->endOfWeek();
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

            $payments = $paymentQuery->get();
            $studentFees = $feeQuery->get();

            $totalPayments = $payments->sum('amount');
            $totalFees = $studentFees->sum('final_amount');

            $students = Helpers::get_students() ;

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

        // فلترة حسب الطالب أو المجموعة
        if ($studentId) {
            $query->where('student_id', $studentId);
        }
        if ($groupId) {
            $query->where('group_id', $groupId);
        }

        // فلترة حسب الفترة الزمنية
        if ($type === 'monthly') {
            $query->whereMonth('date', now()->month)
                ->whereYear('date', now()->year);
        } elseif ($type === 'yearly') {
            $query->whereYear('date', now()->year);
        } elseif ($type === 'custom' && $from && $to) {
            $query->whereBetween('date', [$from, $to]);
        }

        $attendances = $query->orderBy('status',"DESC")->get();
        // حساب الحضور والغياب
        $presentCount = $attendances->where('status', 1)->count();
        $absentCount = $attendances->where('status', 0)->count();

        $students = Student::all();

        return view('admin.reports.attendance', compact(
            'attendances',
            'presentCount',
            'absentCount',
            'students',
            'studentId',
            'groupId',
            'type',
            'from',
            'to'
        ));
    }
}
