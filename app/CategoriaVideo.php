<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CategoriaVideo extends Model
{
    //
    protected $table = 'video_categories';
    protected $fillable = [
        'parent_id',
        'order',
        'name',
        'slug',
        'delegacion_id',
    ];
    
    public function Delegacion()
    {
        return $this->belongsTo('App\Delegacion');
    }  
    
    public function Parent()
    {
        return $this->belongsTo('App\CategoriaVideo','parent_id');
    }    
}
