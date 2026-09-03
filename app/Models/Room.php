<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    protected $fillable = [
        'code',
        'game_type',
        'status',
        'host_name'
    ];

    public function players()
    {
        return $this->hasMany(RoomPlayer::class);
    }
}
