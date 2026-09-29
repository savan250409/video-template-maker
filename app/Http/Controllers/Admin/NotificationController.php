<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{ Category, AnimatedTemplate, Setting };
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;

class NotificationController extends Controller
{
    /**
     * Set up authorization middleware for the notification routes.
     */
    public function __construct() {
        $this->middleware('permission:notification', ['only' => ['create', 'store']]);
    }

    /**
     * Show the custom notification creation view.
     *
     * @param int|null $templateId
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function create($templateId = null) {
        $data['categories'] = Category::where('type', 'template')->where('is_active', 1)->latest('id')->get();
        $data['templateId'] = $templateId ?? null;
        return view('Admin.notification.create', $data);
    }

    /**
     * Store and send a custom push notification using OneSignal API.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request) {
        // 1. Validate required notification inputs
        $request->validate([
            'notification_title' => 'required|string|max:255',
            'notification_message' => 'required|string|max:1000',
        ]);

        // Branch 1: Handling notification associated with a specific template ID
        if ($request->template_id) {
            $template = AnimatedTemplate::find($request->template_id);
            if (!$template) {
                return back()->withErrors('Template not found.')->withInput();
            }

            try {
                // Construct the payload content specific to a template.
                // Note: Duplicate 'id' key has been removed to resolve review findings.
                $content = array(
                    'type' => 'template',
                    'catName' => 'direct',
                    "title" => $template->title,
                    'premium' => $template->is_paid == 1 ? true : false,
                    'id' => $template->id,
                    'user_name' => null,
                    'code' => $template->zip,
                    'template' => null,
                    'zip_link' => config('app.url') . 'uploads/template/zip/' . $template->zip . '.zip',
                    'number_of_images' => $template->total_image_count,
                    'video_link' => null,
                    'created' => $template->total_create,
                    'views' => $template->total_views,
                    "video" => false,
                );
              
                // Set OneSignal localization array for headings and content
                $headings = array(
                    "en" => $request->notification_title,
                );
    
                $message  = array(
                    "en" => $request->notification_message,
                );
    
                // Check if a banner image was uploaded for the notification
                if (empty($request->image)) {
                    // Assemble base fields without big_picture field
                    $fields = array(
                        "app_id" => Setting::getSettingValue('one_signal_app_id'),
                        "headings" => $headings,
                        "title" => $headings,
                        'included_segments' => array('All'),
                        "content_available" => true,
                        "data" => $content,
                        "contents" => $message,
                        'type' => 'template'
                    );
                } else {
                    // Upload the custom banner image to public storage using a unique UUID
                    $fileName = Str::uuid() . '.' . $request->image->getClientOriginalExtension();
                    Storage::disk('public')->putFileAs('uploads/notification', $request->image, $fileName);
    
                    // Assemble fields with the big_picture URL
                    $fields = array(
                        "app_id" => Setting::getSettingValue('one_signal_app_id'),
                        "headings" => $headings,
                        "title" => $headings,
                        "data" => $content,
                        'included_segments' => array('All'),
                        "big_picture" => url('uploads/notification/' . $fileName),
                        "content_available" => true,
                        "contents" => $message,
                    );
                }
    
                // Dispatch notification request via helper method using Laravel Http client
                $response = $this->sendOneSignalNotification($fields);
    
                // Verify response status and handle any returned OneSignal errors
                if (!empty($response->errors)) {
                    return back()->withErrors($response->errors)->withInput();
                } else {
                    return back()->with("success", "Notification Sent Successfully!");
                }
                
            } catch (\Exception $e) {
                return back()->withErrors($e->getMessage())->withInput();
            }       
        } 
        // Branch 2: Handling standard notifications (no template associated)
        else {
            try {
                $content = array();

                // Branch 2a: Setup custom data block for Category redirection
                if ($request->type == "category") {
                    $category = Category::find($request->notification_category);
                    if (!$category) {
                        return back()->withErrors('Selected category not found.')->withInput();
                    }
                    $content = array(
                        "type" => $request->type,
                        "catName" => $category->name,
                        "name" => $category->name,
                        "id" => $request->notification_category,
                        'cat_id' => $request->notification_category,
                        "video" => false,
                    );
                } 
                // Branch 2b: Setup custom data block for External URL redirection
                elseif ($request->type == "url") {
                    $content = array(
                        "type" => $request->type,
                        "url" => $request->url,
                        "video" => false,
                    );
                } 
                // Branch 2c: Setup custom data block for App Home redirection
                elseif ($request->type == 'Home') {
                    $content = array(
                        'type' => $request->type,
                        'video' => false
                    );
                }
    
                // Define OneSignal localizations
                $headings = array(
                    "en" => $request->notification_title,
                );
    
                $message  = array(
                    "en" => $request->notification_message,
                );
                
                // Handle optional notification banner image
                if (empty($request->image)) {
                    $fields = array(
                        "app_id" => Setting::getSettingValue('one_signal_app_id'),
                        "headings" => $headings,
                        "title" => $headings,
                        'included_segments' => array('All'),
                        "content_available" => true,
                        "data" => $content,
                        "contents" => $message,
                    );
                } else {
                    $fileName = Str::uuid() . '.' . $request->image->getClientOriginalExtension();
                    Storage::disk('public')->putFileAs('uploads/notification', $request->image, $fileName);
    
                    $fields = array(
                        "app_id" => Setting::getSettingValue('one_signal_app_id'),
                        "headings" => $headings,
                        "title" => $headings,
                        "data" => $content,
                        'included_segments' => array('All'),
                        "big_picture" => url('uploads/notification/' . $fileName),
                        "content_available" => true,
                        "contents" => $message,
                    );
                }

                // Dispatch notification request via helper method using Laravel Http client
                $response = $this->sendOneSignalNotification($fields);
    
                // Verify response status and handle any returned OneSignal errors
                if (!empty($response->errors)) {
                    return back()->withErrors($response->errors)->withInput();
                } else {
                    return back()->with("success", "Notification Sent Successfully!");
                }
            } catch (\Exception $e) {
                return back()->withErrors($e->getMessage())->withInput();
            }
        }
    }

    /**
     * Dispatch notification payload to OneSignal REST API using Laravel Http client facade.
     *
     * @param array $payload
     * @return object
     * @throws \Exception
     */
    private function sendOneSignalNotification(array $payload)
    {
        $oneSignalRestKey = Setting::getSettingValue('one_signal_rest_key');

        if (empty($oneSignalRestKey)) {
            throw new \Exception('OneSignal REST API key is not configured.');
        }

        // Post request to OneSignal API using HTTP Client facade
        $response = Http::withHeaders([
            'Authorization' => 'Basic ' . $oneSignalRestKey,
            'Content-Type'  => 'application/json; charset=utf-8',
            'Accept'        => 'application/json',
        ])->post('https://onesignal.com/api/v1/notifications', $payload);

        // Parse and return JSON response as a standard object representation
        return $response->object();
    }
}
