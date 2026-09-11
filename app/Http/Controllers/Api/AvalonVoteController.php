<?php

namespace App\Http\Controllers\Api;

use App\Events\AvalonStateUpdated;
use App\Http\Controllers\Controller;
use App\Models\AvalonProposal;
use App\Models\AvalonTeamVote;
use App\Models\Room;
use App\Models\RoomPlayer;
use Illuminate\Http\Request;

class AvalonVoteController extends Controller
{
    // POST /api/rooms/{code}/avalon/votes - Boto ng isang player (Approve/Reject) sa
    // kasalukuyang proposal. Pag nag-resubmit, ia-update na lang ang dating boto.
    public function submit($code, Request $request)
    {
        $validated = $request->validate([
            'proposal_id' => 'required|integer',
            'room_player_id' => 'required|integer',
            'vote' => 'required|in:approve,reject',
        ]);

        $room = Room::where('code', $code)->first();
        if (!$room) {
            return response()->json(['message' => 'Room not found'], 404);
        }

        $proposal = AvalonProposal::where('room_id', $room->id)
            ->where('id', $validated['proposal_id'])
            ->first();
        if (!$proposal) {
            return response()->json(['message' => 'Proposal not found in this room'], 404);
        }

        $player = RoomPlayer::where('room_id', $room->id)
            ->where('id', $validated['room_player_id'])
            ->first();
        if (!$player) {
            return response()->json(['message' => 'Player not found in this room'], 404);
        }

        $vote = AvalonTeamVote::updateOrCreate(
            [
                'proposal_id' => $proposal->id,
                'room_player_id' => $player->id,
            ],
            [
                'vote' => $validated['vote'],
            ],
        );

        $room->touchActivity();
        broadcast(new AvalonStateUpdated($room, 'vote'));

        return response()->json($vote, 201);
    }

    // GET /api/rooms/{code}/avalon/votes - Kunin lahat ng votes (?proposal_id=1, o lahat kapag wala)
    public function index($code, Request $request)
    {
        $validated = $request->validate([
            'proposal_id' => 'nullable|integer',
        ]);

        $room = Room::where('code', $code)->first();
        if (!$room) {
            return response()->json(['message' => 'Room not found'], 404);
        }

        $proposalIds = AvalonProposal::where('room_id', $room->id)->pluck('id');

        $query = AvalonTeamVote::whereIn('proposal_id', $proposalIds);
        if (!empty($validated['proposal_id'])) {
            $query->where('proposal_id', $validated['proposal_id']);
        }

        $votes = $query->orderBy('proposal_id')->orderBy('id')->get();

        return response()->json($votes);
    }
}
