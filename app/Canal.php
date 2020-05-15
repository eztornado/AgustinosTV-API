<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Canal extends Model
{
    protected $table = 'canales';
    protected $fillable = [
        'nombre',
        'imagen',
        'estado',
        'delegacion_id',
        'ruta',
        'url',
        'likes',
    ];
    //
    
    public function Delegacion()
    {
        return $this->belongsTo('App\Delegacion');
    }     
}
