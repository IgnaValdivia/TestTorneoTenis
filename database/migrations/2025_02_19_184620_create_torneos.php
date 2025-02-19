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
        Schema::create('torneos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->enum('tipo', ['Masculino', 'Femenino']);
            $table->enum('estado', ['Finalizado', 'Pendiente']);
            $table->foreignId('ganador_id')->nullable()->constrained('jugadores')->nullOnDelete();
            $table->date('fecha')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('torneos');
    }
};
