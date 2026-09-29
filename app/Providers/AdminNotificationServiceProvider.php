<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Models\{ ScheduleNotification };

class AdminNotificationServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        View::composer('*', function ($view) {
            if(Auth::check()) {
                $sentNotifications = ScheduleNotification::with('getTemplate')->where('is_sent', 1)->latest('id')->get();
                $sentNotificationCount = ScheduleNotification::with('getTemplate')->where('is_sent', 1)->count();
                $view->with('sentNotifications', $sentNotifications)->with('sentNotificationCount', $sentNotificationCount);
            }
        });
    }
}
