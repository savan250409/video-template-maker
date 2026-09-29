<?php
use App\Http\Controllers\Auth\{ RegisterController, LoginController };

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::controller(LoginController::class)->group(function() {
    Route::get('/', 'login')->name('login');
    Route::post('authenticate', 'authenticate')->name('authenticate');
    Route::get('logout', 'logout')->name('logout');
    Route::get('forbidden', 'forbidden')->name('forbidden');
});
Route::controller(RegisterController::class)->group(function() {
    Route::get('register', 'register')->name('register');
    Route::post('registration', 'store')->name('registration');
});
include 'admin.php';

// 404 for undefined routes
Route::any('/{page?}',function(){
    return View::make('pages.error.404');
})->where('page','.*');
