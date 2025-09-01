<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\Group;
use App\Models\Student;
use App\Helpers\Helpers;
use Milon\Barcode\DNS1D;
use App\Models\Attendance;
use App\Models\StudentFee;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Services\StudentService;
use App\Exports\StudentExamsExport;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\StudentPaymentsExport;
use App\Exports\StudentAttendanceExport;

class StudentController extends Controller
{

    private $StudentService;
    function __construct(StudentService $StudentService)
    {
        $this->StudentService = $StudentService;
    }

    public function index(Request $request)
    {

        $group_id = $request->group_id;
        $search = $request->search;
        $students = Student::where("grade_level", $request->grade_level)
            ->with('group')
            ->when($group_id, function ($q) use ($group_id) {
                if ($group_id != 'all')
                    $q->where("group_id", $group_id);
            })
            ->when($search, function ($q) use ($search) {
                $q->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('student_code', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('national_id', 'like', "%{$search}%");
                });
            })
            ->withSum('fees as total_fees', 'amount')
            ->withSum('payments as total_paid', 'amount')
            ->orderBy("id", "DESC")
            ->paginate(30)->appends(request()->query());
        $groups = Helpers::get_groups();
        return view('admin.students.index', compact('students', 'groups'));
    }
    public function blockedList()
    {
        $blockedStudents = Student::withoutGlobalScope('notBlocked')
            ->where('blocked', 1)
            ->with("group")
            ->get();

        return view('admin.students.blocked', compact('blockedStudents'));
    }



    public function show(Request $request, $id)
    {
        $today = now()->format('l');
        $todayDate = now()->toDateString();

        $today = Carbon::now();


        // if ($today->month == 8 && $today->day >= 24) {
        //     // من 24/8 لآخر 8 → يعتبر شهر 9
        //     $month = 9;
        //     $year = now()->year;
        // } else {
            // عادي: الشهر الحالي
            $month = now()->month;
            $year = now()->year;
        // }

        // $month = $request->month ?? now()->month;
        // $year = $request->year ?? now()->year;
        $exam_month = $request->exam_month ?? now()->month;
        $student = Student::withoutGlobalScopes()->with('group')
            ->with(['fees' => function ($query) use ($month, $year) {
                $query->where('month', $month)->where('year', $year);
            }])
            ->with(['payments' => function ($query) use ($month, $year) {
                $query->whereHas('studentFee', function ($q) use ($month, $year) {
                    $q->where('month', $month)->where('year', $year);
                });
            }])
            ->with([
                'exams_results' => function ($query) use ($exam_month) {
                    $query->whereHas('exam', function ($q) use ($exam_month,) {
                        $q->where('month', $exam_month);
                    });
                },
                'exams_results.exam:id,total_score,exam_date',
            ])
            ->withSum(['fees as total_fees' => function ($query) use ($year) {
                $query->where('year', $year);
            }], 'final_amount')
            ->withSum(['payments as total_paid' => function ($query) use ($year) {
                $query->whereHas('studentFee', function ($q) use ($year) {
                    $q->where('year', $year);
                });
            }], 'amount')
            ->withCount([
                'attendance as total_absent' => function ($query) use ($year) {
                    $query->where('status', false)->whereYear('date', $year);
                },
                'attendance as total_present' => function ($query) use ($year) {
                    $query->where('status', true)->whereYear('date', $year);
                }
            ])
            ->findOrFail($id);

        $availableMonths = $student->fees()->select('month')->distinct()->pluck('month');


        $query = Attendance::query();


        if($request->filled('AttendanceStatus'))
            $query->where('status',$request->AttendanceStatus);

        if($request->filled('AttendanceMonth'))
            $query->where('month',$request->AttendanceMonth);
        else 
            $query->where("month", $month);
            
        $attendances = $query->where("student_id", $student->id)
            
            ->where("year", $year)
            ->orderBy("id", "DESC")
            ->get();

        $presentCount = $attendances->where('status', 1)->count();
        $absentCount = $attendances->where('status', 0)->count();

        $tab = $request->tab ?? session('tab', 0);
        session()->forget('tab');
        $groups = Helpers::get_groups();

        // // تحميل الحضور
        // if ($request->has('download') && $request->download === 'excel') {
        //             $excel_name= 'حضور وغياب_' . $student->name  .'.xlsx'; 

        //     return Excel::download(new StudentAttendanceExport($attendances), $excel_name);
        // }

        // // تحميل الدرجات
        // if ($request->has('download') && $request->download === 'exams_excel') {
        //     $excel_name= 'امتحانات_' . $student->name  .'.xlsx'; 
        //     return Excel::download(new StudentExamsExport($student->exams_results,$student), $excel_name);
        // }

        if ($request->has('download') && $request->download === 'all_excel') {
            $arabicMonths = [
                1 => 'يناير',
                2 => 'فبراير',
                3 => 'مارس',
                4 => 'أبريل',
                5 => 'مايو',
                6 => 'يونيو',
                7 => 'يوليو',
                8 => 'أغسطس',
                9 => 'سبتمبر',
                10 => 'أكتوبر',
                11 => 'نوفمبر',
                12 => 'ديسمبر'
            ];

            $zipFileName = 'ملفات الطالب ' . $student->name . ' - ' . $arabicMonths[$month] . '.zip';

            $zip = new \ZipArchive();
            $tempFile = tempnam(sys_get_temp_dir(), 'zip');

            if ($zip->open($tempFile, \ZipArchive::CREATE) === TRUE) {


                // إنشاء ملفات Excel مؤقتة في الذاكرة
                $attendanceContent = Excel::raw(new StudentAttendanceExport($attendances), \Maatwebsite\Excel\Excel::XLSX);
                $examsContent = Excel::raw(new StudentExamsExport($student->exams_results, $student), \Maatwebsite\Excel\Excel::XLSX);
                $studentPayment =     Excel::raw(new StudentPaymentsExport($student->payments, $student), \Maatwebsite\Excel\Excel::XLSX);
                // إضافة الملفات للـ ZIP
                $zip->addFromString('حضور وغياب.xlsx', $attendanceContent);
                $zip->addFromString('امتحانات.xlsx', $examsContent);
                $zip->addFromString('مدفوعات الطالب.xlsx', $studentPayment);
                $zip->close();

                return response()->download($tempFile, $zipFileName)->deleteFileAfterSend(true);
            }
        }



        return view('admin.students.show', compact(
            'groups',
            'student',
            'attendances',
            'tab',
            'presentCount',
            'absentCount',
            'availableMonths',
            'month',
            'exam_month'
        ));
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

        $lastStudent = Student::where('group_id', $group->id)->orderBy('student_code', 'desc')->first();
        $nextNumber = $lastStudent ? intval($lastStudent->student_code) + 1  : intval($group->code) + 1;
        $student_code = $nextNumber;


        // Create barcode image as base64
        $barcode = new DNS1D();
        $barcode->setStorPath(__DIR__ . "/cache/");
        $barcodeImage = $barcode->getBarcodePNG($student_code, 'C39');


        // Check if phone or national_id already exists
        $exists = Student::where('phone', $request->phone)
            ->orWhere('national_id', $request->national_id)
            ->exists();

        if ($exists) {
            return back()->with('error', 'هذا الطالب موجود بالفعل');
        }

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
            'discount' => $request->discount ?? 0,
            'discount_reason' => $request->discount_reason,
            'barcode' => $barcodeImage,
        ]);
        $this->StudentService->generateMonthlyFeeIfNotExists($student);
        Helpers::recache_students();
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
            'discount' => 'nullable|integer|min:0|max:100',
            'discount_reason' => [
                'nullable',
                'string',
                Rule::requiredIf(function () use ($request) {
                    return $request->discount > 0;
                }),
            ],
        ], [
            'discount_reason.required' => 'يجب كتابة سبب الخصم .',
            'discount_reason.string' => 'سبب الخصم يجب أن يكون نصاً.',
        ]);
        $updateData = $request->only([
            'group_id',
            'name',
            'phone',
            'parent_phone',
            'national_id',
            'address',
            'discount',
            'discount_reason',
            'grade_level'
        ]);

        if ($student->group_id != $request->group_id) {
            $group = Group::withCount('students')->findOrFail($request->group_id);
            $studentCount = $group->students_count;
            if ($group->limit <= $studentCount) {
                return back()->with("error", 'لقد تم الوصول إلى الحد الأقصى للطلاب في هذه المجموعة.');
            }
            $lastStudent = Student::where('group_id', $group->id)->orderBy('student_code', 'desc')->first();
            $nextNumber = $lastStudent ? intval($lastStudent->student_code) + 1  : intval($group->code) + 1;
            $updateData['student_code'] = $nextNumber;
        }

        $student->update($updateData);
        if ($student->discount > 0) {
            $studentFees = StudentFee::where("student_id", $student->id)
                ->where("status", "unpaid")
                ->get();
            foreach ($studentFees as $fee) {
                $discount = ($fee->amount * $request->discount) / 100;
                $final_amount = $fee->amount - $discount;
                $fee->update([
                    'discount' => $discount,
                    "final_amount" => $final_amount,
                ]);
            }
        }
        session(['tab' => 0]);
        Helpers::recache_students();

        return redirect()->back()
            ->with('success', 'تم تعديل الطالب بنجاح');
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
        Helpers::recache_students();

        return back();
    }

    public function unblock(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
        ]);

        $student = Student::withoutGlobalScopes()->findOrFail($request->student_id);

        $student->blocked = false;
        $student->block_reason = null;
        $student->save();
        Helpers::recache_students();

        return response()->json(['success' => true]);
    }
}
