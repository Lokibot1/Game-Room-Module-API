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

    // Kinukwenta ang kasalukuyang phase/sequence ng Avalon game (team_building -> team_voting ->
    // questing -> assassination -> completed) galing sa mga proposal/vote/quest-card/assassination
    // rows. Ginagamit ito ni AvalonStateUpdated para may laman na agad ang broadcast sa halip na
    // mag-refetch pa lang ang frontend para malaman kung anong phase na tayo.
    public function avalonPhase(): array
    {
        $totalPlayers = $this->players()->count();

        $proposalsByRound = AvalonProposal::where('room_id', $this->id)
            ->with('votes')
            ->orderBy('attempt')
            ->get()
            ->groupBy('round');

        $questCardsByRound = AvalonQuestCard::where('room_id', $this->id)->get()->groupBy('round');

        $questResults = [];
        $successCount = 0;
        $failCount = 0;

        for ($round = 1; $round <= 5; $round++) {
            $roundProposals = $proposalsByRound->get($round, collect());

            if ($roundProposals->isEmpty()) {
                return [
                    'phase' => 'team_building',
                    'round' => $round,
                    'attempt' => 1,
                    'quest_results' => $questResults,
                ];
            }

            $latestProposal = $roundProposals->last();
            $approveCount = $latestProposal->votes->where('vote', 'approve')->count();
            $rejectCount = $latestProposal->votes->where('vote', 'reject')->count();

            if ($approveCount + $rejectCount < $totalPlayers) {
                return [
                    'phase' => 'team_voting',
                    'round' => $round,
                    'attempt' => $latestProposal->attempt,
                    'quest_results' => $questResults,
                ];
            }

            if ($approveCount <= $totalPlayers / 2) {
                if ($latestProposal->attempt >= 5) {
                    return [
                        'phase' => 'completed',
                        'round' => $round,
                        'attempt' => $latestProposal->attempt,
                        'quest_results' => $questResults,
                        'reason' => 'five_rejected_proposals',
                    ];
                }

                return [
                    'phase' => 'team_building',
                    'round' => $round,
                    'attempt' => $latestProposal->attempt + 1,
                    'quest_results' => $questResults,
                ];
            }

            $teamSize = count($latestProposal->member_room_player_ids);
            $cardsThisRound = $questCardsByRound->get($round, collect());

            if ($cardsThisRound->count() < $teamSize) {
                return [
                    'phase' => 'questing',
                    'round' => $round,
                    'attempt' => $latestProposal->attempt,
                    'quest_results' => $questResults,
                ];
            }

            $failed = $cardsThisRound->contains('card', 'fail');
            $questResults[$round] = $failed ? 'fail' : 'success';
            $failed ? $failCount++ : $successCount++;

            if ($failCount >= 3) {
                return [
                    'phase' => 'completed',
                    'round' => $round,
                    'attempt' => $latestProposal->attempt,
                    'quest_results' => $questResults,
                    'reason' => 'evil_three_fails',
                ];
            }

            if ($successCount >= 3) {
                $hasAssassination = AvalonAssassination::where('room_id', $this->id)->exists();

                return [
                    'phase' => $hasAssassination ? 'completed' : 'assassination',
                    'round' => $round,
                    'attempt' => $latestProposal->attempt,
                    'quest_results' => $questResults,
                    'reason' => $hasAssassination ? 'assassination_resolved' : null,
                ];
            }
        }

        return [
            'phase' => 'completed',
            'round' => 5,
            'attempt' => 1,
            'quest_results' => $questResults,
            'reason' => 'quest_rounds_exhausted',
        ];
    }
}
