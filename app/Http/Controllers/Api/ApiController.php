<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\{ AnimatedTemplate, Category, Banner, Music, Setting, Report, Device };
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;

class ApiController extends Controller
{

    public function getTemplatesByCategory() {
        $categories = Category::with('templates')->whereHas('templates')->where('is_active', 1)->orderBy('sort_order', 'ASC')->get();

        $collection = $categories->map(function($category) {
            return [
                'id'            => $category->id,
                'type'          => $category->type,
                'name'          => $category->name,
                'template_type' => $category->template_type,
                'image_url'     => url('uploads/category/' . $category->type . '/' . $category->logo),
                'templates'     => $category->templates->take(6)->map(fn($t) => $this->templateArray($t)),
            ];
        });

        $latest = AnimatedTemplate::where('is_active', 1)->latest('id')->take(6)->get();
        $collection->prepend([
            'id'            => 0,
            'type'          => 'static',
            'name'          => 'Latest',
            'template_type' => 'latest',
            'image_url'     => null,
            'templates'     => $latest->map(fn($t) => $this->templateArray($t)),
        ]);

        return response()->json($collection);
    }

    public function getTemplates(Request $request) {
        $category = $request->cat ?? null;
        $type     = $request->type ?? null;

        if (is_null($category)) {
            return response()->json(['code' => 400, 'msg' => 'Invalid Parameter'], 400);
        }

        $page      = max(1, (int)($request->page ?? 1));
        $limit     = min(50, max(1, (int)($request->limit ?? 10)));
        $query     = AnimatedTemplate::where('is_active', 1);
        $templates = null;

        if ($category == '-1') {
            $templates = $query->latest('total_create')->paginate($limit, ['*'], 'page', $page);
        } elseif ($category == '-2') {
            $templates = $query->latest('id')->paginate($limit, ['*'], 'page', $page);
        } elseif (!is_null($type)) {
            $query->where('category_id', (int)$category);
            if ($type == 'trending') {
                $templates = $query->latest('total_views')->paginate($limit, ['*'], 'page', $page);
            } elseif ($type == 'latest') {
                $templates = $query->latest('id')->paginate($limit, ['*'], 'page', $page);
            } elseif ($type == 'random') {
                $templates = $query->orderByRaw('RAND()')->paginate($limit, ['*'], 'page', $page);
            }
        }

        if (is_null($templates)) {
            return response()->json(['code' => 400, 'msg' => 'Invalid parameters'], 400);
        }

        return response()->json([
            'code' => 200,
            'msg'  => collect($templates->items())->map(fn($t) => $this->templateArray($t))->values(),
        ]);
    }

    public function getAllCategory(Request $request) {
        $type       = $request->type ?? 'template';
        $categories = Category::where('type', $type)->where('is_active', 1)->orderBy('sort_order', 'ASC')->get();
        return response()->json([
            'code' => 200,
            'msg'  => $categories->map(function($category) {
                return [
                    'id'            => $category->id,
                    'type'          => $category->type,
                    'category'      => $category->name,
                    'template_type' => $category->template_type,
                    'image_url'     => url('uploads/category/' . $category->type . '/' . $category->logo),
                ];
            }),
        ]);
    }

    public function getAllMusicList(Request $request) {
        $category = $request->cat_id ?? null;
        $page     = max(1, (int)($request->page ?? 1));
        $limit    = min(50, max(1, (int)($request->limit ?? 10)));
        $query    = Music::where('is_active', 1);
        if (!is_null($category) && $category != '-1') {
            $query->where('category_id', (int)$category);
        }
        $musics = $query->latest('id')->paginate($limit, ['*'], 'page', $page);
        return response()->json(collect($musics->items())->map(function($music) {
            return [
                'id'            => $music->id,
                'song_name'     => $music->music,
                'song_duration' => 0,
                'song_url'      => url('uploads/music/' . $music->music),
            ];
        }));
    }

