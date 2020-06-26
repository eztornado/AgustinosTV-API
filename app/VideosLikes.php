<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VideosLikes extends Model
{
    protected $fillable = [
        'video_id',
        'user_id'
    ];
    
    protected $table = 'videos_likes';
    //
}
