<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    //
    protected $table = 'videos';
    
    use \Illuminate\Database\Eloquent\SoftDeletes;
    protected $fillable= [
        'user_id',
        'video_category_id',
        'estado',
        'title',
        'type',
        'access',
        'details',
        'description',
        'active',
        'duration',
        'featured',
        'likes',
        'views',
        'image',
        'rutaf_video',
        'video_nombre',
        'delegacion_id',
        'nodo_id',
    ];
    
    public function User()
    {
        return $this->belongsTo('App\User');
    } 
    public function Delegacion()
    {
        return $this->belongsTo('App\Delegacion');
    }     
    public function VideoCategory()
    {
        return $this->belongsTo('App\CategoriaVideo');
    }    
    
    
}
