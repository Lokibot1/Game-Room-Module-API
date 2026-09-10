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
        Schema::create('night_actions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained('rooms')->onDelete('cascade');
            $table->unsignedInteger('round');
            $table->foreignId('room_player_id')->constrained('room_players')->onDelete('cascade');
            $table->enum('role', ['mafia', 'detective', 'doctor', 'town']);
            $table->foreignId('target_room_player_id')->nullable()->constrained('room_players')->nullOnDelete();
            $table->timestamps();

            // Isang action lang bawat player kada round
            $table->unique(['room_id', 'round', 'room_player_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('night_actions');
    }
};
