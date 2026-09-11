<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AvalonQuestCard extends Model
{
    protected $fillable = [
        'room_id',
        'round',
        'room_player_id',
        'card',
    ];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function player()
    {
        return $this->belongsTo(RoomPlayer::class, 'room_player_id');
    }
}
