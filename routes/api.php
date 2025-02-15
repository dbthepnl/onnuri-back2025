<?php

use Illuminate\Http\Request;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\ParticipantController;
use App\Http\Controllers\AskController;
use App\Http\Controllers\FormController;
use App\Http\Controllers\TrainController;
use App\Http\Controllers\MainController;
use App\Http\Controllers\NoticeController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::get('/user-id', [AuthController::class, 'userId']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('forget-password', [AuthController::class,'forgetPassword']);
Route::get('reset-password',[AuthController::class, 'resetPasswordLoad']);
Route::post('reset-password',[AuthController::class, 'resetPassword']);
Route::post('email-verify', [AuthController::class,'emailVerify']);
Route::post('password-reset-token', [AuthController::class,'passwordResetToken']);

//보안을 위해 미들웨어 추가
Route::middleware('auth:sanctum')->group(function() {
    //개인정보, 로그인, 로그아웃 Auth Dir > Login, Register, get/{id}
    Route::apiResource('me', 'App\Http\Controllers\Auth\AuthController'); 
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::apiResource('forms', 'App\Http\Controllers\FormController'); 
    Route::get('form-check', [FormController::class, 'formCheck']);
    Route::post('me/profile', [AuthController::class, 'profile']);
    Route::get('history', [AuthController::class, 'history']);
    Route::post('password-reset', [AuthController::class, 'passwordReset']);
});

Route::apiResource('participants', 'App\Http\Controllers\ParticipantController'); 
Route::apiResource('notices', 'App\Http\Controllers\NoticeController'); 
Route::apiResource('buses', 'App\Http\Controllers\BusController'); 
Route::apiResource('calendars', 'App\Http\Controllers\CalendarController'); 
Route::apiResource('asks', 'App\Http\Controllers\AskController'); 
Route::apiResource('categories', 'App\Http\Controllers\CategoryController'); 
Route::apiResource('trains', 'App\Http\Controllers\TrainController'); 
Route::apiResource('reservations', 'App\Http\Controllers\ReservationController'); 
Route::get('homes', [HomeController::class, 'index']);
Route::get('mains', [MainController::class, 'index']);
Route::get('calendars', [HomeController::class, 'calendar']);
Route::get('menus', [HomeController::class, 'menu']);
Route::get('gongzimes', [NoticeController::class, 'indexGongzimes']);
Route::get('gongzimes/{id}', [NoticeController::class, 'show']);
Route::get('shorts', [NoticeController::class, 'shorts']);
Route::get('videos', [NoticeController::class, 'videos']);
Route::get('popups', [NoticeController::class, 'popups']);
Route::get('sliders', [NoticeController::class, 'sliders']);

Route::apiResource('donations', 'App\Http\Controllers\DonationController'); 


//ROUTE 관리자
include 'admin.php';

