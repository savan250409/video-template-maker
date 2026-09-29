<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{ Category, AnimatedTemplate, Report, User, AdminNotification, ScheduleNotification, Setting };
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use ZipArchive;
use Image;

class AnimatedTemplateController extends Controller
{
    function __construct() {
        $this->middleware('permission:animated-template-list|animated-template-create|animated-template-edit|animated-template-delete', ['only' => ['index', 'show']]);
        $this->middleware('permission:animated-template-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:animated-template-edit', ['only' => ['edit', 'update', 'templateStatus', 'templateFreeStatus']]);
        $this->middleware('permission:animated-template-delete', ['only' => 'destroy']);
    }

    public function appHomeData($order) {
        Setting::where('key', 'home')->update(['value' => $order]);
        return back()->with('success', 'App home data changed!');
    }

    public function search(Request $request) {
        $query = AnimatedTemplate::query();
        if (Auth::user()->type == 'user') {
            $query->where('user_id', Auth::user()->id);
        }
        $data['templates'] = $query->where('zip', 'LIKE', '%' . $request->search . '%')->latest('id')->paginate(15);
        $data['search']    = $request->search;
        return view('Admin.template.index', $data);
    }

    public function templateStatus(Request $request) {
        $template = AnimatedTemplate::findOrFail($request->id);
        if (Auth::user()->type == 'user' && $template->user_id !== Auth::user()->id) {
            return response()->json(['error' => 'Unauthorized action.'], 403);
        }
        $template->update([
            'is_active' => $request->status == 'true' ? 1 : 0
        ]);
    }

    public function templateFreeStatus(Request $request) {
        $template = AnimatedTemplate::findOrFail($request->id);
        if (Auth::user()->type == 'user' && $template->user_id !== Auth::user()->id) {
            return response()->json(['error' => 'Unauthorized action.'], 403);
        }
        $status = $request->status == 'true' ? 1 : 0;
        $template->update(['is_paid' => $status]);
        return $status;
    }

    public function index() {
        $query = AnimatedTemplate::query();
        if (Auth::user()->type == 'user') {
            $query->where('user_id', Auth::user()->id);
        }
        $data['templates'] = $query->with('category')->latest('id')->paginate(15);
        return view('Admin.template.index', $data);
    }

    public function create() {
        $data['categories'] = Category::where('type', 'template')->where('is_active', 1)->latest()->get();
        $data['users']      = User::latest('id')->get(['id', 'name']);
        return view('Admin.template.create', $data);
    }

    public function store(Request $request) {
        $request->validate([
            'template_title'     => 'required',
            'template_category'  => 'required',
            'template_zip'       => 'required',
            'template_thumbnail' => 'required|image',
        ]);

        $template              = new AnimatedTemplate();
        $template->user_id     = Auth::user()->id;
        $template->category_id = $request->template_category;
        $template->title       = $request->template_title;
        $template->tags        = $request->template_video_tag;
        $template->is_paid     = $request->template_premium == 'on' ? 1 : 0;
        $template->is_active   = $request->template_active == 'on' ? 1 : 0;
        $template->total_views = 0;
        $template->total_create= 0;
        $template->type        = $request->type;
        $template->save();

        if ($request->hasFile('template_thumbnail')) {
            $this->saveThumbnail($template, $request->file('template_thumbnail'));
        }

        if ($request->type == 'video' && $request->hasFile('template_zip')) {
            $this->storeVideoZip($template, $request->file('template_zip'));
        } elseif ($request->type == 'post') {
            $this->storeZip($template->id, $request->file('template_zip'));
        }

        return redirect()->route('animated-template.index')->with('success', 'New template created successfully!');
    }

    public function edit(string $id) {
        $template = AnimatedTemplate::findOrFail($id);
        if (Auth::user()->type == 'user' && $template->user_id !== Auth::user()->id) {
            abort(403, 'Unauthorized action.');
        }
        $data['categories'] = Category::where('type', 'template')->latest()->get();
        $data['users']      = User::latest('id')->get(['id', 'name']);
        $data['template']   = $template;
        return view('Admin.template.edit', $data);
    }

    public function update(Request $request, string $id) {
        $request->validate([
            'template_category' => 'required',
            'template_title'    => 'required',
        ]);

        $template = AnimatedTemplate::findOrFail($id);
        if (Auth::user()->type == 'user' && $template->user_id !== Auth::user()->id) {
            abort(403, 'Unauthorized action.');
        }
        $template->category_id = $request->template_category;
        $template->title       = $request->template_title;
        $template->tags        = $request->template_video_tag;
        $template->is_paid     = $request->template_premium == 'on' ? 1 : 0;
        $template->is_active   = $request->template_active == 'on' ? 1 : 0;
        $template->type        = $request->type;

        if ($request->type == 'video' && $request->hasFile('template_zip')) {
            // Delete old zip
            if ($template->zip) {
                Storage::disk('public')->delete('uploads/template/zip/' . $template->zip . '.zip');
                Storage::disk('public')->deleteDirectory('uploads/template/' . $id);
            }
            $this->storeVideoZip($template, $request->file('template_zip'));
        } elseif ($request->type == 'post' && $request->hasFile('template_zip')) {
            $this->storeZip($id, $request->file('template_zip'));
        }

        if ($request->hasFile('template_thumbnail')) {
            if ($template->thumbnail) {
                Storage::disk('public')->delete('uploads/template/thumbnail/' . $template->thumbnail);
            }
            $this->saveThumbnail($template, $request->file('template_thumbnail'));
        }

        $template->save();
        return redirect()->route('animated-template.index')->with('success', 'Template updated successfully!');
    }

