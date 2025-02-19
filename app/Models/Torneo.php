<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Torneo extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['nombre', 'tipo', 'fecha', 'estado', 'ganador_id'];

    //Relación uno a muchos con partidas
    public function partidas()
    {
        return $this->hasMany(Partida::class);
    }

    //Relación de muchos a muchos de tabla intermedia con jugadores
    public function jugadores()
    {
        return $this->belongsToMany(Jugador::class, 'torneo_jugador');
    }

    //Relación uno a uno 
    public function ganador()
    {
        return $this->belongsTo(Jugador::class, 'ganador_id');
    }
}
