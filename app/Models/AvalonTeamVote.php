<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AvalonTeamVote extends Model
{
    protected $fillable = [
        'proposal_id',
        'room_player_id',
        'vote',
    ];

    public function proposal()
    {
        return $this->belongsTo(AvalonProposal::class, 'proposal_id');
    }

    public function player()
    {
        return $this->belongsTo(RoomPlayer::class, 'room_player_id');
    }
}
