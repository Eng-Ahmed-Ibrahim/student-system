<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\Group;
use App\Models\Student;
use Milon\Barcode\DNS1D;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $students = Student::where("grade_level",$request->grade_level)->with('group')->paginate(15)->appends(request()->query());
        $groups = Group::all();
        return view('admin.students.index', compact('students', 'groups'));
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
    public function destroy($id){
        $student=Student::findOrFail($id);
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
