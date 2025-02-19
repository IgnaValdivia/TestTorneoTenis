<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class JugadorFemenino extends Model
{

    use HasFactory;

    protected $table = "jugadores_femeninos";

    protected $fillable = ['id', 'reaccion'];

    public $incrementing = false;
    protected $keyType = 'int';
    protected $primaryKey = 'id';

    //Relación uno a uno con Jugador
    public function jugador()
    {
        return $this->belongsTo(Jugador::class, 'id');
    }
}
