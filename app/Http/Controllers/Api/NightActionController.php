<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\NightAction;
use App\Models\Room;
use App\Models\RoomPlayer;
use Illuminate\Http\Request;

class NightActionController extends Controller
{
    // POST /api/rooms/{code}/night-actions - I-submit/i-update ang night action (target/
    // investigation/protection) Isang row lang bawat player kada round (see migration's unique constraint) - update na lang existing
    // action kapag nagbago ang pinili bago pa ma-confirm sa frontend.
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

    // GET /api/rooms/{code}/night-actions?round=1 - Kunin ang lahat ng na-submit na actions
    // para sa isang round - dito iccheck kung kumpleto na ba ang lahat ng required na actor (Mafia/Detective/Doctor) para maatapos na ang gabi.
    public function index($code, Request $request)
    {
        $validated = $request->validate([
            'round' => 'required|integer|min:1',
        ]);

        $room = Room::where('code', $code)->first();
        if (!$room) {
            return response()->json(['message' => 'Room not found'], 404);
        }

        $actions = NightAction::where('room_id', $room->id)
            ->where('round', $validated['round'])
            ->get();

        return response()->json($actions);
    }
}
