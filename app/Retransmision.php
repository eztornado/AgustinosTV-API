<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Retransmision extends Model
{
    protected $fillable = [
        'evento_id',
        'canal_id',
        'delegacion_id'
    ];
    
    protected $table = 'retransmisiones';
    //
    
    public function Evento()
    {
        return $this->belongsTo('App\Evento');
    }    
    
    public function Canal()
    {
        return $this->belongsTo('App\Canal');
    }    
    
    public function Delegacion()
    {
        return $this->belongsTo('App\Delegacion');
    }        
}
