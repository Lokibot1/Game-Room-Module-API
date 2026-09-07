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
            'last_active_at' => now(),
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

        $room->touchActivity();

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

        $room->touchActivity();

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

        $room->touchActivity();

        return response()->json($room->players);
    }

    // POST /api/rooms/{code}/leave - Leave room (auto-delete the room once everyone has left) eto na ung parang expire link ganern
    public function leave($code, Request $request)
    {
        $validated = $request->validate([
            'room_player_id' => 'required|integer',
        ]);

        $room = Room::where('code', $code)->first();

        if (!$room) {
            return response()->json(['message' => 'Room not found'], 404);
        }

        $player = RoomPlayer::where('room_id', $room->id)
            ->where('id', $validated['room_player_id'])
            ->first();

        if ($player) {
            $wasHost = (bool) $player->is_host;
            $player->delete();

            $remaining = $room->players()->orderBy('id')->get();

            if ($remaining->isEmpty()) {
                $room->delete();

                return response()->json(['message' => 'Room closed, no players remaining']);
            }

            if ($wasHost) {
                $newHost = $remaining->first();
                $newHost->is_host = true;
                $newHost->save();
                $room->host_name = $newHost->player_name;
            }
        }

        $room->touchActivity();

        return response()->json(['message' => 'Left room']);
    }
}
