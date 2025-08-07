<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\Group;
use App\Models\Student;
use Milon\Barcode\DNS1D;
use App\Models\Attendance;
use Illuminate\Http\Request;
use App\Services\StudentService;
use App\Http\Controllers\Controller;

class StudentController extends Controller
{
    private $StudentService;
    public function __construct(StudentService $StudentService)
    {
        $this->StudentService = $StudentService;
    }
    public function index(Request $request)
    {
        $students = Student::where("grade_level", $request->grade_level)
            ->with('group')
            ->withSum('fees as total_fees', 'amount')
            ->withSum('payments as total_paid', 'amount')
            ->paginate(15)->appends(request()->query());
        $groups = Group::all();
        return view('admin.students.index', compact('students', 'groups'));
    }

    public function show(Request $request, $id)
    {
        $today = now()->format('l');
        $todayDate = now()->toDateString();

        $month = $request->month ?? now()->month; // لو فاضي، استخدم الشهر الحالي
        $year = $request->year ?? now()->year;    // لو فاضي، استخدم السنة الحالية

        $student = Student::with(['group'])
            ->with(['fees' => function ($query) use ($month, $year) {
                $query->where('month', $month)->where('year', $year);
            }])
            ->withSum(['fees as total_fees' => function ($query) use ( $year) {
                $query->where('year', $year);
            }], 'amount')
            ->withSum(['payments as total_paid' => function ($query) use ( $year) {
                $query->whereHas('studentFee', function ($q) use ( $year) {
                    $q->where('year', $year);
                });
            }], 'amount')
            ->with(['payments' => function ($query) use ($month, $year) {
                $query->whereHas('studentFee', function ($q) use ($month, $year) {
                    $q->where('month', $month)->where('year', $year);
                });
            }])
            ->first();

        // لجلب كل الشهور التي فيها رسوم لهذا الطالب
        $availableMonths = $student->fees()->select('month')->distinct()->pluck('month');

        if (! $student)
            abort(404);



        $attendances = Attendance::where("student_id", $student->id)
            ->where("month", $month)
            ->where("year", $year)
            ->orderBy("id", "DESC")
            ->get();
        $presentCount = $attendances->where('status', 1)->count();
        $absentCount = $attendances->where('status', 0)->count();

        
        return view('admin.students.show', compact('student', 'attendances', 'presentCount', 'absentCount','availableMonths','month'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'group_id' => 'required|exists:groups,id',
            'name' => 'required|string',
            'phone' => 'required|string',
            'parent_phone' => 'required|string',
            'national_id' => 'required|string',
            'address' => 'required|string',
            'grade_level' => 'required|string',
            'discount' => 'nullable|string',
            'discount_reason' => 'nullable|string',
        ]);

        $group = Group::withCount('students')->findOrFail($request->group_id);
        $studentCount = $group->students_count;

        if ($group->limit <= $studentCount) {
            return back()->with("error", 'لقد تم الوصول إلى الحد الأقصى للطلاب في هذه المجموعة.');
        }

        $lastStudent = Student::where('group_id', $group->id)->orderBy('id', 'desc')->first();
        $nextNumber = $lastStudent ? intval(str_replace($group->code, '', $lastStudent->student_code)) + 1 : 1;
        $student_code = $group->code . $nextNumber;

        // Create barcode image as base64
        $barcode = new DNS1D();
        $barcode->setStorPath(__DIR__ . "/cache/");
        $barcodeImage = $barcode->getBarcodePNG($student_code, 'C39');

        $student = Student::create([
            'group_id' => $group->id,
            'student_code' => $student_code,
            'name' => $request->name,
            'phone' => $request->phone,
            'grade_level' => $request->grade_level,
            'parent_phone' => $request->parent_phone,
            'national_id' => $request->national_id,
            'address' => $request->address,
            'blocked' => false,
            'discount' => $request->discount,
            'discount_reason' => $request->discount_reason,
            'barcode' => $barcodeImage,
        ]);
        $this->StudentService->generateMonthlyFeeIfNotExists($student);

        return redirect()->back()->with('success', 'تم إضافة الطالب بنجاح');
    }


    public function update(Request $request, Student $student)
    {
        $request->validate([
            'group_id' => 'required|exists:groups,id',
            'name' => 'required|string',
            'phone' => 'required|string',
            'parent_phone' => 'required|string',
            'national_id' => 'required|string',
            'address' => 'required|string',
            'grade_level' => 'required|string',

        ]);

        $student->update($request->only([
            'group_id',
            'name',
            'phone',
            'parent_phone',
            'national_id',
            'address',
            'grade_level'
        ]));

        return redirect()->back()->with('success', 'تم تعديل الطالب بنجاح');
    }
    public function destroy($id)
    {
        $student = Student::findOrFail($id);
        $student->delete();
        return redirect()->back()->with('success', 'تم الحذف الطالب بنجاح');
    }

    public function block(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'reason' => 'required|string',
        ]);

        $student = Student::findOrFail($request->student_id);
        $student->blocked = true;
        $student->block_reason = $request->reason;
        $student->save();

        return back();
    }

    public function unblock(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
        ]);

        $student = Student::findOrFail($request->student_id);
        $student->blocked = false;
        $student->block_reason = null;
        $student->save();

        return response()->json(['success' => true]);
    }
}
