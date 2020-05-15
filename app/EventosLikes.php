<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EventosLikes extends Model
{
    protected $fillable = [
        'evento_id',
        'user_id'
    ];
    
    protected $table = 'eventos_likes';
    //
}
