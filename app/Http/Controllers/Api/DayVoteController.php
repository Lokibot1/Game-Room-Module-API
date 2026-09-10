<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DayVote;
use App\Models\Room;
use App\Models\RoomPlayer;
use Illuminate\Http\Request;

class DayVoteController extends Controller
{
    public function submit($code, Request $request)
    {
        $validated = $request->validate([
            'voter_room_player_id' => 'required|integer',
            'round' => 'required|integer|min:1',
            'target_room_player_id' => 'required|integer',
        ]);

        $room = Room::where('code', $code)->first();
        if (!$room) {
            return response()->json(['message' => 'Room not found'], 404);
        }

        $voter = RoomPlayer::where('room_id', $room->id)
            ->where('id', $validated['voter_room_player_id'])
            ->first();
        if (!$voter) {
            return response()->json(['message' => 'Player not found in this room'], 404);
        }

        $vote = DayVote::updateOrCreate(
            [
                'room_id' => $room->id,
                'round' => $validated['round'],
                'voter_room_player_id' => $voter->id,
            ],
            [
                'target_room_player_id' => $validated['target_room_player_id'],
            ],
        );

        $room->touchActivity();

        return response()->json($vote, 201);
    }

    public function index($code, Request $request)
    {
        $validated = $request->validate([
            'round' => 'nullable|integer|min:1',
        ]);

        $room = Room::where('code', $code)->first();
        if (!$room) {
            return response()->json(['message' => 'Room not found'], 404);
        }

        $query = DayVote::where('room_id', $room->id);
        if (!empty($validated['round'])) {
            $query->where('round', $validated['round']);
        }

        $votes = $query->orderBy('round')->orderBy('id')->get();

        return response()->json($votes);
    }
}
