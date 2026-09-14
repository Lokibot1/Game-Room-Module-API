<?php

use App\Http\Controllers\Api\AvalonAssassinationController;
use App\Http\Controllers\Api\AvalonProposalController;
use App\Http\Controllers\Api\AvalonQuestCardController;
use App\Http\Controllers\Api\AvalonVoteController;
use App\Http\Controllers\Api\DayVoteController;
use App\Http\Controllers\Api\NightActionController;
use App\Http\Controllers\Api\RoomController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// para hindi mag-spin down yung render kasi inactive.
Route::get('/ping', function () {
    return response()->json(['status' => 'ok']);
});

// Room APIs
Route::post('/rooms', [RoomController::class, 'store']); //create
Route::get('/rooms/{code}', [RoomController::class, 'show']); //get room by code
Route::post('/rooms/{code}/join', [RoomController::class, 'join']); //join room syempre by code uli
Route::get('/rooms/{code}/players', [RoomController::class, 'getPlayers']); //eto para makita mo ibang tao sa lobby
Route::post('/rooms/{code}/leave', [RoomController::class, 'leave']); //pag-alis sa room, nade-delete room pag wala ng natitirang player
Route::post('/rooms/{code}/start', [RoomController::class, 'start']); //host lang pwede mag-start, dito nagiging in_progress yung status para makita ng iba na nagsimula na
Route::post('/rooms/{code}/restart', [RoomController::class, 'restart']); //"Play Again" - host lang, binubura ang night actions/day votes at babalik sa waiting

// Mafia
// Night Action APIs (Mafia target/Detective investigation/Doctor protection)
Route::post('/rooms/{code}/night-actions', [NightActionController::class, 'submit']); //i-submit/i-update ang sariling action sa isang round
Route::get('/rooms/{code}/night-actions', [NightActionController::class, 'index']); //kunin lahat ng na-submit na actions (?round=1, o lahat kapag wala)
// Day Vote APIs 
Route::post('/rooms/{code}/day-votes', [DayVoteController::class, 'submit']); //i-submit/i-update ang sariling boto sa isang round
Route::get('/rooms/{code}/day-votes', [DayVoteController::class, 'index']); //kunin lahat ng na-submit na boto (?round=1, o lahat kapag wala)


//AVALON APIs
Route::post('/rooms/{code}/avalon/proposals', [AvalonProposalController::class, 'submit']);
Route::get('/rooms/{code}/avalon/proposals', [AvalonProposalController::class, 'index']);
Route::post('/rooms/{code}/avalon/votes', [AvalonVoteController::class, 'submit']);
Route::get('/rooms/{code}/avalon/votes', [AvalonVoteController::class, 'index']);
Route::post('/rooms/{code}/avalon/quest-cards', [AvalonQuestCardController::class, 'submit']);
Route::get('/rooms/{code}/avalon/quest-cards', [AvalonQuestCardController::class, 'index']);
Route::post('/rooms/{code}/avalon/assassination', [AvalonAssassinationController::class, 'submit']);
Route::get('/rooms/{code}/avalon/assassination', [AvalonAssassinationController::class, 'show']);


// Route::get('/api/custom', [RoomController::class, 'customMethod']);
