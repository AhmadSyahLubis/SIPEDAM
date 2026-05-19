<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes — SIPEDAM
|--------------------------------------------------------------------------
*/

// Public Auth Routes
Route::prefix('auth')->group(function () {
    Route::post('register', RegisterController::class);
    Route::post('login', LoginController::class);
});

// Protected Auth Routes (require JWT)
Route::prefix('auth')->middleware('jwt.verify')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::get('me', [AuthController::class, 'me']);
});

// Categories Endpoint (Public for authenticated users)
Route::middleware('jwt.verify')->get('categories', [\App\Http\Controllers\Api\CategoryController::class, 'index']);

// Protected Admin Routes
Route::prefix('admin')->middleware(['jwt.verify', 'role:admin'])->group(function () {
    Route::get('dashboard/stats', [\App\Http\Controllers\Admin\DashboardController::class, 'getStats']);
    
    // Reports Management
    Route::get('reports', [\App\Http\Controllers\Api\ReportController::class, 'index']);
    Route::put('reports/{id}/status', [\App\Http\Controllers\Api\ReportController::class, 'updateStatus']);
    
    // Services Management
    Route::get('services', [\App\Http\Controllers\Api\ServiceRequestController::class, 'index']);
    Route::put('services/{id}/status', [\App\Http\Controllers\Api\ServiceRequestController::class, 'updateStatus']);
    
    // Categories Management
    Route::post('categories', [\App\Http\Controllers\Api\CategoryController::class, 'store']);
    Route::put('categories/{id}', [\App\Http\Controllers\Api\CategoryController::class, 'update']);
    Route::delete('categories/{id}', [\App\Http\Controllers\Api\CategoryController::class, 'destroy']);
    Route::put('categories/{id}/toggle', [\App\Http\Controllers\Api\CategoryController::class, 'toggleActive']);
    
    // User Data Management
    Route::get('users', [\App\Http\Controllers\Api\UserController::class, 'index']);
});

// Protected User Routes
Route::prefix('user')->middleware(['jwt.verify', 'role:user'])->group(function () {
    Route::get('dashboard/stats', [\App\Http\Controllers\User\DashboardController::class, 'getStats']);
    
    // User Reports
    Route::get('reports', [\App\Http\Controllers\Api\ReportController::class, 'index']);
    Route::post('reports', [\App\Http\Controllers\Api\ReportController::class, 'store']);
    Route::delete('reports/{id}', [\App\Http\Controllers\Api\ReportController::class, 'destroy']);
    
    // User Services
    Route::get('services', [\App\Http\Controllers\Api\ServiceRequestController::class, 'index']);
    Route::post('services', [\App\Http\Controllers\Api\ServiceRequestController::class, 'store']);
    Route::delete('services/{id}', [\App\Http\Controllers\Api\ServiceRequestController::class, 'destroy']);
    
    // User Profile
    Route::get('profile', [\App\Http\Controllers\Api\ProfileController::class, 'get']);
    Route::put('profile', [\App\Http\Controllers\Api\ProfileController::class, 'update']);
    Route::put('profile/password', [\App\Http\Controllers\Api\ProfileController::class, 'changePassword']);
});

