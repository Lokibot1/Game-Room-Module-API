<?php

namespace App\Http\Controllers\Api;

use App\Events\AvalonStateUpdated;
use App\Http\Controllers\Controller;
use App\Models\AvalonProposal;
use App\Models\Room;
use App\Models\RoomPlayer;
use Illuminate\Http\Request;

class AvalonProposalController extends Controller
{
    // POST /api/rooms/{code}/avalon/proposals - Ang Leader ang nag-propose ng Team para sa
    // isang Quest. Awtomatikong tinutuloy ng backend ang "attempt" number bawat round, hindi
    // na kailangan ipasa ng client.
    public function submit($code, Request $request)
    {
        $validated = $request->validate([
            'leader_room_player_id' => 'required|integer',
            'round' => 'required|integer|min:1',
            'member_room_player_ids' => 'required|array|min:1',
            'member_room_player_ids.*' => 'integer',
        ]);

        $room = Room::where('code', $code)->first();
        if (!$room) {
            return response()->json(['message' => 'Room not found'], 404);
        }

        $leader = RoomPlayer::where('room_id', $room->id)
            ->where('id', $validated['leader_room_player_id'])
            ->first();
        if (!$leader) {
            return response()->json(['message' => 'Player not found in this room'], 404);
        }

        $lastAttempt = AvalonProposal::where('room_id', $room->id)
            ->where('round', $validated['round'])
            ->max('attempt');

        $proposal = AvalonProposal::create([
            'room_id' => $room->id,
            'round' => $validated['round'],
            'attempt' => ($lastAttempt ?? 0) + 1,
            'leader_room_player_id' => $leader->id,
            'member_room_player_ids' => $validated['member_room_player_ids'],
        ]);

        $room->touchActivity();
        broadcast(new AvalonStateUpdated($room, 'proposal'));

        return response()->json($proposal, 201);
    }

    // GET /api/rooms/{code}/avalon/proposals - Kunin lahat ng na-propose na Teams
    // (?round=1, o lahat kapag wala), kasama ang mga votes bawat proposal.
    public function index($code, Request $request)
    {
        $validated = $request->validate([
            'round' => 'nullable|integer|min:1',
        ]);

        $room = Room::where('code', $code)->first();
        if (!$room) {
            return response()->json(['message' => 'Room not found'], 404);
        }

        $query = AvalonProposal::where('room_id', $room->id)->with('votes');
        if (!empty($validated['round'])) {
            $query->where('round', $validated['round']);
        }

        $proposals = $query->orderBy('round')->orderBy('attempt')->get();

        return response()->json($proposals);
    }
}
