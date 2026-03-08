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
use App\Http\Controllers\UserPhotoController;
use App\Http\Controllers\ClassRoomController;

/**
 * PUBLIC ROUTES
 */
Route::post('/login', [AuthController::class, 'login']);

/**
 * PROTECTED ROUTES - Require authentication
 */
Route::middleware('auth:sanctum')->group(function () {

    /**
     * Authentication Routes
     */
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/logout-all', [AuthController::class, 'logoutAll']);
    Route::get('/me', [AuthController::class, 'me']);

    /**
     * Public Data Routes - Available to all authenticated users
     */
    Route::apiResource('subjects', SubjectController::class);
    Route::apiResource('sub-subjects', SubSubjectController::class);

    /**
     * ADMIN ROUTES
     */
    Route::prefix('admin')
        ->middleware('role:admin')
        ->group(function () {
            Route::get('/dashboard', [AdminDashboardController::class, 'index']);

            // User Import
            Route::post('/import/teachers', [UserController::class, 'importTeachers']);
            Route::post('/import/students', [UserController::class, 'importStudents']);

            // Photo Upload
            Route::post('/import/photos', [UserPhotoController::class, 'upload']);

            // User Management
            Route::apiResource('users', UserController::class);

            // Class Management
            Route::apiResource('classes', ClassRoomController::class);
            Route::post('/classes/{id}/students', [ClassRoomController::class, 'addStudents']);

            // Schedule Management
            Route::apiResource('schedules', ScheduleController::class);

            // Filtering Helpers
            Route::get('/subjects/{subject}/teachers', [ScheduleController::class, 'teachersBySubject']);
            Route::get('/subjects/{subject}/sub-subjects', [ScheduleController::class, 'subSubjectsBySubject']);

            // Schedule Views
            Route::get('/schedules/class/{classRoom}', [ScheduleController::class, 'byClass']);
            Route::get('/schedules/teacher/{teacher}', [ScheduleController::class, 'byTeacher']);
        });

    /**
     * TEACHER ROUTES
     */
    Route::prefix('teacher')
        ->middleware('role:teacher')
        ->group(function () {
            Route::get('/dashboard', [TeacherDashboardController::class, 'index']);
            Route::get('/schedules', [ScheduleController::class, 'mySchedule']);
        });

    /**
     * STUDENT ROUTES
     */
    Route::prefix('student')
        ->middleware('role:student')
        ->group(function () {
            Route::get('/dashboard', [StudentDashboardController::class, 'index']);
            Route::get('/schedules', [ScheduleController::class, 'classSchedule']);
        });
});
