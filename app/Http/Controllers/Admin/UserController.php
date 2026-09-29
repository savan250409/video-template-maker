<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{ User, AnimatedTemplate };
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Hash;
use Image;

class UserController extends Controller
{
    function __construct() {
        $this->middleware('permission:user-list|user-create|user-edit|user-delete', ['only' => ['index', 'show']]);
        $this->middleware('permission:user-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:user-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:user-delete', ['only' => 'destroy']);
    }

    public function index() {
        $data['users'] = User::latest('id')->get();
        return view('Admin.user.index', $data);
    }

    public function create() {
        $data['roles'] = Role::pluck('name', 'name')->all();
        return view('Admin.user.create', $data);
    }

    public function store(Request $request) {
        $request->validate([
            'user_name'  => 'required',
            'user_email' => 'required|email|unique:users,email',
            'password'   => 'nullable|same:confirm_password',
            'roles'      => 'required',
            'mobile'     => 'required|numeric|digits:10',
        ]);
        $user = User::create([
            'name'       => $request->user_name,
            'email'      => $request->user_email,
            'password'   => Hash::make($request->password),
            'ip_address' => $request->ip(),
            'mobile'     => $request->mobile,
        ]);
        if ($request->hasFile('user_logo')) {
            $this->saveAvatar($user, $request->file('user_logo'));
        }
        $user->assignRole($request->roles);
        return redirect()->route('user.index')->with('success', 'New user added successfully!');
    }

    public function show(string $id) {
        $data['user'] = User::findOrFail($id);
        return view('Admin.user.show', $data);
    }

    public function edit(string $id) {
        $data['user']      = User::findOrFail($id);
        $data['roles']     = Role::pluck('name', 'name')->all();
        $data['userRoles'] = $data['user']->roles->pluck('name', 'name')->all();
        return view('Admin.user.edit', $data);
    }

    public function update(Request $request, string $id) {
        $request->validate([
            'user_name'  => 'required',
            'user_email' => 'required|email|unique:users,email,' . $id,
            'password'   => 'nullable|same:confirm_password',
            'roles'      => 'required',
            'mobile'     => 'required|numeric|digits:10',
        ]);
        $user           = User::findOrFail($id);
        $user->name     = $request->user_name;
        $user->email    = $request->user_email;
        $user->password = !empty($request->password) ? Hash::make($request->password) : $user->password;
        $user->mobile   = $request->mobile;
        $user->save();
        if ($request->hasFile('user_logo')) {
            $this->saveAvatar($user, $request->file('user_logo'));
        }
        DB::table('model_has_roles')->where('model_id', $id)->delete();
        $user->assignRole($request->roles);
        return redirect()->route('user.index')->with('success', 'User modified successfully!');
    }

    public function destroy(Request $request) {
        $user = User::findOrFail($request->id);
        if ($user->logo) {
            Storage::disk('public')->delete('uploads/profile/' . $user->logo);
        }
        $user->delete();
    }

    private function saveAvatar(User $user, $file) {
        if ($user->logo) {
            Storage::disk('public')->delete('uploads/profile/' . $user->logo);
        }
        $fileName = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $path     = public_path('uploads/profile/');
        if (!is_dir($path)) mkdir($path, 0775, true);
        Image::make($file)->resize(400, null, fn($c) => $c->aspectRatio())->save($path . $fileName, 70);
        $user->logo = $fileName;
        $user->save();
    }
}
