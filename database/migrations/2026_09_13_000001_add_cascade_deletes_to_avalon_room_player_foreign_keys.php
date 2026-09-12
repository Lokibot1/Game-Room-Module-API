<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// The original Avalon migrations left these room_players foreign keys without an onDelete
// behavior (default RESTRICT), unlike night_actions/day_votes which cascade. That means
// leaving a room 500s (FK constraint violation) for anyone who ever led a proposal, voted,
// played a quest card, or was named in an assassination - i.e. almost anyone past the lobby.
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('avalon_proposals', function (Blueprint $table) {
            $table->dropForeign(['leader_room_player_id']);
            $table->foreign('leader_room_player_id')->references('id')->on('room_players')->onDelete('cascade');
        });

        Schema::table('avalon_team_votes', function (Blueprint $table) {
            $table->dropForeign(['room_player_id']);
            $table->foreign('room_player_id')->references('id')->on('room_players')->onDelete('cascade');
        });

        Schema::table('avalon_quest_cards', function (Blueprint $table) {
            $table->dropForeign(['room_player_id']);
            $table->foreign('room_player_id')->references('id')->on('room_players')->onDelete('cascade');
        });

        Schema::table('avalon_assassinations', function (Blueprint $table) {
            $table->dropForeign(['assassin_room_player_id']);
            $table->dropForeign(['target_room_player_id']);
            $table->foreign('assassin_room_player_id')->references('id')->on('room_players')->onDelete('cascade');
            $table->foreign('target_room_player_id')->references('id')->on('room_players')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('avalon_proposals', function (Blueprint $table) {
            $table->dropForeign(['leader_room_player_id']);
            $table->foreign('leader_room_player_id')->references('id')->on('room_players');
        });

        Schema::table('avalon_team_votes', function (Blueprint $table) {
            $table->dropForeign(['room_player_id']);
            $table->foreign('room_player_id')->references('id')->on('room_players');
        });

        Schema::table('avalon_quest_cards', function (Blueprint $table) {
            $table->dropForeign(['room_player_id']);
            $table->foreign('room_player_id')->references('id')->on('room_players');
        });

        Schema::table('avalon_assassinations', function (Blueprint $table) {
            $table->dropForeign(['assassin_room_player_id']);
            $table->dropForeign(['target_room_player_id']);
            $table->foreign('assassin_room_player_id')->references('id')->on('room_players');
            $table->foreign('target_room_player_id')->references('id')->on('room_players');
        });
    }
};
