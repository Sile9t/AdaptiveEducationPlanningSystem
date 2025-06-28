<?php

use App\Http\Controllers\Auth\AuthenticateController;
use App\Http\Controllers\Auth\ChangePasswordController;
use App\Http\Controllers\PriorityController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('login', [AuthenticateController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [AuthenticateController::class, 'logout']);
    Route::post('change-password', [ChangePasswordController::class, 'store']);
});

Route::middleware('auth:sanctum')->controller(PriorityController::class)->prefix('priorities')->name('priorities.')->group(function () {
    Route::get('/check', 'checkData')->name('checkData');
    Route::post('/upload', 'upload')->name('upload');
    Route::get('/all', 'getPriorities')->name('all');
});
