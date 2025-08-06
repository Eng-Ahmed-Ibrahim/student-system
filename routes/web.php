<?php

use Flasher\Laravel\Facade\Flasher;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {

    return view('admin.app')->with('success', 'تم بنجاح');
});

