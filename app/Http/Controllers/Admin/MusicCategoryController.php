<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{ Category, Music };
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Image;

class MusicCategoryController extends Controller
{
    function __construct() {
        $this->middleware('permission:music-category-list|music-category-create|music-category-edit|music-category-delete', ['only' => ['index', 'show']]);
        $this->middleware('permission:music-category-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:music-category-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:music-category-delete', ['only' => 'destroy']);
    }

    public function quoteCategoryStatus(Request $request) {
        Category::where('id', $request->id)->update([
            'sort_order' => $request->order
        ]);
        return 1;
    }

    public function categoryStatus(Request $request) {
        Category::where('id', $request->id)->update([
            'is_active' => $request->status == 'true' ? 1 : 0
        ]);
    }

    public function index() {
        $data['categories'] = Category::where('type', 'music')->orderBy('sort_order', 'ASC')->get();
        return view('Admin.music.category.index', $data);
    }

    public function create() {
        return view('Admin.music.category.create');
    }

    public function store(Request $request) {
        $request->validate(['category_name' => 'required']);
        $id = Category::insertGetId(['type' => 'music', 'name' => $request->category_name]);
        if ($request->hasFile('category_logo')) {
            $this->saveCategoryImage($request->file('category_logo'), 'music', $id);
        }
        return redirect()->route('music-category.index')->with('success', 'New music category created successfully!');
    }

    public function edit(string $id) {
        $data['category'] = Category::where('type', 'music')->findOrFail($id);
        return view('Admin.music.category.edit', $data);
    }

    public function update(Request $request, string $id) {
        $request->validate(['category_name' => 'required']);
        Category::where('id', $id)->where('type', 'music')->update(['name' => $request->category_name]);
        if ($request->hasFile('category_logo')) {
            $this->saveCategoryImage($request->file('category_logo'), 'music', $id);
        }
        return redirect()->route('music-category.index')->with('success', 'Music category updated successfully!');
    }

    public function destroy(Request $request) {
        $category = Category::where('type', 'music')->findOrFail($request->id);
        if ($category->logo) {
            Storage::disk('public')->delete('uploads/category/music/' . $category->logo);
            Storage::disk('public')->delete('uploads/category/music/thumbnail/' . $category->logo);
        }
        $musics = Music::where('category_id', $request->id)->get();
        foreach ($musics as $music) {
            if ($music->music) {
                Storage::disk('public')->delete('uploads/music/' . $music->music);
            }
            $music->delete();
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