    public function destroy(Request $request) {
        $template = AnimatedTemplate::findOrFail($request->id);
        if (Auth::user()->type == 'user' && $template->user_id !== Auth::user()->id) {
            abort(403, 'Unauthorized action.');
        }

        if ($template->zip) {
            Storage::disk('public')->delete('uploads/template/zip/' . $template->zip . '.zip');
            Storage::disk('public')->deleteDirectory('uploads/template/' . $request->id);
        }
        if ($template->thumbnail) {
            Storage::disk('public')->delete('uploads/template/thumbnail/' . $template->thumbnail);
        }
        if ($template->zip_folder) {
            Storage::disk('public')->deleteDirectory('uploads/template/post/' . $request->id);
        }

        AdminNotification::where('template_id', $request->id)->delete();
        ScheduleNotification::where('template_id', $request->id)->delete();
        Report::where('template_id', $request->id)->delete();
        $template->delete();
    }

    // ─── Private Helpers ────────────────────────────────────────────────────────

    private function saveThumbnail(AnimatedTemplate $template, $file) {
        $fileName = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $dir      = public_path('uploads/template/thumbnail/');
        if (!is_dir($dir)) mkdir($dir, 0775, true);
        Image::make($file)->resize(400, null, fn($c) => $c->aspectRatio())->save($dir . $fileName, 80);
        $template->thumbnail = $fileName;
        $template->save();
    }

    private function storeVideoZip(AnimatedTemplate $template, $file) {
        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $zipName      = Str::uuid();
        $zipFileName  = $zipName . '.' . $file->getClientOriginalExtension();
        $zipDir       = public_path('uploads/template/zip/');
        $extractDir   = public_path('uploads/template/' . $template->id . '/');
        if (!is_dir($zipDir))     mkdir($zipDir, 0775, true);
        if (!is_dir($extractDir)) mkdir($extractDir, 0775, true);

        $zipPath = $zipDir . $zipFileName;
        $file->move($zipDir, $zipFileName);

        $zip = new ZipArchive;
        if ($zip->open($zipPath) === true) {
            $zip->extractTo($extractDir);
            $zip->close();
        }

        $template->zip               = $zipName;
        $template->zip_original_name = $originalName;
        $template->save();
    }

    public function storeZip($id, $file) {
        $post         = AnimatedTemplate::findOrFail($id);
        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);

        // Delete old post folder
        if ($post->zip_folder) {
            Storage::disk('public')->deleteDirectory('uploads/template/post/' . $id . '/' . $post->zip_folder);
        }

        $uuid        = (string) Str::uuid();
        $extractPath = public_path('uploads/template/post/' . $id . '/' . $uuid);
        if (!is_dir($extractPath)) mkdir($extractPath, 0775, true);

        // Move and extract zip
        $zipPath = $extractPath . '/' . $id . '.zip';
        $file->move($extractPath, $id . '.zip');

        $zipArchive = new ZipArchive;
        if ($zipArchive->open($zipPath) !== true) {
            throw new \Exception('Failed to open zip file');
        }
        $zipArchive->extractTo($extractPath);
        $zipArchive->close();
        unlink($zipPath);

        // Read and patch JSON
        $jsonFile = $extractPath . '/json/poster.json';
        if (!file_exists($jsonFile)) {
            throw new \Exception('poster.json not found');
        }

        $jsonData = json_decode(file_get_contents($jsonFile), true);
        $height   = null;
        $width    = null;

        foreach ($jsonData['layers'] as &$layer) {
            if ($layer['type'] === 'image' && isset($layer['src'])) {
                $layer['src'] = 'storage/uploads/template/post/' . $id . '/' . $uuid . '/' . ltrim($layer['src'], './');
                if ($layer['name'] === 'background') {
                    $height = $layer['height'];
                    $width  = $layer['width'];
                }
            }
            if ($layer['type'] === 'text' && isset($layer['font'])) {
                $layer['font'] = 'storage/uploads/template/post/' . $id . '/' . $uuid . '/fonts/' . $layer['font'];
            }
        }
        unset($layer);

        file_put_contents($jsonFile, json_encode($jsonData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        $post->zip_folder        = $uuid;
        $post->json              = json_encode($jsonData);
        $post->height            = $height;
        $post->width             = $width;
        // $post->zip_original_name = $originalName;
        $post->zip               = $originalName;
        $post->save();
    }
}
