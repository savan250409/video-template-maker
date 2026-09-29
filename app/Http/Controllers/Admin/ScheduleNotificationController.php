<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{ AnimatedTemplate, Setting, ScheduleNotification };
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Image;
use Carbon\Carbon;

class ScheduleNotificationController extends Controller
{
    function __construct() {
        $this->middleware('permission:schedule-notification-list|schedule-notification-create|schedule-notification-edit|schedule-notification-delete', ['only' => ['index', 'show']]);
        $this->middleware('permission:schedule-notification-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:schedule-notification-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:schedule-notification-delete', ['only' => 'destroy']);
    }

    public function scheduleNotificationStatus(Request $request) {
        ScheduleNotification::where('id', $request->id)->update([
            'is_active' => $request->status == 'true' ? 1 : 0
        ]);
    }

    public function index() {
        $data['scheduledNotifications'] = ScheduleNotification::with('getTemplate')->latest('id')->get();
        return view('Admin.notification.schedule.index', $data);
    }

    public function create() {
        $data['templates'] = AnimatedTemplate::where('is_active', 1)->latest('id')->get(['id', 'zip']);
        return view('Admin.notification.schedule.create', $data);
    }

    public function store(Request $request) {
        $request->validate([
            'notification_title'   => 'required',
            'notification_message' => 'required',
            'template'             => 'required',
            'notification_date'    => 'required|date',
            'notification_time'    => 'required',
        ]);
        $notification = ScheduleNotification::create([
            'template_id' => $request->template,
            'title'       => $request->notification_title,
            'description' => $request->notification_message,
            'date'        => $request->notification_date,
            'time'        => $request->notification_time,
        ]);
        if ($request->hasFile('image')) {
            $this->saveImage($notification, $request->file('image'));
        }
        return redirect()->route('schedule-notifications.index')->with('success', 'New notification scheduled successfully!');
    }

    public function show($id) {}

    public function edit($id) {
        $data['notification'] = ScheduleNotification::findOrFail($id);
        $data['templates']    = AnimatedTemplate::where('is_active', 1)->latest('id')->get();
        return view('Admin.notification.schedule.edit', $data);
    }

    public function update(Request $request, $id) {
        $request->validate([
            'notification_title'   => 'required',
            'notification_message' => 'required',
            'template'             => 'required',
            'notification_date'    => 'required|date',
            'notification_time'    => 'required',
        ]);
        $notification = ScheduleNotification::findOrFail($id);
        if ($notification->date != $request->notification_date || $notification->time != $request->notification_time) {
            $notification->is_sent = 0;
        }
        $notification->template_id  = $request->template;
        $notification->title        = $request->notification_title;
        $notification->description  = $request->notification_message;
        $notification->date         = $request->notification_date;
        $notification->time         = $request->notification_time;
        $notification->save();
        if ($request->hasFile('image')) {
            $this->saveImage($notification, $request->file('image'));
        }
        return redirect()->route('schedule-notifications.index')->with('success', 'Scheduled notification modified successfully!');
    }

    public function destroy(Request $request) {
        $notification = ScheduleNotification::findOrFail($request->id);
        if ($notification->image) {
            Storage::disk('public')->delete('uploads/notification/' . $notification->image);
        }
        $notification->delete();
    }

    public function executeScheduler() {
        $now             = Carbon::now();
        $currentDateTime = $now->toDateTimeString();
        $schedules       = ScheduleNotification::where('is_active', 1)->where('is_sent', 0)->latest()->get();

        foreach ($schedules as $schedule) {
            $scheduleDateTime = $schedule->date . ' ' . $schedule->time;
            if ($scheduleDateTime > $currentDateTime) continue;

            $template = AnimatedTemplate::find($schedule->template_id);
            if (!$template) continue;

            $content = [
                'catName'          => '',
                'title'            => $template->title,
                'premium'          => $template->is_paid == 1,
                'id'               => $template->id,
                'code'             => $template->zip,
                'zip_link'         => config('app.url') . 'storage/uploads/template/zip/' . $template->zip . '.zip',
                'number_of_images' => $template->total_image_count,
                'views'            => $template->total_views,
                'video'            => false,
            ];

            $fields = [
                'app_id'            => Setting::getSettingValue('one_signal_app_id'),
                'headings'          => ['en' => $schedule->title],
                'contents'          => ['en' => $schedule->description],
                'included_segments' => ['All'],
                'content_available' => true,
                'data'              => $content,
            ];

            if ($schedule->image) {
                $fields['big_picture'] = url('uploads/notification/' . $schedule->image);
            }

            $headers = [
                'Accept: application/json',
                'Authorization: Basic ' . Setting::getSettingValue('one_signal_rest_key'),
                'Content-Type: application/json',
            ];

            try {
                $ch = curl_init();
                curl_setopt_array($ch, [
                    CURLOPT_URL            => 'https://onesignal.com/api/v1/notifications',
                    CURLOPT_POST           => true,
                    CURLOPT_HTTPHEADER     => $headers,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_POSTFIELDS     => json_encode($fields),
                ]);
                $result   = curl_exec($ch);
                curl_close($ch);
                $response = json_decode($result);

                if (empty($response->errors)) {
                    $schedule->update(['is_sent' => 1]);
                }
            } catch (\Exception $e) {
                // log and continue to next schedule
                \Log::error('Scheduler notification failed: ' . $e->getMessage());
            }
        }
    }

    private function saveImage(ScheduleNotification $notification, $file) {
        if ($notification->image) {
            Storage::disk('public')->delete('uploads/notification/' . $notification->image);
        }
        $fileName = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $path     = public_path('uploads/notification/');
        if (!is_dir($path)) mkdir($path, 0775, true);
        Image::make($file)->resize(400, null, fn($c) => $c->aspectRatio())->save($path . $fileName, 70);
        $notification->image = $fileName;
        $notification->save();
    }
}
