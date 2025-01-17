<?php

use App\Http\Controllers\Auth\AuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return "ERP API v1.0";
});


Route::post("/register2", function() {
    return 'Route is working!';
});
