<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\StudentDashboardController;
use App\Http\Controllers\TeacherDashboardController;

// | AUTH ROUTES


Route::post('/login', [AuthController::class, 'login']);

// | PROTECTED ROUTES (SANCTUM)


Route::middleware('auth:sanctum')->group(function () {

    // | AUTH


    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/logout-all', [AuthController::class, 'logoutAll']);
    Route::get('/me', [AuthController::class, 'me']);

    // | ADMIN ROUTES


        Route::prefix('admin')
            ->middleware('role:admin')
            ->group(function () {

        // | Dashboard
        Route::get('/dashboard', [AdminDashboardController::class, 'index']);

        // | 🔥 IMPORT USERS
        Route::post('/import/teachers', [UserController::class, 'importTeachers']);
        Route::post('/import/students', [UserController::class, 'importStudents']);

        // | Users Management
        Route::apiResource('/users', UserController::class);

        // | Schedule Management
        Route::apiResource('/schedules', ScheduleController::class);

        // | Filtering
        Route::get('/subjects/{subject}/teachers', [ScheduleController::class, 'teachersBySubject']);
        Route::get('/subjects/{subject}/sub-subjects', [ScheduleController::class, 'subSubjectsBySubject']);

        // | View Schedule
        Route::get('/schedules/class/{classRoom}', [ScheduleController::class, 'byClass']);
        Route::get('/schedules/teacher/{teacher}', [ScheduleController::class, 'byTeacher']);
    });

    // | TEACHER ROUTES


    Route::prefix('teacher')
        ->middleware('role:teacher')
        ->group(function () {

        Route::get('/dashboard', [TeacherDashboardController::class, 'index']);

        // Teacher hanya lihat jadwalnya sendiri
        Route::get('/schedules', [ScheduleController::class, 'mySchedule']);
    });

    // | STUDENT ROUTES


    Route::prefix('student')
        ->middleware('role:student')
        ->group(function () {

        Route::get('/dashboard', [StudentDashboardController::class, 'index']);

        // Student lihat jadwal berdasarkan kelasnya
        Route::get('/schedules', [ScheduleController::class, 'classSchedule']);
    });

});
