<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Delegacion extends Model
{
    //
    protected $table = 'delegaciones';
    protected $fillable= [
        'nombre',
        'web',
        'imagen'
    ];
}

