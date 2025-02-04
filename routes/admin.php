<?php

use Illuminate\Http\Request;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\PostController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\TrainController;
use App\Http\Controllers\Admin\LocationController;
use App\Http\Controllers\Admin\FormOptionController;
use App\Http\Controllers\Admin\AskController;
use App\Http\Controllers\Admin\FormController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\NoticeController;
use App\Http\Controllers\Admin\ParticipantController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\BoardController;
use App\Http\Controllers\Admin\CalendarController;
use App\Http\Controllers\Admin\BusController;
use App\Http\Controllers\Admin\CardinalController;
use App\Http\Controllers\Admin\DonationController;
use Illuminate\Support\Facades\Route;

//관리자
Route::group(['middleware' => ['auth:sanctum'], 'prefix' => 'admin'], function () {
    Route::get('shorts', [NoticeController::class, 'indexShorts']);
    Route::get('videos', [NoticeController::class, 'indexVideos']);
    Route::get('popups', [NoticeController::class, 'indexPopups']);
    Route::get('gongzimes', [NoticeController::class, 'indexGongzimes']);
    Route::get('form_elements', [FormController::class, 'indexElement']);

    //신청자 관리
    Route::get('register-list', [FormController::class, 'registerIndex']);
    Route::get('register-show/{id}', [FormController::class, 'registerShow']);
    Route::apiResource('form_options', FormOptionController::class);
    Route::apiResource('boards', BoardController::class);
    Route::apiResource('posts', PostController::class);
    Route::apiResource('cardinals', CardinalController::class);
    Route::get('latest-cardinal', [CardinalController::class, 'cardinalList']);
    Route::apiResource('users', UserController::class); //회원관리
    Route::apiResource('trains', TrainController::class); //훈련관리
    Route::apiResource('roles', RoleController::class); //Role 관리
    Route::get('cardinal-category', [TrainController::class, 'cardinalCategory']);
    Route::get('board-category', [TrainController::class, 'boardCategory']);
    Route::apiResource('notices', NoticeController::class); //소식관리
    Route::apiResource('forms', FormController::class); //신청서관리
    Route::apiResource('participants', ParticipantController::class); //참석자관리
    Route::apiResource('calendars', CalendarController::class);
    Route::apiResource('locations', LocationController::class);
    Route::apiResource('asks', AskController::class); 
    Route::apiResource('buses', BusController::class);
    Route::apiResource('donations', DonationController::class);
    Route::post('donations/{id}', [DonationController::class, 'updateDonation']);
    Route::post('donations/profile/{id}', [DonationController::class, 'profile']);
    Route::apiResource('categories', CategoryController::class);
    Route::post('users/profile/{id}', [UserController::class, 'profile']);
    Route::post('notices/profile/{id}', [NoticeController::class, 'profile']);
    Route::post('notices/files/{id}', [NoticeController::class, 'fileUpdate']);
    Route::post('trains/profile/{id}', [NoticeController::class, 'profile']);
    Route::post('trains/files/{id}', [NoticeController::class, 'fileUpdate']);
    Route::post('store/image', [NoticeController::class, 'storeImage']);
});