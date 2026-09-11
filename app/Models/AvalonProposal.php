<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AvalonProposal extends Model
{
    protected $fillable = [
        'room_id',
        'round',
        'attempt',
        'leader_room_player_id',
        'member_room_player_ids',
    ];

    protected $casts = [
        'member_room_player_ids' => 'array',
    ];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function leader()
    {
        return $this->belongsTo(RoomPlayer::class, 'leader_room_player_id');
    }

    public function votes()
    {
        return $this->hasMany(AvalonTeamVote::class, 'proposal_id');
    }
}
