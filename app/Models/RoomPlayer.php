<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoomPlayer extends Model
{
    protected $fillable = [
        'room_id',
        'player_name',
        'is_host'
    ];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }
}
