<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Storage;
use App\Models\{ AdminNotification };
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrap();

        // Global helper: {{ uploadUrl('banner/file.jpg') }} => <current-host>/uploads/banner/file.jpg
        Blade::directive('uploadUrl', function ($expression) {
            return "<?php echo url('uploads/' . $expression); ?>";
        });
    }
}
