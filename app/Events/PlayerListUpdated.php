<?php

namespace App\Events;

use App\Models\Room;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

// Pinapaputok tuwing may nagbago sa player list ng isang room (join/leave) - ShouldBroadcastNow
// (hindi ShouldBroadcast) para direktang mag-broadcast nang walang queue worker.
class PlayerListUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Room $room)
    {
        //
    }

    /**
     * Public channel lang - walang login/auth system ang app na ito, kaya walang
     * kailangang i-authorize (see routes/channels.php note kung private/presence channel sana).
     *
     * @return array<int, Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('room.' . $this->room->code),
        ];
    }

    // Custom event name - kailangan ang leading dot (".player.updated") sa frontend listener
    // dahil dito ito babasahin, hindi sa default namespaced class name.
    public function broadcastAs(): string
    {
        return 'player.updated';
    }

    /**
     * Buong current player list ang ipinapadala - direktang mapapalitan ito ng frontend sa
     * kanyang players array nang walang bespoke merge logic.
     *
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return [
            'players' => $this->room->players()->orderBy('id')->get(),
        ];
    }
}
