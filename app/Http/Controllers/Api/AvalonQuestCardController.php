<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AvalonQuestCard;
use App\Models\Room;
use App\Models\RoomPlayer;
use Illuminate\Http\Request;

class AvalonQuestCardController extends Controller
{
    // POST /api/rooms/{code}/avalon/quest-cards - Isang Quest team member ang nag-susumite ng
    // Success o Fail card para sa isang round. Pag nag-resubmit, ia-update na lang.
    public function submit($code, Request $request)
    {
        $validated = $request->validate([
            'room_player_id' => 'required|integer',
            'round' => 'required|integer|min:1',
            'card' => 'required|in:success,fail',
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

        $card = AvalonQuestCard::updateOrCreate(
            [
                'room_id' => $room->id,
                'round' => $validated['round'],
                'room_player_id' => $player->id,
            ],
            [
                'card' => $validated['card'],
            ],
        );

        $room->touchActivity();

        return response()->json($card, 201);
    }

    // GET /api/rooms/{code}/avalon/quest-cards - Kunin lahat ng na-submit na cards
    // (?round=1, o lahat kapag wala)
    public function index($code, Request $request)
    {
        $validated = $request->validate([
            'round' => 'nullable|integer|min:1',
        ]);

        $room = Room::where('code', $code)->first();
        if (!$room) {
            return response()->json(['message' => 'Room not found'], 404);
        }

        $query = AvalonQuestCard::where('room_id', $room->id);
        if (!empty($validated['round'])) {
            $query->where('round', $validated['round']);
        }

        $cards = $query->orderBy('round')->orderBy('id')->get();

        return response()->json($cards);
    }
}
