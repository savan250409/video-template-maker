<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{ Category, Music };
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class MusicController extends Controller
{
    function __construct() {
        $this->middleware('permission:musics-list|musics-create|musics-edit|musics-delete', ['only' => ['index', 'show']]);
        $this->middleware('permission:musics-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:musics-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:musics-delete', ['only' => 'destroy']);
    }

    public function index() {
        $data['musics'] = Music::with('getCategory')->latest('id')->get();
        return view('Admin.music.index', $data);
    }

    public function create() {
        $data['categories'] = Category::where('type', 'music')->where('is_active', 1)->latest('id')->get();
        return view('Admin.music.create', $data);
    }

    public function store(Request $request) {
        $request->validate([
            'music_file'     => 'required',
            'music_category' => 'required',
        ]);
        foreach ($request->music_file as $key => $musicFile) {
            $id = Music::insertGetId(['category_id' => $request->music_category]);
            $this->saveMusicFile($id, $musicFile);
        }
        return redirect()->route('musics.index')->with('success', 'New music(s) added successfully!');
    }

    public function edit(string $id) {
        $data['music']      = Music::find($id);
        $data['categories'] = Category::where('type', 'music')->where('is_active', 1)->latest('id')->get();
        return view('Admin.music.edit', $data);
    }

    public function update(Request $request, string $id) {
        $request->validate(['music_category' => 'required']);
        $music = Music::findOrFail($id);
        if ($request->hasFile('music_file')) {
            if ($music->music) {
                Storage::disk('public')->delete('uploads/music/' . $music->music);
            }
            $music->music = $this->saveMusicFile($id, $request->file('music_file'));
        }
        $music->category_id = $request->music_category;
        $music->save();
        return redirect()->route('musics.index')->with('success', 'Music updated successfully!');
    }

    public function destroy(Request $request) {
        $music = Music::findOrFail($request->id);
        if ($music->music) {
            Storage::disk('public')->delete('uploads/music/' . $music->music);
        }
        $music->delete();
    }

    private function saveMusicFile($id, $file): string {
        $fileName = Str::uuid() . '.' . $file->getClientOriginalExtension();
        Storage::disk('public')->putFileAs('uploads/music', $file, $fileName);
        $music = Music::find($id);
        $music->music = $fileName;
        $music->save();
        return $fileName;
    }
}
