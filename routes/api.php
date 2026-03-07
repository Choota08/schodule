<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\StudentDashboardController;
use App\Http\Controllers\TeacherDashboardController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\SubSubjectController;

// Login

Route::post('/login', [AuthController::class, 'login']);


// PROTECTED

Route::middleware('auth:sanctum')->group(function () {

    // AUTH

    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/logout-all', [AuthController::class, 'logoutAll']);
    Route::get('/me', [AuthController::class, 'me']);


    // PUBLIC

    Route::apiResource('subjects', SubjectController::class);
    Route::apiResource('sub-subjects', SubSubjectController::class);


    // ADMIN

    Route::prefix('admin')
        ->middleware('role:admin')
        ->group(function () {

        // Dashboard
        Route::get('/dashboard', [AdminDashboardController::class, 'index']);

        // Import users
        Route::post('/import/teachers', [UserController::class, 'importTeachers']);
        Route::post('/import/students', [UserController::class, 'importStudents']);

        // Users management
        Route::apiResource('users', UserController::class);

        // Schedule management
        Route::apiResource('schedules', ScheduleController::class);

        // Filtering helpers
        Route::get('/subjects/{subject}/teachers', [ScheduleController::class, 'teachersBySubject']);
        Route::get('/subjects/{subject}/sub-subjects', [ScheduleController::class, 'subSubjectsBySubject']);

        // Schedule views
        Route::get('/schedules/class/{classRoom}', [ScheduleController::class, 'byClass']);
        Route::get('/schedules/teacher/{teacher}', [ScheduleController::class, 'byTeacher']);
    });


    // Teacher

    Route::prefix('teacher')
        ->middleware('role:teacher')
        ->group(function () {

        Route::get('/dashboard', [TeacherDashboardController::class, 'index']);

        //jadwal sendiri
        Route::get('/schedules', [ScheduleController::class, 'mySchedule']);
    });


    // Student

    Route::prefix('student')
        ->middleware('role:student')
        ->group(function () {

        Route::get('/dashboard', [StudentDashboardController::class, 'index']);

        //jadwal kelasnya
        Route::get('/schedules', [ScheduleController::class, 'classSchedule']);
    });

});
