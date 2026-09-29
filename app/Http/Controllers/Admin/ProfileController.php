<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\Models\{ User };
use Hash;
use Image;

class ProfileController extends Controller
{
    public function edit() {
        $data['user'] = User::find(Auth::user()->id);
        return view('Admin.profile.edit', $data);
    }

    public function update(Request $request, $id) {
        if ((int)$id !== Auth::user()->id) {
            abort(403);
        }
        $request->validate([
            'user_name'     => 'required',
            'user_email'    => 'required|email',
            'user_password' => 'nullable|same:confirm_password',
            'mobile'        => 'required|numeric|digits:10',
        ]);
        $user           = User::findOrFail($id);
        $user->name     = $request->user_name;
        $user->email    = $request->user_email;
        $user->password = !empty($request->user_password) ? Hash::make($request->user_password) : $user->password;
        $user->mobile   = $request->mobile;
        $user->save();
        if ($request->hasFile('user_avatar')) {
            $this->saveAvatar($user, $request->file('user_avatar'));
        }
        return redirect()->route('logout')->with('success', 'Profile updated successfully!');
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
