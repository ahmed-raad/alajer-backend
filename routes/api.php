<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\OfferController;
use App\Http\Controllers\RequestController;
use App\Http\Controllers\Auth\ApiAuthController;

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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });



Route::group(['middleware' => ['cors', 'json.response']], function () {
    // public routes
    Route::post('/login', [ApiAuthController::class, 'login'])->name('login.api');
    Route::post('/register', [ApiAuthController::class, 'register'])->name('register.api');

    // ...
});


Route::middleware('auth:api')->group(function () {
    Route::get('/get_user', [UserController::class, 'get_user'])->name('get_user.api');
    Route::put('/user_update', [UserController::class, 'update_user'])->name('update_user.api');
    Route::put('/change_password', [UserController::class, 'change_password'])->name('change_password.api');
    Route::post('/create_offer', [UserController::class, 'create_offer'])->name('create_offer.api');
    Route::post('/create_request', [UserController::class, 'create_request'])->name('create_offer.api');
    Route::get('/get_image', [UserController::class, 'get_img'])->name('get_img.api');
    Route::put('/change_image', [UserController::class, 'change_img'])->name('change_img.api');
    Route::post('/logout', [ApiAuthController::class, 'logout'])->name('logout.api');
});


Route::get('/offers', [OfferController::class, 'get_offer'])->name('get_offer.api');
Route::get('/requests', [RequestController::class, 'get_request'])->name('get_request.api');