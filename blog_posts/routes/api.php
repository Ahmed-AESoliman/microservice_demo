<?php

use App\Http\Controllers\AuthController;
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
|->middleware(['tokenIsValid']);
*/
route::post('/posts', [PostController::class, 'store'])->middleware(['tokenIsValid']);
Route::post('/login', [AuthController::class, 'login']);

Route::get('user', [AuthController::class, 'user']);

// Route::middleware(['tokenIsValid'])->group(function () {
//     route::post('/user', [AuthController::class, 'user']);
//     route::post('/logout', [AuthController::class, 'loggout']);
// });
