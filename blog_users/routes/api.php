<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\EmailVerificationController;
use App\Http\Resources\AuthenticatedUserResource;
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
Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::get('verify-email/{id}/{hash}', [EmailVerificationController::class, 'verify'])->name('verification.verify');
Route::post('resend-verification', [EmailVerificationController::class, 'resend'])->name('verification.resend');

Route::post('/login', [AuthController::class, 'login']);

Route::get('user', [AuthController::class, 'user']);

Route::middleware(['auth:sanctum'])->group(function () {
    
    route::post('/logout', [AuthController::class, 'loggout']);

});
