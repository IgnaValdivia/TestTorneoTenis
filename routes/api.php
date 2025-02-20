<?php

use App\Http\Controllers\API\JugadorController;
use App\Http\Controllers\API\PartidaController;
use App\Http\Controllers\API\TorneoController;
use Illuminate\Support\Facades\Route;

Route::middleware('api')->group(function () {

    Route::prefix('torneos')->group(function () {
        Route::get('/', [TorneoController::class, 'index'])->name('torneos.index');  // Obtener todos los torneos 
        Route::get('{id}', [TorneoController::class, 'show'])->middleware('validate.id')->name('torneos.show'); // Obtener torneo por ID 
        Route::post('/', [TorneoController::class, 'store'])->name('torneos.store'); // Crear torneo 
        Route::put('{id}', [TorneoController::class, 'update'])->middleware('validate.id')->name('torneos.update'); // Actualizar torneo
        Route::delete('{id}', [TorneoController::class, 'destroy'])->middleware('validate.id')->name('torneos.destroy'); // Eliminar torneo
        Route::get('{id}/partidas', [TorneoController::class, 'partidas'])->middleware('validate.id')->name('torneos.partidas'); // Obtener partidas de un torneo
        Route::get('{id}/estado', [TorneoController::class, 'estadoTorneo'])->middleware('validate.id')->name('torneos.estado'); // Obtener estado de un torneo 
        Route::get('{id}/ronda/{ronda}', [TorneoController::class, 'partidasPorRonda'])->middleware('validate.id')->name('torneos.partidasPorRonda'); // Obtener partidas de cierta ronda de un torneo 
        Route::post('{id}/asignar-jugadores', [TorneoController::class, 'asignarJugadores'])->middleware('validate.id')->name('torneos.asignarJugadores'); //asignar jugadores a torneo 
        Route::get('{id}/comenzar', [TorneoController::class, 'comenzarTorneo'])->middleware('validate.id')->name('torneos.comenzar');  //comenzar un torneo 
    });

    Route::prefix('jugadores')->group(function () {
        Route::get('/', [JugadorController::class, 'index'])->name('jugadores.index'); // Obtener todos los jugadores
        Route::get('{id}', [JugadorController::class, 'show'])->middleware('validate.id')->name('jugadores.show'); // Obtener jugador por ID
        Route::get('dni/{dni}', [JugadorController::class, 'showByDni'])->name('jugadores.showByDni'); // Obtener jugador por DNI
        Route::post('/', [JugadorController::class, 'store'])->name('jugadores.store'); // Crear jugador 
        Route::put('{id}', [JugadorController::class, 'update'])->middleware('validate.id')->name('jugadores.update'); // Actualizar jugador 
        Route::delete('{id}', [JugadorController::class, 'destroy'])->middleware('validate.id')->name('jugadores.destroy'); // Eliminar jugador 
        Route::get('{id}/torneos', [JugadorController::class, 'torneos'])->middleware('validate.id')->name('jugadores.torneos'); // Torneos en los que participa un jugador (filtros generales - ganadas - perdidas)
        Route::get('{id}/partidas', [JugadorController::class, 'partidas'])->middleware('validate.id')->name('jugadores.partidas'); // Obtener las partidas jugadas del jugador (filtros ganadas - perdidas) 
    });

    Route::prefix('partidas')->group(function () {
        Route::get('{id}', [PartidaController::class, 'show'])->name('partidas.show')->middleware('validate.id'); // Obtener partida por ID 
    });
});
