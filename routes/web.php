<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InstagramController;

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

Route::get('/', function () {
    return view('welcome');
});

Route::get('oauth', [InstagramController::class, 'loginInstagram']); // untuk login instagram
Route::get('oauth/callback/instagram', [InstagramController::class, 'instagramCallback']); // untuk callback instagram
Route::get('fetch-posts-instagram', [InstagramController::class, 'instagramFetchPost']); // untuk fetch posts
Route::get('instagram-feeds', [InstagramController::class, 'instagramFeed']);