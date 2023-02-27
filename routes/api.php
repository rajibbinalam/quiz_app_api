<?php

use App\Http\Controllers\Api\QuizController;
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


Route::get('quiz', [QuizController::class, 'index']);
Route::post('quiz', [QuizController::class, 'store']);
Route::put('quiz/{id}', [QuizController::class, 'update']);
Route::get('quiz/{id}', [QuizController::class, 'show']);
Route::delete('quiz/{id}', [QuizController::class, 'destroy']);
