<?php

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

Route::prefix('auth')->group(function () {
    Route::post('get-token', [\App\Http\Controllers\Api\AuthController::class, 'getToken']);
});
Route::prefix('loan')->middleware('auth:sanctum')->group(function () {
    Route::put('', [\App\Http\Controllers\Api\LoanController::class, 'store']);
    Route::get('{id}', [\App\Http\Controllers\Api\LoanController::class, 'index']);
    Route::Post('{id}/approve', [\App\Http\Controllers\Api\LoanController::class, 'approveLoan']);
});
Route::prefix('loan-repayment')->middleware('auth:sanctum')->group(function () {
    Route::post('{id}/pay', [\App\Http\Controllers\Api\LoanRepaymentController::class, 'pay']);
});
