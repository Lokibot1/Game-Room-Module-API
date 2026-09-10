<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NightAction extends Model
{
    protected $fillable = [
        'room_id',
        'round',
        'room_player_id',
        'role',
        'target_room_player_id',
    ];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function player()
    {
        return $this->belongsTo(RoomPlayer::class, 'room_player_id');
    }

    public function target()
    {
        return $this->belongsTo(RoomPlayer::class, 'target_room_player_id');
    }
}
