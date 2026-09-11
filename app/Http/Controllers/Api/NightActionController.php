<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\NightAction;
use App\Models\Room;
use App\Models\RoomPlayer;
use Illuminate\Http\Request;

class NightActionController extends Controller
{
    public function submit($code, Request $request)
    {
        $validated = $request->validate([
            'room_player_id' => 'required|integer',
            'round' => 'required|integer|min:1',
            'role' => 'required|in:mafia,detective,doctor,town',
            'target_room_player_id' => 'nullable|integer',
        ]);

        $room = Room::where('code', $code)->first();
        if (!$room) {
            return response()->json(['message' => 'Room not found'], 404);
        }

        $player = RoomPlayer::where('room_id', $room->id)
            ->where('id', $validated['room_player_id'])
            ->first();
        if (!$player) {
            return response()->json(['message' => 'Player not found in this room'], 404);
        }

        $action = NightAction::updateOrCreate(
            [
                'room_id' => $room->id,
                'round' => $validated['round'],
                'room_player_id' => $player->id,
            ],
            [
                'role' => $validated['role'],
                'target_room_player_id' => $validated['target_room_player_id'] ?? null,
            ],
        );

        $room->touchActivity();

        return response()->json($action, 201);
    }

    // GET /api/rooms/{code}/night-actions - Kunin mga na-submit na actions
    public function index($code, Request $request)
    {
        $validated = $request->validate([
            'round' => 'nullable|integer|min:1',
        ]);

        $room = Room::where('code', $code)->first();
        if (!$room) {
            return response()->json(['message' => 'Room not found'], 404);
        }

        $query = NightAction::where('room_id', $room->id);
        if (!empty($validated['round'])) {
            $query->where('round', $validated['round']);
        }

        $actions = $query->orderBy('round')->orderBy('id')->get();

        return response()->json($actions);
    }
}
