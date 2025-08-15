<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\ExamController;
use App\Http\Controllers\Admin\GroupController;
use App\Http\Controllers\Admin\RolesController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\ReportsController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\AttendanceController;

Route::get('/',[DashboardController::class,'index'])->name('dashboard');
Route::get('/roles', [RolesController::class, 'index'])->name('roles.index');
Route::post('/add-role', [RolesController::class, 'store'])->name('roles.store');
Route::get('/edit-role/{id}', [RolesController::class, 'edit'])->name('roles.edit');
Route::post('/update-role', [RolesController::class, 'update'])->name('roles.update');
Route::resource('groups', GroupController::class);

Route::resource('students', StudentController::class);
Route::post('/students/block', [StudentController::class, 'block'])->name('students.block');
Route::post('/unblock', [StudentController::class, 'unblock'])->name('students.unblock');
Route::get('/blocked-list', [StudentController::class, 'blockedList'])->name('students.blocked');


Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
Route::post('/attendance/mark/{student}/{status}', [AttendanceController::class, 'mark'])->name('attendance.mark');
Route::post('/attendance/mark-barcode', [AttendanceController::class, 'markByBarcode'])
    ->name('attendance.mark-barcode');

Route::get('/groups/by-grade', [GroupController::class, 'getByGrade']);
Route::get('/groups/exports/group-students/{id}', [GroupController::class, 'exportGroupStudents'])->name('groups.export');

Route::post('/payments/store', [PaymentController::class, 'store'])->name('payments.store');
Route::resource('exams', ExamController::class);
Route::patch('exams/update-student-score/{id}',[ExamController::class,'update_score'])->name('exams.update_student_score');

Route::name("reports.")->prefix("/reports/")->controller(ReportsController::class)->group(function () {
    Route::get('/financial','financial')->name('financial');
    Route::get('/attendance','attendance')->name('attendance');
    Route::get('/examResults','examResults')->name('examResults');
});
