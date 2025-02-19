<?php

use App\Http\Controllers\API\JugadorController;
use App\Http\Controllers\API\PartidaController;
use App\Http\Controllers\API\TorneoController;
use Illuminate\Support\Facades\Route;

Route::middleware('api')->group(function () {

    Route::prefix('torneos')->group(function () {
        Route::get('/', [TorneoController::class, 'index']);  // Obtener todos los torneos //TESTED
        Route::get('{id}', [TorneoController::class, 'show'])->middleware('validate.id'); // Obtener torneo por ID //TESTED
        Route::post('/', [TorneoController::class, 'store']); // Crear torneo //TESTED
        Route::put('{id}', [TorneoController::class, 'update'])->middleware('validate.id'); // Actualizar torneo //TESTED
        Route::delete('{id}', [TorneoController::class, 'destroy'])->middleware('validate.id'); // Eliminar torneo //TESTED
        Route::get('{id}/partidas', [TorneoController::class, 'partidas'])->middleware('validate.id'); // Obtener partidas de un torneo //TESTED
        Route::get('{id}/estado', [TorneoController::class, 'estadoTorneo'])->middleware('validate.id'); // Obtener estado de un torneo //TESTED
        Route::get('{id}/ronda/{ronda}', [TorneoController::class, 'partidasPorRonda'])->middleware('validate.id'); // Obtener partidas de cierta ronda de un torneo //TESTED
        Route::post('{id}/asignar-jugadores', [TorneoController::class, 'asignarJugadores']); //asignar jugadores a torneo //TESTED
        Route::get('{id}/comenzar', [TorneoController::class, 'comenzarTorneo']); //comenzar un torneo //TESTED
    });

    Route::prefix('jugadores')->group(function () {
        Route::get('/', [JugadorController::class, 'index']); // Obtener todos los jugadores //TESTED
        Route::get('{id}', [JugadorController::class, 'show'])->middleware('validate.id'); // Obtener jugador por ID //TESTED
        Route::get('dni/{dni}', [JugadorController::class, 'showByDni']); // Obtener jugador por DNI //TESTED
        Route::post('/', [JugadorController::class, 'store']); // Crear jugador //TESTED
        Route::put('{id}', [JugadorController::class, 'update'])->middleware('validate.id'); // Actualizar jugador //TESTED
        Route::delete('{id}', [JugadorController::class, 'destroy'])->middleware('validate.id'); // Eliminar jugador //TESTED
        Route::get('{id}/torneos', [JugadorController::class, 'torneos'])->middleware('validate.id'); // Torneos en los que participa un jugador (filtros generales - ganadas - perdidas) //TESTED
        Route::get('{id}/partidas', [JugadorController::class, 'partidas'])->middleware('validate.id'); // Obtener las partidas jugadas del jugador (filtros ganadas - perdidas) //TESTED
    });

    Route::prefix('partidas')->group(function () {
        Route::get('{id}', [PartidaController::class, 'show'])->name('partidas.show')->middleware('validate.id'); // Obtener partida por ID //TESTED
    });
});
