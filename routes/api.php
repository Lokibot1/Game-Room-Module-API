<?php
use App\Http\Controllers\Api\RoomController;

Route::post('/rooms', [RoomController::class, 'store']);
Route::get('/rooms/{code}', [RoomController::class, 'show']);
Route::post('/rooms/{code}/join', [RoomController::class, 'join']);
Route::get('/rooms/{code}/players', [RoomController::class, 'getPlayers']);

?>