<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Nodo extends Model
{
   protected $fillable = [
       'nombre',
       'url',
       'estado',
       'capacidad',       
       'uso_disco',
       'ruta_videos_src',
       'ruta_videos_originales',
       'ruta_grabaciones',
       'ruta_imagenes',
   ];
   
   protected  $table = 'nodos';
    //
}
