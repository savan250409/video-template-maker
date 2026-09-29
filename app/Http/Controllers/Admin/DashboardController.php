<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{User, AnimatedTemplate, Banner, Category, Music, Quote, QuoteBackground, Request as appRequest, Report, Device};
use Kreait\Firebase\Factory;
use Auth;
use Carbon\Carbon;


class DashboardController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    protected $database;
    public function dashboard()
    {
        $data['totalUsers'] = User::select('id')->count();
        $data['totalAnimatedTemplates'] = AnimatedTemplate::select('id')->count();
        $data['totalBanners'] = Banner::select('id')->count();
        $data['totalMusics'] = Music::select('id')->count();
        $data['totalAimatedTemplateCategories'] = Category::where('type', 'template')->select('id')->count();
        $data['totalBannerCategories'] = Category::where('type', 'banner')->select('id')->count();
        $data['totalMusicCategories'] = Category::where('type', 'music')->select('id')->count();

        $data['totalReports'] = Report::select('id')->count();
        $data['userAnalytic'] = Device::selectRaw('YEAR(updated_at) as year, COUNT(*) as count')
            ->groupBy('year')
            ->pluck('count', 'year')
            ->map(function ($count, $year) {
                return [
                    "dates" => $year,
                    "counts" => $count
                ];
            })
            ->values()
            ->toArray();

        $data['userCurrentWeekAnalytic'] = Device::selectRaw('WEEK(updated_at) as week, DATE(updated_at) as date, COUNT(*) as count')
            ->whereYear('updated_at', now()->year)
            ->whereBetween('updated_at', [now()->startOfWeek(), now()->endOfWeek()])
            ->groupBy('week', 'date')
            ->pluck('count', 'date')
            ->map(function ($count, $date) {
                return [
                    "dates" => Carbon::createFromFormat('Y-m-d', $date)->format('l'),
                    "counts" => $count
                ];
            })
            ->values()
            ->toArray();

        $data['userCurrentWeekTotalCount'] = Device::whereBetween('updated_at', [now()->startOfWeek(), now()->endOfWeek()])->count();

        $data['userCurrentMonthAnalytic'] = Device::selectRaw('MONTH(updated_at) as month, COUNT(*) as count')
            ->whereYear('updated_at', now()->year)
            ->groupBy('month')
            ->pluck('count', 'month')
            ->map(function ($count, $month) {
                return [
                    "dates" => Carbon::createFromFormat('m', $month)->format('M'),
                    "counts" => $count
                ];
            })
            ->values()
            ->toArray();

        $data['userLast30DaysAnalytic'] = Device::selectRaw('DAY(updated_at) as day, COUNT(*) as count')
            ->where('updated_at', '>=', now()->subDays(30))
            ->groupBy('day')
            ->pluck('count', 'day')
            ->map(function ($count, $day) {
                return [
                    "dates" => Carbon::createFromFormat('d', $day)->format('d M'),
                    "counts" => $count
                ];
            })
            ->values()
            ->toArray();

        $data['userCurrentMonthTotalCount'] = Device::whereBetween('updated_at', [now()->startOfMonth(), now()->endOfMonth()])->count();

        $data['userTodayAnalytic'] = Device::selectRaw('HOUR(updated_at) as hour, COUNT(*) as count')
            ->whereDate('updated_at', now())
            ->groupBy('hour')
            ->pluck('count', 'hour')
            ->map(function ($count, $hour) {
                return [
                    "hour" => $hour,
                    "count" => $count
                ];
            })
            ->values()
            ->toArray();

        $data['userTodayTotalCount'] = Device::whereDate('created_at', now())->select('id', 'created_at')->count();

        return view('dashboard', $data);
    }
}
