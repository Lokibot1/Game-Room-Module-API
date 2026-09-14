<?php

namespace App\Events;

use App\Models\Room;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

// Pinapaputok tuwing may bagong proposal/vote/quest-card/assassination sa Avalon. Dala nito ang
// kasalukuyang phase/sequence (Room::avalonPhase()) para agad malaman ng mga players kung anong
// bahagi na ng laro tayo (team_building/team_voting/questing/assassination/completed) sa
// pamamagitan ng Pusher, kaysa umasa pa rin sa 1s polling o hulaan ng frontend ang phase. Ang mga
// detalye ng bawat row (sino ang team, ilang boto, atbp.) kinukuha pa rin via GET endpoints.
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
        return array_merge(
            ['type' => $this->type],
            $this->room->avalonPhase(),
        );
    }
}
