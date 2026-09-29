<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{ Category, Banner };
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Image;
use DB;

class BannerController extends Controller
{
    function __construct() {
        $this->middleware('permission:banners-list|banners-create|banners-edit|banners-delete', ['only' => ['index', 'show']]);
        $this->middleware('permission:banners-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:banners-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:banners-delete', ['only' => 'destroy']);
    }

    public function bannerStatus(Request $request) {
        Banner::where('id', $request->id)->update([
            'is_active' => $request->status == 'true' ? 1 : 0
        ]);
    }

    public function index() {
        $data['banners'] = Banner::latest('id')->get();
        return view('Admin.banner.index', $data);
    }

    public function create() {
        $data['categories'] = Category::where('type', 'template')->where('is_active', 1)->latest('id')->get();
        return view('Admin.banner.create', $data);
    }

    public function store(Request $request) {
        $request->validate([
            'banner_name' => 'required',
            'banner'      => 'required|image',
        ]);
        $id = Banner::insertGetId([
            'name'        => $request->banner_name,
            'type'        => $request->banner_type ?? $request->type,
            'url'         => $request->url,
            'category_id' => $request->banner_category,
        ]);
        if ($request->hasFile('banner')) {
            $this->saveBannerImage($id, $request->file('banner'));
        }
        return redirect()->route('banners.index')->with('success', 'New banner added successfully!');
    }

    public function edit(string $id) {
        $data['categories'] = Category::where('type', 'template')->where('is_active', 1)->latest('id')->get();
        $data['banner']     = Banner::find($id);
        return view('Admin.banner.edit', $data);
    }

    public function update(Request $request, string $id) {
        $request->validate(['banner_name' => 'required']);
        Banner::where('id', $id)->update([
            'name'        => $request->banner_name,
            'type'        => $request->banner_type ?? $request->type,
            'url'         => $request->url,
            'category_id' => $request->banner_category,
        ]);
        if ($request->hasFile('banner')) {
            $this->saveBannerImage($id, $request->file('banner'));
        }
        return redirect()->route('banners.index')->with('success', 'Banner updated successfully!');
    }

    public function destroy(Request $request) {
        $banner = Banner::findOrFail($request->id);
        if ($banner->banner) {
            Storage::disk('public')->delete('uploads/banner/' . $banner->banner);
        }
        $banner->delete();
    }

    private function saveBannerImage($id, $file) {
        $banner   = Banner::find($id);
        if ($banner->banner) {
            Storage::disk('public')->delete('uploads/banner/' . $banner->banner);
        }
        $fileName = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $path     = public_path('uploads/banner/');
        if (!is_dir($path)) mkdir($path, 0775, true);
        Image::make($file)->save($path . $fileName, 70);
        $banner->banner = $fileName;
        $banner->save();
    }
}
