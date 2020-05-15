<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Sesiones extends Model
{
    protected $table = 'sesiones';
    protected $fillable = [
        'user_id',
        'token',
        'active',
        'isMobile',
        'isMacOs',
        'ip',
        'webBrowserData',
    ];
    //
}
