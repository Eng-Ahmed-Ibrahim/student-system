<?php

namespace App\Http\Controllers\Admin;

use App\Models\Group;
use App\Helpers\Helpers;
use Illuminate\Http\Request;
use App\Exports\GroupStudentsExport;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;

class GroupController extends Controller
{
       public function __construct()
    {
        // المجموعات
        $this->middleware('permission:view groups')->only(['index']);
        $this->middleware('permission:create groups')->only(['store']);
        $this->middleware('permission:edit groups')->only(['update']);
        $this->middleware('permission:view students of group')->only(['show']);
        $this->middleware('permission:download students excel sheet of group')->only(['exportGroupStudents']);
    }

    public function index(Request $request)
    {
        $groups = Group::where("grade_level", $request->grade_level)
            ->withCount('students')
            ->paginate(15)
            ->appends(request()->query());
        return view('admin.groups.index', compact('groups'));
    }
    public function show($id)
    {
        $group = Group::with(['students'])->findOrFail($id);
        return view('admin.groups.show', compact('group'));
    }
    public function exportGroupStudents($id)
    {
        $group = Group::with('students')->findOrFail($id);
        $students = $group->students;

        $fileName = "مجموعة_{$group->name}_الطلاب.xlsx";
        // return $students;
        return Excel::download(new GroupStudentsExport($students,$group->name), $fileName);
    }
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'code' => 'required|unique:groups,code',
            'limit' => 'required|integer',
            'days' => 'required|array',
            'time' => 'required',
            'monthly_fee' => 'required|numeric|min:0',
            'grade_level' => 'required|string',

        ]);

        Group::create([
            'name' => $request->name,
            'code' => $request->code,
            'limit' => $request->limit,
            'days' => ($request->days),
            'time' => $request->time,
            'monthly_fee' => $request->monthly_fee,
            'grade_level' => $request->grade_level,
        ]);
        Helpers::recache_groups();

        return redirect()->back()->with('success', 'Group added successfully.');
    }

    public function update(Request $request, Group $group)
    {
        $request->validate([
            'name' => 'required',
            'code' => 'required|unique:groups,code,' . $group->id,
            'limit' => 'required|integer',
            'days' => 'required|array',
            'time' => 'required',
            'monthly_fee' => 'required|numeric|min:0',
            'grade_level' => 'required|string',

        ]);

        $group->update([
            'name' => $request->name,
            'code' => $request->code,
            'limit' => $request->limit,
            'days' => ($request->days),
            'time' => $request->time,
            'monthly_fee' => $request->monthly_fee,
            'grade_level' => $request->grade_level,

        ]);
        Helpers::recache_groups();

        return redirect()->back()->with('success', 'Group updated successfully.');
    }

    public function getByGrade(Request $request)
    {
        $groups = Group::where('grade_level', $request->grade_level)->get(['id', 'name']);

        return response()->json($groups);
    }
}
