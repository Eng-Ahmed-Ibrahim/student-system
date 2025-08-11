<?php

namespace App\Http\Controllers\Admin;

use App\Models\Exam;
use App\Models\Group;
use App\Models\Student;
use App\Helpers\Helpers;
use App\Models\ExamResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class ExamController extends Controller
{
    public function index()
    {
        $groups = Helpers::get_groups();
        $exams = Exam::with('group')->latest()->get();
        return view('admin.exams.index', compact('groups', 'exams'));
    }
    public function show($id)
    {
        $exam = Exam::with([
                'results' => function ($query) {
                    $query->orderBy('score', 'DESC')->select('id', 'exam_id', 'student_id', 'score');
                },
                'results.student:id,name,student_code',
                'group:id,name'
            ])
            ->select('id', 'group_id', 'name', 'exam_date', 'total_score')
            ->findOrFail($id);
        return view('admin.exams.show', compact('exam'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'grade_level'    => 'required',
            'group_id'    => 'required|exists:groups,id',
            'name'        => 'nullable|string|max:255',
            'exam_date'   => 'required|date',
            'total_score' => 'required|numeric|min:1',
        ]);

        DB::beginTransaction();
        try {
            $exam = Exam::create($request->all());
            $students = Student::where("group_id", $request->group_id)->select('id')->get();
            $data = [];
            foreach ($students as $student) {
                $data[] = [
                    "student_id" => $student->id,
                    "exam_id" => $exam->id,
                    "score" => 0,
                    "created_at" => now(),
                ];
            }

            DB::table('exam_results')->insert($data);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            // Optionally log the error or return a response
            return back()->with('error', 'Failed to create exam and results');
        }

        return redirect()->back()->with('success', 'تم إضافة الامتحان بنجاح');
    }

    public function update(Request $request, Exam $exam)
    {
        $request->validate([
            'grade_level'    => 'required',
            'group_id'    => 'required|exists:groups,id',
            'name'        => 'nullable|string|max:255',
            'exam_date'   => 'required|date',
            'total_score' => 'required|numeric|min:1',
        ]);

        $exam->update($request->all());

        return redirect()->back()->with('success', 'تم تعديل الامتحان بنجاح');
    }

    public function destroy(Exam $exam)
    {
        $exam->delete();
        return redirect()->back()->with('success', 'تم حذف الامتحان بنجاح');
    }
    public function update_score(Request $request, $id)
    {
        $result = ExamResult::findOrFail($id);
        $result->update([
            "score" => $request->score,
        ]);
        return redirect()->back()->with('success', 'تم تعديل درجه الطالب  بنجاح');
    }
}
