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
Route::post('/rooms/{code}/leave', [RoomController::class, 'leave']); //pag-alis sa room, nade-delete room pag wala ng natitirang player
Route::post('/rooms/{code}/start', [RoomController::class, 'start']); //host lang pwede mag-start, dito nagiging in_progress yung status para makita ng iba na nagsimula na



// Route::get('/api/custom', [RoomController::class, 'customMethod']);
