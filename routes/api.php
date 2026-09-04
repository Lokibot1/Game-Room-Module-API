<?php

use App\Http\Controllers\Api\RoomController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Room APIs
Route::post('/rooms', [RoomController::class, 'store']); //create
Route::get('/rooms/{code}', [RoomController::class, 'show']); //get room by code
Route::post('/rooms/{code}/join', [RoomController::class, 'join']); //join room syempre by code uli
Route::get('/rooms/{code}/players', [RoomController::class, 'getPlayers']); //eto para makita mo ibang tao sa lobby

// Route::get('/api/custom', [RoomController::class, 'customMethod']);