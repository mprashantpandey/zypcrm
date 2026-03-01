<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;

// ── Public Auth Routes ──────────────────────────────────────────────────────
Route::post('/register/library', [AuthController::class , 'registerLibraryOwner']);
Route::post('/login', [AuthController::class , 'login']);

// Firebase Mobile Auth — verifies Firebase Phone ID token from Flutter app
Route::post('/auth/firebase', [AuthController::class , 'firebaseLogin']);

Route::post('/logout', [AuthController::class , 'logout'])->middleware('auth:sanctum');

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// ── Tenant (Library Owner) Protected Routes ─────────────────────────────────
Route::middleware(['auth:sanctum', 'role:library_owner'])->prefix('tenant')->group(function () {
    Route::apiResource('students', \App\Http\Controllers\Api\Tenant\StudentController::class);
    Route::apiResource('seats', \App\Http\Controllers\Api\Tenant\SeatController::class);
    Route::apiResource('fees', \App\Http\Controllers\Api\Tenant\FeePaymentController::class);
});

// ── Student Protected Routes (Firebase phone-auth users) ────────────────────
Route::middleware('auth:sanctum')->prefix('student')->group(function () {
    Route::get('/profile', function (Request $request) {
            return response()->json([
            'user' => $request->user()->only(['id', 'name', 'phone', 'role', 'tenant_id']),
            'tenant' => $request->user()->tenant,
            ]);
        }
        );
    });