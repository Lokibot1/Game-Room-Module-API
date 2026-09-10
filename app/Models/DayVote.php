<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DayVote extends Model
{
    protected $fillable = [
        'room_id',
        'round',
        'voter_room_player_id',
        'target_room_player_id',
    ];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function voter()
    {
        return $this->belongsTo(RoomPlayer::class, 'voter_room_player_id');
    }

    public function target()
    {
        return $this->belongsTo(RoomPlayer::class, 'target_room_player_id');
    }
}
