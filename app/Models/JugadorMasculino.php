<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class JugadorMasculino extends Model
{

    use HasFactory;

    protected $table = "jugadores_masculinos";

    protected $fillable = ['id', 'fuerza', 'velocidad'];

    public $incrementing = false;
    protected $keyType = 'int';
    protected $primaryKey = 'id';

    //Relación uno a uno con Jugador
    public function jugador()
    {
        return $this->belongsTo(Jugador::class, 'id');
    }
}
