<?php

use App\Http\Controllers\AccessController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ScanController;
use App\Http\Controllers\VisitorController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    Route::get('/visitors', [VisitorController::class, 'index']);
    Route::post('/visitors', [VisitorController::class, 'store']);
    Route::get('/visitors/{visitor}', [VisitorController::class, 'show']);
    Route::put('/visitors/{visitor}', [VisitorController::class, 'update']);
    Route::post('/visitors/{visitor}/anonymize', [VisitorController::class, 'anonymize']);

    Route::get('/accesses', [AccessController::class, 'index']);
    Route::post('/accesses', [AccessController::class, 'store']);
    Route::post('/accesses/{access}/exit', [AccessController::class, 'close']);

    Route::get('/reports/summary', [ReportController::class, 'summary']);
    Route::get('/departments', [DepartmentController::class, 'index']);
    Route::post('/departments', [DepartmentController::class, 'store']);
});

Route::post('/scan', ScanController::class);
