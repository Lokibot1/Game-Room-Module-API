<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('avalon_team_votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proposal_id')->constrained('avalon_proposals')->onDelete('cascade');
            $table->foreignId('room_player_id')->constrained('room_players');
            $table->enum('vote', ['approve', 'reject']);
            $table->timestamps();

            // Isang boto lang bawat player kada proposal, pag nag-resubmit i-a-update na lang existing row.
            $table->unique(['proposal_id', 'room_player_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('avalon_team_votes');
    }
};
