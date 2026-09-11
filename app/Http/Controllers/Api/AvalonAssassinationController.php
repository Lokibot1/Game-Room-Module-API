<?php

namespace App\Http\Controllers\Api;

use App\Events\AvalonStateUpdated;
use App\Http\Controllers\Controller;
use App\Models\AvalonAssassination;
use App\Models\Room;
use App\Models\RoomPlayer;
use Illuminate\Http\Request;

class AvalonAssassinationController extends Controller
{
    // POST /api/rooms/{code}/avalon/assassination - Evil's last chance, ang Assassin ang
    // nag-guguess kung sino ang Merlin. Isang beses lang ito bawat room.
    public function submit($code, Request $request)
    {
        $validated = $request->validate([
            'assassin_room_player_id' => 'required|integer',
            'target_room_player_id' => 'required|integer',
        ]);

        $room = Room::where('code', $code)->first();
        if (!$room) {
            return response()->json(['message' => 'Room not found'], 404);
        }

        $assassin = RoomPlayer::where('room_id', $room->id)
            ->where('id', $validated['assassin_room_player_id'])
            ->first();
        if (!$assassin) {
            return response()->json(['message' => 'Player not found in this room'], 404);
        }

        $target = RoomPlayer::where('room_id', $room->id)
            ->where('id', $validated['target_room_player_id'])
            ->first();
        if (!$target) {
            return response()->json(['message' => 'Target player not found in this room'], 404);
        }

        $assassination = AvalonAssassination::updateOrCreate(
            [
                'room_id' => $room->id,
            ],
            [
                'assassin_room_player_id' => $assassin->id,
                'target_room_player_id' => $target->id,
            ],
        );

        $room->touchActivity();
        broadcast(new AvalonStateUpdated($room, 'assassination'));

        return response()->json($assassination, 201);
    }

    // GET /api/rooms/{code}/avalon/assassination - Kunin ang assassination attempt (kung meron na)
    public function show($code)
    {
        $room = Room::where('code', $code)->first();
        if (!$room) {
            return response()->json(['message' => 'Room not found'], 404);
        }

        $assassination = AvalonAssassination::where('room_id', $room->id)->first();

        return response()->json($assassination);
    }
}
