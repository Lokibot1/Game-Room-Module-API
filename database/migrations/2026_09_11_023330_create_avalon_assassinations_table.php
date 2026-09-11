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
        Schema::create('avalon_assassinations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained('rooms')->onDelete('cascade');
            $table->foreignId('assassin_room_player_id')->constrained('room_players');
            $table->foreignId('target_room_player_id')->constrained('room_players');
            $table->timestamps();

            // Isang assassination attempt lang bawat room
            $table->unique('room_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('avalon_assassinations');
    }
};
