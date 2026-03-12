<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;

// ── Public: auth config (no auth; for mobile to know allowed login methods) ─
Route::get('/auth/config', [AuthController::class, 'authConfig']);

// ── Public Auth Routes (rate-limited to reduce abuse) ───────────────────────
Route::middleware('throttle:10,1')->group(function () {
    Route::post('/register/library', [AuthController::class, 'registerLibraryOwner']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/auth/firebase', [AuthController::class, 'firebaseLogin']);
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::post('/push/token', [AuthController::class, 'updatePushToken'])->middleware('auth:sanctum');

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// ── Tenant (Library Owner) Protected Routes (rate-limited) ───────────────────
Route::middleware(['auth:sanctum', 'role:library_owner', 'throttle:60,1'])->prefix('tenant')->group(function () {
    Route::apiResource('students', \App\Http\Controllers\Api\Tenant\StudentController::class);
    Route::apiResource('seats', \App\Http\Controllers\Api\Tenant\SeatController::class);
    Route::apiResource('fees', \App\Http\Controllers\Api\Tenant\FeePaymentController::class);
});

// ── Student Protected Routes (rate-limited) ─────────────────────────────────
Route::middleware(['auth:sanctum', 'throttle:60,1'])->prefix('student')->group(function () {
    Route::get('/profile', function (Request $request) {
            return response()->json([
            'user' => $request->user()->only(['id', 'name', 'phone', 'role', 'tenant_id']),
            'tenant' => $request->user()->tenant,
            ]);
        }
        );
    });
