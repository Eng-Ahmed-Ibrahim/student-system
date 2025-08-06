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
        $this->AttendanceService->generateAttendanceIfNotExists();
        $today = Carbon::today()->toDateString();
        $group = Group::select('id', 'name', 'time')->findOrFail($request->group);

        $students = Student::whereHas('attendance', function ($query) use ($today) {
            $query->where('date', $today);
        })->with(['attendance' => function ($query) use ($today) {
            $query->where('date', $today);
        }])
            ->where("group_id", $request->group)
            ->get();

            
        $presentCount = $students->filter(fn($student) => optional($student->attendance->first())->status == 1)->count();
        $absentCount = $students->count() - $presentCount;


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
