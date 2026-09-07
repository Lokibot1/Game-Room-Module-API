<?php

namespace App\Console\Commands;

use App\Models\Room;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('rooms:prune-abandoned')]
#[Description('Delete rooms with no recent activity from any player (abandoned lobbies)')]
class PruneAbandonedRooms extends Command
{
    /**
     * Rooms with no join/poll/leave activity for this long are considered abandoned.
     */
    private const ABANDONED_AFTER_MINUTES = 15;

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $cutoff = now()->subMinutes(self::ABANDONED_AFTER_MINUTES);

        $abandoned = Room::where(function ($query) use ($cutoff) {
            $query->where('last_active_at', '<', $cutoff)
                ->orWhereNull('last_active_at');
        })->where('created_at', '<', $cutoff);

        $count = $abandoned->count();
        $abandoned->delete();

        $this->info("Pruned {$count} abandoned room(s).");
    }
}
