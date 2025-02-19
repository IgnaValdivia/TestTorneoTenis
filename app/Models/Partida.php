<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Partida extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['jugador1_id', 'puntaje1', 'jugador2_id', 'puntaje2', 'ganador_id', 'ronda', 'torneo_id'];

    //Relación uno a uno 
    public function jugador1()
    {
        return $this->belongsTo(Jugador::class, 'jugador1_id');
    }

    //Relación uno a uno 
    public function jugador2()
    {
        return $this->belongsTo(Jugador::class, 'jugador2_id');
    }

    //Relación uno a uno 
    public function ganador()
    {
        return $this->belongsTo(Jugador::class, 'ganador_id');
    }

    //Relación uno a uno
    public function torneo()
    {
        return $this->belongsTo(Torneo::class, 'torneo_id');
    }
}
