<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{ Category };
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Image;

class BannerCategoryController extends Controller
{
    function __construct() {
        $this->middleware('permission:banner-category-list|banner-category-create|banner-category-edit|banner-category-delete', ['only' => ['index', 'show']]);
        $this->middleware('permission:banner-category-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:banner-category-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:banner-category-delete', ['only' => 'destroy']);
    }

    public function bannerCategoryOrder(Request $request) {
        foreach ($request->sortedIds as $key => $value) {
            Category::where('id', $value)->update(['sort_order' => $key]);
        }
        return 1;
    }

    public function categoryStatus(Request $request) {
        Category::where('id', $request->id)->update([
            'is_active' => $request->status == 'true' ? 1 : 0
        ]);
    }

    public function index() {
        $data['categories'] = Category::where('type', 'banner')->latest('id')->get();
        return view('Admin.banner.category.index', $data);
    }

    public function create() {
        return view('Admin.banner.category.create');
    }

    public function store(Request $request) {
        $request->validate([
            'category_name' => 'required',
            'category_logo' => 'required|image',
        ]);
        $id = Category::insertGetId(['type' => 'banner', 'name' => $request->category_name]);
        if ($request->hasFile('category_logo')) {
            $this->saveCategoryImage($request->file('category_logo'), 'banner', $id);
        }
        return redirect()->route('banner-category.index')->with('success', 'New banner category created successfully!');
    }

    public function edit(string $id) {
        $data['category'] = Category::where('type', 'banner')->findOrFail($id);
        return view('Admin.banner.category.edit', $data);
    }

    public function update(Request $request, string $id) {
        $request->validate(['category_name' => 'required']);
        Category::where('id', $id)->where('type', 'banner')->update(['name' => $request->category_name]);
        if ($request->hasFile('category_logo')) {
            $this->saveCategoryImage($request->file('category_logo'), 'banner', $id);
        }
        return redirect()->route('banner-category.index')->with('success', 'Banner category updated successfully!');
    }

    public function destroy(Request $request) {
        $category = Category::where('type', 'banner')->findOrFail($request->id);
        if ($category->logo) {
            Storage::disk('public')->delete('uploads/category/banner/' . $category->logo);
            Storage::disk('public')->delete('uploads/category/banner/thumbnail/' . $category->logo);
        }
        $category->delete();
    }

    private function saveCategoryImage($file, $folder, $id) {
        $category = Category::find($id);
        if ($category->logo) {
            Storage::disk('public')->delete('uploads/category/' . $folder . '/' . $category->logo);
            Storage::disk('public')->delete('uploads/category/' . $folder . '/thumbnail/' . $category->logo);
        }
        $fileName  = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $dir       = public_path('uploads/category/' . $folder . '/');
        $thumbDir  = $dir . 'thumbnail/';
        if (!is_dir($dir))      mkdir($dir, 0775, true);
        if (!is_dir($thumbDir)) mkdir($thumbDir, 0775, true);

        Image::make($file)->resize(400, null, fn($c) => $c->aspectRatio())->save($thumbDir . $fileName, 70);
        $file->move($dir, $fileName);

        $category->logo = $fileName;
        $category->save();
    }
}
