<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{ Category, AnimatedTemplate, Report, Banner, AdminNotification };
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Image;

class TemplateCategoryController extends Controller
{
    function __construct() {
        $this->middleware('permission:template-category-list|template-category-create|template-category-edit|template-category-delete', ['only' => ['index', 'show']]);
        $this->middleware('permission:template-category-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:template-category-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:template-category-delete', ['only' => 'destroy']);
    }

    public function templateCategoryOrder(Request $request) {
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
        $data['categories'] = Category::where('type', 'template')->orderBy('sort_order', 'ASC')->get();
        return view('Admin.template.category.index', $data);
    }

    public function create() {
        return view('Admin.template.category.create');
    }

    public function store(Request $request) {
        $request->validate([
            'category_name' => 'required',
            'category_logo' => 'required|image',
            'category_type' => 'required',
        ]);
        $id = Category::insertGetId([
            'type'          => 'template',
            'template_type' => $request->category_type,
            'name'          => $request->category_name,
        ]);
        if ($request->hasFile('category_logo')) {
            $this->saveCategoryImage($request->file('category_logo'), 'template', $id);
        }
        return redirect()->route('template-category.index')->with('success', 'New template category created successfully!');
    }

    public function edit(string $id) {
        $data['category'] = Category::where('type', 'template')->findOrFail($id);
        return view('Admin.template.category.edit', $data);
    }

    public function update(Request $request, string $id) {
        $request->validate([
            'category_name' => 'required',
            'category_type' => 'required',
        ]);
        Category::where('id', $id)->where('type', 'template')->update([
            'name'          => $request->category_name,
            'template_type' => $request->category_type,
        ]);
        if ($request->hasFile('category_logo')) {
            $this->saveCategoryImage($request->file('category_logo'), 'template', $id);
        }
        return redirect()->route('template-category.index')->with('success', 'Category updated successfully!');
    }

    public function destroy(Request $request) {
        $category = Category::where('type', 'template')->findOrFail($request->id);

        if ($category->logo) {
            Storage::disk('public')->delete('uploads/category/template/' . $category->logo);
            Storage::disk('public')->delete('uploads/category/template/thumbnail/' . $category->logo);
        }

        $templates = AnimatedTemplate::where('category_id', $request->id)->get();
        foreach ($templates as $template) {
            Storage::disk('public')->deleteDirectory('uploads/template/' . $template->id);
            if ($template->thumbnail) {
                Storage::disk('public')->delete('uploads/template/thumbnail/' . $template->thumbnail);
            }
            Report::where('template_id', $template->id)->delete();
            AdminNotification::where('template_id', $template->id)->delete();
            $template->delete();
        }

        $banners = Banner::where('category_id', $request->id)->get();
        foreach ($banners as $banner) {
            if ($banner->banner) {
                Storage::disk('public')->delete('uploads/banner/' . $banner->banner);
            }
            $banner->delete();
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
