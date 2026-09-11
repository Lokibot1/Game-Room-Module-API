<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AvalonAssassination extends Model
{
    protected $fillable = [
        'room_id',
        'assassin_room_player_id',
        'target_room_player_id',
    ];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function assassin()
    {
        return $this->belongsTo(RoomPlayer::class, 'assassin_room_player_id');
    }

    public function target()
    {
        return $this->belongsTo(RoomPlayer::class, 'target_room_player_id');
    }
}
