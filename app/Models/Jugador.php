<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Jugador extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = "jugadores";

    protected $fillable = ['nombre', 'dni', 'genero', 'habilidad'];

    //Relación con JugadorMasculino
    public function jugadorMasculino()
    {
        return $this->hasOne(JugadorMasculino::class, 'id');
    }

    //Relación con JugadorFemenino
    public function jugadorFemenino()
    {
        return $this->hasOne(JugadorFemenino::class, 'id');
    }
}
