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
        Schema::create('jugadores_masculinos', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->primary();
            $table->foreign('id')->references('id')->on('jugadores')->cascadeOnDelete();
            $table->integer('fuerza');
            $table->integer('velocidad');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jugadores_masculinos');
    }
};
