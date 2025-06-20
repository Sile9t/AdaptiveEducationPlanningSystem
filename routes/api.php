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

Route::post('login', [AuthenticateController::class, 'store']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [AuthenticateController::class, 'destroy']);
    Route::post('change-password', [ChangePasswordController::class, 'store']);
});

Route::middleware('auth:sanctum')->controller(PriorityController::class)->prefix('priority')->name('priority.')->group(function () {
    Route::get('/', 'index')->name('index');
    Route::post('/upload', 'upload')->name('upload');
});
