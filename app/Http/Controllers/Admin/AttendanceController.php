<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\Group;
use App\Models\Student;
use App\Models\Attendance;
use Illuminate\Http\Request;
use App\Services\AttendanceService;
use App\Http\Controllers\Controller;

class AttendanceController extends Controller
{
    private $AttendanceService;
    public function __construct(AttendanceService $AttendanceService)
    {
        $this->AttendanceService = $AttendanceService;
    }
    public function index(Request $request)
    {

        $group = Group::select('id', 'name', 'time', 'days', 'monthly_fee')->findOrFail($request->group);
        $this->AttendanceService->generateAttendanceIfNotExists($group);
        $today = Carbon::today()->toDateString();

        $year = now()->year;
        $students = Student::whereHas('attendance', function ($query) use ($today) {
            $query->where('date', $today);
        })->with(['attendance' => function ($query) use ($today) {
            $query->where('date', $today);
        }])
            ->withSum(['fees as total_fees' => function ($query) use ($year) {
                $query->where('year', $year);
            }], 'final_amount')
            ->withSum(['payments as total_paid' => function ($query) use ($year) {
                $query->whereHas('studentFee', function ($q) use ($year) {
                    $q->where('year', $year);
                });
            }], 'amount')
            ->where("group_id", $request->group)
            ->get();

        $presentCount = 0;
        $absentCount = 0;

        foreach ($students as $student) {
            $attendance = $student->attendance->first();

            /** @phpstan-ignore-next-line */
            if ($attendance && $attendance->status == 1) {
                $presentCount++;
            } else {
                $absentCount++;
            }
        }

        return view('admin.attendance.index', compact('students', 'today', 'group', 'presentCount', 'absentCount'));
    }

    public function mark(Request $request, $studentId, $status)
    {
        $attendance = $this->AttendanceService->changeStatusOfAttendance($studentId, $status);
        return redirect()->back()->with('success', 'تم تحديث الحضور بنجاح.');
    }
    public function markByBarcode(Request $request)
    {
        $code = $request->code;

        $student = Student::where('student_code', $code)->first();

        if (!$student) {
            return response()->json(['success' => false, 'message' => 'الطالب غير موجود']);
        }


        $attendance = $this->AttendanceService->changeStatusOfAttendance($student->id, true);

        if (! $attendance || $attendance == null) {
            return response()->json([
                'success' => false,
                'message' => 'لا يوجد حصص للطالب: ' . $student->name . ' اليوم.'
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'تم تسجيل حضور الطالب: ' . $student->name
        ]);
    }
}
