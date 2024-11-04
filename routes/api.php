<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\SymptomController;
use App\Http\Controllers\Api\DiagnosisController;
use App\Http\Controllers\Api\ConsultationController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->post('logout', [AuthController::class, 'logout']);
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user/{id}', [UserController::class, 'getUserById']);
    Route::apiResource('users', UserController::class);
    Route::apiResource('symptoms', SymptomController::class);
    Route::apiResource('diagnoses', DiagnosisController::class);
    Route::apiResource('consultations', ConsultationController::class);
});