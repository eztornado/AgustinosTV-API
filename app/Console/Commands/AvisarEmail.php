<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Notifications\VideoSubidoNotification;
use Telegram\Bot\Api;
use GuzzleHttp\Client;

class AvisarEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'planificador:email_aviso';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        //
        $telegram = new Api(env('TELEGRAM_TOKEN'));
        $actual = \App\Planner::where('accion','RENDERIZAR_VIDEO')->where('estado','FINALIZADO')->where('emails_enviados',0)->orderBy('id','ASC')->first();
        
        if(!is_null($actual))
        {
            
            $actual->emails_enviados = 1;
            $actual->save();
            $usuarios = \App\User::where('id','>=',1)->where('active',1)->get();
            
            $telegram->sendMessage([
            'chat_id' => env('TELEGRAM_ADMIN_GROUP'),
            'text' => "[Aviso] - Enviando emails nueva subida de vídeo : ".$actual->id_video."\n"
            ]);            
            
            foreach($usuarios as $u)
            {
                    $u->notify(new VideoSubidoNotification($actual->id_video));
            }
            

        }
        //        
    }
}
