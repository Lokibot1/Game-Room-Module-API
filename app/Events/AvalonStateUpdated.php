<?php

namespace App\Events;

use App\Models\Room;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

// Pinapaputok tuwing may bagong proposal/vote/quest-card/assassination sa Avalon. Hindi ito
// nagdadala ng buong laro state (ire-replay pa rin iyon ng frontend gamit ang GET endpoints,
// tulad ng ginagawa nito para sa night_actions/day_votes) - signal lang ito para malaman ng
// ibang players na oras na para mag-refetch, kaysa umasa lang sa 1s polling.
class AvalonStateUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Room $room, public string $type)
    {
        //
    }

    /**
     * Public channel lang, tulad ng PlayerListUpdated - walang login/auth system ang app na ito.
     *
     * @return array<int, Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('room.' . $this->room->code),
        ];
    }

    // Custom event name - kailangan ang leading dot (".avalon.updated") sa frontend listener.
    public function broadcastAs(): string
    {
        return 'avalon.updated';
    }

    /**
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return [
            'type' => $this->type,
        ];
    }
}