    public function getAllBanners() {
        $banners = Banner::with('getCategory')->where('is_active', 1)->latest('id')->get();
        if ($banners->isEmpty()) {
            return response()->json('Banner not available');
        }
        return response()->json($banners->map(function($banner) {
            return [
                'id'              => $banner->id,
                'category_id'     => $banner->category_id,
                'banner_name'     => $banner->name,
                'banner_url'      => $banner->url,
                'banner_category' => $banner->getCategory->name ?? null,
                'banner_type'     => $banner->type,
                'banner_image'    => url('uploads/banner/' . $banner->banner),
                'status'          => $banner->is_active == 1,
            ];
        }));
    }

    public function addViews(Request $request) {
        $request->validate(['id' => 'required|integer|exists:animated_templates,id']);
        $template = AnimatedTemplate::findOrFail($request->id);
        $template->increment('total_views');
        return response()->json(['created' => $template->total_create, 'views' => $template->total_views]);
    }

    public function addCreated(Request $request) {
        $request->validate(['id' => 'required|integer|exists:animated_templates,id']);
        $template = AnimatedTemplate::findOrFail($request->id);
        $template->increment('total_create');
        return response()->json(['created' => $template->total_create, 'views' => $template->total_views]);
    }

    public function addReport(Request $request) {
        $request->validate([
            'id'      => 'required|integer|exists:animated_templates,id',
            'email'   => 'nullable|email|max:255',
            'reason'  => 'required|string|max:500',
            'message' => 'nullable|string|max:1000',
        ]);
        Report::create([
            'email'       => $request->email,
            'template_id' => $request->id,
            'report'      => $request->reason,
            'is_active'   => 1,
            'message'     => $request->message,
        ]);
        return response()->json(['msg' => 'Template reported successfully!']);
    }

    public function getAllSettings() {
        return response()->json([
            'notification' => [
                'one_signal_app_id'   => Setting::getSettingValue('one_signal_app_id'),
            ],
            'ads' => [
                '0'                   => 1,
                'ad_enabled'          => Setting::getSettingValue('ad_status') == 'on' ? 1 : 0,
                'admob_banner'        => Setting::getSettingValue('banner_ad_id'),
                'admob_banner_status' => Setting::getSettingValue('banner_ad_id_status') == 'on',
                'admob_inter'         => Setting::getSettingValue('interstital_ad_id'),
                'admob_inter_status'  => Setting::getSettingValue('interstital_ad_id_status') == 'on',
                'admob_reward'        => Setting::getSettingValue('reward_ad_id'),
                'admob_reward_status' => Setting::getSettingValue('reward_ad_id_status') == 'on',
                'admob_native'        => Setting::getSettingValue('native_ad_id'),
                'admob_native_status' => Setting::getSettingValue('native_ad_id_status') == 'on',
                'admob_app'           => Setting::getSettingValue('app_open_id'),
                'admob_app_status'    => Setting::getSettingValue('app_open_id_status') == 'on',
                'inter_count'         => 5,
                'native_count'        => 3,
            ],
            'appUpdate' => [
                'app_update_popup'         => Setting::getSettingValue('app_update_popup') == 'on',
                'app_update_version'       => Setting::getSettingValue('app_update_version'),
                'app_update_description'   => Setting::getSettingValue('app_update_description'),
                'app_update_app_link'      => Setting::getSettingValue('app_update_app_link'),
                'app_update_cancel_option' => Setting::getSettingValue('app_update_cancel_option') == 'on',
            ],
            'appHomeData' => [
                'order'   => Setting::getSettingValue('home'),
                'privacy' => Setting::getSettingValue('privacy'),
                'terms'   => Setting::getSettingValue('terms'),
                'refund'  => Setting::getSettingValue('refund'),
            ],
        ]);
    }

    private function templateArray($template): array {
        return [
            'id'             => $template->id,
            'code'           => $template->zip,
            'category'       => $template->category_id,
            'group'          => 'play',
            'no_of_image'    => $template->total_image_count,
            'total_editable' => $template->total_editable,
            'title'          => $template->title,
            'thumb_link'     => url('uploads/template/thumbnail/' . $template->thumbnail),
            'zip_link'       => url('uploads/template/zip/' . $template->zip . '.zip'),
            'views'          => $template->total_views,
            'created'        => $template->total_create,
            'premium'        => $template->is_paid == 1,
            'type'           => $template->type,
            'json'           => $template->json,
            'height'         => $template->height,
            'width'          => $template->width,
        ];
    }
}
