<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\LocationController;
use App\Http\Controllers\Api\DoctorController;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\EmployeeController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::post('/auth/login', [AuthController::class, 'login']);

// Test route
Route::post('/test', function () {
    return response()->json(['message' => 'POST works!']);
});

// Protected routes
Route::middleware('auth:api')->group(function () {
    // Auth
    Route::get('/auth/me', [AuthController::class, 'me']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::post('/auth/refresh', [AuthController::class, 'refresh']);

    // Locations
    Route::apiResource('locations', LocationController::class);

    // Doctors
    Route::apiResource('doctors', DoctorController::class);
    Route::get('doctors/location/{locationId}', [DoctorController::class, 'byLocation']);
    Route::post('doctors/{id}/locations', [DoctorController::class, 'assignLocation']);
    Route::delete('doctors/{id}/locations', [DoctorController::class, 'removeLocation']);

    // Services
    Route::apiResource('services', ServiceController::class);

    // Users/Staff
    Route::get('users', [UserController::class, 'index']);
    Route::put('users/{id}', [UserController::class, 'update']);
    Route::put('users/{id}/role', [UserController::class, 'updateRole']);
    Route::post('users/{id}/locations', [UserController::class, 'assignLocation']);
    Route::delete('users/{id}/locations', [UserController::class, 'removeLocation']);

    // Employees
    Route::apiResource('employees', EmployeeController::class);
    Route::post('employees/{id}/locations', [EmployeeController::class, 'assignLocation']);
    Route::delete('employees/{id}/locations', [EmployeeController::class, 'removeLocation']);
    Route::get('employees/{id}/work-hours', [EmployeeController::class, 'workHoursStats']);

    // Reports
    Route::get('reports/location/{locationId}', [ReportController::class, 'byLocation']);
    Route::post('reports/{id}/submit', [ReportController::class, 'submit']);
    Route::get('reports/{id}/export', [ReportController::class, 'export']);
    Route::post('reports/{id}/email', [ReportController::class, 'sendEmail']);
    Route::get('reports/{id}/pdf', [ReportController::class, 'downloadPdf']);
    Route::apiResource('reports', ReportController::class);
});

// Health check
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'timestamp' => now()->toISOString(),
    ]);
});
