<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Yoeunes\Toastr\Facades\Toastr;
use Flasher\Laravel\Facade\Flasher;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Permission;

class RolesController extends Controller
{
 
    public function index(Request $request){

        $permissions=Permission::orderBy("id","DESC")
        ->orderBy('section')
        ->get()->groupBy('section');
        $roles=Role::orderBy("id","DESC")->get();
        return view('admin.roles.index')
        ->with('permissions',$permissions)
        ->with('roles',$roles)
        ;
    }
    public function store(Request $request){
      // هتتفعل فقط لو نجحت الفاليديشن
    Flasher::success("تم الحفظ بنجاح");

    $request->validate([
        'name' => 'required',
        'permissions' => 'required',
    ]);
        if(Role::where("name",$request->name)->first()){
            session()->flash("error",__('messages.Already_exists'));
            return back();
        }
        $role=Role::create([
            "name"=>$request->name,
        ]);

        $permissions = $request->permissions;


        $role->syncPermissions($permissions);
        session()->flash('success',__("messages.Added_successfully"));
        return back();
    }
    public function edit($id){
        $role=Role::find($id);
        $users = User::role($role->name)->get();

        $rolePermissions = $role->permissions->pluck('name')->toArray();
        $permissions = Permission::orderBy("id","DESC")->orderBy('section')->get()->groupBy('section');
        
        return view('admin.roles.edit', compact('role','users', 'permissions', 'rolePermissions'));

    }
    public function update(Request $request){
        $role = Role::findById($request->id);
        if ($role == null) {
            return redirect()->back()->with('error', __('messages.Not_found'));
        }
        $role->update([
            "name"=>$request->name,
        ]);
        $permissions = $request->permissions;

        
        $role->syncPermissions($permissions);
        Flasher::success(__("messages.Updated_successfully"));
        return back();
 
    }

}
