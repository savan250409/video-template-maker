<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{ Setting };
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Image;

class SettingController extends Controller
{
    public function create() {
        return view('Admin.setting.setting');
    }

    public function adsUpdate(Request $request) {
        Setting::where('key', $request->ads_key)->update([
            'value' => $request->ads_value == 'true' ? 'on' : 'off'
        ]);
    }

    public function store(Request $request) {
        foreach ($request->settings as $key => $setting) {
            if (in_array($key, ['app_logo', 'app_favicon'])) {
                $currentSetting = Setting::where('key', $key)->first();
                if ($currentSetting && $currentSetting->value) {
                    Storage::disk('public')->delete('uploads/setting/' . $currentSetting->value);
                }
                $fileName = Str::uuid() . '.' . $setting->getClientOriginalExtension();
                $path     = public_path('uploads/setting/');
                if (!is_dir($path)) mkdir($path, 0775, true);
                Image::make($setting)->save($path . $fileName);
                $currentSetting->value = $fileName;
                $currentSetting->save();
            } else {
                Setting::where('key', $key)->update(['value' => $setting]);
            }
        }
        return back()->with('success', 'Settings updated successfully!');
    }
}
