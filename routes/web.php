<?php

use Flasher\Laravel\Facade\Flasher;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuthController;



Route::get("/",[AuthController::class,'login_form'])->name('login');
Route::post("/login/submit",[AuthController::class,'login'])->name('login.submit');
