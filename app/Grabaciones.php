<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Grabaciones extends Model
{
    
    //
    protected $table = 'grabaciones';
    
    use \Illuminate\Database\Eloquent\SoftDeletes;
    protected $fillable= [
        'nombre_fichero',
        'nodo_id',
        'evento_id',
        'canal_id',
        'video_id'
    ];    
    
    public function Canal()
    {
        return $this->belongsTo('App\Canal');
    }      

    public function Nodo()
    {
        return $this->belongsTo('App\Nodo');
    }          
    
    public function Evento()
    {
        return $this->belongsTo('App\Evento');
    }          
    
    public function Video()
    {
        return $this->belongsTo('App\Video');
    }          
}
