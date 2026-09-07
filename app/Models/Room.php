<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    protected $fillable = [
        'code',
        'game_type',
        'status',
        'host_name',
        'last_active_at',
    ];

    protected $casts = [
        'last_active_at' => 'datetime',
    ];

    public function players()
    {
        return $this->hasMany(RoomPlayer::class);
    }

    public function touchActivity(): void
    {
        $this->forceFill(['last_active_at' => now()])->save();
    }
}
