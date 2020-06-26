<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Evento extends Model
{
    protected $table = 'eventos';
    protected $fillable = [
        'title',
        'descripcion',
        'inicio',
        'final',
        'delegacion_id',
        'canal_id',
        'estado',
        'views',
        'likes',        
        'max_online',
    ];
    //
    
    public function Delegacion()
    {
        return $this->belongsTo('App\Delegacion');
    }  

    public function Canal()
    {
        return $this->belongsTo('App\Canal');
    }    
    
    public function Grabaciones()
    {
        return $this->hasMany(Grabaciones::class, 'evento_id', 'id');
    }    
    
    public function Retransmisiones()
    {
        return $this->hasMany(Retransmision::class, 'evento_id', 'id');
    }        
}
