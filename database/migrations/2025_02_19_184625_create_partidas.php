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
        Schema::create('partidas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('torneo_id')->constrained('torneos')->cascadeOnDelete();
            $table->foreignId('jugador1_id')->constrained('jugadores');
            $table->integer('puntaje1')->nullable();
            $table->foreignId('jugador2_id')->constrained('jugadores');
            $table->integer('puntaje2')->nullable();
            $table->foreignId('ganador_id')->nullable()->constrained('jugadores')->nullOnDelete();
            $table->integer('ronda');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('partidas');
    }
};
