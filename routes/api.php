<?php

use Illuminate\Http\Request;
use App\Http\Controllers\Api\{ ApiController };

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::controller(ApiController::class)->middleware(['ApiMiddleware'])->group(function() {
   Route::get('getTemplatesByCategory', 'getTemplatesByCategory');
   Route::get('getTemplates', 'getTemplates');
   Route::get('getAllCategory', 'getAllCategory');
   Route::get('getAllMusicList', 'getAllMusicList');
   Route::get('getAllBanners', 'getAllBanners');
   Route::get('addViews', 'addViews');
   Route::get('addCreated', 'addCreated');
   Route::post('addReport', 'addReport');
   Route::get('getAllSettings', 'getAllSettings');
});