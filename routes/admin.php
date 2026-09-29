<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\{ RegisterController, LoginController };
use App\Http\Controllers\Admin\{ DashboardController, TemplateCategoryController, MusicCategoryController, BannerCategoryController, AnimatedTemplateController, MusicController, BannerController, NotificationController, ProfileController, RoleController, UserController, ReportController, SettingController, ScheduleNotificationController, AdminNotificationController };

Route::group(['middleware' => ['auth']], function(){
    
    Route::prefix('admin')->controller(DashboardController::class)->group(function() {
        Route::get('dashboard', 'dashboard')->name('dashboard');
    });
    
    Route::prefix('admin')->controller(RoleController::class)->group(function() {
        Route::resource('role', RoleController::class)->except('destroy');
        Route::post('role/destroy', 'destroy')->name('role.destroy');
    });
    
    Route::prefix('admin')->controller(UserController::class)->group(function() {
        Route::resource('user', UserController::class)->except('destroy');
        Route::post('user/destroy', 'destroy')->name('user.destroy');
    });
    
    Route::prefix('admin')->controller(TemplateCategoryController::class)->group(function() {
        Route::resource('template-category', TemplateCategoryController::class)->except('destroy');
        Route::post('template-category/destroy', 'destroy')->name('template-category.destroy');
        Route::post('template-category/status', 'categoryStatus')->name('template-category.status');
        Route::post('template-category-order', 'templateCategoryOrder')->name('template-category.order');
    });
    Route::prefix('admin')->controller(MusicCategoryController::class)->group(function() {
        Route::resource('music-category', MusicCategoryController::class)->except('destroy');
        Route::post('music-category/destroy', 'destroy')->name('music-category.destroy');
        Route::post('music-category/status', 'categoryStatus')->name('music-category.status');
        Route::post('music-category/order', 'quoteCategoryStatus')->name('quote-category.status');
    });
    Route::prefix('admin')->controller(BannerCategoryController::class)->group(function() {
        Route::resource('banner-category', BannerCategoryController::class)->except('destroy');
        Route::post('banner-category/destroy', 'destroy')->name('banner-category.destroy');
        Route::post('banner-category/status', 'categoryStatus')->name('banner-category.status');
        Route::post('banner-category/order', 'bannerCategoryOrder')->name('banner-category.order');
    });
    
    Route::prefix('admin')->controller(AnimatedTemplateController::class)->group(function() {
        Route::resource('animated-template', AnimatedTemplateController::class)->except('destroy');
        Route::post('animated-template/destroy', 'destroy')->name('animated-template.destroy');
        Route::post('animated-template/status', 'templateStatus')->name('animated-template.status');
        Route::post('animated-template/free', 'templateFreeStatus')->name('animated-template.free.status');
        Route::get('app-home-data/{order}', 'appHomeData')->name('app.home.data');
        Route::post('animated-template/search', 'search')->name('animated-template.search');
    });
    
    Route::prefix('admin')->controller(MusicController::class)->group(function() {
        Route::resource('musics', MusicController::class)->except('destroy');
        Route::post('musics/destroy', 'destroy')->name('musics.destroy');
        Route::post('musics/status', 'musicStatus')->name('musics.status');
    });
    
    Route::prefix('admin')->controller(BannerController::class)->group(function() {
        Route::resource('banners', BannerController::class)->except('destroy');
        Route::post('banners/destroy', 'destroy')->name('banners.destroy');
        Route::post('banners/status', 'bannerStatus')->name('banners.status');
    });
    
    Route::prefix('admin')->controller(NotificationController::class)->group(function() {
        Route::get('custom-notification/{templateId?}', 'create')->name('custom-notification.create');
        Route::post('custom-notification/store', 'store')->name('custom-notification.store');
    });
    
    Route::prefix('admin')->controller(ProfileController::class)->group(function() {
        Route::get('profile', 'edit')->name('profile');
        Route::put('profile/{id}', 'update')->name('profile.update');
    });  
    
    Route::prefix('admin')->controller(SettingController::class)->group(function() {
       Route::get('setting', 'create')->name('setting.index'); 
       Route::post('setting/store', 'store')->name('setting.store');
       Route::post('setting/ads/update', 'adsUpdate')->name('ads.update');
    });
    
    Route::prefix('admin')->controller(ReportController::class)->group(function() {
      Route::get('reported', 'index'); 
      Route::post('report/destroy', 'destroy')->name('report.destroy');
    });
    
    Route::prefix('admin')->controller(ScheduleNotificationController::class)->group(function() {
        Route::resource('schedule-notifications', ScheduleNotificationController::class)->except('destroy');
        Route::post('schedule-notifications/destroy', 'destroy')->name('schedule-notifications.destroy');
        Route::post('schedule-notification/status', 'scheduleNotificationStatus')->name('schedule-notification.status');
    });
    
    Route::prefix('admin')->controller(AdminNotificationController::class)->group(function() {
       Route::post('click-status', 'clickStatus')->name('admin.notification.click.status');
       Route::post('destroy', 'destroy')->name('admin.notification.destroy');
    });
    
});

Route::get('execute-scheduler', [ScheduleNotificationController::class, 'executeScheduler'])->middleware('auth');
