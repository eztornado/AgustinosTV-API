<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BotScoutResult extends Model
{
    //
    protected $fillable = [
        'ip',
        'result'
    ];
    
    protected $table = 'botScoutResults';
}
