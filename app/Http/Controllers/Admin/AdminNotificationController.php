<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{ AdminNotification };

class AdminNotificationController extends Controller
{
    public function clickStatus(Request $request) {
        AdminNotification::where('id', $request->id)->update([
            'clicked' => 1,
            ]);
    }
    public function destroy(Request $request) {
        AdminNotification::find($request->id)->delete();
    }
}
