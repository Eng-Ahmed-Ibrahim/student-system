<?php

namespace App\Http\Controllers\Admin;

use App\Models\Group;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class GroupController extends Controller
{
    public function index()
    {
        $groups = Group::all();
        return view('admin.groups.index', compact('groups'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'code' => 'required|unique:groups,code',
            'limit' => 'required|integer',
            'days' => 'required|array',
            'time' => 'required',
            'grade_level' => 'required|string',

        ]);

        Group::create([
            'name' => $request->name,
            'code' => $request->code,
            'limit' => $request->limit,
            'days' => ($request->days),
            'time' => $request->time,
            'grade_level' => $request->grade_level,
        ]);

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
            'grade_level' => 'required|string',

        ]);

        $group->update([
            'name' => $request->name,
            'code' => $request->code,
            'limit' => $request->limit,
            'days' => ($request->days),
            'time' => $request->time,
            'grade_level' => $request->grade_level,

        ]);

        return redirect()->back()->with('success', 'Group updated successfully.');
    }

    public function getByGrade(Request $request)
{
    $groups = Group::where('grade_level', $request->grade_level)->get(['id', 'name']);
    return response()->json($groups);
}

}
