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
        Schema::create('day_votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained('rooms')->onDelete('cascade');
            $table->unsignedInteger('round');
            $table->foreignId('voter_room_player_id')->constrained('room_players')->onDelete('cascade');
            $table->foreignId('target_room_player_id')->constrained('room_players')->onDelete('cascade');
            $table->timestamps();

            // Isang boto lang bawat player kada round ng Day phase, pag nag-resubmit i-a-update na lang existing row.
            $table->unique(['room_id', 'round', 'voter_room_player_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('day_votes');
    }
};
