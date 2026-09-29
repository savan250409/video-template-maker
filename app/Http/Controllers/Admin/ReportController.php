<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{ Category, Report, AnimatedTemplate };
use Storage;
use File;
use DB;
use Image;
use Illuminate\Support\Str;


class ReportController extends Controller
{
    /**
     * Show the application report.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    function __construct() {
        $this->middleware('permission:report-list', ['only' => ['index']]);
        $this->middleware('permission:report-delete', ['only' => 'destroy']);
    }
    public function categoryStatus(Request $request) {
        $id = $request->id;
        $status = $request->status == 'true' ? 1 : 0; 
        Category::where('id', $id)->update([
            'is_active' => $status
        ]);
    }
    public function index()
    {
        $data['reports'] = Report::with('getTemplate')->latest('id')->get();
        return view('Admin.report.index', $data);
    }
  
    /**
     * Remove the specified resource from report.
     */
    public function destroy(Request $request)
    {
        if($request->type == 'template') {
            $template = AnimatedTemplate::find($request->id);
            // dd($template);
            if(File::exists(public_path('uploads/template/zip/'.$template->zip.'.zip'))) {
                unlink(public_path('uploads/template/zip/'. $template->zip.'.zip'));
            }
            if(File::exists(public_path('uploads/template/thumbnail/'. $template->thumbnail))) {
                unlink(public_path('uploads/template/thumbnail/'. $template->thumbnail));
            }
            if(File::exists(public_path('uploads/template/video/'. $template->video))) {
                unlink(public_path('uploads/template/video/'. $template->video));
            }
            if(File::exists(public_path('uploads/template/json/'. $template->json))) {
                unlink(public_path('uploads/template/json/'. $template->json));
            }
            AnimatedTemplate::find($request->id)->delete();
            Report::where('template_id', $request->id)->delete();
        } elseif($request->type == 'report') {
            Report::find($request->id)->delete();
        }
    }
}