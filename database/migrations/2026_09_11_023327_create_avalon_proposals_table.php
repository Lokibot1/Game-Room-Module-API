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
        Schema::create('avalon_proposals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained('rooms')->onDelete('cascade');
            $table->unsignedInteger('round');
            $table->unsignedInteger('attempt');
            $table->foreignId('leader_room_player_id')->constrained('room_players');
            $table->json('member_room_player_ids');
            $table->timestamps();

            // Isang proposal lang bawat (round, attempt) kada room
            $table->unique(['room_id', 'round', 'attempt']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('avalon_proposals');
    }
};
