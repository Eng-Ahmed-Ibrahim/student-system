<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\BackGround;
use App\Models\Categories;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{


    public function index()
    {
        $user = Auth::user();
        $users = User::with('roles')->orderBy("id", "DESC")->where("id",'!=',$user->id)->get();
        $roles = Role::all();
        return view('admin.users.index', compact('users', 'roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            "name" => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            "password" => 'required|confirmed|min:6',
            "role" => 'required|exists:roles,name',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $user->assignRole($request->role);

        return back()->with("success", "تم إنشاء المستخدم بنجاح");
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            "name" => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            "password" => 'nullable|confirmed|min:6',
            "role" => 'required|exists:roles,name',
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        if ($request->password) {
            $user->password = Hash::make($request->password);
        }
        $user->save();

        // Sync roles
        $user->syncRoles([$request->role]);

        return back()->with("success", "تم تحديث بيانات المستخدم بنجاح");
    }
    public function destroy(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $user->delete();


        return back()->with("success", "تم تحديث بيانات المستخدم بنجاح");
    }
}
