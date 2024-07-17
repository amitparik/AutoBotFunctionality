<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\API\ChallengeBotController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Challenge api endpoints
Route::get('/challengebot', [ChallengeBotController::class, 'startchallenge']);
