<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use DB;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class RoleController extends Controller
{
    /**
     * Show the application relo.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    function __construct() {
        $this->middleware('permission:role-list|role-create|role-edit|role-delete', ['only' => ['index', 'show']]);
        $this->middleware('permission:role-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:role-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:role-delete', ['only' => 'destroy']);
    }
    public function index()
    {
        $data['roles'] = Role::latest('id')->get();
        return view('Admin.role.index', $data);
    }

    /**
     * Show the form for creating a new role.
     */
    public function create()
    {
        $data['permissions'] = Permission::get();
        return view('Admin.role.create', $data);
    }

    /**
     * Store a newly created resource in role.
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'role_name' => 'required|unique:roles,name',
            'permission' => 'required'
        ]);
        $role = Role::create([
            'name' => $request->role_name
        ]);
        $role->syncPermissions($request->permission);
        return redirect()->route('role.index')->with('success', 'New role created successfully!');
    }

    /**
     * Display the specified role.
     */
    public function show(string $id)
    {
        $data['role'] = Role::find($id);
        $data['rolePermissions'] = Permission::join('role_has_permissions', 'role_has_permissions.id', '=', 'permissions.id')->where('role_has_permissions.role_id', $id)->get();
    
        return view('Admin.role.show', $data);
    }

    /**
     * Show the form for editing the specified role.
     */
    public function edit(string $id)
    {
        $data['role'] = Role::find($id);
        $data['permissions'] = Permission::get();
        $data['rolePermissions'] = DB::table("role_has_permissions")->where("role_has_permissions.role_id",$id)
            ->pluck('role_has_permissions.permission_id','role_has_permissions.permission_id')
            ->all();
        return view('Admin.role.edit', $data);
    }

    /**
     * Update the specified resource in role.
     */
    public function update(Request $request, string $id)
    {
        $this->validate($request, [
            'role_name' => 'required',
            'permission' => 'required',
        ]);
        $role = Role::find($id);
        $role->name = $request->input('role_name');
        $role->save();

        $role->syncPermissions($request->permission);

        return redirect()->route('role.index')->with('success', 'Role modified successfully!');
    }

    /**
     * Remove the specified resource from role.
     */
    public function destroy(Request $request)
    {
        DB::table('roles')->where('id', $request->id)->delete();
    }
}
