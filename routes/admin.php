<?php

use Illuminate\Http\Request;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\PostController;
use App\Http\Controllers\Admin\AskController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\NoticeController;
use App\Http\Controllers\Admin\ParticipantController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\CalendarController;
use App\Http\Controllers\Admin\BusController;
use App\Http\Controllers\Admin\DonationController;
use Illuminate\Support\Facades\Route;

//관리자
Route::group(['middleware' => ['auth:sanctum'], 'prefix' => 'admin'], function () {
    Route::get('shorts', [NoticeController::class, 'indexShorts']);
    Route::get('videos', [NoticeController::class, 'indexVideos']);
    Route::get('popups', [NoticeController::class, 'indexPopups']);
    Route::get('gongzimes', [NoticeController::class, 'indexGongzimes']);
    Route::apiResource('posts', PostController::class);
    Route::apiResource('users', UserController::class);
    Route::apiResource('notices', NoticeController::class);
    Route::apiResource('participants', ParticipantController::class);
    Route::apiResource('calendars', CalendarController::class);
    Route::apiResource('asks', AskController::class);
    Route::apiResource('buses', BusController::class);
    Route::apiResource('donations', DonationController::class);
    Route::post('donations/{id}', [DonationController::class, 'updateDonation']);
    Route::post('donations/profile/{id}', [DonationController::class, 'profile']);
    Route::apiResource('categories', CategoryController::class);
    Route::post('users/profile/{id}', [UserController::class, 'profile']);
    Route::post('notices/{id}', [NoticeController::class, 'updatePost']);
    Route::post('notices/profile/{id}', [NoticeController::class, 'profile']);
    Route::post('notices/files/{id}', [NoticeController::class, 'fileUpdate']);
    Route::post('store/image', [NoticeController::class, 'storeImage']);
});