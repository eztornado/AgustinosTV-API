<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Planner extends Model
{
    //
    protected $table = 'planificador';
    protected $fillable  = [
        'estado',
        'accion',
        'id_video',
        'porcentaje',
        'titulo_video',
        'categoria_video',
        'acceso_video',
        'detalles_video',
        'es_pendiente',
        'emails_enviados'
    ];
}
