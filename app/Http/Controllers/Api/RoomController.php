<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\RoomPlayer;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RoomController extends Controller
{
    // POST /api/rooms - Create room
    public function store(Request $request)
    {
        $validated = $request->validate([
            'game_type' => 'required|in:mafia,avalon',
            'host_name' => 'nullable|string|max:255', 
        ]);

        // Generate unique code
        do {
            $code = strtoupper(Str::random(6));
        } while (Room::where('code', $code)->exists());

        $room = Room::create([
            'code' => $code,
            'game_type' => $validated['game_type'],
            'host_name' => $validated['host_name'],
        ]);

        // Add host as first player
        $player = RoomPlayer::create([
            'room_id' => $room->id,
            'player_name' => $validated['host_name'] ?? 'Host',
            'is_host' => true,
        ]);

        return response()->json([
            'room' => $room,
            'room_player_id' => $player->id,
        ], 201);
    }

    // GET /api/rooms/{code} - Fetch room by code
    public function show($code)
    {
        $room = Room::where('code', $code)->first();

        if (!$room) {
            return response()->json(['message' => 'Room not found'], 404);
        }

        return response()->json($room);
    }

    // POST /api/rooms/{code}/join - Join room
    public function join($code, Request $request)
    {
        $validated = $request->validate([
            'player_name' => 'required|string|max:255',
        ]);

        $room = Room::where('code', $code)->first();

        if (!$room) {
            return response()->json(['message' => 'Room not found'], 404);
        }

        $player = RoomPlayer::create([
            'room_id' => $room->id,
            'player_name' => $validated['player_name'],
            'is_host' => false,
        ]);

        return response()->json([
            'room' => $room,
            'room_player_id' => $player->id,
], 201);
    }

    // GET /api/rooms/{code}/players - List players in room
    public function getPlayers($code)
    {
        $room = Room::where('code', $code)->with('players')->first();

        if (!$room) {
            return response()->json(['message' => 'Room not found'], 404);
        }

        return response()->json($room->players);
    }
}