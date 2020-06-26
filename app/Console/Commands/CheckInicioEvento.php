<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Telegram\Bot\Api;

class CheckInicioEvento extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gestion_eventos:check_inicio';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Comprueba cada 1 minuto si un evento comienza en ese minuto y lo pone en estado EMITIENDO mandando los emails de aviso';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        
        $eventos = DB::select("SELECT * FROM eventos
            WHERE YEAR(inicio) = YEAR(CURRENT_TIMESTAMP())
            AND MONTH(inicio) = MONTH(CURRENT_TIMESTAMP())
            AND DAY(inicio) = DAY(CURRENT_TIMESTAMP())
            AND HOUR(inicio) = HOUR(CURRENT_TIMESTAMP())
            AND MINUTE(inicio) = MINUTE(CURRENT_TIMESTAMP())
            AND estado = 'EN_ESPERA'");
        
        foreach($eventos as $e)
        {
            $event = \App\Evento::find($e->id);
            $event->estado = 'EMITIENDO';
            $event->save();
            
            $telegram = new Api(env('TELEGRAM_TOKEN'));
            $telegram->sendMessage([
            'chat_id' => env('TELEGRAM_ADMIN_GROUP'),
            'text' => "[Inicio Evento] : ".$event->title.". A la espera de una retransmisión entrante"
            ]);     
            
            //$usuarios = \App\User::where('id','>=',1)->where('active',1)->get();
            /*foreach($usuarios as $u)
            {
                    $u->notify(new \App\Notifications\InicioEventoNotification($event->id));
            }*/
            
            $u = \App\User::find(1);
            $u->notify(new \App\Notifications\InicioEventoNotification($event->id));
            
            
        }
        //
    }
}
